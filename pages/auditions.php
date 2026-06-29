<?php
render_component('head', [
    'page_title' => 'Auditions | New Ballet Era',
    'meta_desc' => 'Join New Ballet Era as a dancer or choreographer.'
]);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <div class="page-hero" style="background-image:url('<?= BASE_URL ?>/img/IMG_4548.jpeg');">
            <h2 class="page-hero-title">Auditions</h2>
        </div>

        <div class="auditions-hub">
            <p class="auditions-hub-intro">
                New Ballet Era is seeking professional dancers and talented choreographers to join the company
                for upcoming productions and touring engagements. Choose your path below to begin your application.
            </p>
            <div class="audition-hub-cards">
                <a href="<?= nav_url('/dancers') ?>" class="audition-hub-card">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.2">
                        <circle cx="12" cy="5" r="2" />
                        <path d="M12 7v5l-3 4m3-4l3 4M9 11H7m10 0h-2" />
                    </svg>
                    <h3>For Dancers</h3>
                    <p>Apply to perform in original full-length ballets and touring productions.</p>
                    <span class="primary-btn" style="margin-top:8px;font-size:0.72rem;padding:10px 24px;">Apply
                        Now</span>
                </a>
                <a href="<?= nav_url('/choreographers') ?>" class="audition-hub-card">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    <h3>For Choreographers</h3>
                    <p>Create original works for New Ballet Era productions and artistic collaborations.</p>
                    <span class="primary-btn" style="margin-top:8px;font-size:0.72rem;padding:10px 24px;">Apply
                        Now</span>
                </a>
            </div>
        </div>

    </main>
</div>

<?php render_component('footer'); ?>