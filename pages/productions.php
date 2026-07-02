<?php
render_component('head', [
    'page_title' => 'Productions | New Ballet Era',
    'meta_desc' => 'Upcoming and past productions by New Ballet Era.'
]);
render_component('mobile-header');

require_once __DIR__ . '/../config/database.php';
$productions = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM productions ORDER BY date ASC, id DESC");
        $productions = $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log('Productions query failed: ' . $e->getMessage());
    }
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
                        $slug  = htmlspecialchars($prod['slug'] ?? '');
                        $title = htmlspecialchars($prod['title']);
                        $date  = $prod['date'] ? date('m.d.Y', strtotime($prod['date'])) : 'TBA';
                        $cover = !empty($prod['image_url']) ? BASE_URL . '/' . htmlspecialchars($prod['image_url']) : '';
                        ?>
                        <a href="<?= nav_url('/productions/' . $slug) ?>" class="production-card">
                            <div class="production-card-inner"<?= $cover ? ' style="background-image:url(\'' . $cover . '\');background-size:cover;background-position:center;"' : '' ?>>
                                <div style="aspect-ratio:16/9;"></div>
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