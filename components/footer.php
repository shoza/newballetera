<?php global $NAV_LINKS; $b = defined('BASE_URL') ? BASE_URL : ''; ?>
<footer class="site-footer">
    <div class="footer-inner">

        <a href="<?= nav_url('/') ?>" class="footer-logo">
            NEWBALLET<span class="footer-logo-era">ERA</span>
        </a>

        <nav class="footer-nav">
            <ul>
                <?php foreach (array_slice($NAV_LINKS, 0, 3) as $item): ?>
                <li><a href="<?= nav_url($item['path']) ?>"><?= htmlspecialchars($item['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <ul>
                <?php
                // Auditions + its children
                $auditions = array_values(array_filter($NAV_LINKS, fn($n) => $n['path'] === '/auditions'))[0] ?? null;
                if ($auditions):
                ?>
                <li><a href="<?= nav_url($auditions['path']) ?>"><?= htmlspecialchars($auditions['label']) ?></a></li>
                <?php foreach ($auditions['children'] ?? [] as $child): ?>
                <li><a href="<?= nav_url($child['path']) ?>"><?= htmlspecialchars($child['label']) ?></a></li>
                <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <ul>
                <?php
                $contacts = array_values(array_filter($NAV_LINKS, fn($n) => $n['path'] === '/contacts'))[0] ?? null;
                if ($contacts):
                ?>
                <li><a href="<?= nav_url($contacts['path']) ?>"><?= htmlspecialchars($contacts['label']) ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="footer-social">
            <a href="#" aria-label="Facebook">
                <img src="<?= $b ?>/img/Facebook.png" alt="Facebook" width="22" height="22">
            </a>
            <a href="mailto:info@newballetera.com" aria-label="Email">
                <img src="<?= $b ?>/img/Mail.png" alt="Email" width="22" height="22">
            </a>
            <a href="#" aria-label="Instagram">
                <img src="<?= $b ?>/img/Instagram.png" alt="Instagram" width="22" height="22">
            </a>
        </div>

    </div>

    <div class="footer-bottom">
        &copy; <?= date("Y") ?> New Ballet Era. All rights reserved.
    </div>
</footer>
<script src="<?= $b ?>/js/main.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
