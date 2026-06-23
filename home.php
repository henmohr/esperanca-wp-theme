<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$posts_page_id = (int) get_option('page_for_posts');
$news_title = $posts_page_id > 0 ? get_the_title($posts_page_id) : '';

if ($news_title === '') {
    $news_title = __('Notícias', 'feicoop');
}
?>
<main class="posts">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php echo esc_html($news_title); ?></h1>
                <p class="page__desc"><?php esc_html_e('Atualizações, anúncios e matérias recentes da FEICOOP.', 'feicoop'); ?></p>
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
