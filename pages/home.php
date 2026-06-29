<?php
render_component('head', ['page_title' => 'Home | New Ballet Era']);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">
        <section class="hero-section">
            <p class="hero-title-small">New Ballet Era</p>
            <h2 class="hero-headline">The Future of Ballet Begins Today</h2>
            <div class="button-group">
                <a href="<?= nav_url('/auditions') ?>" class="primary-btn">Sign Up for Audition</a>
                <a href="<?= nav_url('/productions') ?>" class="secondary-btn">Upcoming Productions</a>
            </div>
        </section>
    </main>
</div>

<?php render_component('footer'); ?>