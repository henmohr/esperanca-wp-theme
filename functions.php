<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/inc/helpers.php';

function feicoop_setup(): void {
    load_theme_textdomain('feicoop', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 120,
        'width' => 360,
        'flex-height' => true,
        'flex-width' => true,
    ]);
add_theme_support('html5', ['comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'search-form']);
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');

    register_nav_menus([
        'mainMenu' => __('Main menu', 'feicoop'),
        'footerMenu' => __('Footer menu', 'feicoop'),
    ]);

    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category('feicoop', [
            'label' => __('FEICOOP', 'feicoop'),
        ]);

        register_block_pattern_category('feicoop-home', [
            'label' => __('FEICOOP Home', 'feicoop'),
        ]);
    }

    add_image_size('feicoop-card', 900, 600, true);
    add_image_size('feicoop-hero', 1600, 900, true);
}
add_action('after_setup_theme', 'feicoop_setup');

function feicoop_document_title_parts(array $parts): array {
    unset($parts['tagline']);
    return $parts;
}
add_filter('document_title_parts', 'feicoop_document_title_parts');

function feicoop_document_title_separator(string $separator): string {
    return '-';
}
add_filter('document_title_separator', 'feicoop_document_title_separator');

function feicoop_enqueue_assets(): void {
    $theme = wp_get_theme();

    wp_enqueue_style(
        'feicoop-fonts',
        'https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400..900;1,400..900&family=Bitter:ital,wght@0,400..900;1,400..900&family=Maven+Pro:wght@400..900&display=swap',
        [],
        null
    );

    wp_enqueue_style('feicoop-main', feicoop_asset_url('assets/css/main.css'), [], $theme->get('Version'));
    wp_enqueue_style('feicoop-custom', feicoop_asset_url('assets/css/feicoop-custom.css'), ['feicoop-main'], $theme->get('Version'));

    wp_enqueue_script('feicoop-scripts', feicoop_asset_url('assets/js/scripts.min.js'), [], $theme->get('Version'), true);

    wp_localize_script('feicoop-scripts', 'publiiThemeMenuConfig', [
        'mobileMenuMode' => 'overlay',
        'animationSpeed' => 300,
        'submenuWidth' => 300,
        'doubleClickTime' => 500,
        'mobileMenuExpandableSubmenus' => false,
        'isHoverMenu' => true,
        'wrapperSelector' => '.navbar',
        'buttonSelector' => '.navbar__toggle',
        'menuSelector' => '.navbar__menu',
        'submenuSelector' => '.navbar__submenu',
        'relatedContainerForOverlayMenuSelector' => '.top',
    ]);
}
add_action('wp_enqueue_scripts', 'feicoop_enqueue_assets');

function feicoop_add_body_classes(array $classes): array {
    $classes[] = 'feicoop-theme';

    if (is_front_page()) {
        $classes[] = 'home-template';
    }

    if (is_home()) {
        $classes[] = 'blogindex-template';
    }

    if (is_page()) {
        $classes[] = 'page-template';
    }

    if (is_single()) {
        $classes[] = 'post-template';
    }

    if (is_author()) {
        $classes[] = 'author-template';
    }

    if (is_tag()) {
        $classes[] = 'tag-template';
        $classes[] = 'tags-template';
    }

    if (is_search()) {
        $classes[] = 'search-template';
    }

    if (is_404()) {
        $classes[] = 'error-template';
    }

    if (is_paged()) {
        $classes[] = 'pagination-template';
    }

    return array_values(array_unique($classes));
}
add_filter('body_class', 'feicoop_add_body_classes');

function feicoop_nav_menu_css_class(array $classes, WP_Post $item, $args, int $depth): array {
    if (is_string($item->url) && str_contains($item->url, '#')) {
        return array_values(array_diff($classes, [
            'current-menu-item',
            'current_page_item',
            'current-menu-ancestor',
            'current-menu-parent',
            'current_page_parent',
            'current_page_ancestor',
            'menu-item-home',
        ]));
    }

    if (!empty($item->current) || !empty($item->current_item_ancestor) || !empty($item->current_item_parent)) {
        $classes[] = 'current-menu-item';
    }

    if (in_array('menu-item-has-children', $classes, true)) {
        $classes[] = 'has-submenu';
    }

    return array_values(array_unique($classes));
}
add_filter('nav_menu_css_class', 'feicoop_nav_menu_css_class', 10, 4);

function feicoop_nav_menu_objects(array $items, $args): array {
    foreach ($items as $item) {
        if (!isset($item->url) || !is_string($item->url) || !str_contains($item->url, '#')) {
            continue;
        }

        $item->current = false;
        $item->current_item_ancestor = false;
        $item->current_item_parent = false;
        $item->classes = array_values(array_diff((array) $item->classes, [
            'current-menu-item',
            'current_page_item',
            'current-menu-ancestor',
            'current-menu-parent',
            'current_page_parent',
            'current_page_ancestor',
            'menu-item-home',
        ]));
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'feicoop_nav_menu_objects', 10, 2);

function feicoop_nav_menu_link_attributes(array $atts, WP_Post $item, $args, int $depth): array {
    if (isset($item->url) && is_string($item->url) && str_contains($item->url, '#')) {
        unset($atts['aria-current']);
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'feicoop_nav_menu_link_attributes', 10, 4);

function feicoop_nav_menu_submenu_css_class(array $classes, $args, int $depth): array {
    $classes = ['navbar__submenu'];

    if ($depth > 0) {
        $classes[] = 'navbar__submenu__submenu';
    }

    return $classes;
}
add_filter('nav_menu_submenu_css_class', 'feicoop_nav_menu_submenu_css_class', 10, 3);

function feicoop_customize_register(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('feicoop_social', [
        'title' => __('FEICOOP Social', 'feicoop'),
        'priority' => 40,
    ]);

    $fields = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'linkedin' => 'LinkedIn',
        'x' => 'X / Twitter',
    ];

    foreach ($fields as $key => $label) {
        $wp_customize->add_setting("feicoop_social_{$key}", [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control("feicoop_social_{$key}", [
            'label' => $label,
            'section' => 'feicoop_social',
            'type' => 'url',
        ]);
    }
}
add_action('customize_register', 'feicoop_customize_register');

function feicoop_main_menu_fallback(): void {
    echo '<ul class="navbar__menu"><li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></li></ul>';
}

function feicoop_footer_menu_fallback(): void {
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'feicoop') . '</a></li></ul>';
}
