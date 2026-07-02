<?php
header('Content-Type: application/json');

// Catch warnings/notices → JSON
set_error_handler(function (int $errno, string $errstr) {
    echo json_encode(['success' => false, 'message' => "PHP warning: $errstr"]);
    exit;
});

// Catch fatal errors (E_ERROR, undefined function, etc.) → JSON
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Fatal: ' . $e['message']]);
    }
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

require_once __DIR__ . '/../config/settings.php';

$allowed_dirs = ['choreographers', 'dancers/headshots', 'dancers/resumes', 'dancers/photos'];
$subdir = $_POST['subdir'] ?? '';
if (!in_array($subdir, $allowed_dirs, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid directory']);
    exit;
}

$upload_id    = preg_replace('/[^a-z0-9]/i', '', $_POST['upload_id'] ?? '');
$chunk_index  = (int)($_POST['chunk_index'] ?? 0);
$total_chunks = (int)($_POST['total_chunks'] ?? 1);
$original     = basename($_POST['filename'] ?? 'file');

if (!$upload_id || $total_chunks < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (empty($_FILES['chunk']['tmp_name']) || $_FILES['chunk']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['chunk']['error'] ?? -1;
    echo json_encode(['success' => false, 'message' => "Chunk upload error (code $code)"]);
    exit;
}

$tmp_dir = UPLOAD_DIR . 'tmp/' . $upload_id . '/';
if (!is_dir($tmp_dir) && !mkdir($tmp_dir, 0755, true)) {
    echo json_encode(['success' => false, 'message' => 'Cannot create temp dir: ' . UPLOAD_DIR . 'tmp/']);
    exit;
}

// Prune abandoned tmp dirs older than 1 hour (safe glob — may return false if dir is empty)
$old_dirs = glob(UPLOAD_DIR . 'tmp/*', GLOB_ONLYDIR);
if (is_array($old_dirs)) {
    foreach ($old_dirs as $dir) {
        if (filemtime($dir) < time() - 3600) {
            $old_chunks = glob($dir . '/*');
            if (is_array($old_chunks)) array_map('unlink', $old_chunks);
            @rmdir($dir);
        }
    }
}

$chunk_file = $tmp_dir . 'chunk_' . sprintf('%05d', $chunk_index);
if (!move_uploaded_file($_FILES['chunk']['tmp_name'], $chunk_file)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save chunk (check folder permissions on ' . $tmp_dir . ')']);
    exit;
}

$saved = glob($tmp_dir . 'chunk_*');
if (!is_array($saved) || count($saved) < $total_chunks) {
    echo json_encode(['success' => true, 'done' => false]);
    exit;
}

// Assemble
$ext       = preg_replace('/[^a-z0-9]/i', '', pathinfo($original, PATHINFO_EXTENSION));
$final_dir = UPLOAD_DIR . $subdir . '/';
if (!is_dir($final_dir) && !mkdir($final_dir, 0755, true)) {
    echo json_encode(['success' => false, 'message' => 'Cannot create upload dir: ' . $final_dir]);
    exit;
}

$final_name = uniqid('', true) . '.' . strtolower($ext);
$final_path = $final_dir . $final_name;

$out = fopen($final_path, 'wb');
if (!$out) {
    echo json_encode(['success' => false, 'message' => 'Cannot write to ' . $final_dir]);
    exit;
}
for ($i = 0; $i < $total_chunks; $i++) {
    $data = file_get_contents($tmp_dir . 'chunk_' . sprintf('%05d', $i));
    if ($data === false) {
        fclose($out);
        @unlink($final_path);
        echo json_encode(['success' => false, 'message' => 'Missing chunk ' . $i]);
        exit;
    }
    fwrite($out, $data);
}
fclose($out);

// Cleanup temp
$chunks = glob($tmp_dir . 'chunk_*');
if (is_array($chunks)) array_map('unlink', $chunks);
@rmdir($tmp_dir);

// Validate type
$ext_lower = strtolower($ext);
$is_image  = in_array($subdir, ['dancers/headshots', 'dancers/photos']);

// MIME detection — fallback to extension map when fileinfo is unavailable
function detect_mime(string $path, string $ext): string {
    if (function_exists('mime_content_type')) {
        $m = @mime_content_type($path);
        if ($m && $m !== 'application/x-empty') return $m;
    }
    $map = [
        'jpg'  => 'image/jpeg',  'jpeg' => 'image/jpeg',
        'png'  => 'image/png',   'gif'  => 'image/gif',  'webp' => 'image/webp',
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    return $map[$ext] ?? 'application/octet-stream';
}
$mime = detect_mime($final_path, $ext_lower);

if ($is_image) {
    if (!in_array($mime, ALLOWED_IMG_TYPES, true)) {
        @unlink($final_path);
        echo json_encode(['success' => false, 'message' => 'Please upload an image (JPEG, PNG, WebP, or GIF). Detected: ' . $mime]);
        exit;
    }
} else {
    $doc_mimes = array_merge(ALLOWED_DOC_TYPES, [
        'application/zip',
        'application/x-zip-compressed',
        'application/x-zip',
        'application/octet-stream',
        'application/vnd.ms-office',
    ]);
    if (!in_array($ext_lower, ['pdf', 'doc', 'docx'], true) || !in_array($mime, $doc_mimes, true)) {
        @unlink($final_path);
        echo json_encode(['success' => false, 'message' => 'Invalid file type (' . $mime . '). Use .pdf, .doc, or .docx']);
        exit;
    }
}

// Validate size
$max = $is_image ? MAX_PHOTOS_SIZE : MAX_DOC_SIZE;
if (filesize($final_path) > $max) {
    @unlink($final_path);
    echo json_encode(['success' => false, 'message' => 'File too large (max ' . ($max / 1024 / 1024) . ' MB)']);
    exit;
}

echo json_encode(['success' => true, 'done' => true, 'path' => 'uploads/' . $subdir . '/' . $final_name]);
