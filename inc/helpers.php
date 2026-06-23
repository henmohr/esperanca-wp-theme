<?php
if (!defined('ABSPATH')) {
    exit;
}

function feicoop_asset_url(string $path = ''): string {
    return trailingslashit(get_template_directory_uri()) . ltrim($path, '/');
}

function feicoop_page_url(string $slug, string $fallback = '/'): string {
    $page = get_page_by_path(trim($slug, '/'));

    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }

    return home_url($fallback);
}

function feicoop_posts_page_url(): string {
    $posts_page_id = (int) get_option('page_for_posts');

    if ($posts_page_id > 0) {
        return get_permalink($posts_page_id);
    }

    return home_url('/#publicacoes');
}

function feicoop_template_meta(int $post_id, string $key, string $default = ''): string {
    $value = get_post_meta($post_id, $key, true);

    if (is_string($value) && $value !== '') {
        return $value;
    }

    return $default;
}

function feicoop_social_links(): array {
    return [
        'facebook' => get_theme_mod('feicoop_social_facebook', ''),
        'instagram' => get_theme_mod('feicoop_social_instagram', ''),
        'youtube' => get_theme_mod('feicoop_social_youtube', ''),
        'linkedin' => get_theme_mod('feicoop_social_linkedin', ''),
        'x' => get_theme_mod('feicoop_social_x', ''),
    ];
}

function feicoop_page_has_children(int $post_id): bool {
    $children = get_pages([
        'child_of' => $post_id,
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'number' => 1,
    ]);

    return !empty($children);
}

function feicoop_excerpt(string $fallback = ''): string {
    $excerpt = get_the_excerpt();

    if ($excerpt !== '') {
        return $excerpt;
    }

    if ($fallback !== '') {
        return wpautop(wp_kses_post($fallback));
    }

    return '';
}
