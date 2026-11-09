<?php
/**
 * Bloco "Programação em PDF": botões e visualizador do PDF da programação.
 *
 * @package feicoop
 */

if (!defined('ABSPATH')) {
    exit;
}

$pdf_url = feicoop_programacao_pdf_url();
?>
<div class="programacao-pdf">
    <p class="hero__actions">
        <a class="btn" href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abrir programação em PDF', 'feicoop'); ?></a>
        <a class="btn btn--ghost" href="<?php echo esc_url($pdf_url); ?>" download><?php esc_html_e('Baixar programação em PDF', 'feicoop'); ?></a>
    </p>

    <section class="programacao-pdf-viewer" aria-label="<?php esc_attr_e('Visualização do PDF da programação', 'feicoop'); ?>">
        <object class="programacao-pdf-viewer__frame" data="<?php echo esc_url($pdf_url); ?>" type="application/pdf">
            <p>
                <?php esc_html_e('Seu navegador não conseguiu exibir o PDF embutido.', 'feicoop'); ?>
                <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abra o arquivo em uma nova aba', 'feicoop'); ?></a>
            </p>
        </object>
    </section>
</div>
