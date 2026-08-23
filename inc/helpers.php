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

    return home_url('/#noticias');
}

function feicoop_programacao_archive_url(): string {
    $url = get_post_type_archive_link('programacao');

    if (is_string($url) && $url !== '') {
        return $url;
    }

    return home_url('/programacao/');
}

function feicoop_programacao_pdf_url(): string {
    $pdf = (string) get_theme_mod('feicoop_programacao_pdf_url', '');

    if ($pdf !== '' && filter_var($pdf, FILTER_VALIDATE_URL)) {
        return $pdf;
    }

    return feicoop_asset_url('programacao-feicoop-2026.pdf');
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

function feicoop_home_contact_fields(): array {
    return [
        'coordinator' => (string) get_theme_mod('feicoop_home_contact_coordinator', 'José Carlos Peranconi'),
        'phones' => (string) get_theme_mod('feicoop_home_contact_phones', 'José Carlos Peranconi: 55 99974 4567'),
        'email' => (string) get_theme_mod('feicoop_home_contact_email', 'projeto@esperancacooesperanca.org.br'),
        'whatsapp' => (string) get_theme_mod('feicoop_home_contact_whatsapp', '5555999744567'),
        'address' => (string) get_theme_mod('feicoop_home_contact_address', "Rua Heitor Campos, s/n\nMedianeira, Santa Maria - RS\nCEP 97060-290"),
        'facebook' => (string) get_theme_mod('feicoop_home_contact_facebook', 'https://www.facebook.com/share/18i1BbrmgR/'),
        'instagram' => (string) get_theme_mod('feicoop_home_contact_instagram', 'https://www.instagram.com/feirao.ecosol/'),
        'youtube' => (string) get_theme_mod('feicoop_home_contact_youtube', 'https://www.youtube.com/channel/UC9fE3YsQNza8UpiYULNHIZw'),
    ];
}

/**
 * Blocos de conteúdo da home (intro, quicklinks, cards de projetos, destaque
 * e cabeçalho de notícias), editáveis pelo Customizer. Os defaults preservam
 * o conteúdo atual do tema.
 */
function feicoop_home_intro_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_intro_kicker', 'Projeto Esperança/Cooesperança'),
        'title' => (string) get_theme_mod('feicoop_home_intro_title', 'Organização popular, economia solidária e articulação em rede'),
        'text' => (string) get_theme_mod('feicoop_home_intro_text', "O Projeto Esperança/Cooesperança articula experiências de economia popular e solidária, agricultura familiar, comércio justo e cooperativismo em Santa Maria e na região central do Rio Grande do Sul.\n\nSeu trabalho conecta grupos urbanos e rurais, promove circulação de renda no território e fortalece iniciativas coletivas comprometidas com a autogestão, a cooperação e o bem viver."),
    ];
}

function feicoop_home_quicklink_defaults(): array {
    return [
        ['url' => feicoop_page_url('quem-somos', '/quem-somos.html'), 'kicker' => 'Institucional', 'label' => 'Quem somos'],
        ['url' => feicoop_page_url('historia', '/historia.html'), 'kicker' => 'Memória', 'label' => 'História'],
        ['url' => feicoop_page_url('feirao-colonial', '/feirao-colonial.html'), 'kicker' => 'Comercialização', 'label' => 'Feirão EcoSol'],
    ];
}

function feicoop_home_quicklinks(): array {
    $links = [];

    foreach (feicoop_home_quicklink_defaults() as $index => $default) {
        $number = $index + 1;

        $links[] = [
            'url' => (string) get_theme_mod("feicoop_home_quicklink_{$number}_url", $default['url']),
            'kicker' => (string) get_theme_mod("feicoop_home_quicklink_{$number}_kicker", $default['kicker']),
            'label' => (string) get_theme_mod("feicoop_home_quicklink_{$number}_label", $default['label']),
        ];
    }

    return $links;
}

function feicoop_home_project_card_defaults(): array {
    return [
        [
            'url' => feicoop_page_url('quem-somos', '/quem-somos.html'),
            'image' => feicoop_asset_url('assets/img/card-projeto-esperanca.avif'),
            'eyebrow' => 'Articulação',
            'title' => 'Projeto Esperança/Cooesperança',
            'text' => 'Espaço onde acontece o Feirão EcoSol e de onde parte a articulação anual da FEICOOP.',
        ],
        [
            'url' => feicoop_page_url('feirao-colonial', '/feirao-colonial.html'),
            'image' => feicoop_asset_url('assets/img/card-feirao-colonial.avif'),
            'eyebrow' => 'Comercialização',
            'title' => 'Feirão EcoSol',
            'text' => 'Comercialização solidária, alimentação e agroecologia em atividade permanente aos sábados.',
        ],
        [
            'url' => home_url('/'),
            'image' => feicoop_asset_url('assets/img/card-feicoop.avif'),
            'eyebrow' => 'Evento anual',
            'title' => 'FEICOOP',
            'text' => 'Feira internacional realizada anualmente em Santa Maria, reunindo cooperativismo, cultura e economia solidária.',
        ],
    ];
}

function feicoop_home_project_cards(): array {
    $cards = [];

    foreach (feicoop_home_project_card_defaults() as $index => $default) {
        $number = $index + 1;

        $cards[] = [
            'url' => (string) get_theme_mod("feicoop_home_project_{$number}_url", $default['url']),
            'image' => (string) get_theme_mod("feicoop_home_project_{$number}_image", $default['image']),
            'eyebrow' => (string) get_theme_mod("feicoop_home_project_{$number}_eyebrow", $default['eyebrow']),
            'title' => (string) get_theme_mod("feicoop_home_project_{$number}_title", $default['title']),
            'text' => (string) get_theme_mod("feicoop_home_project_{$number}_text", $default['text']),
        ];
    }

    return $cards;
}

function feicoop_home_highlight_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_highlight_kicker', 'Memória e agenda'),
        'title' => (string) get_theme_mod('feicoop_home_highlight_title', 'Um espaço para reunir história, programação, redes parceiras e notícias da feira'),
        'text' => (string) get_theme_mod('feicoop_home_highlight_text', 'A FEICOOP reúne iniciativas do campo e da cidade em torno da cooperação, da comercialização solidária e da troca de saberes entre grupos e comunidades.'),
    ];
}

function feicoop_home_news_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_news_kicker', 'Notícias'),
        'title' => (string) get_theme_mod('feicoop_home_news_title', 'Última publicação'),
        'text' => (string) get_theme_mod('feicoop_home_news_text', 'Acompanhe a atualização mais recente da feira, os comunicados oficiais e as matérias que também aparecem na página completa de notícias.'),
    ];
}

/**
 * Seção "Projeto Cooesperança" da home (em construção).
 */
function feicoop_home_cooesperanca_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_cooesperanca_kicker', 'Projeto Cooesperança'),
        'title' => (string) get_theme_mod('feicoop_home_cooesperanca_title', 'Em construção'),
        'text' => (string) get_theme_mod('feicoop_home_cooesperanca_text', 'A página do Projeto Cooesperança está em construção. Em breve você encontrará aqui as informações sobre a cooperativa, sua história, seus empreendimentos e formas de participação.'),
    ];
}

function feicoop_site_construction_notice(): string {
    return (string) get_theme_mod('feicoop_site_construction_notice', 'Site em construção — o portal está sendo atualizado com novas informações do Projeto Cooesperança.');
}

/**
 * Cartas de encerramento das edições da FEICOOP (publicações do tipo texto
 * cujo título começa com "Carta de Encerramento"), em ordem cronológica.
 */
function feicoop_feicoop_cartas(): array {
    $query = new WP_Query([
        'post_type' => 'publicacao',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_feicoop_publicacao_year',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'meta_query' => [
            ['key' => '_feicoop_publicacao_type', 'value' => 'texto'],
        ],
        'no_found_rows' => true,
    ]);

    $cartas = [];

    foreach ($query->posts as $post) {
        $title = (string) get_the_title($post);

        if (!str_starts_with($title, 'Carta de Encerramento')) {
            continue;
        }

        $cartas[] = [
            'id' => (int) $post->ID,
            'title' => $title,
            'url' => (string) get_permalink($post),
            'year' => (int) get_post_meta($post->ID, '_feicoop_publicacao_year', true),
        ];
    }

    wp_reset_postdata();

    return $cartas;
}
