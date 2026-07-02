<?php
render_component('head', [
    'page_title' => 'Contacts | New Ballet Era',
    'meta_desc' => 'Get in touch with New Ballet Era.'
]);
render_component('mobile-header');

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        require_once __DIR__ . '/../config/settings.php';
        $to = ADMIN_EMAIL;
        $headers = "From: " . SITE_NAME . " <noreply@newballetera.com>\r\n"
            . "Reply-To: " . $name . " <" . $email . ">\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        $body = "Contact form submission from " . SITE_NAME . "\n\n"
            . "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
        @mail($to, "Contact: " . ($subject ?: 'New message'), $body, $headers);
        $sent = true;
    } else {
        $error = 'Please fill in all required fields with a valid email.';
    }
}
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <div class="page-hero">
            <h2 class="page-hero-title">Contacts</h2>
        </div>

        <div class="contacts-content">

            <!-- Contact info -->
            <div class="contacts-info">
                <h2>Get in Touch</h2>

                <div class="contact-item">
                    <div class="contact-item-label">Email</div>
                    <div class="contact-item-text">
                        <a href="mailto:info@newballetera.com">info@newballetera.com</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item-label">Based in</div>
                    <div class="contact-item-text">New York, USA</div>
                </div>

                <div class="contact-item">
                    <div class="contact-item-label">For Auditions</div>
                    <div class="contact-item-text">
                        <a href="<?= nav_url('/auditions') ?>">Open Applications &rarr;</a>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <img src="<?= BASE_URL ?>/img/Facebook.png" alt="Facebook" width="25" height="25">
                    </a>
                    <a href="mailto:info@newballetera.com" class="social-link" aria-label="Email">
                        <img src="<?= BASE_URL ?>/img/Mail.png" alt="Email" width="25" height="25">
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <img src="<?= BASE_URL ?>/img/Instagram.png" alt="Instagram" width="25" height="25">
                    </a>
                </div>
            </div>

            <!-- Contact form -->
            <div class="contact-form">
                <h2>Send a Message</h2>

                <?php if ($sent): ?>
                    <div style="padding:30px;background:#f0f9f0;border:1px solid #c3e6cb;font-size:1.5rem;line-height:1.5;">
                        Thank you for reaching out. We'll get back to you soon.
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <p style="color:#c0392b;font-size:0.8rem;margin-bottom:16px;"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <form method="POST" action="<?= nav_url('/contacts') ?>">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" required placeholder="Your name"
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required placeholder="your@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="What is this about?"
                                value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" required placeholder="Your message…"
                                rows="6"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="submit-form-btn" style="margin-top:8px;">Send</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

    </main>
</div>

<?php render_component('footer'); ?>