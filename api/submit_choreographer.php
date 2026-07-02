<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../config/mail.php';

$full_name          = trim($_POST['full_name'] ?? '');
$email              = trim($_POST['email'] ?? '');
$phone              = trim($_POST['phone'] ?? '');
$message            = trim($_POST['message'] ?? '');
$artistic_statement = trim($_POST['artistic_statement'] ?? '');
$video_link_1       = trim($_POST['video_link_1'] ?? '');
$video_link_2       = trim($_POST['video_link_2'] ?? '');
$video_link_3       = trim($_POST['video_link_3'] ?? '');

if (!$full_name || !$email || !$phone || !$video_link_1 || !$video_link_2 || !$video_link_3) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}
if (!preg_match('/^[\+]?[\d\s\-\(\)]{7,20}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number.']);
    exit;
}
if (count(array_unique([$video_link_1, $video_link_2, $video_link_3])) < 3) {
    echo json_encode(['success' => false, 'message' => 'Please provide three different video links.']);
    exit;
}

$resume_path       = trim($_POST['resume_path']       ?? '') ?: null;
$cover_letter_path = trim($_POST['cover_letter_path'] ?? '') ?: null;
$bio_path          = trim($_POST['bio_path']          ?? '') ?: null;

// Save application as pending
try {
    if (!$pdo) throw new Exception('No DB connection');
    $stmt = $pdo->prepare(
        "INSERT INTO choreographer_applications
            (full_name, email, phone, message, resume_path, cover_letter_path, bio_path,
             video_link_1, video_link_2, video_link_3, artistic_statement, payment_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->execute([
        $full_name, $email, $phone, $message ?: null,
        $resume_path, $cover_letter_path, $bio_path,
        $video_link_1, $video_link_2, $video_link_3,
        $artistic_statement ?: null,
    ]);
    $app_id = (int)$pdo->lastInsertId();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

// Create Stripe PaymentIntent
if (!STRIPE_SK || STRIPE_SKIP_PAYMENT) {
    $subject = "New Choreographer Application — $full_name";
    $body    = "New choreographer application received.\n\n"
             . "Name:  $full_name\nEmail: $email\nPhone: $phone\n\n"
             . "Message:\n$message\n\n"
             . "Videos:\n  1. $video_link_1\n  2. $video_link_2\n  3. $video_link_3\n\n"
             . "Resume:       " . ($resume_path       ?: 'not uploaded') . "\n"
             . "Cover Letter: " . ($cover_letter_path ?: 'not uploaded') . "\n";
    send_mail(ADMIN_EMAIL,                $subject, $body, "$full_name <$email>");
    send_mail('yarikfarifonov@gmail.com', $subject, $body, "$full_name <$email>");

    $confirm = "Dear $full_name,\n\nThank you for applying to New Ballet Era. "
             . "We have received your application and will review your materials. "
             . "We will be in touch soon.\n\nWarm regards,\nNew Ballet Era";
    send_mail($email, "Application Received — New Ballet Era", $confirm);

    echo json_encode(['success' => true, 'skip_payment' => true]);
    exit;
}

$pi = stripe_post('payment_intents', [
    'amount'                         => STRIPE_AMOUNT,
    'currency'                       => STRIPE_CURRENCY,
    'payment_method_types[]'         => 'card',
    'description'                    => 'New Ballet Era — Choreographer Application Fee',
    'receipt_email'                  => $email,
    'metadata[application_id]'       => $app_id,
    'metadata[application_type]'     => 'choreo',
    'metadata[applicant_name]'       => $full_name,
]);

if (empty($pi['client_secret'])) {
    error_log('Stripe PI error: ' . json_encode($pi));
    echo json_encode(['success' => false, 'message' => 'Payment initialization failed. Please try again.']);
    exit;
}

$pdo->prepare("UPDATE choreographer_applications SET stripe_pi_id = ? WHERE id = ?")
    ->execute([$pi['id'], $app_id]);

echo json_encode([
    'success'        => true,
    'client_secret'  => $pi['client_secret'],
    'application_id' => $app_id,
]);
