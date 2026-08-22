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
    <div class="wrapper site-banner-wrap">
        <?php feicoop_render_site_banner(); ?>
    </div>
    <div class="wrapper top__inner">
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
