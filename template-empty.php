<?php
/*
Template Name: Container vazio
*/
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="page">
    <?php while (have_posts()) : the_post(); ?>
        <div class="wrapper content__entry content__entry--nospace">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
