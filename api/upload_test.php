<?php
// Temporary diagnostic — delete after debugging
header('Content-Type: application/json');
require_once __DIR__ . '/../config/settings.php';

$results = [];

// 1. PHP upload settings
$results['php_upload_max'] = ini_get('upload_max_filesize');
$results['php_post_max']   = ini_get('post_max_size');
$results['tmp_dir']        = sys_get_temp_dir();
$results['upload_dir']     = UPLOAD_DIR;

// 2. Directory write tests
foreach (['tmp', 'choreographers', 'dancers/headshots', 'dancers/resumes', 'dancers/photos'] as $sub) {
    $path = UPLOAD_DIR . $sub;
    $results['dirs'][$sub] = [
        'exists'   => is_dir($path),
        'writable' => is_writable($path),
    ];
}

// 3. Try creating a tmp subdir
$test_dir = UPLOAD_DIR . 'tmp/test_diag_' . rand(1000, 9999);
$results['mkdir_test'] = mkdir($test_dir, 0755, true) ? 'ok' : 'FAILED';
if (is_dir($test_dir)) rmdir($test_dir);

// 4. Apache process user
$results['process_user'] = function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'unknown';

echo json_encode($results, JSON_PRETTY_PRINT);
