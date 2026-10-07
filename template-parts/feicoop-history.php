<?php
/**
 * Seção "A trajetória da FEICOOP" e "Eixos" exibida na página /feicoop/.
 *
 * O conteúdo vem da Página "A FEICOOP" (slug a-feicoop), editável no editor
 * de blocos do WordPress (Páginas → A FEICOOP).
 *
 * @package feicoop
 */

if (!defined('ABSPATH')) {
    exit;
}

$feicoop_history_page = get_page_by_path('a-feicoop', OBJECT, 'page');
$feicoop_history_content = $feicoop_history_page instanceof WP_Post
    ? (string) $feicoop_history_page->post_content
    : '';

if (trim(wp_strip_all_tags($feicoop_history_content)) === '') {
    $feicoop_history_content = feicoop_feicoop_history_default();
}
?>
<section class="section section--feicoop-history" id="trajetoria">
    <div class="wrapper feicoop-history">
        <?php echo apply_filters('the_content', $feicoop_history_content); ?>
    </div>
</section>
