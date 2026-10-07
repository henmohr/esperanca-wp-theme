<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/legacy-content.php';

/*
 * Atualizações automáticas do tema a partir dos releases do GitHub.
 *
 * A biblioteca Plugin Update Checker (PUC) consulta o release mais recente do
 * repositório. Quando a "Version" do style.css publicado lá for maior que a
 * versão instalada, o WordPress mostra o aviso de atualização em
 * Aparência > Temas e permite atualizar com um clique (como um tema do .org).
 *
 * IMPORTANTE: para um novo release ser detectado, o número em "Version" no
 * style.css precisa ser incrementado antes do push/tag. O workflow "Package
 * theme" gera o release automaticamente a partir desse número.
 */
require_once __DIR__ . '/inc/plugin-update-checker/plugin-update-checker.php';

$feicoopThemeUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/henmohr/esperanca-wp-theme/', // repositório com os releases
    __FILE__,                                          // functions.php (o PUC detecta que é um tema)
    ''                                                 // slug vazio: usa o nome da pasta do tema
);

// Branch estável do repositório.
$feicoopThemeUpdateChecker->setBranch('main');

// Baixa o ZIP anexado ao release (feicoop-wp-template.zip) em vez do
// "Source code (zip)" gerado pelo GitHub.
$feicoopThemeUpdateChecker->getVcsApi()->enableReleaseAssets('/\.zip$/i');

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

function feicoop_seo_meta(): void {
    if (is_admin() || is_feed() || is_robots()) {
        return;
    }

    $description = '';

    if (is_singular()) {
        $description = wp_strip_all_tags((string) get_the_excerpt(get_queried_object_id()));
    } elseif (is_front_page() || is_home()) {
        $description = (string) get_bloginfo('description');
    } elseif (is_archive()) {
        $description = wp_strip_all_tags((string) get_the_archive_description());
    }

    $description = trim(wp_strip_all_tags((string) $description));

    if ($description === '') {
        $description = trim((string) get_bloginfo('description'));
    }

    if ($description === '') {
        $description = (string) get_bloginfo('name');
    }

    if ($description !== '') {
        echo "\n" . '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    if (is_singular()) {
        $current_url = (string) get_permalink();
        $og_type = is_singular('post') ? 'article' : 'website';
        $og_image = has_post_thumbnail() ? (string) get_the_post_thumbnail_url(get_queried_object_id(), 'full') : '';
    } else {
        $current_url = is_front_page() || is_home() ? home_url('/') : home_url((string) ($GLOBALS['wp']->request ?? ''));
        $og_type = 'website';
        $og_image = '';
    }

    if ($og_image === '') {
        $og_image = feicoop_asset_url('assets/img/card-feicoop.avif');
    }

    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($current_url) . '">' . "\n";

    if ($og_image !== '') {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    }

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action('wp_head', 'feicoop_seo_meta', 5);

function feicoop_json_ld(): void {
    if (is_admin() || is_feed() || is_robots()) {
        return;
    }

    $graph = [];

    // Organização (dados básicos do projeto/cooperativa).
    $organization = [
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => (string) get_bloginfo('name'),
        'url' => home_url('/'),
    ];

    $custom_logo_id = (int) get_theme_mod('custom_logo');

    if ($custom_logo_id > 0) {
        $organization['logo'] = (string) wp_get_attachment_image_url($custom_logo_id, 'full');
    }

    $graph[] = $organization;

    // Site.
    $graph[] = [
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'name' => (string) get_bloginfo('name'),
        'url' => home_url('/'),
    ];

    // Evento FEICOOP — emitido na home quando há datas configuradas.
    if (is_front_page()) {
        $event_fields = feicoop_home_event_fields();
        $start_date = (string) get_theme_mod('feicoop_event_start_date', '');

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
            $event = [
                '@type' => 'Event',
                '@id' => home_url('/#event'),
                'name' => (string) get_theme_mod('feicoop_home_hero_eyebrow', 'FEICOOP'),
                'startDate' => $start_date,
                'url' => home_url('/'),
                'image' => feicoop_asset_url('assets/img/banner-topo.avif'),
            ];

            $end_date = (string) get_theme_mod('feicoop_event_end_date', '');

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
                $event['endDate'] = $end_date;
            }

            if ($event_fields['where_value'] !== '' && $event_fields['where_value'] !== __('Em breve', 'feicoop')) {
                $event['location'] = [
                    '@type' => 'Place',
                    'name' => $event_fields['where_value'],
                ];
            }

            $graph[] = $event;
        }
    }

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'feicoop_json_ld', 6);

function feicoop_sanitize_checkbox($checked): int {
    return !empty($checked) ? 1 : 0;
}

function feicoop_sanitize_digits($value): string {
    return (string) preg_replace('/\D/', '', (string) $value);
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

function feicoop_register_programacao_cpt(): void {
    register_post_type('programacao', [
        'labels' => [
            'name' => __('FEICOOP', 'feicoop'),
            'singular_name' => __('Item da FEICOOP', 'feicoop'),
            'add_new_item' => __('Adicionar item da FEICOOP', 'feicoop'),
            'edit_item' => __('Editar item da FEICOOP', 'feicoop'),
            'new_item' => __('Novo item da FEICOOP', 'feicoop'),
            'view_item' => __('Ver item da FEICOOP', 'feicoop'),
            'search_items' => __('Buscar itens da FEICOOP', 'feicoop'),
            'not_found' => __('Nenhum item da FEICOOP encontrado', 'feicoop'),
            'not_found_in_trash' => __('Nenhum item da FEICOOP na lixeira', 'feicoop'),
            'all_items' => __('Todos os itens da FEICOOP', 'feicoop'),
            'menu_name' => __('FEICOOP', 'feicoop'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'feicoop', 'with_front' => false],
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);
}
add_action('init', 'feicoop_register_programacao_cpt');

/* =====================================================================
 * Produções e publicações (CPT publicacao: PDFs e vídeos)
 * ===================================================================== */
function feicoop_register_publicacao_cpt(): void {
    register_post_type('publicacao', [
        'labels' => [
            'name' => __('Produções e publicações', 'feicoop'),
            'singular_name' => __('Publicação', 'feicoop'),
            'add_new_item' => __('Adicionar publicação', 'feicoop'),
            'edit_item' => __('Editar publicação', 'feicoop'),
            'new_item' => __('Nova publicação', 'feicoop'),
            'view_item' => __('Ver publicação', 'feicoop'),
            'search_items' => __('Buscar publicações', 'feicoop'),
            'not_found' => __('Nenhuma publicação encontrada', 'feicoop'),
            'not_found_in_trash' => __('Nenhuma publicação na lixeira', 'feicoop'),
            'all_items' => __('Todas as publicações', 'feicoop'),
            'menu_name' => __('Produções e publicações', 'feicoop'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => 'producoes',
        'rewrite' => ['slug' => 'publicacao', 'with_front' => false],
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);
}
add_action('init', 'feicoop_register_publicacao_cpt');

/* Cartas de encerramento vivem dentro da seção FEICOOP: /feicoop/carta-de-encerramento-ANO/ */
function feicoop_carta_permalink(string $permalink, WP_Post $post): string {
    if ($post->post_type === 'publicacao' && str_starts_with((string) $post->post_name, 'carta-de-encerramento-')) {
        return home_url('/feicoop/' . $post->post_name . '/');
    }

    return $permalink;
}
add_filter('post_type_link', 'feicoop_carta_permalink', 10, 2);

function feicoop_carta_rewrite_rule(): void {
    add_rewrite_rule('^feicoop/(carta-de-encerramento-[0-9]{4})/?$', 'index.php?post_type=publicacao&name=$matches[1]', 'top');
}
add_action('init', 'feicoop_carta_rewrite_rule', 11);

function feicoop_redirect_old_feicoop_urls(): void {
    $path = trim((string) ($GLOBALS['wp']->request ?? ''), '/');

    // Antiga URL do arquivo da programação/FEICOOP.
    if ($path === 'programacao') {
        wp_safe_redirect(home_url('/feicoop/'), 301);
        exit;
    }

    // Antigas URLs das cartas (antes ficavam em /publicacao/...).
    if (str_starts_with($path, 'publicacao/carta-de-encerramento-')) {
        wp_safe_redirect(home_url('/feicoop/' . substr($path, strlen('publicacao/')) . '/'), 301);
        exit;
    }
}
add_action('template_redirect', 'feicoop_redirect_old_feicoop_urls');

function feicoop_publicacao_metabox(): void {
    add_meta_box('feicoop_publicacao', __('Arquivo da publicação', 'feicoop'), 'feicoop_publicacao_metabox_html', 'publicacao', 'side', 'default');
}
add_action('add_meta_boxes', 'feicoop_publicacao_metabox');

function feicoop_publicacao_metabox_html(WP_Post $post): void {
    wp_nonce_field('feicoop_publicacao_save', 'feicoop_publicacao_nonce');

    $type = (string) get_post_meta($post->ID, '_feicoop_publicacao_type', true);
    if ($type === '') {
        $type = 'pdf';
    }

    $file_id = (int) get_post_meta($post->ID, '_feicoop_publicacao_file_id', true);
    $video_url = (string) get_post_meta($post->ID, '_feicoop_publicacao_video_url', true);
    $year = (string) get_post_meta($post->ID, '_feicoop_publicacao_year', true);

    if ($year === '') {
        $year = (string) get_the_date('Y', $post);
    }
    ?>
    <p>
        <label><input type="radio" name="feicoop_publicacao_type" value="pdf" <?php checked($type, 'pdf'); ?>> <?php esc_html_e('PDF', 'feicoop'); ?></label><br>
        <label><input type="radio" name="feicoop_publicacao_type" value="video" <?php checked($type, 'video'); ?>> <?php esc_html_e('Vídeo', 'feicoop'); ?></label><br>
        <label><input type="radio" name="feicoop_publicacao_type" value="texto" <?php checked($type, 'texto'); ?>> <?php esc_html_e('Documento (texto)', 'feicoop'); ?></label>
    </p>
    <p>
        <label><strong><?php esc_html_e('Arquivo PDF (biblioteca de mídia)', 'feicoop'); ?></strong></label><br>
        <input type="hidden" name="feicoop_publicacao_file_id" id="feicoop-publicacao-file-id" value="<?php echo esc_attr((string) $file_id); ?>">
        <button type="button" class="button" id="feicoop-publicacao-pick"><?php esc_html_e('Selecionar PDF', 'feicoop'); ?></button>
        <span id="feicoop-publicacao-file-name"><?php
            if ($file_id > 0) {
                echo esc_html(wp_basename((string) get_attached_file($file_id)));
            }
        ?></span>
    </p>
    <p>
        <label><strong><?php esc_html_e('URL do vídeo (YouTube)', 'feicoop'); ?></strong></label><br>
        <input type="url" name="feicoop_publicacao_video_url" value="<?php echo esc_attr($video_url); ?>" class="widefat" placeholder="https://www.youtube.com/watch?v=...">
    </p>
    <p>
        <label><strong><?php esc_html_e('Ano', 'feicoop'); ?></strong></label><br>
        <input type="number" name="feicoop_publicacao_year" min="1900" max="2100" value="<?php echo esc_attr($year); ?>">
    </p>
    <script>
    (function ($) {
        var frame;
        $('#feicoop-publicacao-pick').on('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Selecionar PDF',
                library: { type: 'application/pdf' },
                multiple: false,
            });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                $('#feicoop-publicacao-file-id').val(att.id);
                $('#feicoop-publicacao-file-name').text(att.filename || '');
            });
            frame.open();
        });
    })(jQuery);
    </script>
    <?php
}

function feicoop_save_publicacao_meta(int $post_id, WP_Post $post, bool $update): void {
    if (!isset($_POST['feicoop_publicacao_nonce']) || !wp_verify_nonce(wp_unslash($_POST['feicoop_publicacao_nonce']), 'feicoop_publicacao_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $type = isset($_POST['feicoop_publicacao_type']) ? (string) $_POST['feicoop_publicacao_type'] : 'pdf';

    if (!in_array($type, ['pdf', 'video', 'texto'], true)) {
        $type = 'pdf';
    }

    $file_id = isset($_POST['feicoop_publicacao_file_id']) ? absint($_POST['feicoop_publicacao_file_id']) : 0;
    $video_url = isset($_POST['feicoop_publicacao_video_url']) ? esc_url_raw(wp_unslash($_POST['feicoop_publicacao_video_url'])) : '';
    $year = isset($_POST['feicoop_publicacao_year']) ? absint($_POST['feicoop_publicacao_year']) : 0;

    if ($year < 1900 || $year > 2100) {
        $year = (int) get_the_date('Y', $post_id);
    }

    update_post_meta($post_id, '_feicoop_publicacao_type', $type);
    update_post_meta($post_id, '_feicoop_publicacao_file_id', $file_id);
    update_post_meta($post_id, '_feicoop_publicacao_video_url', $video_url);
    update_post_meta($post_id, '_feicoop_publicacao_year', $year);

    delete_transient('feicoop_publicacao_years');
}
add_action('save_post_publicacao', 'feicoop_save_publicacao_meta', 10, 3);

function feicoop_publicacao_query_vars(array $vars): array {
    $vars[] = 'publicacao_year';
    $vars[] = 'publicacao_tipo';

    return $vars;
}
add_filter('query_vars', 'feicoop_publicacao_query_vars');

function feicoop_publicacao_archive_query(WP_Query $query): void {
    if (is_admin() || !$query->is_main_query() || !is_post_type_archive('publicacao')) {
        return;
    }

    $year = (int) get_query_var('publicacao_year');
    $tipo = (string) get_query_var('publicacao_tipo');

    $meta_query = [];

    if ($year > 0) {
        $meta_query[] = ['key' => '_feicoop_publicacao_year', 'value' => $year, 'type' => 'NUMERIC'];
    }

    if ($tipo === 'pdf' || $tipo === 'video') {
        $meta_query[] = ['key' => '_feicoop_publicacao_type', 'value' => $tipo];
    }

    if ($meta_query !== []) {
        $query->set('meta_query', $meta_query);
    }

    // Ordem cronológica: ano (desc) e depois data (desc).
    $query->set('meta_key', '_feicoop_publicacao_year');
    $query->set('orderby', ['meta_value_num' => 'DESC', 'date' => 'DESC']);
}
add_action('pre_get_posts', 'feicoop_publicacao_archive_query');

function feicoop_publicacao_years(): array {
    $years = get_transient('feicoop_publicacao_years');

    if (is_array($years)) {
        return $years;
    }

    $ids = get_posts([
        'post_type' => 'publicacao',
        'post_status' => 'publish',
        'fields' => 'ids',
        'numberposts' => -1,
    ]);

    $years = [];

    foreach ($ids as $id) {
        $year = (int) get_post_meta($id, '_feicoop_publicacao_year', true);

        if ($year > 0) {
            $years[$year] = $year;
        }
    }

    krsort($years);
    $years = array_values($years);
    set_transient('feicoop_publicacao_years', $years, DAY_IN_SECONDS);

    return $years;
}

function feicoop_publicacao_media_html(int $post_id): string {
    $type = (string) get_post_meta($post_id, '_feicoop_publicacao_type', true);

    if ($type === 'texto') {
        // Documento textual: o conteúdo é exibido na própria página.
        return '';
    }

    if ($type === 'video') {
        $url = (string) get_post_meta($post_id, '_feicoop_publicacao_video_url', true);

        if ($url === '') {
            return '';
        }

        $embed = wp_oembed_get($url);

        return $embed !== false ? $embed : '';
    }

    $file_id = (int) get_post_meta($post_id, '_feicoop_publicacao_file_id', true);
    $src = $file_id > 0 ? (string) wp_get_attachment_url($file_id) : '';

    if ($src === '') {
        return '';
    }

    $filename = wp_basename($src);

    return '<a class="btn contact-btn" href="' . esc_url($src) . '" target="_blank" rel="noopener noreferrer" download>'
        . esc_html__('Baixar PDF', 'feicoop') . ' <small>(' . esc_html($filename) . ')</small></a>';
}

/* =====================================================================
 * Destaque de notícias na home
 * ===================================================================== */
function feicoop_post_featured_metabox(): void {
    add_meta_box('feicoop_post_featured', __('Destaque na página inicial', 'feicoop'), 'feicoop_post_featured_metabox_html', 'post', 'side', 'default');
}
add_action('add_meta_boxes', 'feicoop_post_featured_metabox');

function feicoop_post_featured_metabox_html(WP_Post $post): void {
    wp_nonce_field('feicoop_post_featured_save', 'feicoop_post_featured_nonce');
    $featured = (bool) get_post_meta($post->ID, '_feicoop_post_featured', true);
    echo '<p><label><input type="checkbox" name="feicoop_post_featured" value="1" ' . checked($featured, true, false) . '> '
        . esc_html__('Exibir como destaque na página inicial', 'feicoop') . '</label></p>';
}

function feicoop_save_post_featured(int $post_id): void {
    if (!isset($_POST['feicoop_post_featured_nonce']) || !wp_verify_nonce(wp_unslash($_POST['feicoop_post_featured_nonce']), 'feicoop_post_featured_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $value = isset($_POST['feicoop_post_featured']) ? '1' : '';
    update_post_meta($post_id, '_feicoop_post_featured', $value);
}
add_action('save_post_post', 'feicoop_save_post_featured');

/* =====================================================================
 * Galeria de fotos (template template-galeria.php)
 * ===================================================================== */
function feicoop_gallery_metabox(string $post_type, WP_Post $post): void {
    // O hook add_meta_boxes passa ($post_type, $post) — assinatura corrigida.
    if ($post_type !== 'page' || get_page_template_slug($post->ID) !== 'template-galeria.php') {
        return;
    }

    add_meta_box('feicoop_gallery', __('Galeria de fotos', 'feicoop'), 'feicoop_gallery_metabox_html', 'page', 'normal', 'default');
}
add_action('add_meta_boxes', 'feicoop_gallery_metabox', 10, 2);

function feicoop_gallery_metabox_html(WP_Post $post): void {
    wp_nonce_field('feicoop_gallery_save', 'feicoop_gallery_nonce');
    $ids = feicoop_gallery_ids($post->ID);
    ?>
    <p>
        <button type="button" class="button button-primary" id="feicoop-gallery-pick"><?php esc_html_e('Adicionar imagens', 'feicoop'); ?></button>
        <button type="button" class="button" id="feicoop-gallery-clear"><?php esc_html_e('Limpar todas', 'feicoop'); ?></button>
    </p>
    <input type="hidden" name="feicoop_gallery_ids" id="feicoop-gallery-ids" value="<?php echo esc_attr(implode(',', $ids)); ?>">
    <ul class="feicoop-gallery-preview">
        <?php foreach ($ids as $image_id) : ?>
            <li data-id="<?php echo esc_attr((string) $image_id); ?>">
                <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                <button type="button" class="feicoop-gallery-remove"><?php esc_html_e('Remover', 'feicoop'); ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
    <style>
        .feicoop-gallery-preview { display: flex; flex-wrap: wrap; gap: 10px; margin: 12px 0 0; padding: 0; list-style: none; }
        .feicoop-gallery-preview li { position: relative; width: 120px; border: 1px solid #dcdcde; border-radius: 6px; overflow: hidden; background: #fff; }
        .feicoop-gallery-preview img { display: block; width: 100%; height: 80px; object-fit: cover; }
        .feicoop-gallery-preview .feicoop-gallery-remove { display: block; width: 100%; border: 0; border-top: 1px solid #dcdcde; background: #f6f7f7; color: #b32d2e; cursor: pointer; padding: 4px 0; font-size: 12px; }
    </style>
    <script>
    (function ($) {
        var frame;
        var $ids = $('#feicoop-gallery-ids');
        var $preview = $('.feicoop-gallery-preview');

        function refresh() {
            var ids = $ids.val().split(',').filter(Boolean);
            $preview.find('li').each(function () {
                if (ids.indexOf($(this).data('id').toString()) === -1) { $(this).remove(); }
            });
        }

        $('#feicoop-gallery-pick').on('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Selecionar imagens', multiple: true, library: { type: 'image' } });
            frame.on('select', function () {
                var selection = frame.state().get('selection').toJSON();
                var current = $ids.val().split(',').filter(Boolean);
                selection.forEach(function (att) {
                    if (current.indexOf(att.id.toString()) === -1) {
                        current.push(att.id);
                        var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                        $preview.append(
                            '<li data-id="' + att.id + '"><img src="' + thumb + '" alt=""><button type="button" class="feicoop-gallery-remove">Remover</button></li>'
                        );
                    }
                });
                $ids.val(current.join(','));
            });
            frame.open();
        });

        $preview.on('click', '.feicoop-gallery-remove', function () {
            var id = $(this).closest('li').data('id');
            var current = $ids.val().split(',').filter(Boolean).filter(function (v) { return v !== id.toString(); });
            $ids.val(current.join(','));
            $(this).closest('li').remove();
        });

        $('#feicoop-gallery-clear').on('click', function () { $ids.val(''); $preview.empty(); });
    })(jQuery);
    </script>
    <?php
}

function feicoop_gallery_ids(int $post_id = 0): array {
    $post_id = $post_id > 0 ? $post_id : (int) get_the_ID();
    $raw = (string) get_post_meta($post_id, '_feicoop_gallery_ids', true);

    return array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw) ?: [])));
}

function feicoop_save_gallery_meta(int $post_id, WP_Post $post): void {
    if (!isset($_POST['feicoop_gallery_nonce']) || !wp_verify_nonce(wp_unslash($_POST['feicoop_gallery_nonce']), 'feicoop_gallery_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $raw = isset($_POST['feicoop_gallery_ids']) ? (string) wp_unslash($_POST['feicoop_gallery_ids']) : '';
    $ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', sanitize_text_field($raw)) ?: [])));
    update_post_meta($post_id, '_feicoop_gallery_ids', implode(',', $ids));
}
add_action('save_post_page', 'feicoop_save_gallery_meta', 10, 2);

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
    feicoop_seed_theme_pages();
    feicoop_seed_programacao_items();
    flush_rewrite_rules(false);
    update_option('feicoop_rewrite_version', (string) wp_get_theme()->get('Version'));
}
add_action('after_switch_theme', 'feicoop_flush_rewrite_rules_on_switch');

function feicoop_theme_page_definitions(): array {
    return [
        [
            'slug' => 'inicio',
            'title' => __('Início', 'feicoop'),
            'content' => '<p>' . esc_html__('Página inicial do portal FEICOOP.', 'feicoop') . '</p>',
            'is_front_page' => true,
        ],
        [
            'slug' => 'quem-somos',
            'title' => __('Quem somos', 'feicoop'),
            'content' => '<p>O <strong>Projeto Esperança/Cooesperança</strong> atua em Santa Maria, no Rio Grande do Sul, articulando iniciativas de economia popular e solidária, agricultura familiar, comércio justo, cooperativismo e organização comunitária.<br><br>Ao longo de sua trajetória, consolidou-se como referência nacional e latino-americana na promoção de alternativas econômicas baseadas no trabalho associado, na solidariedade, na autogestão e no bem viver.<br><br>Mais do que organizar eventos, o projeto sustenta processos permanentes de formação, comercialização solidária, articulação em rede e valorização de experiências coletivas do campo e da cidade.<br><br><strong>Linhas de atuação:</strong></p><ul><li>Fortalecimento da economia popular e solidária</li><li>Articulação de redes e empreendimentos</li><li>Formação política e organizativa</li><li>Comercialização solidária e comércio justo</li><li>Promoção da FEICOOP e de espaços permanentes como o Feirão EcoSol</li></ul>',
        ],
        [
            'slug' => 'historia',
            'title' => __('História', 'feicoop'),
            'content' => '<p>A história do Projeto Esperança/Cooesperança está ligada à construção de experiências coletivas de geração de trabalho e renda, fortalecimento da agricultura familiar e organização de empreendimentos solidários na região central do Rio Grande do Sul.<br><br>Com o tempo, esse processo deu origem a redes de comercialização, espaços permanentes de feira e grandes encontros de articulação, como a FEICOOP, que passou a reunir grupos, cooperativas, movimentos e instituições de diferentes regiões do Brasil e da América Latina.<br><br>A caminhada do projeto expressa uma aposta política e social na economia solidária como caminho concreto de inclusão, cooperação e desenvolvimento territorial.</p>',
        ],
        [
            'slug' => 'feirao-colonial',
            'title' => __('Feirão EcoSol', 'feicoop'),
            'content' => '<p>O Feirão EcoSol é um espaço permanente de comercialização solidária, circulação de alimentos e encontro entre consumidores e produtores. Ele expressa, na prática, os princípios da economia solidária e da agroecologia. Além da venda direta, o Feirão fortalece vínculos comunitários, amplia a renda dos empreendimentos e torna visível a produção da agricultura familiar e do cooperativismo popular. Destaques: Comercialização sem intermediários; Valorização da agricultura familiar; Consumo ético e consciente; Presença permanente no calendário do projeto.</p><h2>Como participar</h2><p>Descreva aqui como produtores e grupos podem participar do Feirão EcoSol (contatos, regras, inscrição).</p><h2>Nossos empreendimentos</h2><p>Liste aqui os empreendimentos da economia solidária que participam do Feirão EcoSol.</p>',
        ],
        [
            'slug' => 'contato',
            'title' => __('Contato', 'feicoop'),
            'content' => '<p>' . esc_html__('Publique aqui os canais oficiais, telefones e redes sociais do projeto.', 'feicoop') . '</p>',
        ],
        [
            'slug' => 'cooesperanca',
            'title' => __('A Cooesperança', 'feicoop'),
            'content' => '<p><strong>PROJETO ESPERANÇA/COOESPERANÇA</strong></p><p><strong>1987/2023 – 36 anos</strong></p><p>O Projeto Esperança surgiu do estudo do Livro: “A Pobreza Riqueza dos Povos” do autor Africano Albert Tévoèdjeré. O estudo iniciou em 1980 e em 1986 iniciaram os primeiros PACs (Projetos Alternativos Comunitários) e em 15 de agosto de 1987, foi criado o Projeto Esperança. É uma proposta que na Arquidiocese de Santa Maria, articula e congrega as experiências de EPS (Economia Popular Solidária), e Agricultura Familiar no meio urbano e rural e na Prestação de Serviços, Desenvolvimento Solidário e Sustentável, Comércio Justo e Consumo Ético na perspectiva de “Uma Outra Economia Que Acontece”.</p><p>O Projeto Esperança desde 1987, vem construindo o Associativismo, o Trabalho, a Solidariedade, a Cidadania e um Novo Modelo de Desenvolvimento Solidário Sustentável, Territorial e Augestionário, através da Economia Solidária e da Inclusão Social. A UFSM, UFN, IRFADI, IFFar, Banco de Alimentos, Governo Federal através de vários Ministérios e a Prefeitura Municipal de Santa Maria/RS são parceiros históricos, que muito contribuíram nesta construção coletiva e participativa ao longo destes anos.</p><p>O Projeto foi idealizado por Dom Ivo Lorscheiter em conjunto com Professores da UFSM, EMATER, lideranças da Diocese de Santa Maria e representações Religiosas, entre as quais as Filhas do Amor Divino, que coordenaram por 35 anos este setor.</p><p>Principais segmentos que são atingidos pelo Projeto Esperança/Cooesperança:</p><p>A Organização e a Formação; o Cooperativismo e a Economia Solidária; a Agricultura e Agroindústria Familiar; os Catadores/as de material reciclado; os Povos Indígenas; os Quilombolas; a cultura afro brasileira; os Artesãos/as; os trabalhadores urbanos na parte de alimentação e confecção; a Agroecologia; as Políticas Públicas; a Segurança Alimentar Nutricional Sustentável; as parcerias e a articulação com as Redes Nacionais e Internacionais de Economia Solidária; CPT – Comissão Pastoral da Terra de Santa Maria; as Feiras em Rede e Pontos Fixos de Comercialização Solidária; Rede COMSOL e o Centro de Referência de Economia solidária Dom Ivo Lorscheiter.</p><p><strong>Tipo de público beneficiado:</strong></p><p>Agricultores/as Familiares, Artesãos/as, Agroindústria Familiar, Movimentos Populares, Pastorais Sociais, Educadores/as, Ecologistas, Acadêmicos, Cooperativas, Catadores/as de Resíduos Sólidos, Povos Indígenas, Quilombolas, grupos Afro-descendentes, Moradores em Situação de Rua, Refugiados e Migrantes, bem como um grande público de consumidores conscientes, participativos e apoiadores de nossa história.</p><p>A meta é não dar o peixe, mas ensinar a pescar, mas os trabalhos de emergência, para o qual são destinados os alimentos que vem do Banco de Alimentos são para as famílias extremamente necessitadas.</p><p><strong>Articulação do Projeto Esperança/Cooesperança nos níveis:</strong></p><p>Regional, Estadual, Nacional, Internacional, Latino Americano e Intercontinental por ocasião dos grandes Eventos Internacionais que se realizam no mês de julho de cada ano, em Santa Maria/RS, através das Redes Intercontinentais de Economia Solidária - ECOSOL.</p><p>O Projeto Esperança/Cooesperança se propõe a “Transformação pela Solidariedade”, fortalecendo “Uma Outra Economia que já Acontece” e por isso afirmamos com o sábio Provérbio Africano que:</p><p>“Muita gente pequena, em muitos lugares pequenos, fazendo coisas pequenas, mudarão a face da Terra”.</p>',
        ],
        [
            'slug' => 'ponto-de-cultura',
            'title' => __('Ponto de Cultura', 'feicoop'),
            'content' => '<p><strong>Ponto de Cultura Vozes da Esperança</strong></p><p>Somos um Ponto de Cultura ” VOZES DA ESPERANÇA” que faz parte do Projeto Esperança/Cooesperança e da Rede Esperança da Arquidiocese de Santa Maria/RS. A sede fica no Centro de Referência de Economia Solidária Dom Ivo Lorscheiter no Bairro Medianeira de Santa Maria-RS. Somos parte da Rede dos Pontos da Teia Cultural e da Rede dos Pontos de Cultura do Rio Grande do Sul desde 2014. O nosso Ponto de Cultura “VOZES DA ESPERANÇA” é composto pela Economia Solidária, com diversificação e Arte Criativa, Artistas Populares, Comunidades Indígenas Kaigangues, Guaranis e Terenas com artesanato, alimentos saudáveis , agroecologia, Levante Popular da Juventude, entre outros. Os momentos fortes da Cultura Viva se realizam durante os eventos da Feira da Primavera anual há 46 anos, FEICOOP (Feira Internacional do Cooperativismo da Economia Solidária), que acontece anualmente há 27 anos e todos os sábados no Feirão Colonial Semanal em articulação com a Rádio “VOZES DA ESPERANÇA” com músicas, integração, oficinas e atividades culturais diversas, fortalecendo a Economia Solidária, Cooperativismo, Cultura,Autogestão e Solidariedade. Somo a Rede Esperança que faz parte de 12 pontos do Projeto de Redes da Caritas Brasileira. Construímos uma uma Outra Economia em um proposta de um Outro Mundo Possível.</p><p><strong>Espetáculo Vozes da Esperança</strong></p><p><strong>Abertura Feicoop Online</strong></p><p>Para saber mais sobre os Pontos de Cultura, acesse: http://pontosdeculturars.redelivre.org.br/</p>',
        ],
        [
            'slug' => 'galeria',
            'title' => __('Galeria de Fotos', 'feicoop'),
            'template' => 'template-galeria.php',
            'content' => '',
        ],
        [
            'slug' => 'inscricoes',
            'title' => __('Inscrições', 'feicoop'),
            'template' => 'template-inscricoes.php',
            'content' => '<p>' . esc_html__('Atualize esta página com as orientações e o cronograma das inscrições.', 'feicoop') . '</p>',
        ],
        [
            'slug' => 'noticias',
            'title' => __('Notícias', 'feicoop'),
            'is_posts_page' => true,
        ],
    ];
}

function feicoop_ensure_theme_page(array $definition): int {
    $slug = isset($definition['slug']) ? (string) $definition['slug'] : '';
    $title = isset($definition['title']) ? (string) $definition['title'] : '';
    $content = isset($definition['content']) ? (string) $definition['content'] : '';
    $template = isset($definition['template']) ? (string) $definition['template'] : '';

    if ($slug === '' || $title === '') {
        return 0;
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    $page_id = $page instanceof WP_Post ? (int) $page->ID : 0;
    $page_existed = $page_id > 0;

    if ($page_id <= 0) {
        $result = wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $content,
            'post_author' => get_current_user_id() ?: 1,
        ], true);

        if (is_wp_error($result)) {
            return 0;
        }

        $page_id = (int) $result;
        update_post_meta($page_id, '_feicoop_seed_content', $content);
    } else {
        // Atualiza o conteúdo de páginas que nunca foram editadas (ainda com o
        // placeholder do seed), para refletir mudanças de estrutura do tema em
        // versões futuras. Páginas editadas pelo usuário nunca são sobrescritas.
        $current_content = (string) $page->post_content;
        $seeded_content = (string) get_post_meta($page_id, '_feicoop_seed_content', true);

        if ($seeded_content === '') {
            // Compara sem as tags, pois os placeholders antigos podiam vir com <p>...</p>.
            $current_text = trim(wp_strip_all_tags($current_content));
            $is_seed_placeholder = $current_text === trim(wp_strip_all_tags($content)) || in_array($current_text, feicoop_legacy_seed_placeholders(), true);
            $seeded_content = $is_seed_placeholder ? $current_content : "\0edited";
            update_post_meta($page_id, '_feicoop_seed_content', $seeded_content);
        }

        $never_edited = $seeded_content !== "\0edited" && $current_content === $seeded_content;

        if ($content !== '' && $never_edited && $current_content !== $content) {
            wp_update_post(['ID' => $page_id, 'post_content' => $content]);
            update_post_meta($page_id, '_feicoop_seed_content', $content);
        }
    }

    // Aplica o template apenas na criação, para não reverter escolhas do usuário
    // quando o seed roda novamente em versões futuras do tema.
    if (!$page_existed && $page_id > 0 && $template !== '') {
        update_post_meta($page_id, '_wp_page_template', $template);
    }

    return $page_id > 0 ? $page_id : 0;
}

/**
 * Placeholders antigos do seed (versões anteriores do tema). Se uma página
 * ainda tiver exatamente um desses textos, considera-se que nunca foi editada.
 */
function feicoop_legacy_seed_placeholders(): array {
    return [
        'Conte nesta página como funciona o Feirão EcoSol, a comercialização e a visitação.',
        'Conte nesta página como funciona o Feirão Colonial, a comercialização e a visitação.',
        'Apresente aqui a história do Projeto Esperança/Cooesperança, sua missão e a atuação da FEICOOP.',
        'Use esta página para registrar a memória da FEICOOP, os marcos do movimento e a evolução da feira.',
        'Publique aqui os canais oficiais, telefones e redes sociais do projeto.',
        'Página inicial do portal FEICOOP.',
        'Atualize esta página com as orientações e o cronograma das inscrições.',
        'Página da Cooesperança (Cooperativa Mista dos Pequenos Produtores Rurais e Urbanos Vinculados ao Projeto Esperança Ltda).',
        'Conte aqui o que é o Ponto de Cultura do Projeto Esperança/Cooesperança.',
    ];
}

function feicoop_seed_theme_pages(): void {
    $pages = feicoop_theme_page_definitions();
    $posts_page_id = (int) get_option('page_for_posts');
    $posts_page_exists = $posts_page_id > 0 && get_post($posts_page_id) instanceof WP_Post;
    $front_page_id = (int) get_option('page_on_front');
    $front_page_exists = $front_page_id > 0 && get_post($front_page_id) instanceof WP_Post;

    foreach ($pages as $definition) {
        $page_id = feicoop_ensure_theme_page($definition);

        if (!empty($definition['is_front_page']) && $page_id > 0 && !$front_page_exists) {
            update_option('page_on_front', $page_id);
            update_option('show_on_front', 'page');
            $front_page_id = $page_id;
            $front_page_exists = true;
        }

        if (!empty($definition['is_posts_page']) && $page_id > 0 && !$posts_page_exists) {
            update_option('page_for_posts', $page_id);
            $posts_page_id = $page_id;
            $posts_page_exists = true;
        }
    }
}

function feicoop_maybe_seed_theme_pages(): void {
    if (!is_admin() || !current_user_can('publish_pages')) {
        return;
    }

    $theme_version = (string) wp_get_theme()->get('Version');
    $stored_version = (string) get_option('feicoop_theme_pages_version', '');

    if ($stored_version === $theme_version) {
        $required_slugs = ['inicio', 'quem-somos', 'historia', 'feirao-colonial', 'contato', 'cooesperanca', 'ponto-de-cultura', 'galeria', 'inscricoes', 'noticias'];
        foreach ($required_slugs as $slug) {
            $existing_page = get_page_by_path($slug, OBJECT, 'page');
            if (!($existing_page instanceof WP_Post)) {
                feicoop_seed_theme_pages();
                update_option('feicoop_theme_pages_version', $theme_version);
                return;
            }
        }

        $posts_page_id = (int) get_option('page_for_posts');
        if ($posts_page_id > 0 && get_post($posts_page_id) instanceof WP_Post) {
            $front_page_id = (int) get_option('page_on_front');
            if ($front_page_id > 0 && get_post($front_page_id) instanceof WP_Post) {
                return;
            }
        }
    }

    feicoop_seed_theme_pages();
    update_option('feicoop_theme_pages_version', $theme_version);
}
add_action('admin_init', 'feicoop_maybe_seed_theme_pages', 20);

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
        'numberposts' => 50,
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

function feicoop_programacao_static_schedule(): array {
    return [
        [
            'date' => '2026-07-09',
            'time' => '08:30',
            'track' => __('Recepção', 'feicoop'),
            'title' => __('Recepção das caravanas e credenciamento', 'feicoop'),
            'location' => __('Parque da Medianeira - Lonão da Praça da Alimentação', 'feicoop'),
            'excerpt' => __('Chegada das caravanas ao longo da manhã. Organização do espaço da feira em mutirão, sem comercialização neste dia. Almoço por adesão no Espaço Comida e Cultura.', 'feicoop'),
        ],
        [
            'date' => '2026-07-09',
            'time' => '16:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Quem somos e como atuamos', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do terminal de comercialização', 'feicoop'),
            'excerpt' => __('Coordenação: Maribel Kauffmann. Entidade responsável: Fórum Gaúcho de Economia Popular Solidária e Associação do Voluntariado e da Solidariedade Avesol, no projeto Construindo um novo futuro para o RS - Feiras de Economia Solidária.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '07:00',
            'track' => __('Abertura', 'feicoop'),
            'title' => __('Alvorada festiva', 'feicoop'),
            'location' => __('Território da 32ª FEICOOP', 'feicoop'),
            'excerpt' => __('Início do dia com atividades festivas e circulação do público pela feira.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '07:00',
            'track' => __('Comercialização', 'feicoop'),
            'title' => __('Comercialização direta dos empreendimentos da ECOSOL', 'feicoop'),
            'location' => __('Pavilhões da feira', 'feicoop'),
            'excerpt' => __('Economia Solidária em atividade nos pavilhões durante todo o dia.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Avaliação dos custos e impactos nos preços dos produtos', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Luiz Henrique Figueira Marquezan. Entidade responsável: Programa de Pós-Graduação em Administração e Ciências Contábeis da UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Encontro Nacional de Redes de Cooperação Solidária', 'feicoop'),
            'location' => __('Salão da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Rizoneide Souza Amorim e Lidiane Freire de Jesus. Entidade responsável: Secretaria Nacional de Economia Popular e Solidária (SENAES/MTE).', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Entendendo a reforma tributária', 'feicoop'),
            'location' => __('Sala 2 - Frei Sérgio Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Vinícius Costa da Silva Zonatto. Entidade responsável: Programa de Pós-Graduação em Administração e Ciências Contábeis da UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '10:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Educar e Cooperar: formação e assessoramento técnico em economia solidária nos territórios', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Flávia Santana. Entidade responsável: Departamento de Formação e Estudos / Projeto Educar e Cooperar.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '09:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Qual o futuro que queremos para o Fórum Brasileiro de Economia Solidária?', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do Terminal de Comercialização', 'feicoop'),
            'excerpt' => __('Coordenação: Maribel Kauffmann. Entidade responsável: Fórum Brasileiro de Economia Solidária (FBES).', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '11:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Economia Popular Solidária: um caminho para a promoção dos direitos humanos', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Carlos Santana e Douglas Lima. Entidade responsável: Associação do Voluntariado e da Solidariedade - AVESOL.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Como a Moeda Social Mumbuca impacta o desenvolvimento socioeconômico de Maricá', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Renda básica e moeda social. Coordenação: Natália Assunção Sciammarella. Entidade responsável: Associação Banco Comunitário Popular de Maricá (Banco Mumbuca).', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Tecendo soluções coletivas', 'feicoop'),
            'location' => __('Sala 2 - Frei Sérgio Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Roda colaborativa para fortalecimento dos empreendimentos da Economia Solidária. Coordenação: Nathália Rigui Trindade. Entidade responsável: Incubadora Social (Hub de Inovação Social UFSM).', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Matricárias: do corpo à terra', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Fernanda Nielsen da Cruz. Entidade responsável: Ponto de Cultura Associação Cantalomba.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Costurando afetos: arte e saúde mental', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Ricardo Pawlak da Silveira. Entidade responsável: Avesol.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de quadrinhos para quem não sabe desenhar', 'feicoop'),
            'location' => __('Sala 5 - Bruno Pereira e Don Phillips - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Maria Clara da Silva Ramos Carneiro. Entidade responsável: Departamento de Letras Estrangeiras Modernas.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Educação popular, economia solidária e autogestão na construção da democracia', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do terminal de comercialização', 'feicoop'),
            'excerpt' => __('Coordenação: Alzira Medeiros. Entidade responsável: Rede Autogestionária de Educação Popular em Economia Solidária.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Educação cooperativa, associativismo e gênero', 'feicoop'),
            'location' => __('Sala 6 - Verônica Oliveira - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Fernanda de Figueiredo Ferreira. Entidade responsável: Daruê e AME.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Biochar: o que é, seus usos e as Carvoeiras Modernas', 'feicoop'),
            'location' => __('Sala 7 - Luisa Bairros - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Chuy. Entidade responsável: Associação de Agroecologistas do Caraá.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Entre Saberes & Fazeres: experiências do Programa Paul Singer no Brasil', 'feicoop'),
            'location' => __('Salão da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: André Mombach. Entidade responsável: Programa de Formação Paul Singer: Agentes de Economia Popular e Solidária.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '16:00',
            'track' => __('Mística', 'feicoop'),
            'title' => __('Mística de abertura', 'feicoop'),
            'location' => __('Palco da Feira - Espaço Comida e Cultura', 'feicoop'),
            'excerpt' => __('Momento de acolhida antes da abertura oficial da feira.', 'feicoop'),
        ],
        [
            'date' => '2026-07-10',
            'time' => '16:30',
            'track' => __('Abertura', 'feicoop'),
            'title' => __('Abertura oficial da 32ª FEICOOP', 'feicoop'),
            'location' => __('Palco da Feira - Espaço Comida e Cultura', 'feicoop'),
            'excerpt' => __('Início oficial da programação pública da feira.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '07:00',
            'track' => __('Abertura', 'feicoop'),
            'title' => __('Alvorada festiva', 'feicoop'),
            'location' => __('Território da 32ª FEICOOP', 'feicoop'),
            'excerpt' => __('Início do sábado com atividades festivas e circulação do público.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '07:00',
            'track' => __('Comercialização', 'feicoop'),
            'title' => __('Comercialização direta dos empreendimentos da ECOSOL', 'feicoop'),
            'location' => __('Pavilhões da feira', 'feicoop'),
            'excerpt' => __('Atividades de comercialização até as 19h.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '08:30',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Resíduos sólidos domésticos', 'feicoop'),
            'location' => __('Sala 7 - Luisa Bairros - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Cláudia Alves dos Santos, Ana Flávia Souto de Oliveira e Tiago Portella Fialho. Entidade responsável: Comunidade que apoia a agricultura (CSA) - VIDA.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('As eleições e os movimentos sociais', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Lúcia Maria Pauli Kist. Entidade responsável: Escola Fé e Política RS.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Construindo o futuro com a economia solidária', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do terminal de comercialização', 'feicoop'),
            'excerpt' => __('Tema: Caminhos para um novo cenário econômico. Convidados: André Machado, Fernando Zambam, Arildo Mota, Nelsa Nespolo, Helena Singer, Márcio Viera e Gervásio Plucinski. Mediação e coordenação: Ana Inês de Castro. Entidade responsável: Central de Cooperativas e Empreendimentos Solidários do RS (UNISOL - RS).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de ESG em empreendimentos de economia solidária', 'feicoop'),
            'location' => __('Sala 2 - Frei Sérgio Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Natália Assunção Sciammarella. Entidade responsável: Associação Banco Comunitário Popular de Maricá (Banco Mumbuca).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Casa do Empreendedor e das Cooperativas de Maricá: quando cooperação gera futuro', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Ludmilla de Araujo Mello. Entidade responsável: Casa do Empreendedor e das Cooperativas de Maricá.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('ATER Nacional no fortalecimento do cooperativismo solidário da agricultura familiar', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Luís Fernando Tividini de Oliveira e Isabel Cristina Lourenço da Silva. Entidade responsável: Agência Nacional de Assistência Técnica e Extensão Rural (ANATER).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Roda de conversa sobre o Cadsol', 'feicoop'),
            'location' => __('Sala 5 - Bruno Pereira e Don Phillips - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Cadastro Nacional de Empreendimentos Econômicos Solidários. Coordenação: Diogo de Carvalho Antunes Silva. Entidade responsável: Secretaria Nacional de Economia Popular e Solidária (SENAES).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Políticas públicas de agricultura urbana e periurbana', 'feicoop'),
            'location' => __('Sala 6 - Verônica Oliveira - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Diretrizes e o potencial de geração de renda. Coordenação: Rita Inês Paetzhold Pauli. Entidade responsável: Projeto PROMOVER/ UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Juventude: organização e luta popular', 'feicoop'),
            'location' => __('Sala 8 - Isadora Viana Costa - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Guilherme de Souza Cezar. Entidade responsável: Levante Popular da Juventude.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Soberania digital, software livre e economia solidária e feminista', 'feicoop'),
            'location' => __('Sala 9 - Dom Ivo Lorscheiter - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Adriane Nunes Cordonet. Entidade responsável: Rede de Economia Solidária e Feminista (RESF).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:30',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Assistência Técnica Bem Viver Centro-Oeste, Sul e Sudeste', 'feicoop'),
            'location' => __('Salão da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Fortalecimento das políticas públicas no território. Coordenação: Vinicius Tuchtenhagen Goldas e Sabrina Krupinski Pereira. Entidade responsável: Instituto Cultural Padre Josimo - ICPJ.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Mulheres negras: renda, resistência e coletividade', 'feicoop'),
            'location' => __('Sala 10 - Nei d\'Ogum - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('4 anos de FESPOPE. Coordenação: Gilciane Beatriz Aguiar Das Neves. Entidade responsável: Fórum Estadual das Mulheres Negras Trabalhadoras da Economia Popular Solidária - FESPOPE e CAMP.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Boas práticas em educação', 'feicoop'),
            'location' => __('Salão da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Lúcia Maria Pauli Kist. Entidade responsável: Movimento Brasileiro de Educadores Cristãos (MOBREC Santa Maria).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Novo marco legal do cooperativismo solidário', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Desafios e perspectivas do cooperativismo solidário e da economia solidária. Coordenação: Marcela Vieira. Entidade responsável: União Nacional das Organizações Cooperativistas Solidárias - UNICOPAS.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Reunião equipe de mobilizadores junto a Diretoria da UNISOL RS', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do terminal de comercialização', 'feicoop'),
            'excerpt' => __('Coordenação: Ana Ines de Castro. Entidade responsável: Central de Cooperativas e Empreendimentos Solidários do RS (UNISOL RS).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de bioinsumos, sementes, meliponários e autonomia feminina na biodiversidade do Bioma Pampa', 'feicoop'),
            'location' => __('Sala 2 - Frei Sérgio Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Eduarda da Costa Lucas. Entidade responsável: Grupo de Agroecologia Gaia.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Encontro Articula Cultura na 32ª FEICOOP', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Cultura como direito, integração entre saberes, territórios e práticas comunitárias na conexão campo-cidade. Coordenação: Maria Manoela Lampert Ceolin. Entidade responsável: Articula Cultura Santa Maria.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('III Oficina de encontro entre saúde mental e economia solidária na FEICOOP', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Experiências no Corre Dazarte. Coordenação: Douglas Casaroto de Oliveira, Taciana de Almeida Buchs, Antônio Carlos Motta, Paulo Gilberto Correa Dal Caro, Eva Eloina de Deus Vargas, Andriele da Silva Xavier, Cristiane Verardo de Castro e Natália do Nascimento Moraes. Entidade responsável: Corre Dazarte.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de mestras e mestres da cultura popular e dos saberes tradicionais', 'feicoop'),
            'location' => __('Sala 5 - Bruno Pereira e Don Phillips - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Fernanda de Figueiredo Ferreira. Entidade responsável: Programa de Pós-Graduação em Extensão Rural (PPGExR/UFSM) e Núcleo de Estudos Afro-Brasileiro e Indígena (NEABI/UFSM).', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Sementes crioulas: patrimônio dos povos a serviço da humanidade', 'feicoop'),
            'location' => __('Sala 6 - Verônica Oliveira - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Encontro de guardiãs e guardiões das sementes crioulas. Coordenação: Diulie Almansa da Costa, Felipe Henrique Huff, Maurício Queiroz e Miqueli Schiavon. Entidade responsável: Grupo FlorESer Agroecológico, Comissão Pastoral da Terra e Cooperativa Origem Camponesa - MPA.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:30',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de costura criativa', 'feicoop'),
            'location' => __('Sala 7 - Luisa Bairros - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Segurança alimentar e nutricional. Coordenação: Nadyanni Andres. Entidade responsável: Comitê Ambiental - Casa do Estudante UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Finanças solidárias e moeda social', 'feicoop'),
            'location' => __('Sala 8 - Isadora Viana Costa - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Rita Inês Paetzhold Pauli. Entidade responsável: Projeto PROMOVER / UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Sofá na Rua - uma tecnologia social replicável, um modo de ocupar as ruas', 'feicoop'),
            'location' => __('Sala 9 - Dom Ivo - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Cultura como elemento transformador dos espaços públicos, fortalecimento de comunidades e ampliação do acesso à arte por meio da participação coletiva. Coordenação: Renata da Silva Camargo. Entidade responsável: Comitê de Cultura do RS / Associação Cultural e Educacional Sofá na Rua.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('O futuro das cooperativas de plataforma na economia solidária', 'feicoop'),
            'location' => __('Sala 10 - Nei d\'Ogum - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: O futuro das cooperativas de plataforma na economia solidária. Coordenação: Rozelaine dos Santos Lima. Entidade responsável: Superintendência Regional do Trabalho e Emprego no Rio Grande do Sul.', 'feicoop'),
        ],
        [
            'date' => '2026-07-11',
            'time' => '14:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Seminário Nacional de Etnodesenvolvimento', 'feicoop'),
            'location' => __('Ginásio da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Aldori Marques dos Santos. Entidade responsável: Associação São Jerônimo.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '07:00',
            'track' => __('Abertura', 'feicoop'),
            'title' => __('Alvorada festiva', 'feicoop'),
            'location' => __('Território da 32ª FEICOOP', 'feicoop'),
            'excerpt' => __('Início do domingo com atividades festivas.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '07:00',
            'track' => __('Comercialização', 'feicoop'),
            'title' => __('Comercialização direta dos empreendimentos da Ecosol', 'feicoop'),
            'location' => __('Pavilhões da feira', 'feicoop'),
            'excerpt' => __('Atividades de comercialização até as 19h.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:30',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Cooperativismo habitacional: produção social da moradia através da autogestão', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Cooperativismo Habitacional. Coordenação: Ceniriani Vargas da Silva (Ni). Entidade responsável: Movimento Nacional de Luta pela Moradia - MNLM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Projeto modelo de restauração ambiental e créditos de carbono', 'feicoop'),
            'location' => __('Sala 2 - Frei Sergio Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('No contexto das mudanças climáticas. Coordenação: Vilmar Bagetti. Entidade responsável: Morada do Bambu.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Economia solidária e cultura', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Interconexões entre movimentos e políticas públicas para um mundo possível. Coordenação: Maria Suziane Gutbier. Entidade responsável: Ponto de Cultura Associação Cantalomba, FGEPS, Comitê Cultura Viva RS, UNISOL/RS, RESF/RS e Ponto de Cultura Casa da Praça.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Falando sobre o amor na perspectiva de Bell Hooks', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Oficina sobre o livro "Tudo sobre o amor". Coordenação: Jacilene Aguiar Silva e Ângela Maria de Souza Lima. Entidade responsável: CAROLINAS e NEABI-UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Roda de conversa', 'feicoop'),
            'title' => __('Avanços e desafios dos EES de artesanato', 'feicoop'),
            'location' => __('Salão da Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('A visão dos expositores na 32ª FEICOOP. Coordenação: Rita de Cássia Arruda Fajardo. Entidade responsável: Rede IF Ecosol.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Óleos essenciais e hidrolatos do Pampa e Mata Atlântica', 'feicoop'),
            'location' => __('Sala 5 - Bruno Pereira e Don Phillips - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Lisiane Gonçalves Brolese. Entidade responsável: Rede Feminista de Destiladoras.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '09:00',
            'track' => __('Seminário', 'feicoop'),
            'title' => __('Peabiru: o mítico e sagrado caminho do Atlântico ao Pacífico', 'feicoop'),
            'location' => __('Sala 6 - Verônica Oliveira - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Saberes ancestrais, intercâmbios milenares e integração latino-americana. Coordenação: Carlos André Echenique Dominguez. Entidade responsável: Ponga Press / UFpel.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '10:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Apresentação do Projeto Construindo um novo futuro para o RS', 'feicoop'),
            'location' => __('Salão Paul Singer - Fundos do terminal de comercialização', 'feicoop'),
            'excerpt' => __('Feiras de Economia Solidária. Coordenação: Maribel Kauffmann. Entidade responsável: Associação do Voluntariado e da Solidariedade - AVESOL.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('1º Batalha de conhecimento 32° Feicoop - O Hip Hop vive a economia popular solidária', 'feicoop'),
            'location' => __('Sala 1 - Margarida Alves - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Tema: Hip Hop e Economia Popular Solidária. Coordenação: Gilciane Beatriz Aguiar Das Neves. Entidade responsável: Fórum Estadual das Mulheres Negras Trabalhadoras da Economia Popular Solidária e CAMP.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Reunião', 'feicoop'),
            'title' => __('Diálogos sobre as questões de gênero no espaço universitário', 'feicoop'),
            'location' => __('Sala 2 - Frei José Gorgen - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Daiane Loreto de Vargas e Gisele Martins Guimarães. Entidade responsável: Universidade Federal de Santa Maria - Centro de Ciências Rurais - Departamento de Educação Agrícola e Extensão Rural.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:30',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de jardinagem sustentável', 'feicoop'),
            'location' => __('Sala 3 - Chico Mendes - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Reutilização de materiais recicláveis para produção vegetal, com princípios agroecológicos. Coordenação: Nadyanni Andres. Entidade responsável: Comitê Ambiental - Casa do Estudante UFSM.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Formación política, economía solidaria, articulación en red', 'feicoop'),
            'location' => __('Sala 4 - Galdino - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Pedagogía freiriana para descolonizar el cuerpo y la mente. "LIVE-FIZINE: Juego, cuerpo y autogestión". Coordenação: Federico Alejandro Servetto Liasv. Entidade responsável: Corredor Multicultural Plurinacional x Abya Yala / COMPIAY.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Oficina de escrita potencial', 'feicoop'),
            'location' => __('Sala 5 - Bruno Pereira e Don Phillips - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Maria Clara da Silva Ramos Carneiro. Entidade responsável: UFSM / Departamento de Letras Estrangeiras Modernas.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Agrofloresta em la Fronteira', 'feicoop'),
            'location' => __('Sala 6 - Verônica Oliveira - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: Matías Bertone. Entidade responsável: Cooperativa Monte Nativa e Ministerio del Agro Misiones Argentina.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '14:00',
            'track' => __('Oficina', 'feicoop'),
            'title' => __('Tambores pulsam vida', 'feicoop'),
            'location' => __('Sala 7 - Luisa Bairros - Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Coordenação: João Eberti de Ogun. Entidade responsável: Povo de Terreiro de Santa Maria.', 'feicoop'),
        ],
        [
            'date' => '2026-07-12',
            'time' => '18:00',
            'track' => __('Encerramento', 'feicoop'),
            'title' => __('Encerramento oficial dos eventos de 2026', 'feicoop'),
            'location' => __('Escola Estadual de Educação Básica Irmão José Otão', 'feicoop'),
            'excerpt' => __('Leitura da Carta da 32ª FEICOOP e lançamento da 33ª FEICOOP de 2026.', 'feicoop'),
        ],
    ];
}

function feicoop_programacao_seed_slug(array $item): string {
    $date = isset($item['date']) ? (string) $item['date'] : '';
    $time = isset($item['time']) ? (string) $item['time'] : '';
    $title = isset($item['title']) ? (string) $item['title'] : '';

    $parts = array_filter([$date, $time, $title], static function (string $value): bool {
        return $value !== '';
    });

    return sanitize_title(implode(' ', $parts));
}

function feicoop_programacao_seed_content(array $item): string {
    $pieces = [];

    if (!empty($item['excerpt'])) {
        $pieces[] = (string) $item['excerpt'];
    }

    $date = isset($item['date']) ? (string) $item['date'] : '';
    $time = isset($item['time']) ? (string) $item['time'] : '';
    $location = isset($item['location']) ? (string) $item['location'] : '';
    $track = isset($item['track']) ? (string) $item['track'] : '';

    if ($date !== '' || $time !== '') {
        $when = trim($date . ' ' . $time);
        $pieces[] = sprintf(__('Quando: %s', 'feicoop'), $when);
    }

    if ($location !== '') {
        $pieces[] = sprintf(__('Local: %s', 'feicoop'), $location);
    }

    if ($track !== '') {
        $pieces[] = sprintf(__('Faixa: %s', 'feicoop'), $track);
    }

    return implode("\n\n", $pieces);
}

function feicoop_programacao_find_seeded_post(string $slug): ?WP_Post {
    $post = get_page_by_path($slug, OBJECT, 'programacao');

    return $post instanceof WP_Post ? $post : null;
}

function feicoop_ensure_programacao_seed_item(array $item): int {
    $slug = feicoop_programacao_seed_slug($item);

    if ($slug === '') {
        return 0;
    }

    $existing = feicoop_programacao_find_seeded_post($slug);

    if ($existing instanceof WP_Post) {
        return (int) $existing->ID;
    }

    $content = feicoop_programacao_seed_content($item);
    $post_id = wp_insert_post([
        'post_type' => 'programacao',
        'post_status' => 'publish',
        'post_title' => isset($item['title']) ? (string) $item['title'] : '',
        'post_name' => $slug,
        'post_content' => $content,
        'post_excerpt' => $content,
        'post_author' => get_current_user_id() ?: 1,
    ], true);

    if (is_wp_error($post_id) || $post_id <= 0) {
        return 0;
    }

    update_post_meta($post_id, '_feicoop_programacao_date', isset($item['date']) ? (string) $item['date'] : '');
    update_post_meta($post_id, '_feicoop_programacao_time', isset($item['time']) ? (string) $item['time'] : '');
    update_post_meta($post_id, '_feicoop_programacao_location', isset($item['location']) ? (string) $item['location'] : '');
    update_post_meta($post_id, '_feicoop_programacao_track', isset($item['track']) ? (string) $item['track'] : '');
    update_post_meta($post_id, '_feicoop_programacao_featured', '');

    return (int) $post_id;
}

function feicoop_seed_programacao_items(): void {
    foreach (feicoop_programacao_static_schedule() as $item) {
        feicoop_ensure_programacao_seed_item($item);
    }

    update_option('feicoop_programacao_seed_version', (string) wp_get_theme()->get('Version'));
}

function feicoop_maybe_seed_programacao_items(): void {
    if (!is_admin() || !current_user_can('publish_posts')) {
        return;
    }

    $theme_version = (string) wp_get_theme()->get('Version');
    $stored_version = (string) get_option('feicoop_programacao_seed_version', '');

    if ($stored_version === $theme_version) {
        return;
    }

    feicoop_seed_programacao_items();
}
add_action('admin_init', 'feicoop_maybe_seed_programacao_items', 20);

/* =====================================================================
 * Cartas de encerramento (seed ao ativar/atualizar o tema)
 * ===================================================================== */
function feicoop_seed_cartas(): void {
    $cartas = require get_template_directory() . '/inc/cartas.php';

    if (!is_array($cartas)) {
        return;
    }

    foreach ($cartas as $year => $carta) {
        $year = (int) $year;

        if ($year <= 0 || !isset($carta['title'], $carta['content'])) {
            continue;
        }

        $slug = 'carta-de-encerramento-' . $year;
        $existing = get_page_by_path($slug, OBJECT, 'publicacao');

        if ($existing instanceof WP_Post) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'publicacao',
            'post_status' => 'publish',
            'post_title' => (string) $carta['title'],
            'post_name' => $slug,
            'post_content' => (string) $carta['content'],
            'post_date' => $year . '-07-15 12:00:00',
            'post_date_gmt' => get_gmt_from_date($year . '-07-15 12:00:00'),
        ]);

        if (is_wp_error($post_id) || $post_id === 0) {
            continue;
        }

        update_post_meta($post_id, '_feicoop_publicacao_type', 'texto');
        update_post_meta($post_id, '_feicoop_publicacao_year', (string) $year);
    }

    update_option('feicoop_cartas_seed_version', (string) wp_get_theme()->get('Version'));
}

function feicoop_maybe_seed_cartas(): void {
    if (!is_admin() || !current_user_can('publish_posts')) {
        return;
    }

    $theme_version = (string) wp_get_theme()->get('Version');
    $stored_version = (string) get_option('feicoop_cartas_seed_version', '');

    if ($stored_version === $theme_version) {
        return;
    }

    feicoop_seed_cartas();
}
add_action('admin_init', 'feicoop_maybe_seed_cartas', 20);

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

    return '<img src="' . esc_url(feicoop_asset_url('assets/img/card-projeto-esperanca.avif')) . '" width="1600" height="1000" alt="' . esc_attr(get_the_title($post_id) !== '' ? get_the_title($post_id) : get_bloginfo('name')) . '"' . ($class !== '' ? ' class="' . esc_attr($class) . '"' : '') . ' loading="lazy" decoding="async">';
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

    // Valida formatos esperados (Y-m-d e H:i); descarta valores fora do padrão
    // para não quebrar o agrupamento por dia nem a formatação na saída.
    if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = '';
    }

    if ($time !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
        $time = '';
    }

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

    if (!$screen) {
        return;
    }

    // Editor de publicações: seletor de PDF via wp.media.
    if ($screen->post_type === 'publicacao') {
        wp_enqueue_media();
        return;
    }

    if ($screen->post_type !== 'page') {
        return;
    }

    // Página com o template de galeria: seletor de imagens.
    if (get_page_template_slug((int) ($_GET['post'] ?? 0)) === 'template-galeria.php') {
        wp_enqueue_media();
        return;
    }

}
add_action('admin_enqueue_scripts', 'feicoop_admin_enqueue_assets');

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

    $wp_customize->add_section('feicoop_home_event', [
        'title' => __('FEICOOP Destaques da home', 'feicoop'),
        'description' => __('Edita as informações curtas exibidas no bloco de fatos da home.', 'feicoop'),
        'priority' => 26,
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

    // Datas estruturadas do evento, usadas no schema Event (SEO).
    foreach (['start' => __('Data de início (AAAA-MM-DD)', 'feicoop'), 'end' => __('Data de término (AAAA-MM-DD)', 'feicoop')] as $edge => $label) {
        $wp_customize->add_setting("feicoop_event_{$edge}_date", [
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control("feicoop_event_{$edge}_date", [
            'label' => $label,
            'description' => __('Preencha para o Google exibir o evento na busca. Ex.: 2026-07-13.', 'feicoop'),
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

    $wp_customize->add_section('feicoop_home_contact', [
        'title' => __('FEICOOP Contatos (home)', 'feicoop'),
        'description' => __('Edita os dados de contato exibidos na seção final da página inicial.', 'feicoop'),
        'priority' => 24,
    ]);

    $home_contact_fields = [
        'coordinator' => [
            'label' => __('Coordenação', 'feicoop'),
            'default' => 'José Carlos Peranconi',
        ],
        'phones' => [
            'label' => __('Telefones', 'feicoop'),
            'default' => 'José Carlos Peranconi: 55 99974 4567',
        ],
        'email' => [
            'label' => __('E-mail', 'feicoop'),
            'default' => 'projeto@esperancacooesperanca.org.br',
            'sanitize_callback' => 'sanitize_email',
        ],
        'whatsapp' => [
            'label' => __('WhatsApp (somente números, com DDI e DDD)', 'feicoop'),
            'default' => '5555999744567',
            'sanitize_callback' => 'feicoop_sanitize_digits',
        ],
        'address' => [
            'label' => __('Endereço', 'feicoop'),
            'description' => __('Use uma linha por item de endereço.', 'feicoop'),
            'default' => "Rua Heitor Campos, s/n\nMedianeira, Santa Maria - RS\nCEP 97060-290",
            'type' => 'textarea',
            'sanitize_callback' => 'sanitize_textarea_field',
        ],
        'facebook' => [
            'label' => __('Facebook', 'feicoop'),
            'default' => 'https://www.facebook.com/share/18i1BbrmgR/',
            'sanitize_callback' => 'esc_url_raw',
        ],
        'instagram' => [
            'label' => __('Instagram Feirão EcoSol', 'feicoop'),
            'default' => 'https://www.instagram.com/feirao.ecosol/',
            'sanitize_callback' => 'esc_url_raw',
        ],
        'youtube' => [
            'label' => __('YouTube', 'feicoop'),
            'default' => 'https://www.youtube.com/channel/UC9fE3YsQNza8UpiYULNHIZw',
            'sanitize_callback' => 'esc_url_raw',
        ],
    ];

    foreach ($home_contact_fields as $key => $config) {
        $wp_customize->add_setting("feicoop_home_contact_{$key}", [
            'default' => $config['default'],
            'sanitize_callback' => $config['sanitize_callback'] ?? 'sanitize_text_field',
        ]);

        $wp_customize->add_control("feicoop_home_contact_{$key}", [
            'label' => $config['label'],
            'description' => $config['description'] ?? '',
            'section' => 'feicoop_home_contact',
            'type' => $config['type'] ?? 'text',
        ]);
    }

    $wp_customize->add_setting('feicoop_footer_copyright', [
        'default' => 'Projeto Esperança/Cooesperança',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('feicoop_footer_copyright', [
        'label' => __('Texto de copyright do rodapé', 'feicoop'),
        'section' => 'feicoop_home_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('feicoop_footer_legal', [
        'default' => "CNPJ: 93.155.067/0001-86\nRazão Social: Cooperativa Mista dos Pequenos Produtores Rurais e Urbanos Vinculados ao Projeto Esperança Ltda (Cooesperança)",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('feicoop_footer_legal', [
        'label' => __('Informações legais do rodapé (CNPJ / Razão Social)', 'feicoop'),
        'description' => __('Use uma linha por item; cada linha vira uma quebra de linha no rodapé.', 'feicoop'),
        'section' => 'feicoop_home_contact',
        'type' => 'textarea',
    ]);

    // --- Conteúdo dos blocos da home (editável pelo Customizer) ---
    $wp_customize->add_section('feicoop_home_intro', [
        'title' => __('FEICOOP Home — Introdução', 'feicoop'),
        'description' => __('Texto da seção de introdução da página inicial.', 'feicoop'),
        'priority' => 23,
    ]);

    $intro = feicoop_home_intro_fields();

    feicoop_customize_add_fields($wp_customize, [
        'feicoop_home_intro_kicker' => ['label' => __('Legenda', 'feicoop'), 'section' => 'feicoop_home_intro', 'default' => $intro['kicker']],
        'feicoop_home_intro_title' => ['label' => __('Título', 'feicoop'), 'section' => 'feicoop_home_intro', 'default' => $intro['title']],
        'feicoop_home_intro_text' => [
            'label' => __('Texto', 'feicoop'),
            'description' => __('Use uma linha em branco para separar parágrafos.', 'feicoop'),
            'section' => 'feicoop_home_intro',
            'type' => 'textarea',
            'default' => $intro['text'],
        ],
    ]);

    $wp_customize->add_section('feicoop_home_quicklinks', [
        'title' => __('FEICOOP Home — Atalhos', 'feicoop'),
        'description' => __('Os quatro links de atalho da home (Institucional, Memória, Rede e Comercialização).', 'feicoop'),
        'priority' => 22,
    ]);

    $quicklink_fields = [];

    foreach (feicoop_home_quicklink_defaults() as $index => $default) {
        $number = $index + 1;
        $quicklink_fields["feicoop_home_quicklink_{$number}_label"] = ['label' => sprintf(__('Atalho %d — rótulo', 'feicoop'), $number), 'section' => 'feicoop_home_quicklinks', 'default' => $default['label']];
        $quicklink_fields["feicoop_home_quicklink_{$number}_kicker"] = ['label' => sprintf(__('Atalho %d — legenda', 'feicoop'), $number), 'section' => 'feicoop_home_quicklinks', 'default' => $default['kicker']];
        $quicklink_fields["feicoop_home_quicklink_{$number}_url"] = ['label' => sprintf(__('Atalho %d — URL', 'feicoop'), $number), 'section' => 'feicoop_home_quicklinks', 'type' => 'url', 'default' => $default['url']];
    }

    feicoop_customize_add_fields($wp_customize, $quicklink_fields);

    $wp_customize->add_section('feicoop_home_projects', [
        'title' => __('FEICOOP Home — Cards de projetos', 'feicoop'),
        'description' => __('Os quatro cards de projetos e redes da home.', 'feicoop'),
        'priority' => 21,
    ]);

    $project_fields = [];

    foreach (feicoop_home_project_card_defaults() as $index => $default) {
        $number = $index + 1;
        $project_fields["feicoop_home_project_{$number}_title"] = ['label' => sprintf(__('Card %d — título', 'feicoop'), $number), 'section' => 'feicoop_home_projects', 'default' => $default['title']];
        $project_fields["feicoop_home_project_{$number}_eyebrow"] = ['label' => sprintf(__('Card %d — legenda', 'feicoop'), $number), 'section' => 'feicoop_home_projects', 'default' => $default['eyebrow']];
        $project_fields["feicoop_home_project_{$number}_text"] = ['label' => sprintf(__('Card %d — texto', 'feicoop'), $number), 'section' => 'feicoop_home_projects', 'type' => 'textarea', 'default' => $default['text']];
        $project_fields["feicoop_home_project_{$number}_url"] = ['label' => sprintf(__('Card %d — URL', 'feicoop'), $number), 'section' => 'feicoop_home_projects', 'type' => 'url', 'default' => $default['url']];
        $project_fields["feicoop_home_project_{$number}_image"] = ['label' => sprintf(__('Card %d — imagem', 'feicoop'), $number), 'section' => 'feicoop_home_projects', 'type' => 'image', 'default' => $default['image']];
    }

    feicoop_customize_add_fields($wp_customize, $project_fields);

    $wp_customize->add_section('feicoop_home_highlight', [
        'title' => __('FEICOOP Home — Destaque central', 'feicoop'),
        'priority' => 20,
    ]);

    $highlight = feicoop_home_highlight_fields();

    feicoop_customize_add_fields($wp_customize, [
        'feicoop_home_highlight_kicker' => ['label' => __('Legenda', 'feicoop'), 'section' => 'feicoop_home_highlight', 'default' => $highlight['kicker']],
        'feicoop_home_highlight_title' => ['label' => __('Título', 'feicoop'), 'section' => 'feicoop_home_highlight', 'default' => $highlight['title']],
        'feicoop_home_highlight_text' => ['label' => __('Texto', 'feicoop'), 'section' => 'feicoop_home_highlight', 'type' => 'textarea', 'default' => $highlight['text']],
    ]);

    $wp_customize->add_section('feicoop_home_news', [
        'title' => __('FEICOOP Home — Notícias', 'feicoop'),
        'description' => __('Cabeçalho da seção de notícias da home.', 'feicoop'),
        'priority' => 19,
    ]);

    $news = feicoop_home_news_fields();

    feicoop_customize_add_fields($wp_customize, [
        'feicoop_home_news_kicker' => ['label' => __('Legenda', 'feicoop'), 'section' => 'feicoop_home_news', 'default' => $news['kicker']],
        'feicoop_home_news_title' => ['label' => __('Título', 'feicoop'), 'section' => 'feicoop_home_news', 'default' => $news['title']],
        'feicoop_home_news_text' => ['label' => __('Texto', 'feicoop'), 'section' => 'feicoop_home_news', 'type' => 'textarea', 'default' => $news['text']],
    ]);

    $wp_customize->add_section('feicoop_home_cooesperanca', [
        'title' => __('FEICOOP Home — Projeto Cooesperança', 'feicoop'),
        'description' => __('Seção sobre o Projeto Cooesperança na página inicial (em construção).', 'feicoop'),
        'priority' => 18,
    ]);

    $cooesperanca = feicoop_home_cooesperanca_fields();

    feicoop_customize_add_fields($wp_customize, [
        'feicoop_home_cooesperanca_kicker' => ['label' => __('Legenda', 'feicoop'), 'section' => 'feicoop_home_cooesperanca', 'default' => $cooesperanca['kicker']],
        'feicoop_home_cooesperanca_title' => ['label' => __('Título', 'feicoop'), 'section' => 'feicoop_home_cooesperanca', 'default' => $cooesperanca['title']],
        'feicoop_home_cooesperanca_text' => ['label' => __('Texto', 'feicoop'), 'section' => 'feicoop_home_cooesperanca', 'type' => 'textarea', 'default' => $cooesperanca['text']],
        'feicoop_site_construction_notice' => [
            'label' => __('Aviso de construção (topo da home)', 'feicoop'),
            'description' => __('Deixe vazio para ocultar a faixa.', 'feicoop'),
            'section' => 'feicoop_home_cooesperanca',
            'type' => 'textarea',
            'default' => 'Site em construção — o portal está sendo atualizado com novas informações do Projeto Cooesperança.',
        ],
    ]);
}
add_action('customize_register', 'feicoop_customize_register');

/**
 * Registra settings/controls do Customizer de forma compacta.
 */
function feicoop_customize_add_fields(WP_Customize_Manager $wp_customize, array $fields): void {
    foreach ($fields as $setting_id => $config) {
        $type = $config['type'] ?? 'text';
        $sanitize = $config['sanitize_callback'] ?? ($type === 'textarea' ? 'sanitize_textarea_field' : ($type === 'url' ? 'esc_url_raw' : 'sanitize_text_field'));

        $wp_customize->add_setting($setting_id, [
            'default' => $config['default'] ?? '',
            'sanitize_callback' => $sanitize,
        ]);

        if ($type === 'image') {
            $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting_id, [
                'label' => $config['label'] ?? $setting_id,
                'description' => $config['description'] ?? '',
                'section' => $config['section'],
            ]));
        } else {
            $wp_customize->add_control($setting_id, [
                'label' => $config['label'] ?? $setting_id,
                'description' => $config['description'] ?? '',
                'section' => $config['section'],
                'type' => $type,
            ]);
        }
    }
}

function feicoop_main_menu_fallback(): void {
    echo '<ul class="navbar__menu">';
    echo '<li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Início', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('quem-somos', '/quem-somos.html')) . '">' . esc_html__('Quem somos', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('historia', '/historia.html')) . '">' . esc_html__('História', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('cooesperanca', '/cooesperanca.html')) . '">' . esc_html__('A Cooesperança', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('feirao-colonial', '/feirao-colonial.html')) . '">' . esc_html__('Feirão EcoSol', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_programacao_archive_url()) . '">' . esc_html__('FEICOOP', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('ponto-de-cultura', '/ponto-de-cultura.html')) . '">' . esc_html__('Ponto de Cultura', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(get_post_type_archive_link('publicacao')) . '">' . esc_html__('Produções e Publicações', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('galeria', '/galeria.html')) . '">' . esc_html__('Galeria de Fotos', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_posts_page_url()) . '">' . esc_html__('Notícias', 'feicoop') . '</a></li>';
    echo '<li><a href="' . esc_url(feicoop_page_url('contato', '/contato.html')) . '">' . esc_html__('Contato', 'feicoop') . '</a></li>';
    echo '</ul>';
}

function feicoop_footer_menu_fallback(): void {
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'feicoop') . '</a></li></ul>';
}
