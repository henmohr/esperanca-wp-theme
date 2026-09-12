<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$home_intro = feicoop_home_intro_fields();
$home_cooesperanca = feicoop_home_cooesperanca_fields();
$home_quicklinks = feicoop_home_quicklinks();
$home_news = feicoop_home_news_fields();
$home_hero_title_style = ' style="--hero-title-size: ' . esc_attr(feicoop_home_hero_title_size()) . ';"';
?>
<main id="main" class="home-template">
    <?php $construction_notice = feicoop_site_construction_notice(); ?>
    <?php if ($construction_notice !== '') : ?>
        <div class="site-construction" role="note">
            <p><?php echo esc_html($construction_notice); ?></p>
        </div>
    <?php endif; ?>

    <section class="hero hero--noimage">
        <div class="wrapper hero__grid">
            <header class="hero__content">
                <?php if ($home_intro['kicker'] !== '') : ?>
                    <p class="hero__eyebrow"><?php echo esc_html($home_intro['kicker']); ?></p>
                <?php endif; ?>
                <?php if ($home_intro['title'] !== '') : ?>
                    <h1<?php echo $home_hero_title_style; ?>><?php echo esc_html($home_intro['title']); ?></h1>
                <?php endif; ?>
                <?php if ($home_intro['text'] !== '') : ?>
                    <div class="hero__lead"><?php echo nl2br(esc_html($home_intro['text'])); ?></div>
                <?php endif; ?>
                <p class="hero__actions">
                    <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn"><?php esc_html_e('Ver notícias', 'feicoop'); ?></a>
                    <a href="<?php echo esc_url(feicoop_programacao_archive_url()); ?>" class="btn btn--ghost"><?php esc_html_e('FEICOOP', 'feicoop'); ?></a>
                </p>
            </header>
            <aside class="hero__panel">
                <?php if ($home_cooesperanca['kicker'] !== '') : ?>
                    <p class="hero__panel-kicker"><?php echo esc_html($home_cooesperanca['kicker']); ?></p>
                <?php endif; ?>
                <?php if ($home_cooesperanca['title'] !== '') : ?>
                    <h2><?php echo esc_html($home_cooesperanca['title']); ?></h2>
                <?php endif; ?>
                <?php if ($home_cooesperanca['text'] !== '') : ?>
                    <p><?php echo esc_html($home_cooesperanca['text']); ?></p>
                <?php endif; ?>
            </aside>
        </div>
    </section>

    <section class="section" id="noticias">
        <div class="wrapper">
            <div class="news-summary">
                <div class="section__header">
                    <?php if ($home_news['kicker'] !== '') : ?>
                        <p class="section__kicker"><?php echo esc_html($home_news['kicker']); ?></p>
                    <?php endif; ?>
                    <?php if ($home_news['title'] !== '') : ?>
                        <h2><?php echo esc_html($home_news['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <div class="news-summary__actions">
                    <?php if ($home_news['text'] !== '') : ?>
                        <p class="section__text section__text--lead"><?php echo esc_html($home_news['text']); ?></p>
                    <?php endif; ?>
                    <a class="btn btn--ghost" href="<?php echo esc_url(feicoop_posts_page_url()); ?>"><?php esc_html_e('Ir para notícias', 'feicoop'); ?></a>
                </div>
            </div>
            <div class="news-grid">
                <?php
                // O acervo histórico (2013–2018) não entra no destaque da home:
                // ele vive na própria listagem da categoria Acervo.
                $acervo_id = function_exists('feicoop_legacy_acervo_term_id') ? feicoop_legacy_acervo_term_id() : 0;
                $excluir_acervo = $acervo_id > 0 ? ['category__not_in' => [$acervo_id]] : [];

                // Prefere um post marcado como destaque na home; senão, o mais recente.
                $latest = new WP_Query(array_merge([
                    'post_type' => 'post',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    'meta_key' => '_feicoop_post_featured',
                    'meta_value' => '1',
                    'no_found_rows' => true,
                ], $excluir_acervo));

                if (!$latest->have_posts()) {
                    wp_reset_postdata();
                    $latest = new WP_Query(array_merge([
                        'post_type' => 'post',
                        'posts_per_page' => 1,
                        'post_status' => 'publish',
                        'no_found_rows' => true,
                    ], $excluir_acervo));
                }

                if ($latest->have_posts()) {
                    while ($latest->have_posts()) {
                        $latest->the_post();
                        $is_featured = (bool) get_post_meta(get_the_ID(), '_feicoop_post_featured', true);
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('news-featured'); ?>>
                            <a class="news-featured__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php echo wp_kses_post(feicoop_post_feature_image_html(get_the_ID(), 'feicoop-card')); ?></a>
                            <div class="news-featured__content">
                                <div class="feed__meta">
                                    <time class="feed__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <span class="feed__author"><?php echo esc_html(get_the_author()); ?></span>
                                </div>
                                <h3 class="news-featured__title">
                                    <?php if ($is_featured) : ?>
                                        <span class="news-featured__badge"><?php esc_html_e('Destaque', 'feicoop'); ?></span>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a>
                                </h3>
                                <div class="news-featured__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
                                <a class="btn" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler notícia', 'feicoop'); ?></a>
                            </div>
                        </article>
                        <?php
                    }

                    wp_reset_postdata();
                } else {
                    get_template_part('template-parts/content', 'none');
                }
                ?>
            </div>
        </div>
    </section>

    <section class="section section--quicklinks">
        <div class="wrapper quicklinks">
            <?php foreach ($home_quicklinks as $quicklink) : ?>
                <?php if ($quicklink['url'] === '' || $quicklink['label'] === '') : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <a class="quicklink" href="<?php echo esc_url($quicklink['url']); ?>">
                    <?php if ($quicklink['kicker'] !== '') : ?>
                        <span class="quicklink__kicker"><?php echo esc_html($quicklink['kicker']); ?></span>
                    <?php endif; ?>
                    <strong><?php echo esc_html($quicklink['label']); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>
