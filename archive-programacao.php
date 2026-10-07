<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$pdf_url = feicoop_programacao_pdf_url();
$hero = feicoop_home_hero_fields();
$event = feicoop_home_event_fields();
$registration = feicoop_home_registration_fields();
$highlight = feicoop_home_highlight_fields();
?>
<main id="main" class="programacao-archive programacao-archive--pdf">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <?php if ($hero['eyebrow'] !== '') : ?>
                    <p class="hero__eyebrow"><?php echo esc_html($hero['eyebrow']); ?></p>
                <?php endif; ?>
                <h1><?php echo esc_html($hero['title'] !== '' ? $hero['title'] : __('FEICOOP', 'feicoop')); ?></h1>
                <?php if ($hero['text'] !== '') : ?>
                    <p class="feicoop-hero__subtitle"><?php echo esc_html($hero['text']); ?></p>
                <?php endif; ?>

                <dl class="hero__facts feicoop-facts">
                    <div class="feicoop-fact">
                        <dt><?php echo esc_html($event['when_label']); ?></dt>
                        <dd><?php echo esc_html($event['when_value']); ?></dd>
                    </div>
                    <div class="feicoop-fact">
                        <dt><?php echo esc_html($event['where_label']); ?></dt>
                        <dd><?php echo esc_html($event['where_value']); ?></dd>
                    </div>
                    <div class="feicoop-fact">
                        <dt><?php echo esc_html($event['focus_label']); ?></dt>
                        <dd><?php echo esc_html($event['focus_value']); ?></dd>
                    </div>
                </dl>

                <p class="hero__actions">
                    <a class="btn" href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abrir programação em PDF', 'feicoop'); ?></a>
                    <a class="btn btn--ghost" href="<?php echo esc_url($pdf_url); ?>" download><?php esc_html_e('Baixar programação em PDF', 'feicoop'); ?></a>
                </p>
                <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
            </div>
        </header>
    </div>

    <?php get_template_part('template-parts/feicoop', 'history'); ?>

    <?php if (feicoop_home_registration_enabled()) : ?>
        <?php $registration_classes = 'homepage-registration' . (($registration['date_label'] === '' && $registration['date_value'] === '') ? ' homepage-registration--single' : ''); ?>
        <section class="section section--registration-callout">
            <div class="wrapper">
                <div class="<?php echo esc_attr($registration_classes); ?>">
                    <div class="homepage-registration__content">
                        <?php if ($registration['kicker'] !== '') : ?>
                            <p class="section__kicker"><?php echo esc_html($registration['kicker']); ?></p>
                        <?php endif; ?>
                        <?php if ($registration['title'] !== '') : ?>
                            <h2><?php echo esc_html($registration['title']); ?></h2>
                        <?php endif; ?>
                        <?php if ($registration['text'] !== '') : ?>
                            <p class="section__text section__text--lead"><?php echo esc_html($registration['text']); ?></p>
                        <?php endif; ?>
                        <?php if ($registration['button_url'] !== '' && $registration['button_label'] !== '') : ?>
                            <p class="homepage-registration__actions">
                                <a class="btn" href="<?php echo esc_url($registration['button_url']); ?>"><?php echo esc_html($registration['button_label']); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if ($registration['date_label'] !== '' || $registration['date_value'] !== '') : ?>
                        <aside class="homepage-registration__date" aria-label="<?php echo esc_attr($registration['date_label'] !== '' ? $registration['date_label'] : __('Data de abertura das inscrições', 'feicoop')); ?>">
                            <?php if ($registration['date_label'] !== '') : ?>
                                <span><?php echo esc_html($registration['date_label']); ?></span>
                            <?php endif; ?>
                            <?php if ($registration['date_value'] !== '') : ?>
                                <strong><?php echo esc_html($registration['date_value']); ?></strong>
                            <?php endif; ?>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($highlight['kicker'] !== '' || $highlight['title'] !== '' || $highlight['text'] !== '') : ?>
        <section class="section section--highlight">
            <div class="wrapper callout">
                <div>
                    <?php if ($highlight['kicker'] !== '') : ?>
                        <p class="section__kicker"><?php echo esc_html($highlight['kicker']); ?></p>
                    <?php endif; ?>
                    <?php if ($highlight['title'] !== '') : ?>
                        <h2><?php echo esc_html($highlight['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($highlight['text'] !== '') : ?>
                    <div class="section__text section__text--lead">
                        <p><?php echo esc_html($highlight['text']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

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
