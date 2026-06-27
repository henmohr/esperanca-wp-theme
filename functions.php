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

function feicoop_banner_fallback_slide(): array {
    return [
        'src' => feicoop_asset_url('assets/img/cabecalho-site-feicoop.png'),
        'alt' => get_bloginfo('name'),
    ];
}

function feicoop_sanitize_checkbox($checked): int {
    return !empty($checked) ? 1 : 0;
}

function feicoop_home_registration_enabled(): bool {
    return (bool) get_theme_mod('feicoop_home_registration_enabled', true);
}

function feicoop_home_registration_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_registration_kicker', __('Inscrições', 'feicoop')),
        'title' => (string) get_theme_mod('feicoop_home_registration_title', __('Abertura das inscrições', 'feicoop')),
        'text' => (string) get_theme_mod('feicoop_home_registration_text', __('Reserve a data e acompanhe os canais oficiais para acessar o formulário.', 'feicoop')),
        'button_label' => (string) get_theme_mod('feicoop_home_registration_button_label', __('Ir para inscrições', 'feicoop')),
        'button_url' => (string) get_theme_mod('feicoop_home_registration_button_url', 'https://inscricoes.esperancacooesperanca.org.br/'),
        'date_label' => (string) get_theme_mod('feicoop_home_registration_date_label', __('Data de abertura', 'feicoop')),
        'date_value' => (string) get_theme_mod('feicoop_home_registration_date_value', __('Em breve', 'feicoop')),
    ];
}

function feicoop_home_hero_fields(): array {
    return [
        'eyebrow' => (string) get_theme_mod('feicoop_home_hero_eyebrow', '32ª FEICOOP'),
        'title' => (string) get_theme_mod('feicoop_home_hero_title', __('Feira Internacional do Cooperativismo e da Economia Solidária', 'feicoop')),
        'text' => (string) get_theme_mod('feicoop_home_hero_text', __('Portal institucional do Projeto Esperança/Cooesperança para divulgar a feira, suas redes, a memória do movimento e as novidades da programação.', 'feicoop')),
        'panel_kicker' => (string) get_theme_mod('feicoop_home_hero_panel_kicker', '32ª FEICOOP'),
        'panel_title' => (string) get_theme_mod('feicoop_home_hero_panel_title', __('A maior feira de economia solidária da América Latina', 'feicoop')),
        'panel_text' => (string) get_theme_mod('feicoop_home_hero_panel_text', __('Encontro anual de articulação, formação, comercialização solidária e troca de experiências entre grupos, redes, cooperativas e comunidades.', 'feicoop')),
    ];
}

function feicoop_sanitize_hero_title_size($value): string {
    $size = (float) $value;

    if ($size < 2.1) {
        $size = 2.1;
    }

    if ($size > 3.1) {
        $size = 3.1;
    }

    return number_format($size, 1, '.', '');
}

function feicoop_home_hero_title_size(): string {
    $size = (float) get_theme_mod('feicoop_home_hero_title_size', 3.0);

    if ($size < 2.1) {
        $size = 2.1;
    }

    if ($size > 3.1) {
        $size = 3.1;
    }

    $formatted = rtrim(rtrim(number_format($size, 1, '.', ''), '0'), '.');

    return $formatted . 'rem';
}

function feicoop_posts_page_title_size(): string {
    $size = (float) get_theme_mod('feicoop_posts_page_title_size', 2.8);

    if ($size < 2.1) {
        $size = 2.1;
    }

    if ($size > 3.1) {
        $size = 3.1;
    }

    $formatted = rtrim(rtrim(number_format($size, 1, '.', ''), '0'), '.');

    return $formatted . 'rem';
}

function feicoop_home_event_fields(): array {
    return [
        'when_label' => (string) get_theme_mod('feicoop_home_event_when_label', __('Quando', 'feicoop')),
        'when_value' => (string) get_theme_mod('feicoop_home_event_when_value', __('Em breve', 'feicoop')),
        'where_label' => (string) get_theme_mod('feicoop_home_event_where_label', __('Onde', 'feicoop')),
        'where_value' => (string) get_theme_mod('feicoop_home_event_where_value', __('Santa Maria, RS', 'feicoop')),
        'focus_label' => (string) get_theme_mod('feicoop_home_event_focus_label', __('Foco', 'feicoop')),
        'focus_value' => (string) get_theme_mod('feicoop_home_event_focus_value', __('Economia solidária, cooperativismo e redes', 'feicoop')),
    ];
}

function feicoop_home_banner_height(): int {
    $height = (int) get_theme_mod('feicoop_home_banner_height', 220);

    if ($height < 160) {
        return 160;
    }

    if ($height > 320) {
        return 320;
    }

    return $height;
}

function feicoop_home_banner_mobile_height(): int {
    $height = (int) get_theme_mod('feicoop_home_banner_mobile_height', 180);

    if ($height < 120) {
        return 120;
    }

    if ($height > 240) {
        return 240;
    }

    return $height;
}

function feicoop_register_programacao_cpt(): void {
    register_post_type('programacao', [
        'labels' => [
            'name' => __('Programação', 'feicoop'),
            'singular_name' => __('Item de programação', 'feicoop'),
            'add_new_item' => __('Adicionar item de programação', 'feicoop'),
            'edit_item' => __('Editar item de programação', 'feicoop'),
            'new_item' => __('Novo item de programação', 'feicoop'),
            'view_item' => __('Ver item de programação', 'feicoop'),
            'search_items' => __('Buscar itens de programação', 'feicoop'),
            'not_found' => __('Nenhum item de programação encontrado', 'feicoop'),
            'not_found_in_trash' => __('Nenhum item de programação na lixeira', 'feicoop'),
            'all_items' => __('Todos os itens de programação', 'feicoop'),
            'menu_name' => __('Programação', 'feicoop'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'programacao', 'with_front' => false],
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);
}
add_action('init', 'feicoop_register_programacao_cpt');

function feicoop_maybe_flush_rewrite_rules(): void {
    if (!is_admin()) {
        return;
    }

    $theme_version = (string) wp_get_theme()->get('Version');
    $stored_version = (string) get_option('feicoop_rewrite_version', '');

    if ($stored_version === $theme_version) {
        return;
    }

    flush_rewrite_rules(false);
    update_option('feicoop_rewrite_version', $theme_version);
}
add_action('admin_init', 'feicoop_maybe_flush_rewrite_rules', 20);

function feicoop_flush_rewrite_rules_on_switch(): void {
    flush_rewrite_rules(false);
    update_option('feicoop_rewrite_version', (string) wp_get_theme()->get('Version'));
}
add_action('after_switch_theme', 'feicoop_flush_rewrite_rules_on_switch');

function feicoop_programacao_meta_fields(int $post_id = 0): array {
    $post_id = $post_id > 0 ? $post_id : (int) get_the_ID();

    return [
        'date' => (string) get_post_meta($post_id, '_feicoop_programacao_date', true),
        'time' => (string) get_post_meta($post_id, '_feicoop_programacao_time', true),
        'location' => (string) get_post_meta($post_id, '_feicoop_programacao_location', true),
        'track' => (string) get_post_meta($post_id, '_feicoop_programacao_track', true),
        'featured' => (bool) get_post_meta($post_id, '_feicoop_programacao_featured', true),
    ];
}

function feicoop_programacao_track_suggestions(): array {
    $suggestions = [
        __('Abertura', 'feicoop'),
        __('Almoço', 'feicoop'),
        __('Café', 'feicoop'),
        __('Comercialização', 'feicoop'),
        __('Cultura', 'feicoop'),
        __('Encerramento', 'feicoop'),
        __('Feira', 'feicoop'),
        __('Formação', 'feicoop'),
        __('Mesa-redonda', 'feicoop'),
        __('Oficina', 'feicoop'),
        __('Painel', 'feicoop'),
        __('Roda de conversa', 'feicoop'),
        __('Sem trilha', 'feicoop'),
    ];

    $existing_tracks = get_posts([
        'post_type' => 'programacao',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    foreach ($existing_tracks as $post_id) {
        $track = trim((string) get_post_meta((int) $post_id, '_feicoop_programacao_track', true));

        if ($track !== '') {
            $suggestions[] = $track;
        }
    }

    $suggestions = array_values(array_unique(array_filter(array_map('sanitize_text_field', $suggestions))));
    sort($suggestions, SORT_NATURAL | SORT_FLAG_CASE);

    return $suggestions;
}

function feicoop_programacao_format_date(string $date): string {
    if ($date === '') {
        return '';
    }

    $datetime = DateTimeImmutable::createFromFormat('!Y-m-d', $date, wp_timezone());

    if ($datetime === false) {
        return $date;
    }

    return wp_date('j \\d\\e F \\d\\e Y', $datetime->getTimestamp(), wp_timezone());
}

function feicoop_programacao_format_time(string $time): string {
    if ($time === '') {
        return '';
    }

    $datetime = DateTimeImmutable::createFromFormat('!H:i', $time, wp_timezone());

    if ($datetime === false) {
        return $time;
    }

    return $datetime->format('H\\hi');
}

function feicoop_post_feature_image_html(?int $post_id = null, string $size = 'feicoop-card', string $class = ''): string {
    $post_id = $post_id !== null ? $post_id : (int) get_the_ID();
    $post_id = $post_id > 0 ? $post_id : 0;

    if ($post_id > 0 && has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        $src = wp_get_attachment_image_url($thumbnail_id, $size);

        if ($src) {
            $alt = (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            if ($alt === '') {
                $alt = get_the_title($thumbnail_id);
            }

            $classes = trim($class);

            return '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt !== '' ? $alt : get_the_title($post_id)) . '"' . ($classes !== '' ? ' class="' . esc_attr($classes) . '"' : '') . ' loading="lazy" decoding="async">';
        }
    }

    return '<img src="' . esc_url(feicoop_asset_url('assets/img/Card_Home_Projeto_Esperanca_Cooesperanca_FEICOOP_Santa_Maria_RS-3.png')) . '" alt="' . esc_attr(get_the_title($post_id) !== '' ? get_the_title($post_id) : get_bloginfo('name')) . '"' . ($class !== '' ? ' class="' . esc_attr($class) . '"' : '') . ' loading="lazy" decoding="async">';
}

function feicoop_get_home_banner_slides(): array {
    $front_page_id = (int) get_option('page_on_front');
    $slides = [feicoop_banner_fallback_slide()];

    if ($front_page_id <= 0) {
        return $slides;
    }

    $raw_ids = (string) get_post_meta($front_page_id, '_feicoop_banner_carousel_ids', true);
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw_ids) ?: [])));

    foreach ($ids as $attachment_id) {
        $src = wp_get_attachment_image_url($attachment_id, 'feicoop-hero');

        if (!$src) {
            continue;
        }

        $alt = (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

        if ($alt === '') {
            $alt = get_the_title($attachment_id);
        }

        $slides[] = [
            'src' => $src,
            'alt' => $alt !== '' ? $alt : get_bloginfo('name'),
        ];
    }

    return $slides;
}

function feicoop_render_site_banner(): void {
    $banner_height = feicoop_home_banner_height();
    $banner_mobile_height = feicoop_home_banner_mobile_height();
    $banner_style = ' style="--site-banner-height: ' . esc_attr((string) $banner_height) . 'px; --site-banner-height-mobile: ' . esc_attr((string) $banner_mobile_height) . 'px;"';

    if (!is_front_page()) {
        $slide = feicoop_banner_fallback_slide();
        echo '<a class="site-banner" href="' . esc_url(home_url('/')) . '" aria-label="' . esc_attr(get_bloginfo('name')) . '"' . $banner_style . '>';
        echo '<img src="' . esc_url($slide['src']) . '" alt="' . esc_attr($slide['alt']) . '" width="720" height="320" loading="eager" fetchpriority="high">';
        echo '</a>';
        return;
    }

    $slides = feicoop_get_home_banner_slides();
    $slide_count = count($slides);

    echo '<div class="site-banner-carousel js-banner-carousel"' . $banner_style . ' data-autoplay="true" data-interval="6000" data-slide-count="' . esc_attr((string) $slide_count) . '" aria-roledescription="carousel" aria-label="' . esc_attr__('Banner principal', 'feicoop') . '">';
    echo '<div class="site-banner-carousel__viewport">';
    echo '<div class="site-banner-carousel__track">';

    foreach ($slides as $index => $slide) {
        $active = $index === 0 ? ' is-active' : '';
        echo '<a class="site-banner-carousel__slide' . esc_attr($active) . '" href="' . esc_url(home_url('/')) . '" aria-label="' . esc_attr(get_bloginfo('name')) . '">';
        echo '<img src="' . esc_url($slide['src']) . '" alt="' . esc_attr($slide['alt']) . '" width="720" height="320" loading="' . ($index === 0 ? 'eager' : 'lazy') . '" fetchpriority="' . ($index === 0 ? 'high' : 'auto') . '">';
        echo '</a>';
    }

    echo '</div>';
    echo '</div>';

    if ($slide_count > 1) {
        echo '<button class="site-banner-carousel__control site-banner-carousel__control--prev" type="button" data-carousel-prev aria-label="' . esc_attr__('Imagem anterior', 'feicoop') . '">&#10094;</button>';
        echo '<button class="site-banner-carousel__control site-banner-carousel__control--next" type="button" data-carousel-next aria-label="' . esc_attr__('Próxima imagem', 'feicoop') . '">&#10095;</button>';
        echo '<div class="site-banner-carousel__dots" data-carousel-dots></div>';
    }

    echo '</div>';
}

function feicoop_get_back_link_url(string $fallback_url): string {
    $referer = wp_get_referer();

    if ($referer === '') {
        return $fallback_url;
    }

    $home_host = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
    $referer_host = (string) wp_parse_url($referer, PHP_URL_HOST);

    if ($home_host !== '' && $referer_host !== '' && strcasecmp($home_host, $referer_host) === 0) {
        return $referer;
    }

    return $fallback_url;
}

function feicoop_render_back_button(string $fallback_url, string $label): void {
    $back_url = feicoop_get_back_link_url($fallback_url);

    echo '<p class="hero__actions hero__actions--back"><a class="btn btn--ghost" href="' . esc_url($back_url) . '">' . esc_html($label) . '</a></p>';
}

function feicoop_register_programacao_metabox(WP_Post $post): void {
    add_meta_box(
        'feicoop_programacao_details',
        __('Detalhes da programação', 'feicoop'),
        'feicoop_render_programacao_metabox',
        'programacao',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_programacao', 'feicoop_register_programacao_metabox');

function feicoop_render_programacao_metabox(WP_Post $post): void {
    $fields = feicoop_programacao_meta_fields($post->ID);
    wp_nonce_field('feicoop_programacao_save', 'feicoop_programacao_nonce');
    ?>
    <p><?php esc_html_e('Preencha os dados para exibir a programação agrupada por dia no site.', 'feicoop'); ?></p>
    <p>
        <label for="feicoop_programacao_date"><strong><?php esc_html_e('Data', 'feicoop'); ?></strong></label><br>
        <input type="date" id="feicoop_programacao_date" name="feicoop_programacao_date" value="<?php echo esc_attr($fields['date']); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="feicoop_programacao_time"><strong><?php esc_html_e('Horário', 'feicoop'); ?></strong></label><br>
        <input type="time" id="feicoop_programacao_time" name="feicoop_programacao_time" value="<?php echo esc_attr($fields['time']); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="feicoop_programacao_location"><strong><?php esc_html_e('Local', 'feicoop'); ?></strong></label><br>
        <input type="text" id="feicoop_programacao_location" name="feicoop_programacao_location" value="<?php echo esc_attr($fields['location']); ?>" class="widefat">
    </p>
    <p>
        <label for="feicoop_programacao_track"><strong><?php esc_html_e('Faixa / categoria', 'feicoop'); ?></strong></label><br>
        <input type="text" id="feicoop_programacao_track" name="feicoop_programacao_track" value="<?php echo esc_attr($fields['track']); ?>" class="widefat" list="feicoop_programacao_track_suggestions" autocomplete="off" placeholder="<?php esc_attr_e('Digite ou escolha uma sugestão', 'feicoop'); ?>">
        <datalist id="feicoop_programacao_track_suggestions">
            <?php foreach (feicoop_programacao_track_suggestions() as $suggestion) : ?>
                <option value="<?php echo esc_attr($suggestion); ?>"></option>
            <?php endforeach; ?>
        </datalist>
    </p>
    <p>
        <label>
            <input type="checkbox" name="feicoop_programacao_featured" value="1" <?php checked($fields['featured']); ?>>
            <?php esc_html_e('Destacar este item na programação', 'feicoop'); ?>
        </label>
    </p>
    <?php
}

function feicoop_save_programacao_meta(int $post_id, WP_Post $post, bool $update): void {
    if (!isset($_POST['feicoop_programacao_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feicoop_programacao_nonce'])), 'feicoop_programacao_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $date = isset($_POST['feicoop_programacao_date']) ? sanitize_text_field(wp_unslash($_POST['feicoop_programacao_date'])) : '';
    $time = isset($_POST['feicoop_programacao_time']) ? sanitize_text_field(wp_unslash($_POST['feicoop_programacao_time'])) : '';
    $location = isset($_POST['feicoop_programacao_location']) ? sanitize_text_field(wp_unslash($_POST['feicoop_programacao_location'])) : '';
    $track = isset($_POST['feicoop_programacao_track']) ? sanitize_text_field(wp_unslash($_POST['feicoop_programacao_track'])) : '';
    $featured = isset($_POST['feicoop_programacao_featured']) ? '1' : '';

    update_post_meta($post_id, '_feicoop_programacao_date', $date);
    update_post_meta($post_id, '_feicoop_programacao_time', $time);
    update_post_meta($post_id, '_feicoop_programacao_location', $location);
    update_post_meta($post_id, '_feicoop_programacao_track', $track);
    update_post_meta($post_id, '_feicoop_programacao_featured', $featured);
}
add_action('save_post_programacao', 'feicoop_save_programacao_meta', 10, 3);

function feicoop_enqueue_assets(): void {
    $theme = wp_get_theme();

    wp_enqueue_style('feicoop-main', feicoop_asset_url('assets/css/main.css'), [], $theme->get('Version'));
    wp_enqueue_style('feicoop-custom', feicoop_asset_url('assets/css/feicoop-custom.css'), ['feicoop-main'], $theme->get('Version'));

    wp_enqueue_script('feicoop-banner-carousel', feicoop_asset_url('assets/js/banner-carousel.js'), [], $theme->get('Version'), true);
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

function feicoop_admin_enqueue_assets(string $hook): void {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    wp_enqueue_media();
    wp_register_style('feicoop-admin-carousel', false, [], wp_get_theme()->get('Version'));
    wp_enqueue_style('feicoop-admin-carousel');
    wp_add_inline_style('feicoop-admin-carousel', '
        .feicoop-banner-carousel-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin: 16px 0 0;
            padding: 0;
            max-height: 360px;
            overflow: auto;
            list-style: none;
        }

        .feicoop-banner-carousel-preview li {
            display: grid;
            gap: 8px;
            padding: 10px;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            background: #fff;
        }

        .feicoop-banner-carousel-preview img {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 4px;
        }

        .feicoop-banner-carousel-remove {
            justify-self: start;
            color: #b32d2e;
            cursor: pointer;
            padding: 0;
        }
    ');
    wp_enqueue_script('feicoop-admin-carousel', feicoop_asset_url('assets/js/admin-carousel.js'), ['jquery'], wp_get_theme()->get('Version'), true);
}
add_action('admin_enqueue_scripts', 'feicoop_admin_enqueue_assets');

function feicoop_register_banner_carousel_metabox(WP_Post $post): void {
    if ((int) $post->ID !== (int) get_option('page_on_front')) {
        return;
    }

    add_meta_box(
        'feicoop_banner_carousel',
        __('Patrocinadores do topo', 'feicoop'),
        'feicoop_render_banner_carousel_metabox',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_page', 'feicoop_register_banner_carousel_metabox');

function feicoop_render_banner_carousel_metabox(WP_Post $post): void {
    $raw_ids = (string) get_post_meta($post->ID, '_feicoop_banner_carousel_ids', true);
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw_ids) ?: [])));
    wp_nonce_field('feicoop_banner_carousel_save', 'feicoop_banner_carousel_nonce');
    ?>
    <p><?php esc_html_e('Adicione aqui os logos ou imagens dos patrocinadores. Prefira artes horizontais, com pouco espaço vazio, para caberem melhor no carrossel. Use a biblioteca de mídia para enviar novos arquivos ou escolher imagens já enviadas.', 'feicoop'); ?></p>
    <input type="hidden" id="feicoop_banner_carousel_ids" name="feicoop_banner_carousel_ids" value="<?php echo esc_attr(implode(',', $ids)); ?>">
    <p>
        <button type="button" class="button button-primary" id="feicoop_banner_carousel_select"><?php esc_html_e('Enviar ou selecionar imagens', 'feicoop'); ?></button>
        <button type="button" class="button" id="feicoop_banner_carousel_clear"><?php esc_html_e('Limpar', 'feicoop'); ?></button>
    </p>
    <p class="description"><?php esc_html_e('Você pode remover uma imagem por vez no preview abaixo sem apagar o restante.', 'feicoop'); ?></p>
    <ul class="feicoop-banner-carousel-preview" id="feicoop_banner_carousel_preview">
        <?php foreach ($ids as $attachment_id) : ?>
            <li data-id="<?php echo esc_attr((string) $attachment_id); ?>">
                <?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
                <button type="button" class="button-link-delete feicoop-banner-carousel-remove" data-remove-item><?php esc_html_e('Remover', 'feicoop'); ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="description"><?php esc_html_e('Você pode enviar novos arquivos no modal da mídia, selecionar imagens existentes e reorganizar a ordem antes de salvar.', 'feicoop'); ?></p>
    <?php
}

function feicoop_save_banner_carousel_meta(int $post_id, WP_Post $post, bool $update): void {
    if ((int) $post->ID !== (int) get_option('page_on_front')) {
        return;
    }

    if (!isset($_POST['feicoop_banner_carousel_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feicoop_banner_carousel_nonce'])), 'feicoop_banner_carousel_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $raw_ids = isset($_POST['feicoop_banner_carousel_ids']) ? (string) wp_unslash($_POST['feicoop_banner_carousel_ids']) : '';
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', sanitize_text_field($raw_ids)) ?: [])));

    update_post_meta($post_id, '_feicoop_banner_carousel_ids', implode(',', $ids));
}
add_action('save_post_page', 'feicoop_save_banner_carousel_meta', 10, 3);

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

        if (str_contains($item->url, '#publicacoes')) {
            $item->url = str_replace('#publicacoes', '#noticias', $item->url);
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

    $wp_customize->add_section('feicoop_home_registration', [
        'title' => __('FEICOOP Inscrições', 'feicoop'),
        'description' => __('Mostra ou oculta a seção de inscrições da home e permite editar seus textos e botão.', 'feicoop'),
        'priority' => 35,
    ]);

    $wp_customize->add_section('feicoop_home_hero', [
        'title' => __('FEICOOP Hero', 'feicoop'),
        'description' => __('Edita a chamada principal do topo da home e o texto do painel lateral.', 'feicoop'),
        'priority' => 30,
    ]);

    $wp_customize->add_section('feicoop_news_archive', [
        'title' => __('FEICOOP Notícias', 'feicoop'),
        'description' => __('Ajusta a aparência da página de notícias, incluindo o tamanho do título.', 'feicoop'),
        'priority' => 29,
    ]);

    $wp_customize->add_section('feicoop_home_banner', [
        'title' => __('FEICOOP Banner', 'feicoop'),
        'description' => __('Ajusta a altura do carrossel principal e da imagem do cabeçalho nas páginas internas.', 'feicoop'),
        'priority' => 25,
    ]);

    $wp_customize->add_section('feicoop_home_event', [
        'title' => __('FEICOOP Destaques da home', 'feicoop'),
        'description' => __('Edita as informações curtas exibidas no bloco de fatos da home.', 'feicoop'),
        'priority' => 26,
    ]);

    $wp_customize->add_setting('feicoop_home_banner_height', [
        'default' => 220,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('feicoop_home_banner_height', [
        'label' => __('Altura do banner', 'feicoop'),
        'description' => __('Use um valor em pixels. Ex.: 180, 220, 280.', 'feicoop'),
        'section' => 'feicoop_home_banner',
        'type' => 'number',
        'input_attrs' => [
            'min' => 160,
            'max' => 320,
            'step' => 10,
        ],
    ]);

    $wp_customize->add_setting('feicoop_home_banner_mobile_height', [
        'default' => 180,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('feicoop_home_banner_mobile_height', [
        'label' => __('Altura do banner no mobile', 'feicoop'),
        'description' => __('Use um valor em pixels. Ex.: 120, 160, 180.', 'feicoop'),
        'section' => 'feicoop_home_banner',
        'type' => 'number',
        'input_attrs' => [
            'min' => 120,
            'max' => 240,
            'step' => 10,
        ],
    ]);

    $home_hero_fields = [
        'eyebrow' => [
            'label' => __('Legenda principal', 'feicoop'),
            'description' => __('Texto pequeno acima do título principal.', 'feicoop'),
            'default' => '32ª FEICOOP',
        ],
        'title' => [
            'label' => __('Título principal', 'feicoop'),
            'description' => __('Título grande exibido na coluna principal da hero.', 'feicoop'),
            'default' => __('Feira Internacional do Cooperativismo e da Economia Solidária', 'feicoop'),
        ],
        'text' => [
            'label' => __('Texto principal', 'feicoop'),
            'description' => __('Descrição curta da home, abaixo do título principal.', 'feicoop'),
            'default' => __('Portal institucional do Projeto Esperança/Cooesperança para divulgar a feira, suas redes, a memória do movimento e as novidades da programação.', 'feicoop'),
        ],
        'panel_kicker' => [
            'label' => __('Legenda do painel', 'feicoop'),
            'description' => __('Texto pequeno no painel lateral da hero.', 'feicoop'),
            'default' => '32ª FEICOOP',
        ],
        'panel_title' => [
            'label' => __('Título do painel', 'feicoop'),
            'description' => __('Chamada principal do painel lateral.', 'feicoop'),
            'default' => __('A maior feira de economia solidária da América Latina', 'feicoop'),
        ],
        'panel_text' => [
            'label' => __('Texto do painel', 'feicoop'),
            'description' => __('Resumo de apoio mostrado abaixo do título do painel.', 'feicoop'),
            'default' => __('Encontro anual de articulação, formação, comercialização solidária e troca de experiências entre grupos, redes, cooperativas e comunidades.', 'feicoop'),
        ],
    ];

    foreach ($home_hero_fields as $key => $config) {
        $setting_id = "feicoop_home_hero_{$key}";

        $wp_customize->add_setting($setting_id, [
            'default' => $config['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control($setting_id, [
            'label' => $config['label'],
            'description' => $config['description'] ?? '',
            'section' => 'feicoop_home_hero',
            'type' => 'text',
        ]);
    }

    $wp_customize->add_setting('feicoop_home_hero_title_size', [
        'default' => '3.0',
        'sanitize_callback' => 'feicoop_sanitize_hero_title_size',
    ]);

    $wp_customize->add_control('feicoop_home_hero_title_size', [
        'label' => __('Tamanho do título', 'feicoop'),
        'description' => __('Use um valor em rem. Ex.: 2.6, 2.8, 3.0.', 'feicoop'),
        'section' => 'feicoop_home_hero',
        'type' => 'number',
        'input_attrs' => [
            'min' => 2.1,
            'max' => 3.1,
            'step' => 0.1,
        ],
    ]);

    $wp_customize->add_setting('feicoop_posts_page_title_size', [
        'default' => '2.8',
        'sanitize_callback' => 'feicoop_sanitize_hero_title_size',
    ]);

    $wp_customize->add_control('feicoop_posts_page_title_size', [
        'label' => __('Tamanho do título', 'feicoop'),
        'description' => __('Use um valor em rem. Ex.: 2.4, 2.6, 2.8.', 'feicoop'),
        'section' => 'feicoop_news_archive',
        'type' => 'number',
        'input_attrs' => [
            'min' => 2.1,
            'max' => 3.1,
            'step' => 0.1,
        ],
    ]);

    $home_event_fields = [
        'when_label' => [
            'label' => __('Rótulo de quando', 'feicoop'),
            'description' => __('Texto exibido antes do valor do primeiro destaque.', 'feicoop'),
            'default' => __('Quando', 'feicoop'),
        ],
        'when_value' => [
            'label' => __('Quando', 'feicoop'),
            'description' => __('Data ou intervalo exibido no primeiro destaque da home.', 'feicoop'),
            'default' => __('Em breve', 'feicoop'),
        ],
        'where_label' => [
            'label' => __('Rótulo de onde', 'feicoop'),
            'description' => __('Texto exibido antes do valor do segundo destaque.', 'feicoop'),
            'default' => __('Onde', 'feicoop'),
        ],
        'where_value' => [
            'label' => __('Onde', 'feicoop'),
            'description' => __('Local exibido no segundo destaque da home.', 'feicoop'),
            'default' => __('Santa Maria, RS', 'feicoop'),
        ],
        'focus_label' => [
            'label' => __('Rótulo de foco', 'feicoop'),
            'description' => __('Texto exibido antes do valor do terceiro destaque.', 'feicoop'),
            'default' => __('Foco', 'feicoop'),
        ],
        'focus_value' => [
            'label' => __('Foco', 'feicoop'),
            'description' => __('Tema principal exibido no terceiro destaque da home.', 'feicoop'),
            'default' => __('Economia solidária, cooperativismo e redes', 'feicoop'),
        ],
    ];

    foreach ($home_event_fields as $key => $config) {
        $setting_id = "feicoop_home_event_{$key}";

        $wp_customize->add_setting($setting_id, [
            'default' => $config['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control($setting_id, [
            'label' => $config['label'],
            'description' => $config['description'] ?? '',
            'section' => 'feicoop_home_event',
            'type' => 'text',
        ]);
    }

    $home_registration_fields = [
        'kicker' => [
            'label' => __('Legenda', 'feicoop'),
            'description' => __('Texto curto acima do título da seção.', 'feicoop'),
            'default' => __('Inscrições', 'feicoop'),
        ],
        'title' => [
            'label' => __('Título', 'feicoop'),
            'description' => __('Título principal mostrado na seção.', 'feicoop'),
            'default' => __('Abertura das inscrições', 'feicoop'),
        ],
        'text' => [
            'label' => __('Texto', 'feicoop'),
            'description' => __('Parágrafo explicando a chamada para inscrições.', 'feicoop'),
            'default' => __('Reserve a data e acompanhe os canais oficiais para acessar o formulário.', 'feicoop'),
        ],
        'button_label' => [
            'label' => __('Texto do botão', 'feicoop'),
            'description' => __('Texto exibido no botão da hero e da seção.', 'feicoop'),
            'default' => __('Ir para inscrições', 'feicoop'),
        ],
        'button_url' => [
            'label' => __('URL do botão', 'feicoop'),
            'description' => __('Destino do botão de inscrições.', 'feicoop'),
            'default' => 'https://inscricoes.esperancacooesperanca.org.br/',
            'type' => 'url',
            'sanitize_callback' => 'esc_url_raw',
        ],
        'date_label' => [
            'label' => __('Legenda da data', 'feicoop'),
            'description' => __('Texto pequeno acima da data de abertura.', 'feicoop'),
            'default' => __('Data de abertura', 'feicoop'),
        ],
        'date_value' => [
            'label' => __('Valor da data', 'feicoop'),
            'description' => __('Data ou prazo em destaque na caixa lateral.', 'feicoop'),
            'default' => __('Em breve', 'feicoop'),
        ],
    ];

    $wp_customize->add_setting('feicoop_home_registration_enabled', [
        'default' => 1,
        'sanitize_callback' => 'feicoop_sanitize_checkbox',
    ]);

        $wp_customize->add_control('feicoop_home_registration_enabled', [
            'label' => __('Mostrar seção de inscrições', 'feicoop'),
            'description' => __('Desmarque para ocultar completamente a seção na home.', 'feicoop'),
            'section' => 'feicoop_home_registration',
            'type' => 'checkbox',
        ]);

    foreach ($home_registration_fields as $key => $config) {
        $setting_id = "feicoop_home_registration_{$key}";
        $sanitize_callback = $config['sanitize_callback'] ?? 'sanitize_text_field';

        $wp_customize->add_setting($setting_id, [
            'default' => $config['default'],
            'sanitize_callback' => $sanitize_callback,
        ]);

        $wp_customize->add_control($setting_id, [
            'label' => $config['label'],
            'description' => $config['description'] ?? '',
            'section' => 'feicoop_home_registration',
            'type' => $config['type'] ?? 'text',
        ]);
    }
}
add_action('customize_register', 'feicoop_customize_register');

function feicoop_main_menu_fallback(): void {
    echo '<ul class="navbar__menu">';
    echo '<li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Início', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('quem-somos', '/quem-somos.html')) . '">' . esc_html__('Quem somos', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('historia', '/historia.html')) . '">' . esc_html__('História', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('rede-esperanca', '/rede-esperanca.html')) . '">' . esc_html__('Rede Esperança', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('feirao-colonial', '/feirao-colonial.html')) . '">' . esc_html__('Feirão Colonial', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_programacao_archive_url()) . '">' . esc_html__('Programação', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_posts_page_url()) . '">' . esc_html__('Notícias', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('contato', '/contato.html')) . '">' . esc_html__('Contato', 'feicoop') . '</a></li>';
    echo '</ul>';
}

function feicoop_footer_menu_fallback(): void {
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'feicoop') . '</a></li></ul>';
}
