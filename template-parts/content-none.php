<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="content-none wrapper">
    <h2><?php esc_html_e('Nada encontrado', 'feicoop'); ?></h2>
    <p><?php esc_html_e('Não encontramos conteúdo para exibir neste momento.', 'feicoop'); ?></p>
    <?php get_search_form(); ?>
</section>
