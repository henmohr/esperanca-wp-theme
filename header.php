<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="top js-header">
    <div class="wrapper site-banner-wrap">
        <a class="site-banner" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <img src="<?php echo esc_url(feicoop_asset_url('assets/img/cabecalho-site-feicoop.png')); ?>" alt="32a FEICOOP - Projeto Esperanca/Cooesperanca" width="720" height="320" loading="eager" fetchpriority="high">
        </a>
    </div>
    <div class="wrapper top__inner">
        <div class="site-branding">
            <?php
            if (has_custom_logo()) {
                $logo = get_custom_logo();
                echo $logo ? str_replace('custom-logo-link', 'custom-logo-link logo', $logo) : '';
            } else {
                echo '<a class="logo" href="' . esc_url(home_url('/')) . '" rel="home">' . esc_html(get_bloginfo('name')) . '</a>';
            }
            ?>
        </div>

        <button class="navbar__toggle js-toggle" aria-label="<?php esc_attr_e('Menu', 'feicoop'); ?>" aria-haspopup="true" aria-expanded="false">
            <span class="navbar__toggle-box">
                <span class="navbar__toggle-inner"><?php esc_html_e('Menu', 'feicoop'); ?></span>
            </span>
        </button>

        <nav class="navbar js-navbar" aria-label="<?php esc_attr_e('Primary menu', 'feicoop'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'mainMenu',
                'container' => false,
                'menu_class' => 'navbar__menu',
                'fallback_cb' => 'feicoop_main_menu_fallback',
                'depth' => 2,
            ]);
            ?>
        </nav>
    </div>
</header>
