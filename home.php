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
<main class="posts posts--news">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <p class="hero__eyebrow"><?php esc_html_e('Imprensa e atualizações', 'feicoop'); ?></p>
                <h1><?php echo esc_html($news_title); ?></h1>
                <p class="page__desc"><?php esc_html_e('Acompanhe a notícia em destaque e os registros mais recentes do portal FEICOOP.', 'feicoop'); ?></p>
            </div>
        </header>
    </div>

    <div class="wrapper news-archive__body">
        <?php if (have_posts()) : ?>
            <?php
            $has_featured = false;
            $has_cards = false;
            ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php if (!$has_featured) : ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('news-featured news-archive__featured'); ?>>
                        <a class="news-featured__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php echo wp_kses_post(feicoop_post_feature_image_html(get_the_ID(), 'feicoop-card')); ?></a>
                        <div class="news-featured__content">
                            <div class="feed__meta">
                                <time class="feed__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                <span class="feed__author"><?php echo esc_html(get_the_author()); ?></span>
                            </div>
                            <h2 class="news-featured__title"><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a></h2>
                            <div class="news-featured__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
                            <a class="btn btn--ghost" href="<?php the_permalink(); ?>"><?php esc_html_e('Leia a notícia', 'feicoop'); ?></a>
                        </div>
                    </article>
                    <?php $has_featured = true; ?>
                <?php else : ?>
                    <?php if (!$has_cards) : ?>
                        <div class="feed feed--cards news-archive__list">
                        <?php $has_cards = true; ?>
                    <?php endif; ?>
                    <?php get_template_part('template-parts/content', 'card'); ?>
                <?php endif; ?>
            <?php endwhile; ?>
            <?php if ($has_cards) : ?>
                </div>
            <?php endif; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
