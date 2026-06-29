<?php global $NAV_LINKS; ?>
<header class="mobile-header">
    <?php render_component('logo', ['logo_class' => 'mobile-logo']); ?>
    <button class="hamburger-btn" id="menu-toggle" aria-label="Open Menu" onclick="openMobileNav()">
        <div class="line"></div>
        <div class="line"></div>
    </button>
</header>

<div class="mobile-nav" id="mobile-nav">
    <div class="mobile-nav-top">
        <?php render_component('logo', ['logo_class' => 'mobile-logo']); ?>
        <button class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Close Menu">&#10005;</button>
    </div>
    <div class="nav-links-wrap">
        <ul class="nav-links">
            <?php foreach ($NAV_LINKS as $item): ?>
            <li>
                <a href="<?= nav_url($item['path']) ?>" onclick="closeMobileNav()"
                   class="<?= nav_active($item['path']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
                <?php if (!empty($item['children'])): ?>
                <ul class="sub-menu">
                    <?php foreach ($item['children'] as $child): ?>
                    <li>
                        <a href="<?= nav_url($child['path']) ?>" onclick="closeMobileNav()"
                           class="<?= nav_active($child['path']) ? 'active' : '' ?>">
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
</div>

<script>
function openMobileNav()  { document.getElementById('mobile-nav').classList.add('open'); }
function closeMobileNav() { document.getElementById('mobile-nav').classList.remove('open'); }
</script>
