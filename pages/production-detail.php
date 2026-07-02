<?php
require_once __DIR__ . '/../config/database.php';

$slug = $GLOBALS['production_slug'] ?? '';
$prod = null;

if ($pdo && $slug) {
    $stmt = $pdo->prepare("SELECT * FROM productions WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $prod = $stmt->fetch();
}

if (!$prod) {
    http_response_code(404);
    echo "<h2>Production not found.</h2>";
    exit;
}

$gallery = [];
if (!empty($prod['gallery_images'])) {
    $gallery = json_decode($prod['gallery_images'], true) ?: [];
}

$date = $prod['date'] ? date('m.d.Y', strtotime($prod['date'])) : 'TBA';

render_component('head', [
    'page_title' => htmlspecialchars($prod['title']) . ' | New Ballet Era',
    'meta_desc' => htmlspecialchars($prod['tagline'] ?? '')
]);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <!-- Hero: cover image -->
        <div class="page-hero"
            style="background-image:url('<?= BASE_URL . '/' . htmlspecialchars($prod['image_url']) ?>');">
            <h2 class="page-hero-title"><?= htmlspecialchars($prod['title']) ?></h2>
        </div>

        <!-- Description -->
        <div class="detail-content">
            <h2 class="detail-title"><?= htmlspecialchars($prod['title']) ?></h2>
            <div class="text-block">
                <?php foreach (explode("\n", trim($prod['description'] ?? '')) as $para): ?>
                    <?php if (trim($para)): ?>
                        <p><?= htmlspecialchars($para) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Photo gallery — only rendered when images exist -->
            <?php if (!empty($gallery)): ?>
            <div class="photo-gallery">
                <?php foreach ($gallery as $gImg): ?>
                    <div class="gallery-cell">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($gImg) ?>" alt="">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- CTA buttons -->
        <div class="detail-cta">
            <a href="<?= nav_url('/dancers') ?>" class="primary-btn">Sign Up as a Dancer</a>
            <a href="<?= nav_url('/choreographers') ?>" class="secondary-btn">Sign Up as a Choreographer</a>
        </div>

        <!-- Prev / Next nav -->
        <div class="production-nav">
            <a href="<?= nav_url('/productions') ?>">&larr; All Productions</a>
            <a href="<?= nav_url('/auditions') ?>">Auditions &rarr;</a>
        </div>

    </main>
</div>

<?php render_component('footer'); ?>