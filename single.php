<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="post">
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/content', 'single'); ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
