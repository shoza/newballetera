<?php
require_once __DIR__ . '/core/render.php';

$base_path = dirname($_SERVER['SCRIPT_NAME']);
if ($base_path === '/' || $base_path === '\\')
    $base_path = '';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!empty($base_path) && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

$request_uri = trim($request_uri, '/');
if (empty($request_uri))
    $request_uri = 'home';

$GLOBALS['current_route'] = $request_uri;

// BASE_URL: '' on GoDaddy root, '/newballetera.com' on XAMPP subfolder
define('BASE_URL', $base_path);

// Handle /productions/{slug} → production detail page
if (preg_match('#^productions/([a-z0-9\-]+)$#', $request_uri, $matches)) {
    $GLOBALS['production_slug'] = $matches[1];
    $page_path = __DIR__ . '/pages/production-detail.php';
} else {
    $page_path = __DIR__ . '/pages/' . $request_uri . '.php';
}

if (file_exists($page_path)) {
    require $page_path;
} else {
    http_response_code(404);
    $error_path = __DIR__ . '/404.php';
    if (file_exists($error_path)) {
        require $error_path;
    } else {
        echo "<h1>404 - Page Not Found</h1>";
    }
}
