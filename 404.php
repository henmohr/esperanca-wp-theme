<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="page page--error">
    <div class="content">
        <div class="hero hero--noimage">
            <header class="hero__content">
                <div class="wrapper">
                    <h1><?php esc_html_e('Pagina nao encontrada', 'feicoop'); ?></h1>
                    <div class="page__desc">
                        <p><?php esc_html_e('A pagina solicitada nao existe ou foi movida.', 'feicoop'); ?></p>
                    </div>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn hero__cta btn--icon">
                        <svg width="18" height="18" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#arrow-prev')); ?>"></use></svg>
                        <span><?php esc_html_e('Voltar para a home', 'feicoop'); ?></span>
                    </a>
                </div>
            </header>
        </div>
    </div>
</main>
<?php get_footer(); ?>
