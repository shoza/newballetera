<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

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

function save_choreo_upload(string $field, string $filename_prefix): ?string
{
    if (empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = array_merge(ALLOWED_DOC_TYPES, ALLOWED_IMG_TYPES);
    $tmp  = $_FILES[$field]['tmp_name'];
    $mime = mime_content_type($tmp);
    if (!in_array($mime, $allowed, true)) return null;
    if ($_FILES[$field]['size'] > MAX_DOC_SIZE) return null;

    $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $name = $filename_prefix . '_' . uniqid('', true) . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
    $dir  = UPLOAD_DIR . 'choreographers/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    move_uploaded_file($tmp, $dir . $name);
    return 'uploads/choreographers/' . $name;
}

$resume_path       = save_choreo_upload('resume', 'resume');
$cover_letter_path = save_choreo_upload('cover_letter', 'cover');
$bio_path          = save_choreo_upload('bio', 'bio');

try {
    if (!$pdo) throw new Exception('Database connection failed.');
    $stmt = $pdo->prepare(
        "INSERT INTO choreographer_applications
            (full_name, email, phone, message, resume_path, cover_letter_path, bio_path,
             video_link_1, video_link_2, video_link_3, artistic_statement)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $full_name, $email, $phone, $message ?: null,
        $resume_path, $cover_letter_path, $bio_path,
        $video_link_1, $video_link_2, $video_link_3,
        $artistic_statement ?: null
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

$subject = "New Choreographer Application — $full_name";
$body    = "New choreographer application received.\n\n"
         . "Name:    $full_name\n"
         . "Email:   $email\n"
         . "Phone:   $phone\n\n"
         . "Message:\n$message\n\n"
         . "Videos:\n  1. $video_link_1\n  2. $video_link_2\n  3. $video_link_3\n\n"
         . "Artistic Statement:\n$artistic_statement\n\n"
         . "Resume:       " . ($resume_path ?: 'not uploaded') . "\n"
         . "Cover Letter: " . ($cover_letter_path ?: 'not uploaded') . "\n"
         . "Bio:          " . ($bio_path ?: 'not uploaded') . "\n";

$headers = "From: " . SITE_NAME . " <noreply@newballetera.com>\r\n"
         . "Reply-To: $full_name <$email>\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

@mail(ADMIN_EMAIL, $subject, $body, $headers);

$confirm_body = "Dear $full_name,\n\nThank you for your interest in choreographing for New Ballet Era. "
    . "We have received your application and will be in touch after reviewing your materials.\n\n"
    . "Warm regards,\nNew Ballet Era";
@mail($email, "Application Received — New Ballet Era", $confirm_body, $headers);

echo json_encode(['success' => true, 'message' => 'Application submitted successfully.']);
