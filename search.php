<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="page page--search">
    <div class="content search-page">
        <div class="hero hero--noimage">
            <header class="hero__content">
                <div class="wrapper">
                    <h1><?php printf(esc_html__('Resultados da busca para: %s', 'feicoop'), esc_html(get_search_query())); ?></h1>
                </div>
            </header>
        </div>

        <div class="entry-wrapper content__entry">
            <?php get_search_form(); ?>
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
    </div>
</main>
<?php get_footer(); ?>
