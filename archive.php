<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="posts">
    <div class="hero hero--noimage">
        <header class="hero__content">
            <div class="wrapper">
                <h1><?php the_archive_title(); ?></h1>
                <p class="page__desc"><?php the_archive_description(); ?></p>
            </div>
        </header>
    </div>

    <div class="wrapper feed">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
