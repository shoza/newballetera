<?php
// core/render.php
require_once __DIR__ . '/../config/nav.php';

function render_component($component, $data = [])
{
    extract($data);
    $path = __DIR__ . "/../components/{$component}.php";
    if (file_exists($path)) {
        require $path;
    } else {
        echo "";
    }
}
?>