<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Validate required fields
$full_name    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$video_link_1 = trim($_POST['video_link_1'] ?? '');
$video_link_2 = trim($_POST['video_link_2'] ?? '');
$video_link_3 = trim($_POST['video_link_3'] ?? '');

if (!$full_name || !$email || !$phone || !$video_link_1 || !$video_link_2 || !$video_link_3) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Helper: save uploaded file
function save_upload(string $field, string $subdir, array $allowed_types, int $max_size): ?string
{
    if (empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmp  = $_FILES[$field]['tmp_name'];
    $mime = mime_content_type($tmp);
    if (!in_array($mime, $allowed_types, true)) return null;
    if ($_FILES[$field]['size'] > $max_size) return null;

    $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $name = uniqid('', true) . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
    $dir  = UPLOAD_DIR . $subdir . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $dest = $dir . $name;
    move_uploaded_file($tmp, $dest);
    return 'uploads/' . $subdir . '/' . $name;
}

$headshot_path  = save_upload('headshot', 'dancers/headshots', ALLOWED_IMG_TYPES, MAX_HEADSHOT_SIZE);
$resume_path    = save_upload('resume', 'dancers/resumes', ALLOWED_DOC_TYPES, MAX_DOC_SIZE);

// Handle multiple dance photos
$dance_photos = [];
if (!empty($_FILES['dance_photos']['tmp_name'])) {
    $files = $_FILES['dance_photos'];
    $count = count($files['tmp_name']);
    for ($i = 0; $i < min($count, 10); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp  = $files['tmp_name'][$i];
        $mime = mime_content_type($tmp);
        if (!in_array($mime, ALLOWED_IMG_TYPES, true)) continue;
        if ($files['size'][$i] > MAX_PHOTOS_SIZE) continue;
        $ext  = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $name = uniqid('', true) . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
        $dir  = UPLOAD_DIR . 'dancers/photos/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($tmp, $dir . $name);
        $dance_photos[] = 'uploads/dancers/photos/' . $name;
    }
}

// Save to database
try {
    if (!$pdo) throw new Exception('Database connection failed.');
    $stmt = $pdo->prepare(
        "INSERT INTO dancer_applications
            (full_name, email, phone, headshot_path, dance_photos, resume_path, video_link_1, video_link_2, video_link_3)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $full_name, $email, $phone,
        $headshot_path,
        $dance_photos ? json_encode($dance_photos) : null,
        $resume_path,
        $video_link_1, $video_link_2, $video_link_3
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

// Send notification email
$subject = "New Dancer Application — $full_name";
$body    = "New dancer application received.\n\n"
         . "Name:  $full_name\n"
         . "Email: $email\n"
         . "Phone: $phone\n\n"
         . "Videos:\n  1. $video_link_1\n  2. $video_link_2\n  3. $video_link_3\n\n"
         . "Headshot: " . ($headshot_path ?: 'not uploaded') . "\n"
         . "Resume:   " . ($resume_path ?: 'not uploaded') . "\n"
         . "Photos:   " . count($dance_photos) . " file(s) uploaded\n";

$headers = "From: " . SITE_NAME . " <noreply@newballetera.com>\r\n"
         . "Reply-To: $full_name <$email>\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

@mail(ADMIN_EMAIL, $subject, $body, $headers);

// Confirmation to applicant
$confirm_body = "Dear $full_name,\n\nThank you for applying to New Ballet Era. "
    . "We have received your application and will review your materials carefully.\n\n"
    . "We will be in touch soon.\n\nWarm regards,\nNew Ballet Era";
@mail($email, "Application Received — New Ballet Era", $confirm_body, $headers);

echo json_encode(['success' => true, 'message' => 'Application submitted successfully.']);
