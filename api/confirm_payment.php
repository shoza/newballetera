<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/stripe.php';

$input  = json_decode(file_get_contents('php://input'), true) ?: [];
$pi_id  = trim($input['payment_intent_id'] ?? '');
$app_id = (int)($input['application_id']   ?? 0);
$type   = $input['type'] ?? '';  // 'dancer' or 'choreo'

if (!$pi_id || !$app_id || !in_array($type, ['dancer', 'choreo'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Verify with Stripe
$pi = stripe_get('payment_intents/' . $pi_id);
if (($pi['status'] ?? '') !== 'succeeded') {
    echo json_encode(['success' => false, 'message' => 'Payment not confirmed']);
    exit;
}

$table = $type === 'dancer' ? 'dancer_applications' : 'choreographer_applications';

// Fetch the application (check not already paid to avoid duplicate emails)
$row = $pdo->prepare("SELECT * FROM $table WHERE id = ? AND stripe_pi_id = ?");
$row->execute([$app_id, $pi_id]);
$app = $row->fetch();

if (!$app) {
    echo json_encode(['success' => false, 'message' => 'Application not found']);
    exit;
}

// Mark as paid
$pdo->prepare("UPDATE $table SET payment_status = 'paid' WHERE id = ?")
    ->execute([$app_id]);

// Send emails only once
if ($app['payment_status'] !== 'paid') {
    $name  = $app['full_name'];
    $email = $app['email'];

    if ($type === 'dancer') {
        $photos = json_decode($app['dance_photos'] ?? '[]', true) ?: [];
        $subject = "New Dancer Application — $name";
        $body    = "New dancer application received (payment confirmed).\n\n"
                 . "Name:  $name\nEmail: $email\nPhone: {$app['phone']}\n\n"
                 . "Videos:\n  1. {$app['video_link_1']}\n  2. {$app['video_link_2']}\n  3. {$app['video_link_3']}\n\n"
                 . "Headshot: " . ($app['headshot_path'] ?: 'not uploaded') . "\n"
                 . "Resume:   " . ($app['resume_path']   ?: 'not uploaded') . "\n"
                 . "Photos:   " . count($photos) . " file(s)\n";
    } else {
        $subject = "New Choreographer Application — $name";
        $body    = "New choreographer application received (payment confirmed).\n\n"
                 . "Name:  $name\nEmail: $email\nPhone: {$app['phone']}\n\n"
                 . "Message:\n{$app['message']}\n\n"
                 . "Videos:\n  1. {$app['video_link_1']}\n  2. {$app['video_link_2']}\n  3. {$app['video_link_3']}\n\n"
                 . "Resume:       " . ($app['resume_path']       ?: 'not uploaded') . "\n"
                 . "Cover Letter: " . ($app['cover_letter_path'] ?: 'not uploaded') . "\n";
    }

    send_mail(ADMIN_EMAIL,                 $subject, $body, "$name <$email>");
    send_mail('yarikfarifonov@gmail.com',  $subject, $body, "$name <$email>");

    $confirm_body = "Dear $name,\n\nThank you for applying to New Ballet Era. "
        . "Your application and payment of \$25 have been received. "
        . "We will review your materials and be in touch soon.\n\nWarm regards,\nNew Ballet Era";
    send_mail($email, "Application Received — New Ballet Era", $confirm_body);
}

echo json_encode(['success' => true]);
