<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo esc_url(feicoop_asset_url('assets/img/favicon.png')); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Pular para o conteúdo', 'feicoop'); ?></a>
<header class="top js-header">
    <div class="wrapper top__inner">
        <?php
        // O tema declara suporte a logo (add_theme_support('custom-logo')) e tem
        // CSS pronto para .logo > img, mas o cabeçalho nunca imprimia a marca.
        // Sem logo definido no Personalizador, cai no nome do site.
        $feicoop_logo_id  = (int) get_theme_mod('custom_logo');
        $feicoop_logo_url = $feicoop_logo_id > 0
            ? (string) wp_get_attachment_image_url($feicoop_logo_id, 'full')
            : '';
        $feicoop_site_name = (string) get_bloginfo('name');
        ?>
        <a class="logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <?php if ($feicoop_logo_url !== '') : ?>
                <img src="<?php echo esc_url($feicoop_logo_url); ?>" alt="<?php echo esc_attr($feicoop_site_name); ?>">
            <?php else : ?>
                <span class="logo__mark"><?php echo esc_html($feicoop_site_name); ?></span>
            <?php endif; ?>
        </a>

        <button class="navbar__toggle js-toggle" aria-label="<?php esc_attr_e('Menu', 'feicoop'); ?>" aria-controls="primary-menu" aria-expanded="false">
            <span class="navbar__toggle-box">
                <span class="navbar__toggle-inner" aria-hidden="true"></span>
            </span>
        </button>

        <nav id="primary-menu" class="navbar js-navbar" aria-label="<?php esc_attr_e('Primary menu', 'feicoop'); ?>">
            <?php if (has_nav_menu('mainMenu')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'mainMenu',
                    'container' => false,
                    'menu_class' => 'navbar__menu',
                    'fallback_cb' => 'feicoop_main_menu_fallback',
                    'depth' => 2,
                ]);
                ?>
            <?php else : ?>
                <?php feicoop_main_menu_fallback(); ?>
            <?php endif; ?>
        </nav>
    </div>
</header>
