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

    return home_url('/feicoop/');
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
        'facebook' => (string) get_theme_mod('feicoop_home_contact_facebook', 'https://www.facebook.com/feicoop'),
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
        ['url' => feicoop_page_url('quem-somos', '/quem-somos.html'), 'kicker' => 'Institucional', 'label' => 'Quem somos', 'image' => feicoop_asset_url('assets/img/card-projeto-esperanca.avif')],
        ['url' => feicoop_page_url('historia', '/historia.html'), 'kicker' => 'Memória', 'label' => 'História', 'image' => feicoop_asset_url('assets/img/card-rede-esperanca.avif')],
        ['url' => feicoop_page_url('feirao-colonial', '/feirao-colonial.html'), 'kicker' => 'Comercialização', 'label' => 'Feirão EcoSol', 'image' => feicoop_asset_url('assets/img/card-feirao-colonial.avif')],
        ['url' => feicoop_programacao_archive_url(), 'kicker' => 'Evento anual', 'label' => 'FEICOOP', 'image' => feicoop_asset_url('assets/img/card-feicoop.avif')],
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
            'image' => (string) get_theme_mod("feicoop_home_quicklink_{$number}_image", $default['image'] ?? ''),
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
 * Seção "A Cooesperança" da home (destaque no painel do hero).
 */
function feicoop_home_cooesperanca_fields(): array {
    return [
        'kicker' => (string) get_theme_mod('feicoop_home_cooesperanca_kicker', 'A Cooesperança'),
        'title' => (string) get_theme_mod('feicoop_home_cooesperanca_title', 'Cooperativa Mista dos Pequenos Produtores'),
        'text' => (string) get_theme_mod('feicoop_home_cooesperanca_text', 'Atuação em economia solidária, agricultura familiar e agroindústria, gerando trabalho e renda para pequenos produtores de Santa Maria e região.'),
    ];
}

function feicoop_site_construction_notice(): string {
    // Vazio por padrão: o aviso "Site em construção" foi removido do ar.
    return (string) get_theme_mod('feicoop_site_construction_notice', '');
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

/**
 * Conteúdo padrão da seção "A trajetória da FEICOOP" (página /feicoop/).
 * Aceita HTML: títulos h2/h3 e parágrafos.
 */
function feicoop_feicoop_history_default(): string {
    return <<<HTML
<h2>A trajetória exitosa da FEICOOP</h2>

<p>A FEICOOP — Feira Internacional do Cooperativismo e da Economia Solidária — acontece desde 1994, em Santa Maria. Muito mais que uma feira de comercialização, ela é um importante espaço de encontro e articulação da Economia Solidária, reunindo grupos, coletivos, cooperativas, associações, empreendimentos solidários, agricultura familiar, artesanato, movimentos sociais e consumidores.</p>

<p>Possui uma trajetória exitosa, reunindo a cada ano grande diversidade de produtos, serviços e construção de conhecimentos. Pessoas e culturas vindas de todos os estados brasileiros e da América Latina compõem um grande mosaico de cooperação, autogestão, comércio justo e solidário, em movimento por outras economias.</p>

<p>A FEICOOP nasce em 1994 como “Feira do Cooperativismo”, com escala regional, reunindo empreendimentos rurais e urbanos das cidades da Região Central do RS. Já em 1998, a “Feira do Cooperativismo” ganha status estadual, recebendo empreendimentos, atividades formativas e culturais de todo o Estado do RS.</p>

<p>Mas é em 2005 que a Feira ganha status nacional e internacional, passando a destacar a Economia Solidária como outro mundo possível para a cooperação e o cooperativismo. Passou então a chamar-se “Feira do Cooperativismo e Economia Solidária”, recebendo a cada ano um grande número de empreendimentos de todo o Brasil, da América Latina e de outros continentes. Importante destacar que em 2003 ocorreu no Brasil a institucionalização da Economia Solidária como política de Estado, com a criação da Senaes (Secretaria Nacional da Economia Solidária) no Ministério do Trabalho (MT). Como parte de um movimento cada vez mais crescente no país, a Economia Solidária passa a ser destacada no nome da Feira, que, incluindo a dimensão da Economia Solidária, passa a ampliar a compreensão do próprio cooperativismo.</p>

<p>O grande destaque da FEICOOP é a metodologia de sua organização, denominada “Aprendente e Ensinante”, que traz desafios de autonomia, mobilização popular, convivência e articulação institucional para a construção de uma grande feira de comercialização direta, de formação e articulação e ainda de arte, cultura e diversidade. Acontece durante três dias, transformando Santa Maria em um grande território da Economia Solidária.</p>

<p>Dessa metodologia, destaque para as Cartas da FEICOOP. Escritas ao final de cada edição, expressam o contexto sociopolítico do país e do mundo, pontuando desafios e conquistas do período. As cartas passaram a ser escritas a partir de 2005.</p>

<p>Assim, a FEICOOP é compreendida como uma experiência “aprendente e ensinante” porque ela não é apenas um espaço de comercialização: é também um espaço educativo, de troca de saberes e construção coletiva do conhecimento, onde agricultores familiares, empreendimentos de economia solidária, cooperativas, movimentos sociais, estudantes, universidades e consumidores compartilham experiências e conhecimentos. Assim, quem participa ensina a partir de sua própria experiência e, ao mesmo tempo, aprende com as experiências dos outros.</p>

<h2>Eixos da FEICOOP: bem mais que exposição de produtos</h2>

<p>A Feira acontece em três eixos distintos e complementares que expressam a Economia Solidária em toda a sua potencialidade, desde oportunidades de geração de trabalho e renda com autonomia e sustentabilidade até a valorização cultural e o fortalecimento dos empreendimentos solidários.</p>

<h3>Eixo Comercialização</h3>

<p>Este eixo está diretamente relacionado aos empreendimentos de Economia Solidária. Representa um espaço de comercialização direta e promoção do consumo consciente, trazendo visibilidade aos pequenos empreendimentos pautados pelos princípios do cooperativismo e da economia solidária.</p>

<p>Estão presentes empreendimentos de diversos segmentos, como artesanato, agricultura familiar, economia circular e criativa, produtos ancestrais confeccionados por populações tradicionais e povos originários, moda autoral etc. Este eixo ganha destaque pela diversidade de produtos que oferece, com empreendimentos do Brasil e do mundo.</p>

<p>O fato de possuir representatividade mundial é o que confere à Feira o título de maior feira de Economia Solidária da América Latina. A cada ano vêm sendo recebidas inscrições de mais empreendimentos da América Latina, o que indica a potência e a visibilidade empenhadas nesta feira, que está cada vez mais gigante.</p>

<p>A comercialização na Economia Solidária fomenta a visibilidade da diversidade de empreendimentos em uma enorme variedade de segmentos. Destaca-se que a FEICOOP tem em sua maioria empreendimentos geridos por mulheres, o que significa o fortalecimento da economia das mulheres, com aumento da renda e, consequentemente, qualidade e autonomia na vida.</p>

<h3>Eixo Formação e Articulação</h3>

<p>A formação está ligada às atividades que promovem reflexões, aperfeiçoamento, debate, capacitações, oficinas e reuniões de grupos, coletivos, movimentos sociais, organizações da sociedade civil, entre outros. Este espaço é um dos mais importantes da FEICOOP, pois permite, a partir de encontros e reuniões, discutir temáticas contemporâneas para pensar estratégias de desenvolvimento da sociedade.</p>

<p>Percebe-se que a cada ano, de acordo com a temática da edição, há um direcionamento para causas sociais, ambientais e econômicas, urgências e emergências. Aqui saem resultados que embasam políticas públicas, trabalhos de pesquisa acadêmica, projetos de lei e uma diversidade de trabalhos frutos de organizações da sociedade civil.</p>

<h3>Eixo Arte, Cultura e Diversidade</h3>

<p>No eixo de Arte, Cultura e Diversidade estão inseridas apresentações e manifestações artísticas, como música, dança, teatro, ritmos e muito mais. Na FEICOOP sempre se estrutura um palco, com ornamentações que representam povos e nações, alimentos e alimentação — que é um direito garantido à população, mas que em muitas situações acaba não atingindo todos e todas — e elementos que são a identidade visual do movimento de Economia Solidária. O palco é espaço de fala, usado para dar voz às etnias e às variedades de estilos artísticos contidos nas apresentações que seguem durante toda a programação da feira, além de discursos e debates importantes.</p>

<p>Este espaço é um local rico em cultura e arte, muitas vezes animado e festivo, porém em alguns momentos também é usado para debates e reflexões acerca dos desafios encontrados no campo, na cidade e em outros locais de moradia e garantia de renda e sustento de muitas famílias.</p>
HTML;
}


