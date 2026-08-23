<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$pdf_url = feicoop_programacao_pdf_url();
?>
<main id="main" class="programacao-archive programacao-archive--pdf">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php esc_html_e('FEICOOP', 'feicoop'); ?></h1>
                <p class="page__desc"><?php esc_html_e('Visualização da programação oficial da FEICOOP em PDF.', 'feicoop'); ?></p>
                <p class="hero__actions">
                    <a class="btn" href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abrir PDF', 'feicoop'); ?></a>
                    <a class="btn btn--ghost" href="<?php echo esc_url($pdf_url); ?>" download><?php esc_html_e('Baixar programação em PDF', 'feicoop'); ?></a>
                </p>
                <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
            </div>
        </header>
    </div>

    <div class="wrapper programacao-archive__body">
        <section class="programacao-pdf-viewer" aria-label="<?php esc_attr_e('Visualização do PDF da programação', 'feicoop'); ?>">
            <object class="programacao-pdf-viewer__frame" data="<?php echo esc_url($pdf_url); ?>" type="application/pdf">
                <p>
                    <?php esc_html_e('Seu navegador não conseguiu exibir o PDF embutido.', 'feicoop'); ?>
                    <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abra o arquivo em uma nova aba', 'feicoop'); ?></a>
                </p>
            </object>
        </section>

        <?php
        $feicoop_cartas = feicoop_feicoop_cartas();

        if ($feicoop_cartas !== []) :
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
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
