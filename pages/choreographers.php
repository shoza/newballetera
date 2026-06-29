<?php
render_component('head', [
    'page_title' => 'Auditions for Choreographers | New Ballet Era',
    'meta_desc' => 'Apply to create original works for New Ballet Era.',
]);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <!-- Hero -->
        <div class="page-hero" style="background-image:url('<?= BASE_URL ?>/img/IMG_4527.jpeg');">
            <h2 class="page-hero-title">Auditions for Choreographers</h2>
        </div>

        <!-- Intro text -->
        <div class="audition-intro">
            <p class="audition-intro-text">
                New Ballet Era is seeking talented choreographers interested in creating original works
                for future productions and artistic collaborations.
            </p>
        </div>

        <section class="submit-section">
            <div class="submit-section-text">
                <p style="margin-bottom:14px;">
                    The company welcomes applications from emerging and established choreographers whose
                    creative vision reflects artistic excellence and a passion for storytelling through dance.
                    Selected choreographers may have the opportunity to develop new works for New Ballet Era
                    productions, work with company dancers, and participate in choreographers' workshops.
                </p>
                <p>
                    As part of the application process, choreographers are invited to share the project they
                    would be most interested in exploring through dance and why. This is intended to provide
                    insight into each artist's creative vision and choreographic interests.
                </p>
            </div>

            <div class="separator" style="margin-bottom:40px;"></div>

            <p class="submit-section-title">Applicants are invited to submit:</p>

            <div class="submit-icons">
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/resume.png" alt="Résumé/CV" class="submit-icon-img">
                    <div class="submit-icon-label">Résumé / CV</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/resume.png" alt="Cover Letter / Bio" class="submit-icon-img">
                    <div class="submit-icon-label">Cover Letter or / and Biography</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/Video.png" alt="Video Links" class="submit-icon-img">
                    <div class="submit-icon-label">Three Video Links of previous choreographic works</div>
                </div>
                <div class="submit-icon-item">
                    <img src="<?= BASE_URL ?>/img/artist.png" alt="Artistic Statement" class="submit-icon-img">
                    <div class="submit-icon-label">Artistic Statement describing your choreographic vision</div>
                </div>
            </div>

            <p class="app-fee">Application Fee: $25 — covers printing documents, judge compensation &amp; space rental
            </p>

            <button class="fill-btn" id="toggle-form-btn" onclick="toggleForm()">Fill the Application</button>

            <!-- Application form -->
            <div class="application-form-wrap" id="application-form">
                <div id="form-success" class="form-success">
                    <h3>Application Received</h3>
                    <p>Thank you for applying to New Ballet Era.<br>We will review your materials and be in touch soon.
                    </p>
                </div>

                <form id="choreo-form" enctype="multipart/form-data" onsubmit="submitChoreoForm(event)">

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

                    <div class="form-group">
                        <label>Message / Note</label>
                        <textarea name="message"
                            placeholder="Share the project you would be most interested in exploring through dance and why…"></textarea>
                    </div>

                    <p class="form-step-title">Step 1. Upload your Résumé</p>
                    <div class="upload-box">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx"
                            onchange="showFilename(this,'resume-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">PDF or Word document (max 5 MB)</div>
                    </div>
                    <p class="upload-filename" id="resume-name"></p>

                    <p class="form-step-title">Step 2. Upload your Cover Letter or Bio (or both)</p>
                    <div class="upload-box">
                        <input type="file" name="cover_letter" accept=".pdf,.doc,.docx"
                            onchange="showFilename(this,'cover-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">Cover letter — PDF or Word (max 5 MB)</div>
                    </div>
                    <p class="upload-filename" id="cover-name"></p>
                    <div class="upload-box">
                        <input type="file" name="bio" accept=".pdf,.doc,.docx" onchange="showFilename(this,'bio-name')">
                        <div class="upload-box-icon">↑</div>
                        <div class="upload-box-text">Biography — PDF or Word (max 5 MB)</div>
                    </div>
                    <p class="upload-filename" id="bio-name"></p>

                    <p class="form-step-title">Step 3. Three Video links of previous choreographic works (links must be
                        open)</p>
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

                    <p class="form-step-title">Step 4. Artistic Statement</p>
                    <div class="form-group">
                        <textarea name="artistic_statement" rows="6"
                            placeholder="Describe your choreographic vision and creative interests…"></textarea>
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