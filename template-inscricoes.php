<?php
/*
Template Name: Abertura das inscrições
*/
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="page page--inscricoes">
    <?php while (have_posts()) : the_post(); ?>
        <article class="content">
            <section class="hero hero--page hero--inscricoes">
                <div class="wrapper inscricoes-hero">
                    <div class="inscricoes-hero__content">
                        <p class="hero__eyebrow">Inscrições FEICOOP</p>
                        <h1><?php the_title(); ?></h1>
                        <p class="inscricoes-hero__lead"><?php echo esc_html(feicoop_template_meta(get_the_ID(), '_feicoop_registration_notice', 'Acompanhe o anúncio oficial e os detalhes do processo de participação na programação da feira.')); ?></p>
                        <?php $cta_url = feicoop_template_meta(get_the_ID(), '_feicoop_registration_cta_url', feicoop_page_url('contato')); ?>
                        <?php $cta_label = feicoop_template_meta(get_the_ID(), '_feicoop_registration_cta_label', 'Saiba mais'); ?>
                        <?php if ($cta_url) : ?><p class="inscricoes-hero__actions"><a href="<?php echo esc_url($cta_url); ?>" class="btn"><?php echo esc_html($cta_label); ?></a></p><?php endif; ?>
                    </div>
                    <aside class="inscricoes-card" aria-label="<?php esc_attr_e('Data de abertura das inscrições', 'feicoop'); ?>">
                        <p class="inscricoes-card__kicker"><?php esc_html_e('Abertura oficial', 'feicoop'); ?></p>
                        <strong class="inscricoes-card__date"><?php echo esc_html(feicoop_template_meta(get_the_ID(), '_feicoop_registration_open_date', __('Em breve', 'feicoop'))); ?></strong>
                        <p class="inscricoes-card__text"><?php esc_html_e('Nesta data será liberado o processo de inscrição para participação na programação.', 'feicoop'); ?></p>
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
