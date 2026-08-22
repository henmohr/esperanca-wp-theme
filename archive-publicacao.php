<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$selected_year = (int) get_query_var('publicacao_year');
$selected_tipo = (string) get_query_var('publicacao_tipo');
$years = feicoop_publicacao_years();
$archive_url = get_post_type_archive_link('publicacao');
?>
<main id="main" class="publicacoes-archive">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php esc_html_e('Produções e publicações', 'feicoop'); ?></h1>
                <p class="page__desc"><?php echo esc_html(get_theme_mod('feicoop_publicacoes_description', 'Documentos, cartas, vídeos e materiais produzidos ao longo da história do Projeto Esperança/Cooesperança e da FEICOOP.')); ?></p>
                <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
            </div>
        </header>
    </div>

    <div class="wrapper publicacoes-archive__body">
        <?php if ($selected_year <= 0) : ?>
            <?php /* Página inicial da área: descrição + listagem dos anos */ ?>
            <?php if ($years !== []) : ?>
                <div class="publicacoes-years">
                    <h2 class="publicacoes-years__title"><?php esc_html_e('Navegue pelos anos', 'feicoop'); ?></h2>
                    <div class="publicacoes-years__grid">
                        <?php foreach ($years as $year) : ?>
                            <a class="publicacoes-year" href="<?php echo esc_url(add_query_arg('publicacao_year', $year, $archive_url)); ?>">
                                <strong><?php echo esc_html((string) $year); ?></strong>
                                <span><?php esc_html_e('Ver produções', 'feicoop'); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <p class="publicacoes-empty"><?php esc_html_e('Nenhuma produção publicada ainda.', 'feicoop'); ?></p>
            <?php endif; ?>

        <?php else : ?>
            <div class="publicacoes-year-view">
                <p class="publicacoes-back">
                    <a href="<?php echo esc_url($archive_url); ?>">&larr; <?php esc_html_e('Todas as produções', 'feicoop'); ?></a>
                </p>

                <h2 class="publicacoes-year-title"><?php echo esc_html(sprintf(__('Produções de %d', 'feicoop'), $selected_year)); ?></h2>

                <div class="publicacoes-filters" role="group" aria-label="<?php esc_attr_e('Filtrar por tipo', 'feicoop'); ?>">
                    <a class="publicacoes-filter<?php echo $selected_tipo === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url(remove_query_arg('publicacao_tipo')); ?>"><?php esc_html_e('Todos', 'feicoop'); ?></a>
                    <a class="publicacoes-filter<?php echo $selected_tipo === 'pdf' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('publicacao_tipo', 'pdf')); ?>"><?php esc_html_e('PDFs', 'feicoop'); ?></a>
                    <a class="publicacoes-filter<?php echo $selected_tipo === 'video' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('publicacao_tipo', 'video')); ?>"><?php esc_html_e('Vídeos', 'feicoop'); ?></a>
                </div>

                <?php if (have_posts()) : ?>
                    <div class="publicacoes-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            $tipo = (string) get_post_meta(get_the_ID(), '_feicoop_publicacao_type', true);
                            $year = (int) get_post_meta(get_the_ID(), '_feicoop_publicacao_year', true);
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('publicacao-card'); ?>>
                                <div class="publicacao-card__media">
                                    <?php if ($tipo === 'video') : ?>
                                        <span class="publicacao-card__type publicacao-card__type--video"><?php esc_html_e('Vídeo', 'feicoop'); ?></span>
                                    <?php else : ?>
                                        <span class="publicacao-card__type publicacao-card__type--pdf"><?php esc_html_e('PDF', 'feicoop'); ?></span>
                                    <?php endif; ?>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('medium'); ?></a>
                                    <?php endif; ?>
                                </div>
                                <div class="publicacao-card__content">
                                    <p class="publicacao-card__meta">
                                        <time datetime="<?php echo esc_attr((string) $year); ?>"><?php echo esc_html((string) $year); ?></time>
                                    </p>
                                    <h3 class="publicacao-card__title"><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                                    <div class="publicacao-card__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
                                    <?php echo wp_kses_post(feicoop_publicacao_media_html(get_the_ID())); ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php the_posts_pagination(); ?>
                <?php else : ?>
                    <p class="publicacoes-empty"><?php esc_html_e('Nenhuma produção neste ano/tipo.', 'feicoop'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
