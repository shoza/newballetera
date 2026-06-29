<?php
render_component('head', [
    'page_title' => 'Productions | New Ballet Era',
    'meta_desc' => 'Upcoming and past productions by New Ballet Era.'
]);
render_component('mobile-header');

require_once __DIR__ . '/../config/database.php';
$productions = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM productions ORDER BY date ASC, id DESC");
    $productions = $stmt->fetchAll();
}
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <div class="repertoire-header">
            <h2 class="repertoire-title">Repertoire</h2>
        </div>

        <div class="productions-hub">
            <div class="hub-label-wrap">
                <span class="hub-label">Upcoming Productions</span>
            </div>

            <?php if (empty($productions)): ?>
                <p style="text-align:center;padding:40px 0;">
                    Productions coming soon.
                </p>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:50px;">
                    <?php foreach ($productions as $prod): ?>
                        <?php
                        $slug = htmlspecialchars($prod['slug'] ?? '');
                        $title = htmlspecialchars($prod['title']);
                        $date = $prod['date'] ? date('m.d.Y', strtotime($prod['date'])) : 'TBA';

                        $gallery = [];
                        if (!empty($prod['gallery_images'])) {
                            $gallery = json_decode($prod['gallery_images'], true) ?: [];
                        }
                        $cells = 12;
                        ?>
                        <a href="<?= nav_url('/productions/' . $slug) ?>" class="production-card">
                            <div class="production-card-inner"<?= !empty($prod['image_url']) && empty($gallery) ? ' style="background-image:url(\'' . BASE_URL . '/' . htmlspecialchars($prod['image_url']) . '\');background-size:cover;background-position:center;"' : '' ?>>
                                <?php if (!empty($gallery)): ?>
                                <div class="production-grid">
                                    <?php for ($i = 0; $i < $cells; $i++):
                                        $img = $gallery[$i] ?? null;
                                        ?>
                                        <div class="production-grid-cell placeholder">
                                            <?php if ($img): ?>
                                                <img src="<?= htmlspecialchars($img) ?>" alt="">
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <?php else: ?>
                                <div style="aspect-ratio:16/9;"></div>
                                <?php endif; ?>
                                <div class="production-overlay">
                                    <div class="production-overlay-text"><?= $title ?></div>
                                </div>
                            </div>
                            <div class="production-meta">
                                <div class="production-meta-date"><?= $date ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php render_component('footer'); ?>