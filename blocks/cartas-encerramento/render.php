<?php
/**
 * Bloco "Cartas de encerramento": lista as cartas das edições da FEICOOP.
 *
 * @package feicoop
 */

if (!defined('ABSPATH')) {
    exit;
}

$feicoop_cartas = feicoop_feicoop_cartas();

if ($feicoop_cartas === []) {
    return;
}
?>
<section class="feicoop-cartas" aria-label="<?php esc_attr_e('Cartas de encerramento das edições da FEICOOP', 'feicoop'); ?>">
    <h2 class="feicoop-cartas__title"><?php esc_html_e('Cartas de encerramento das edições da FEICOOP', 'feicoop'); ?></h2>
    <p class="feicoop-cartas__desc"><?php esc_html_e('Documentos históricos, escritos a várias mãos ao final de cada edição da feira, com números, balanços e o lançamento da edição seguinte.', 'feicoop'); ?></p>
    <div class="publicacoes-years__grid">
        <?php foreach ($feicoop_cartas as $carta) : ?>
            <a class="publicacoes-year" href="<?php echo esc_url($carta['url']); ?>">
                <strong><?php echo esc_html((string) $carta['year']); ?></strong>
                <span><?php esc_html_e('Ler carta', 'feicoop'); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
