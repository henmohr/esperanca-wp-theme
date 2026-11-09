<?php
/**
 * Bloco "A trajetória da FEICOOP": conteúdo da página "A FEICOOP" (slug a-feicoop).
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
    <div class="feicoop-history">
        <?php echo apply_filters('the_content', $feicoop_history_content); ?>
    </div>
</section>
