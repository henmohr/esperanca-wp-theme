<?php
/*
Template Name: Abertura das inscricoes
*/
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="page page--inscricoes">
    <?php while (have_posts()) : the_post(); ?>
        <article class="content">
            <section class="hero hero--page hero--inscricoes">
                <div class="wrapper inscricoes-hero">
                    <div class="inscricoes-hero__content">
                        <p class="hero__eyebrow">Inscricoes FEICOOP</p>
                        <h1><?php the_title(); ?></h1>
                        <p class="inscricoes-hero__lead"><?php echo esc_html(feicoop_template_meta(get_the_ID(), '_feicoop_registration_notice', 'Acompanhe o anuncio oficial e os detalhes do processo de participacao na programacao da feira.')); ?></p>
                        <?php $cta_url = feicoop_template_meta(get_the_ID(), '_feicoop_registration_cta_url', feicoop_page_url('contato')); ?>
                        <?php $cta_label = feicoop_template_meta(get_the_ID(), '_feicoop_registration_cta_label', 'Saiba mais'); ?>
                        <?php if ($cta_url) : ?><p class="inscricoes-hero__actions"><a href="<?php echo esc_url($cta_url); ?>" class="btn"><?php echo esc_html($cta_label); ?></a></p><?php endif; ?>
                    </div>
                    <aside class="inscricoes-card" aria-label="<?php esc_attr_e('Data de abertura das inscricoes', 'feicoop'); ?>">
                        <p class="inscricoes-card__kicker"><?php esc_html_e('Abertura oficial', 'feicoop'); ?></p>
                        <strong class="inscricoes-card__date"><?php echo esc_html(feicoop_template_meta(get_the_ID(), '_feicoop_registration_open_date', '01 de agosto de 2025')); ?></strong>
                        <p class="inscricoes-card__text"><?php esc_html_e('Nesta data sera liberado o processo de inscricao para participacao na programacao.', 'feicoop'); ?></p>
                    </aside>
                </div>
            </section>

            <div class="entry-wrapper content__entry inscricoes-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
