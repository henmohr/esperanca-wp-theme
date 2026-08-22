<?php
if (!defined('ABSPATH')) {
    exit;
}

$search_field_id = function_exists('wp_unique_id') ? wp_unique_id('search-form-field-') : 'search-form-field';
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($search_field_id); ?>">
        <span class="screen-reader-text"><?php esc_html_e('Buscar por:', 'feicoop'); ?></span>
        <input id="<?php echo esc_attr($search_field_id); ?>" type="search" class="search-field" placeholder="<?php echo esc_attr__('Pesquisar…', 'feicoop'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
    </label>
    <button type="submit" class="btn"><?php esc_html_e('Pesquisar', 'feicoop'); ?></button>
</form>
