<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e('Search for:', 'feicoop'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr__('Search ...', 'feicoop'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
    </label>
    <button type="submit" class="btn"><?php esc_html_e('Search', 'feicoop'); ?></button>
</form>
