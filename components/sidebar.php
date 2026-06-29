<?php global $NAV_LINKS; ?>
<nav class="sidebar" id="sidebar-container">
    <?php render_component('logo', ['wrap_h1' => true]); ?>

    <!-- <span class="lang-switcher">EN &#x2304;</span> -->

    <div class="nav-links-wrap">
        <ul class="nav-links">
            <?php foreach ($NAV_LINKS as $item): ?>
                <li>
                    <a href="<?= nav_url($item['path']) ?>" class="<?= nav_active($item['path']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                    <?php if (!empty($item['children'])): ?>
                        <ul class="sub-menu">
                            <?php foreach ($item['children'] as $child): ?>
                                <li>
                                    <a href="<?= nav_url($child['path']) ?>" class="<?= nav_active($child['path']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($child['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>