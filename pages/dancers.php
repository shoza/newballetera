<?php
render_component('head', [
    'page_title' => 'Auditions for Dancers | New Ballet Era',
    'meta_desc' => 'Apply to join New Ballet Era as a professional dancer.',
]);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <!-- Hero -->
        <div class="page-hero" style="background-image:url('<?= BASE_URL ?>/img/IMG_4548.jpeg');">
            <h2 class="page-hero-title">Start Your Journey</h2>
            <p class="audition-intro-text">
                New Ballet Era is seeking professional dancers to join the company for upcoming productions
                and touring engagements.
            </p>
        </div>

        <section class="submit-section">
            <div class="submit-section-text">
                Selected dancers will have the opportunity to perform in original full-length ballets,
                work with distinguished choreographers, participate in company classes led by established
                ballet masters, and attend workshops and master classes taught by internationally
                recognized artists and ballet stars.
            </div>

            <div class="separator" style="margin-bottom:40px;"></div>

            <p class="submit-section-title">Applicants are invited to submit:</p>

            <div class="submit-icons">
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/headshot.png" alt="Headshot" class="submit-icon-img">
                    <div class="submit-icon-label">Headshot</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/artist.png" alt="Dance Photographs" class="submit-icon-img">
                    <div class="submit-icon-label">Dance Photographs</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/resume.png" alt="Résumé/CV" class="submit-icon-img">
                    <div class="submit-icon-label">Résumé / CV</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/Video.png" alt="Video Links" class="submit-icon-img">
                    <div class="submit-icon-label">Three Video Links demonstrating classical ballet technique</div>
                </div>
            </div>

            <p class="app-fee">Application Fee: $25 — covers printing documents, judge compensation &amp; space rental
            </p>

            <button class="fill-btn" id="toggle-form-btn" onclick="toggleForm()">Fill the Application</button>

            <!-- Application form -->
            <div class="application-form-wrap" id="application-form">
                <div id="form-success" class="form-success">
                    <h3>Application Received</h3>
                    <p>Thank you for applying to New Ballet Era.<br>We will review your application and be in touch
                        soon.</p>
                </div>

                <form id="dancer-form" enctype="multipart/form-data" onsubmit="submitDancerForm(event)">

                    <div class="form-basics">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" required placeholder="Your full name">
                        </div>
                        <div class="form-group">
                            <label>E-mail *</label>
                            <input type="email" name="email" required placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="phone" required placeholder="+1 (000) 000-0000">
                        </div>
                    </div>

                    <p class="form-step-title">Step 1. Upload your headshot</p>
                    <div class="upload-box">
                        <input type="file" name="headshot" accept="image/*"
                            onchange="showFilename(this,'headshot-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">Click to upload (JPG, PNG — max 5 MB)</div>
                    </div>
                    <p class="upload-filename" id="headshot-name"></p>

                    <p class="form-step-title">Step 2. Upload your Dance Photos (up to 10)</p>
                    <div class="upload-box">
                        <input type="file" name="dance_photos[]" accept="image/*" multiple
                            onchange="showFilename(this,'photos-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">Click to upload multiple photos (max 10)</div>
                    </div>
                    <p class="upload-filename" id="photos-name"></p>

                    <p class="form-step-title">Step 3. Upload your Résumé</p>
                    <div class="upload-box">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx"
                            onchange="showFilename(this,'resume-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">PDF or Word document (max 5 MB)</div>
                    </div>
                    <p class="upload-filename" id="resume-name"></p>

                    <p class="form-step-title">Step 4. Three Video links demonstrating classical ballet technique and
                        performance ability</p>
                    <div class="video-links-group">
                        <div class="form-group" style="margin:0;">
                            <label>Link 1 *</label>
                            <input type="url" name="video_link_1" required placeholder="https://youtube.com/...">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label>Link 2 *</label>
                            <input type="url" name="video_link_2" required placeholder="https://youtube.com/...">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label>Link 3 *</label>
                            <input type="url" name="video_link_3" required placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <p style="font-size:0.8rem;color:#aaa;margin-bottom:20px;">
                        Step 5. Payment ($25 processing fee) — Stripe integration coming soon.
                    </p>

                    <button type="submit" class="submit-form-btn">Send Application</button>
                </form>
            </div>

        </section>

    </main>
</div>

<?php render_component('footer', ['extra_js' => '<script src="' . BASE_URL . '/js/audition-form.js"></script>']); ?>