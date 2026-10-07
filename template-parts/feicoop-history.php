<?php
/**
 * Seção "A trajetória da FEICOOP" e "Eixos" exibida na página /feicoop/.
 *
 * O conteúdo é editável no Personalizador (seção "FEICOOP — História").
 *
 * @package feicoop
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="section section--feicoop-history" id="trajetoria">
    <div class="wrapper feicoop-history">
        <?php echo wp_kses_post(feicoop_feicoop_history_html()); ?>
    </div>
</section>
