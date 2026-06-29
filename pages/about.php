<?php
render_component('head', [
    'page_title' => 'About | New Ballet Era',
    'meta_desc' => 'Our story and a letter from the founder, Lola Abigail Koch.'
]);
render_component('mobile-header');
?>

<div class="layout-wrapper">
    <?php render_component('sidebar'); ?>

    <main class="content-wrapper">

        <!-- Hero -->
        <div class="page-hero" style="background-image:url('<?= BASE_URL ?>/img/IMG_4527.jpeg');">
            <h2 class="page-hero-title">About Us</h2>
        </div>

        <!-- Our Story -->
        <section class="content-section">
            <h2 class="section-heading">Our Story</h2>
            <div class="text-block">
                <p>New Ballet Era is a New York–based touring ballet company founded by Lola Abigail Koch.</p>
                <p>New Ballet Era is dedicated to creating original ballet productions and providing exceptional
                    artistic opportunities for the next generation of dance artists.</p>
                <p>The company brings together professional dancers, emerging artists, and choreographers to collaborate
                    on innovative full-length ballets and touring productions. Dancers will have the opportunity to
                    perform classical masterpieces and original works created by distinguished choreographers from a
                    variety of artistic backgrounds and styles.</p>
                <p>New Ballet Era is committed to artistic excellence and professional development. Company classes and
                    coaching will be led by established ballet masters, teachers, and répétiteurs with extensive
                    experience in the world's leading ballet institutions.</p>
                <p>Throughout the season, dancers will participate in workshops, master classes, and special artistic
                    programs led by internationally recognised artists, choreographers, company directors, and ballet
                    stars. These experiences are designed to provide mentorship, professional guidance, and exposure to
                    diverse artistic perspectives while helping young dancers transition into professional careers.</p>
                <p>Through performance, education, and touring New Ballet Era seeks to inspire audiences, support
                    emerging talent, and contribute to the future of ballet.</p>
            </div>
        </section>

        <div class="separator"></div>

        <!-- A Letter from the Founder -->
        <section class="content-section">
            <h2 class="section-heading">A Letter from the Founder</h2>
            <div class="founder-grid">
                <div class="founder-text text-block">
                    <p>Welcome to New Ballet Era.</p>
                    <p>Throughout my life, ballet has given me extraordinary opportunities, unforgettable experiences,
                        and the privilege of working with remarkable artists and choreographers around the world. As a
                        dancer, teacher, producer, and arts administrator, I have witnessed both the beauty of our art
                        form and the challenges that young artists face as they begin their professional careers.</p>
                    <p>I founded New Ballet Era with a simple but important vision: to create meaningful opportunities
                        for the next generation of dancers and choreographers while developing original productions that
                        inspire audiences and celebrate artistic excellence.</p>
                    <p>New Ballet Era is more than a ballet company. It is a creative home where talented young artists
                        can continue to grow, perform, collaborate, and learn from established leaders in the ballet
                        world. Through original full-length productions, company classes, workshops, master classes, and
                        mentorship programs, we strive to provide an environment where emerging artists can develop both
                        professionally and artistically.</p>
                    <p>Our company brings together dancers ages 18–25, accomplished choreographers, respected ballet
                        masters, and internationally recognized guest artists who share a commitment to excellence,
                        innovation, and collaboration. Together, we are building a company that honors the traditions of
                        classical ballet while embracing new ideas and new voices.</p>
                    <p>As we begin this exciting journey, I invite you to follow our work, attend our performances,
                        support our artists, and become part of the New Ballet Era community.</p>
                    <p>Thank you for joining us as we shape the future of ballet together.</p>
                </div>
                <div class="founder-photo-placeholder">
                    <img src="<?= BASE_URL ?>/img/lola.jpg" alt="Lola Abigail Koch">
                    <!-- <span>Founder Photo</span> -->
                </div>
            </div>
        </section>

    </main>
</div>

<?php render_component('footer'); ?>