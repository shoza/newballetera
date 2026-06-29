<?php
$page_title = $page_title ?? 'New Ballet Era';
$meta_desc  = $meta_desc ?? 'The future of ballet begins today.';
$base_url   = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
    <meta name="base-url" content="<?= $base_url ?>"><?php // Used by JS to build correct API URLs ?>

    <link rel="stylesheet" href="<?= $base_url ?>/fonts/fonts.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/main.css">

    <?php if (isset($extra_css))
        echo $extra_css; ?>
</head>

<body class="brutalist-dark">