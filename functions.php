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
        'title' => (string) get_theme_mod('feicoop_home_registration_title', __('Abertura das inscrições em 1º de maio', 'feicoop')),
        'text' => (string) get_theme_mod('feicoop_home_registration_text', __('Reserve a data e acompanhe os canais oficiais para acessar o formulário.', 'feicoop')),
        'button_label' => (string) get_theme_mod('feicoop_home_registration_button_label', __('Ir para inscrições', 'feicoop')),
        'button_url' => (string) get_theme_mod('feicoop_home_registration_button_url', 'https://inscricoes.esperancacooesperanca.org.br/'),
        'date_label' => (string) get_theme_mod('feicoop_home_registration_date_label', __('Data de abertura', 'feicoop')),
        'date_value' => (string) get_theme_mod('feicoop_home_registration_date_value', __('1º de maio', 'feicoop')),
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

function feicoop_home_banner_height(): int {
    $height = (int) get_theme_mod('feicoop_home_banner_height', 320);

    if ($height < 180) {
        return 180;
    }

    if ($height > 600) {
        return 600;
    }

    return $height;
}

function feicoop_home_banner_mobile_height(): int {
    $height = (int) get_theme_mod('feicoop_home_banner_mobile_height', 220);

    if ($height < 140) {
        return 140;
    }

    if ($height > 480) {
        return 480;
    }

    return $height;
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

    if ($front_page_id <= 0) {
        return [feicoop_banner_fallback_slide()];
    }

    $raw_ids = (string) get_post_meta($front_page_id, '_feicoop_banner_carousel_ids', true);
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw_ids) ?: [])));
    $slides = [];

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

    if (empty($slides)) {
        $slides[] = feicoop_banner_fallback_slide();
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
    wp_enqueue_script('feicoop-admin-carousel', feicoop_asset_url('assets/js/admin-carousel.js'), ['jquery'], wp_get_theme()->get('Version'), true);
}
add_action('admin_enqueue_scripts', 'feicoop_admin_enqueue_assets');

function feicoop_register_banner_carousel_metabox(WP_Post $post): void {
    if ((int) $post->ID !== (int) get_option('page_on_front')) {
        return;
    }

    add_meta_box(
        'feicoop_banner_carousel',
        __('Carrossel do topo', 'feicoop'),
        'feicoop_render_banner_carousel_metabox',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes_page', 'feicoop_register_banner_carousel_metabox');

function feicoop_render_banner_carousel_metabox(WP_Post $post): void {
    $raw_ids = (string) get_post_meta($post->ID, '_feicoop_banner_carousel_ids', true);
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw_ids) ?: [])));
    wp_nonce_field('feicoop_banner_carousel_save', 'feicoop_banner_carousel_nonce');
    ?>
    <p><?php esc_html_e('Selecione as imagens que vão aparecer no topo da home. A primeira imagem padrão é usada apenas quando a lista está vazia.', 'feicoop'); ?></p>
    <input type="hidden" id="feicoop_banner_carousel_ids" name="feicoop_banner_carousel_ids" value="<?php echo esc_attr(implode(',', $ids)); ?>">
    <p>
        <button type="button" class="button button-primary" id="feicoop_banner_carousel_select"><?php esc_html_e('Selecionar imagens', 'feicoop'); ?></button>
        <button type="button" class="button" id="feicoop_banner_carousel_clear"><?php esc_html_e('Limpar', 'feicoop'); ?></button>
    </p>
    <ul class="feicoop-banner-carousel-preview" id="feicoop_banner_carousel_preview">
        <?php foreach ($ids as $attachment_id) : ?>
            <li data-id="<?php echo esc_attr((string) $attachment_id); ?>">
                <?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="description"><?php esc_html_e('Você pode ordenar as imagens novamente no seletor da biblioteca de mídia.', 'feicoop'); ?></p>
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

    $wp_customize->add_section('feicoop_home_banner', [
        'title' => __('FEICOOP Banner', 'feicoop'),
        'description' => __('Ajusta a altura do carrossel principal e da imagem do cabeçalho nas páginas internas.', 'feicoop'),
        'priority' => 25,
    ]);

    $wp_customize->add_setting('feicoop_home_banner_height', [
        'default' => 320,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('feicoop_home_banner_height', [
        'label' => __('Altura do banner', 'feicoop'),
        'description' => __('Use um valor em pixels. Ex.: 320, 380, 420.', 'feicoop'),
        'section' => 'feicoop_home_banner',
        'type' => 'number',
        'input_attrs' => [
            'min' => 180,
            'max' => 600,
            'step' => 10,
        ],
    ]);

    $wp_customize->add_setting('feicoop_home_banner_mobile_height', [
        'default' => 220,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('feicoop_home_banner_mobile_height', [
        'label' => __('Altura do banner no mobile', 'feicoop'),
        'description' => __('Use um valor em pixels. Ex.: 180, 220, 260.', 'feicoop'),
        'section' => 'feicoop_home_banner',
        'type' => 'number',
        'input_attrs' => [
            'min' => 140,
            'max' => 480,
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

    $home_registration_fields = [
        'kicker' => [
            'label' => __('Legenda', 'feicoop'),
            'description' => __('Texto curto acima do título da seção.', 'feicoop'),
            'default' => __('Inscrições', 'feicoop'),
        ],
        'title' => [
            'label' => __('Título', 'feicoop'),
            'description' => __('Título principal mostrado na seção.', 'feicoop'),
            'default' => __('Abertura das inscrições em 1º de maio', 'feicoop'),
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
            'default' => __('1º de maio', 'feicoop'),
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
    echo '<ul class="navbar__menu"><li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></li></ul>';
}

function feicoop_footer_menu_fallback(): void {
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'feicoop') . '</a></li></ul>';
}
