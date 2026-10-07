<?php
/**
 * Conteúdo legado do site antigo (Wix) — integração com o tema novo.
 *
 * Deve ficar em inc/legacy-content.php, com o conteúdo em inc/legacy/.
 *
 * O que faz:
 *  - importa as imagens de assets/legacy/img/ para a biblioteca de mídia,
 *    sob demanda (só as que cada página realmente usa);
 *  - cria as páginas e posts do site antigo em lotes, sem sobrescrever
 *    conteúdo já editado no painel (padrão _feicoop_seed_content do tema);
 *  - troca os marcadores %%IMG:arquivo%% e %%PAGE:slug%% pelos destinos reais;
 *  - cria a página-índice "Acervo" para dar acesso ao material sem alterar
 *    o menu de navegação existente.
 *
 * @package feicoop
 */

if (!defined('ABSPATH')) {
    exit;
}

const FEICOOP_LEGACY_MANIFEST = 'inc/legacy/manifest.php';
const FEICOOP_LEGACY_CONTENT_DIR = 'inc/legacy/content/';
const FEICOOP_LEGACY_IMG_DIR = 'assets/legacy/img/';
const FEICOOP_LEGACY_FILE_DIR = 'assets/legacy/files/';
const FEICOOP_LEGACY_INDEX_SLUG = 'acervo';
const FEICOOP_LEGACY_BATCH = 15;

/**
 * Verifica se os ativos pesados do acervo (imagens e anexos) estão presentes.
 *
 * Esses arquivos somam ~73 MB e são distribuídos num pacote separado
 * (esperanca-wp-theme-legacy.tar.gz) para não inflar o tamanho do tema. Eles só
 * são necessários na primeira importação; depois ficam na biblioteca de mídia.
 *
 * @return bool
 */
function feicoop_legacy_assets_present(): bool {
    $base = get_template_directory();
    $img = $base . '/' . FEICOOP_LEGACY_IMG_DIR;
    $files = $base . '/' . FEICOOP_LEGACY_FILE_DIR;

    return is_dir($img) && (bool) glob($img . '*')
        && is_dir($files) && (bool) glob($files . '*');
}

/**
 * Carrega o manifesto do conteúdo legado.
 *
 * @return array<int, array<string, string>>
 */
function feicoop_legacy_manifest(): array {
    static $itens = null;

    if ($itens !== null) {
        return $itens;
    }

    $caminho = get_template_directory() . '/' . FEICOOP_LEGACY_MANIFEST;

    if (!is_readable($caminho)) {
        $itens = [];
        return $itens;
    }

    $dados = include $caminho;
    $itens = is_array($dados) ? $dados : [];

    return $itens;
}

/**
 * Importa uma imagem do tema para a biblioteca de mídia, reaproveitando
 * o anexo se ele já tiver sido importado antes.
 *
 * @param string $arquivo Nome do arquivo dentro de assets/legacy/img/.
 * @return int ID do anexo, ou 0 em caso de falha.
 */
function feicoop_legacy_import_image(string $arquivo): int {
    $arquivo = basename($arquivo);

    if ($arquivo === '') {
        return 0;
    }

    $existentes = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_feicoop_legacy_img',
        'meta_value' => $arquivo,
    ]);

    if (!empty($existentes)) {
        return (int) $existentes[0];
    }

    $origem = get_template_directory() . '/' . FEICOOP_LEGACY_IMG_DIR . $arquivo;

    if (!is_readable($origem)) {
        return 0;
    }

    $uploads = wp_upload_dir();

    if (!empty($uploads['error'])) {
        return 0;
    }

    $destino = trailingslashit($uploads['path']) . wp_unique_filename($uploads['path'], $arquivo);

    if (!copy($origem, $destino)) {
        return 0;
    }

    $tipo = wp_check_filetype($destino);

    $anexo_id = wp_insert_attachment([
        'post_mime_type' => $tipo['type'] ?: 'image/jpeg',
        'post_title' => pathinfo($arquivo, PATHINFO_FILENAME),
        'post_content' => '',
        'post_status' => 'inherit',
    ], $destino, 0, true);

    if (is_wp_error($anexo_id) || !$anexo_id) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $meta = wp_generate_attachment_metadata((int) $anexo_id, $destino);

    if (!is_wp_error($meta) && is_array($meta)) {
        wp_update_attachment_metadata((int) $anexo_id, $meta);
    }

    update_post_meta((int) $anexo_id, '_feicoop_legacy_img', $arquivo);

    return (int) $anexo_id;
}

/**
 * Importa uma imagem sob demanda e devolve a URL, com cache em memória.
 *
 * Importar só o que a página usa evita varrer as 782 imagens a cada
 * requisição — o que estouraria o tempo limite de execução.
 *
 * @param string $arquivo Nome do arquivo em assets/legacy/img/.
 * @return string URL da imagem, ou string vazia.
 */
function feicoop_legacy_image_url(string $arquivo): string {
    static $cache = [];

    $arquivo = basename($arquivo);

    if ($arquivo === '') {
        return '';
    }

    if (array_key_exists($arquivo, $cache)) {
        return $cache[$arquivo];
    }

    $id = feicoop_legacy_import_image($arquivo);
    $cache[$arquivo] = $id ? (string) wp_get_attachment_url($id) : '';

    return $cache[$arquivo];
}

/**
 * Resolve o destino de um link interno do conteúdo antigo.
 *
 * Os arquivos mensais do blog ficaram fora do escopo da importação (são
 * listagens repetidas dos mesmos posts), então seus links passam a apontar
 * para a listagem do Acervo, em vez de ficarem mortos.
 *
 * @param string $slug Slug de destino.
 * @return string URL de destino.
 */
function feicoop_legacy_resolve_link(string $slug): string {
    $pagina = get_page_by_path($slug, OBJECT, 'page');

    if ($pagina instanceof WP_Post) {
        return (string) get_permalink($pagina);
    }

    if (str_starts_with($slug, 'arquivo-')) {
        $termo = get_term_by('slug', 'acervo', 'category');

        if ($termo instanceof WP_Term) {
            $link = get_term_link($termo);

            if (!is_wp_error($link) && $link) {
                return (string) $link;
            }
        }

        $blog = get_page_by_path('blog-co9e', OBJECT, 'page');

        return $blog instanceof WP_Post ? (string) get_permalink($blog) : '';
    }

    if ($slug === 'index' || $slug === 'inicial') {
        return home_url('/');
    }

    return '';
}

/**
 * Importa um anexo (PDF, DOC, planilha...) para a biblioteca de mídia.
 *
 * @param string $arquivo Nome do arquivo em assets/legacy/files/.
 * @return int ID do anexo, ou 0.
 */
function feicoop_legacy_import_file(string $arquivo): int {
    $arquivo = basename($arquivo);

    if ($arquivo === '') {
        return 0;
    }

    $existentes = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_feicoop_legacy_file',
        'meta_value' => $arquivo,
    ]);

    if (!empty($existentes)) {
        return (int) $existentes[0];
    }

    $origem = get_template_directory() . '/' . FEICOOP_LEGACY_FILE_DIR . $arquivo;

    if (!is_readable($origem)) {
        return 0;
    }

    $uploads = wp_upload_dir();

    if (!empty($uploads['error'])) {
        return 0;
    }

    $destino = trailingslashit($uploads['path']) . wp_unique_filename($uploads['path'], $arquivo);

    if (!copy($origem, $destino)) {
        return 0;
    }

    $tipo = wp_check_filetype($destino);

    $anexo_id = wp_insert_attachment([
        'post_mime_type' => $tipo['type'] ?: 'application/octet-stream',
        'post_title' => pathinfo($arquivo, PATHINFO_FILENAME),
        'post_content' => '',
        'post_status' => 'inherit',
    ], $destino, 0, true);

    if (is_wp_error($anexo_id) || !$anexo_id) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $meta = wp_generate_attachment_metadata((int) $anexo_id, $destino);

    if (!is_wp_error($meta) && is_array($meta)) {
        wp_update_attachment_metadata((int) $anexo_id, $meta);
    }

    update_post_meta((int) $anexo_id, '_feicoop_legacy_file', $arquivo);

    return (int) $anexo_id;
}

/**
 * URL de um anexo, importando sob demanda e cacheando em memória.
 *
 * @param string $arquivo Nome do arquivo em assets/legacy/files/.
 * @return string
 */
function feicoop_legacy_file_url(string $arquivo): string {
    static $cache = [];

    $arquivo = basename($arquivo);

    if ($arquivo === '') {
        return '';
    }

    if (array_key_exists($arquivo, $cache)) {
        return $cache[$arquivo];
    }

    $id = feicoop_legacy_import_file($arquivo);
    $cache[$arquivo] = $id ? (string) wp_get_attachment_url($id) : '';

    return $cache[$arquivo];
}

/**
 * Substitui os marcadores do conteúdo convertido por valores reais.
 *
 * @param string $html HTML com %%IMG:%%, %%FILE:%% e %%PAGE:%%.
 * @return string
 */
function feicoop_legacy_render_content(string $html): string {
    $html = preg_replace_callback('/%%IMG:([A-Za-z0-9._-]+)%%/', static function (array $m): string {
        return feicoop_legacy_image_url($m[1]);
    }, $html);

    $html = preg_replace_callback('/%%FILE:([A-Za-z0-9._-]+)%%/', static function (array $m): string {
        return feicoop_legacy_file_url($m[1]);
    }, $html);

    $html = preg_replace_callback('/%%PAGE:([A-Za-z0-9._-]+)%%/', static function (array $m): string {
        return feicoop_legacy_resolve_link($m[1]);
    }, $html);

    // Sem destino conhecido: remove o link e mantém o texto, em vez de
    // deixar um href vazio que não leva a lugar nenhum.
    $html = preg_replace('/<a\b[^>]*href=""[^>]*>(.*?)<\/a>/is', '$1', (string) $html);

    // Remove imagens cujo arquivo não pôde ser importado.
    $html = preg_replace('/<img[^>]*src=""[^>]*>/i', '', (string) $html);

    return (string) $html;
}

/**
 * Lê o HTML convertido de um item do manifesto.
 *
 * @param string $arquivo Nome do arquivo em inc/legacy/content/.
 * @return string
 */
function feicoop_legacy_item_html(string $arquivo): string {
    $arquivo = basename($arquivo);
    $caminho = get_template_directory() . '/' . FEICOOP_LEGACY_CONTENT_DIR . $arquivo;

    if (!is_readable($caminho)) {
        return '';
    }

    return (string) file_get_contents($caminho);
}

/**
 * Cria (ou atualiza, se nunca editado) uma página do acervo legado.
 *
 * @param array<string, string> $item Item do manifesto.
 * @return int ID da página, ou 0.
 */
function feicoop_legacy_ensure_page(array $item): int {
    $slug = isset($item['slug']) ? (string) $item['slug'] : '';
    $titulo = isset($item['titulo']) ? (string) $item['titulo'] : '';
    $arquivo = isset($item['arquivo']) ? (string) $item['arquivo'] : '';

    if ($slug === '' || $titulo === '' || $arquivo === '') {
        return 0;
    }

    $conteudo = feicoop_legacy_render_content(feicoop_legacy_item_html($arquivo));

    if (trim(wp_strip_all_tags($conteudo)) === '') {
        return 0;
    }

    $pagina = get_page_by_path($slug, OBJECT, 'page');
    $pagina_id = $pagina instanceof WP_Post ? (int) $pagina->ID : 0;

    if ($pagina_id <= 0) {
        $resultado = wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $titulo,
            'post_name' => $slug,
            'post_content' => $conteudo,
            'post_author' => get_current_user_id() ?: 1,
        ], true);

        if (is_wp_error($resultado)) {
            return 0;
        }

        $pagina_id = (int) $resultado;
        update_post_meta($pagina_id, '_feicoop_seed_content', $conteudo);
        update_post_meta($pagina_id, '_feicoop_legacy', '1');

        return $pagina_id;
    }

    // Nunca sobrescreve páginas editadas no painel.
    $atual = (string) $pagina->post_content;
    $semeado = (string) get_post_meta($pagina_id, '_feicoop_seed_content', true);

    if ($semeado !== '' && $atual === $semeado && $atual !== $conteudo) {
        wp_update_post([
            'ID' => $pagina_id,
            'post_content' => $conteudo,
        ]);
        update_post_meta($pagina_id, '_feicoop_seed_content', $conteudo);
    }

    return $pagina_id;
}

/**
 * Cria (ou atualiza, se nunca editado) um post do blog antigo.
 *
 * @param array<string, string> $item Item do manifesto.
 * @return int ID do post, ou 0.
 */
function feicoop_legacy_ensure_post(array $item): int {
    $slug = isset($item['slug']) ? (string) $item['slug'] : '';
    $titulo = isset($item['titulo']) ? (string) $item['titulo'] : '';
    $arquivo = isset($item['arquivo']) ? (string) $item['arquivo'] : '';

    if ($slug === '' || $titulo === '' || $arquivo === '') {
        return 0;
    }

    $conteudo = feicoop_legacy_render_content(feicoop_legacy_item_html($arquivo));

    if (trim(wp_strip_all_tags($conteudo)) === '') {
        return 0;
    }

    $existente = get_page_by_path($slug, OBJECT, 'post');
    $post_id = $existente instanceof WP_Post ? (int) $existente->ID : 0;

    $dados = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_title' => $titulo,
        'post_name' => $slug,
        'post_content' => $conteudo,
        'post_author' => get_current_user_id() ?: 1,
    ];

    if (!empty($item['data']) && strtotime((string) $item['data'])) {
        $dados['post_date'] = gmdate('Y-m-d H:i:s', (int) strtotime((string) $item['data'] . ' 12:00:00'));
    }

    if ($post_id <= 0) {
        $resultado = wp_insert_post($dados, true);

        if (is_wp_error($resultado)) {
            return 0;
        }

        $post_id = (int) $resultado;
        update_post_meta($post_id, '_feicoop_seed_content', $conteudo);
        update_post_meta($post_id, '_feicoop_legacy', '1');
    } else {
        $atual = (string) $existente->post_content;
        $semeado = (string) get_post_meta($post_id, '_feicoop_seed_content', true);

        // Só mexe em posts que nunca foram editados no painel.
        if ($semeado !== '' && $atual === $semeado) {
            $precisa = ($atual !== $conteudo);

            // Corrige a data de posts importados antes do ajuste do extrator,
            // que os deixou com a data da importação.
            if (!empty($dados['post_date'])
                && substr((string) $existente->post_date, 0, 10) !== substr((string) $dados['post_date'], 0, 10)) {
                $precisa = true;
            }

            if ($precisa) {
                $dados['ID'] = $post_id;
                wp_update_post($dados);
                update_post_meta($post_id, '_feicoop_seed_content', $conteudo);
            }
        }
    }

    // Só o material com data histórica comprovada vai para o Acervo; posts
    // sem data na origem seguem como notícia corrente.
    if ($post_id > 0 && ($item['historico'] ?? '') === '1') {
        $termo = get_term_by('slug', 'acervo', 'category');

        if (!$termo instanceof WP_Term) {
            $novo = wp_insert_term(__('Acervo', 'feicoop'), 'category', ['slug' => 'acervo']);

            if (!is_wp_error($novo)) {
                $termo = get_term((int) $novo['term_id'], 'category');
            }
        }

        if ($termo instanceof WP_Term) {
            wp_set_post_terms($post_id, [(int) $termo->term_id], 'category', true);
        }
    } elseif ($post_id > 0) {
        // Post sem data histórica comprovada: garante que não fique preso
        // no acervo (a atribuição de categoria é aditiva e não se desfaz só).
        $acervo = feicoop_legacy_acervo_term_id();

        if ($acervo > 0) {
            wp_remove_object_terms($post_id, [$acervo], 'category');
        }
    }

    return $post_id;
}

/**
 * Semeia o conteúdo legado em lotes.
 *
 * Criar 234 itens e importar 782 imagens de uma vez estouraria o tempo
 * limite de execução. A cada acesso ao painel processamos um lote e
 * guardamos o progresso, até concluir.
 *
 * @param int $limite Quantidade de itens por lote (0 = tudo).
 * @return array{offset:int,total:int,paginas:int,posts:int,completo:bool}
 */
function feicoop_seed_legacy_content(int $limite = FEICOOP_LEGACY_BATCH): array {
    $itens = feicoop_legacy_manifest();
    $total = count($itens);

    $progresso = get_option('feicoop_legacy_progress');
    $progresso = is_array($progresso) ? $progresso : [];

    // Numa execução completa (limite 0) recomeçamos do início: o seed é
    // idempotente e precisa reprocessar tudo. O offset só serve para
    // retomar a importação em lotes quando ela ficou pela metade.
    $offset = $limite > 0 ? (int) ($progresso['offset'] ?? 0) : 0;
    $paginas = (int) ($progresso['paginas'] ?? 0);
    $posts = (int) ($progresso['posts'] ?? 0);

    if ($limite <= 0) {
        $paginas = 0;
        $posts = 0;
    }

    if ($offset > $total) {
        $offset = $total;
    }

    $fim = $limite > 0 ? min($total, $offset + $limite) : $total;

    for ($i = $offset; $i < $fim; $i++) {
        $item = $itens[$i] ?? null;

        if (!is_array($item)) {
            continue;
        }

        if (($item['tipo'] ?? 'page') === 'post') {
            if (feicoop_legacy_ensure_post($item) > 0) {
                $posts++;
            }
            continue;
        }

        if (feicoop_legacy_ensure_page($item) > 0) {
            $paginas++;
        }
    }

    $completo = $fim >= $total;

    update_option('feicoop_legacy_progress', [
        'offset' => $fim,
        'paginas' => $paginas,
        'posts' => $posts,
        'total' => $total,
    ]);

    if ($completo) {
        feicoop_legacy_ensure_index($paginas, $posts);

        // Imagem destacada dos posts do acervo: sem isso os cards do tema
        // ficariam sem foto, já que o site antigo trazia as imagens no corpo.
        if (!get_option('feicoop_legacy_thumbs')) {
            feicoop_legacy_backfill_thumbnails();
        }

        update_option('feicoop_legacy_seeded', [
            'versao' => (string) wp_get_theme()->get('Version'),
            'paginas' => $paginas,
            'posts' => $posts,
            'quando' => time(),
        ]);
    }

    return [
        'offset' => $fim,
        'total' => $total,
        'paginas' => $paginas,
        'posts' => $posts,
        'completo' => $completo,
    ];
}

/**
 * Cria a página-índice do acervo, listando o que foi importado.
 *
 * Serve para dar acesso ao material antigo sem alterar o menu existente.
 *
 * @param int $paginas Quantidade de páginas importadas.
 * @param int $posts Quantidade de posts importados.
 * @return int
 */
function feicoop_legacy_ensure_index(int $paginas, int $posts): int {
    $grupos = [];

    // O manifesto já vem ordenado por grupo, então a ordem de exibição é a
    // ordem de primeira aparição de cada grupo.
    foreach (feicoop_legacy_manifest() as $item) {
        if (!is_array($item) || ($item['tipo'] ?? 'page') !== 'page') {
            continue;
        }

        $pagina = get_page_by_path((string) ($item['slug'] ?? ''), OBJECT, 'page');

        if (!$pagina instanceof WP_Post) {
            continue;
        }

        $grupo = (string) ($item['grupo'] ?? '');
        $grupo = $grupo !== '' ? $grupo : __('Outros', 'feicoop');
        $grupos[$grupo][] = $pagina;
    }

    $html = '<p>' . esc_html(sprintf(
        /* translators: 1: número de páginas, 2: número de publicações */
        __('Material publicado originalmente no site antigo do Projeto Esperança/Cooesperança: %1$d páginas e %2$d publicações, preservadas aqui como acervo histórico.', 'feicoop'),
        $paginas,
        $posts
    )) . '</p>';

    foreach ($grupos as $nome => $lista) {
        usort($lista, static fn($a, $b) => strcoll((string) $a->post_title, (string) $b->post_title));
        $html .= '<h2>' . esc_html((string) $nome)
            . ' <span class="acervo__count">(' . count($lista) . ')</span></h2><ul>';
        foreach ($lista as $p) {
            $html .= '<li><a href="' . esc_url((string) get_permalink($p)) . '">'
                . esc_html((string) get_the_title($p)) . '</a></li>';
        }
        $html .= '</ul>';
    }

    // Publicações históricas agrupadas por ano, em vez de 164 links soltos.
    if ($posts > 0) {
        $por_ano = [];

        $ids = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_feicoop_legacy',
            'meta_value' => '1',
            'no_found_rows' => true,
        ]);

        foreach ($ids as $pid) {
            $ano = substr((string) get_post_field('post_date', $pid), 0, 4);

            if ($ano !== '') {
                $por_ano[$ano] = ($por_ano[$ano] ?? 0) + 1;
            }
        }

        krsort($por_ano);

        if ($por_ano) {
            $html .= '<h2>' . esc_html__('Publicações históricas por ano', 'feicoop')
                . ' <span class="acervo__count">(' . array_sum($por_ano) . ')</span></h2><ul>';

            foreach ($por_ano as $ano => $qtd) {
                $html .= '<li><a href="' . esc_url((string) get_year_link((int) $ano)) . '">'
                    . esc_html((string) $ano) . '</a>'
                    . ' <span class="acervo__count">(' . (int) $qtd . ')</span></li>';
            }

            $html .= '</ul>';
        }

        $link = get_term_link('acervo', 'category');

        if (!is_wp_error($link)) {
            $html .= '<p><a href="' . esc_url((string) $link) . '">'
                . esc_html__('Ver todas as publicações antigas em sequência', 'feicoop') . '</a></p>';
        }
    }

    $existente = get_page_by_path(FEICOOP_LEGACY_INDEX_SLUG, OBJECT, 'page');

    if ($existente instanceof WP_Post) {
        $semeado = (string) get_post_meta((int) $existente->ID, '_feicoop_seed_content', true);
        if ($semeado === '' || (string) $existente->post_content === $semeado) {
            wp_update_post(['ID' => (int) $existente->ID, 'post_content' => $html]);
            update_post_meta((int) $existente->ID, '_feicoop_seed_content', $html);
        }
        return (int) $existente->ID;
    }

    $id = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => __('Acervo', 'feicoop'),
        'post_name' => FEICOOP_LEGACY_INDEX_SLUG,
        'post_content' => $html,
        'post_author' => get_current_user_id() ?: 1,
    ], true);

    if (is_wp_error($id)) {
        return 0;
    }

    update_post_meta((int) $id, '_feicoop_seed_content', $html);
    update_post_meta((int) $id, '_feicoop_legacy', '1');

    return (int) $id;
}

/**
 * ID do termo da categoria "Acervo", ou 0 se ainda não existir.
 *
 * @return int
 */
function feicoop_legacy_acervo_term_id(): int {
    static $id = null;

    if ($id !== null) {
        return $id;
    }

    $termo = get_term_by('slug', 'acervo', 'category');
    $id = $termo instanceof WP_Term ? (int) $termo->term_id : 0;

    return $id;
}

/**
 * Mantém o acervo histórico fora do feed de notícias.
 *
 * Foram importadas 160 publicações de 2013–2018. Misturadas com as notícias
 * recentes, elas afogam o feed: da segunda página em diante só há material
 * de arquivo. O acervo continua acessível pela própria listagem da categoria.
 *
 * @param WP_Query $query Consulta principal.
 */
function feicoop_exclude_acervo_from_news(WP_Query $query): void {
    if (is_admin() || !$query->is_main_query() || !$query->is_home()) {
        return;
    }

    $acervo = feicoop_legacy_acervo_term_id();

    if ($acervo <= 0) {
        return;
    }

    /**
     * Permite reaproximar o acervo do feed, se desejado.
     *
     * @param bool $excluir Se deve excluir a categoria Acervo.
     */
    if (!apply_filters('feicoop_exclude_acervo_from_news', true)) {
        return;
    }

    $query->set('cat', '-' . $acervo);
}
add_action('pre_get_posts', 'feicoop_exclude_acervo_from_news');

/**
 * Descobre o ID do anexo da primeira imagem dentro do conteúdo de um post.
 *
 * Os posts do site antigo vieram com as imagens no corpo do texto, mas sem
 * imagem destacada — o que deixaria os cards do tema sem foto.
 *
 * @param int $post_id ID do post.
 * @return int ID do anexo, ou 0.
 */
function feicoop_legacy_first_image_id(int $post_id): int {
    $conteudo = (string) get_post_field('post_content', $post_id);

    if ($conteudo === '' || !preg_match('/<img\b[^>]*\bsrc\s*=\s*["\']?([^"\'\s>]+)/i', $conteudo, $m)) {
        return 0;
    }

    $url = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');

    if ($url === '' || str_starts_with($url, 'data:')) {
        return 0;
    }

    $id = attachment_url_to_postid($url);

    return (int) $id;
}

/**
 * Define a imagem destacada dos itens do acervo que ainda não têm uma.
 *
 * @param int $limite Quantidade de posts a processar (0 = todos).
 * @return int Quantos posts receberam imagem.
 */
function feicoop_legacy_backfill_thumbnails(int $limite = 0): int {
    $posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'any',
        'posts_per_page' => $limite > 0 ? $limite : -1,
        'fields' => 'ids',
        'meta_key' => '_feicoop_legacy',
        'meta_value' => '1',
        'no_found_rows' => true,
    ]);

    $definidas = 0;

    foreach ($posts as $post_id) {
        if (get_post_thumbnail_id($post_id)) {
            continue;
        }

        $imagem = feicoop_legacy_first_image_id((int) $post_id);

        if ($imagem > 0 && set_post_thumbnail((int) $post_id, $imagem)) {
            $definidas++;
        }
    }

    update_option('feicoop_legacy_thumbs', [
        'definidas' => $definidas,
        'quando' => time(),
    ]);

    return $definidas;
}

/**
 * Semeia o legado junto com o restante do tema, na ativação.
 *
 * Se os ativos pesados ainda não estiverem no tema (pacote separado), adia:
 * o admin_init retoma a importação assim que eles forem colocados.
 */
function feicoop_legacy_seed_on_switch(): void {
    if (!feicoop_legacy_assets_present()) {
        return;
    }

    feicoop_seed_legacy_content();
}
add_action('after_switch_theme', 'feicoop_legacy_seed_on_switch', 20);

/**
 * Continua o seed em segundo plano, um lote por acesso ao painel.
 *
 * Assim a importação do acervo termina mesmo que a ativação seja
 * interrompida por tempo limite.
 */
function feicoop_maybe_seed_legacy_content(): void {
    if (!is_admin() || !current_user_can('edit_posts')) {
        return;
    }

    $versao = (string) wp_get_theme()->get('Version');
    $estado = get_option('feicoop_legacy_seeded');

    // Nunca importou ainda e os ativos pesados não estão no tema: adia até
    // que o pacote separado seja colocado via cPanel/FTP. (O aviso fica em
    // feicoop_legacy_assets_missing_notice.)
    if (!is_array($estado) && !feicoop_legacy_assets_present()) {
        return;
    }

    if (is_array($estado) && ($estado['versao'] ?? '') === $versao) {
        // O acervo já está importado; garante só o backfill das imagens.
        if (!get_option('feicoop_legacy_thumbs')) {
            feicoop_legacy_backfill_thumbnails();
        }
        return;
    }

    feicoop_seed_legacy_content();
}
add_action('admin_init', 'feicoop_maybe_seed_legacy_content', 30);

/**
 * Aviso no painel enquanto o acervo ainda está sendo importado.
 */
function feicoop_legacy_admin_notice(): void {
    if (!is_admin() || !current_user_can('edit_posts')) {
        return;
    }

    $progresso = get_option('feicoop_legacy_progress');

    if (!is_array($progresso)) {
        return;
    }

    $total = (int) ($progresso['total'] ?? 0);
    $offset = (int) ($progresso['offset'] ?? 0);

    if ($total <= 0 || $offset >= $total) {
        return;
    }

    printf(
        '<div class="notice notice-info"><p>%s</p></div>',
        esc_html(sprintf(
            /* translators: 1: itens já importados, 2: total de itens */
            __('Acervo do site antigo: %1$d de %2$d itens importados. Recarregue esta página para continuar.', 'feicoop'),
            $offset,
            $total
        ))
    );
}
add_action('admin_notices', 'feicoop_legacy_admin_notice');

/**
 * Aviso no painel enquanto o pacote de acervo não foi colocado no tema.
 *
 * Os PDFs/imagens antigos (~73 MB) vêm num ZIP separado para não inflar o
 * tema. Enquanto ele não estiver em assets/legacy/, a importação fica adiada.
 */
function feicoop_legacy_assets_missing_notice(): void {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    // Já importado ou ativos presentes: não há o que avisar.
    if (is_array(get_option('feicoop_legacy_seeded')) || feicoop_legacy_assets_present()) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p>%s</p></div>',
        esc_html__('Acervo do site antigo: para importar as páginas, imagens e anexos, envie o arquivo esperanca-wp-theme-legacy.tar.gz para a pasta do tema (em wp-content/themes/) e extraia dentro dela. Depois recarregue esta página.', 'feicoop')
    );
}
add_action('admin_notices', 'feicoop_legacy_assets_missing_notice');
