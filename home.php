<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$posts_page_id = (int) get_option('page_for_posts');
$news_title = $posts_page_id > 0 ? get_the_title($posts_page_id) : '';
$news_title_style = ' style="--hero-title-size: ' . esc_attr(feicoop_posts_page_title_size()) . ';"';

if ($news_title === '') {
    $news_title = __('Notícias', 'feicoop');
}
?>
<main id="main" class="posts posts--news">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1<?php echo $news_title_style; ?>><?php echo esc_html($news_title); ?></h1>
                <p class="page__desc"><?php esc_html_e('Acompanhe as publicações mais recentes do portal FEICOOP.', 'feicoop'); ?></p>
                <?php
                // O acervo histórico não entra neste feed (para não afogar as
                // notícias recentes), então precisa de um caminho explícito.
                $acervo_page = get_page_by_path('acervo', OBJECT, 'page');
                ?>
                <p class="hero__actions hero__actions--back">
                    <?php if ($acervo_page instanceof WP_Post) : ?>
                        <a class="btn btn--ghost" href="<?php echo esc_url((string) get_permalink($acervo_page)); ?>">
                            <?php esc_html_e('Ver acervo histórico', 'feicoop'); ?>
                        </a>
                    <?php endif; ?>
                    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php esc_html_e('Voltar ao início', 'feicoop'); ?>
                    </a>
                </p>
            </div>
        </header>
    </div>

    <div class="wrapper news-archive__body">
        <?php if (have_posts()) : ?>
            <div class="news-archive__list">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="news-archive__item">
                        <time class="news-archive__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        <a class="news-archive__link" href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
