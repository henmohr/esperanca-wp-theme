<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="publicacao-single">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $tipo = (string) get_post_meta(get_the_ID(), '_feicoop_publicacao_type', true);
        $year = (int) get_post_meta(get_the_ID(), '_feicoop_publicacao_year', true);
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
            <div class="hero hero--noimage">
                <header class="hero__content hero__content--centered">
                    <div class="wrapper">
                        <p class="hero__eyebrow"><?php esc_html_e('Produções e publicações', 'feicoop'); ?></p>
                        <h1><?php echo esc_html(get_the_title()); ?></h1>
                        <p class="publicacao-single__meta">
                            <?php if ($year > 0) : ?>
                                <time datetime="<?php echo esc_attr((string) $year); ?>"><?php echo esc_html((string) $year); ?></time>
                            <?php endif; ?>
                            <?php if ($tipo === 'video') : ?>
                                <span class="publicacao-card__type publicacao-card__type--video"><?php esc_html_e('Vídeo', 'feicoop'); ?></span>
                            <?php elseif ($tipo === 'texto') : ?>
                                <span class="publicacao-card__type publicacao-card__type--texto"><?php esc_html_e('Documento', 'feicoop'); ?></span>
                            <?php else : ?>
                                <span class="publicacao-card__type publicacao-card__type--pdf"><?php esc_html_e('PDF', 'feicoop'); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php feicoop_render_back_button(get_post_type_archive_link('publicacao'), __('Voltar para produções', 'feicoop')); ?>
                    </div>
                </header>
            </div>

            <div class="entry-wrapper content__entry publicacao-single__content">
                <?php echo wp_kses_post(feicoop_publicacao_media_html(get_the_ID())); ?>
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
