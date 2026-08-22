<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$home_hero = feicoop_home_hero_fields();
$home_event = feicoop_home_event_fields();
$home_registration = feicoop_home_registration_fields();
$home_contact = feicoop_home_contact_fields();
$home_intro = feicoop_home_intro_fields();
$home_quicklinks = feicoop_home_quicklinks();
$home_projects = feicoop_home_project_cards();
$home_highlight = feicoop_home_highlight_fields();
$home_news = feicoop_home_news_fields();
$home_hero_title_style = ' style="--hero-title-size: ' . esc_attr(feicoop_home_hero_title_size()) . ';"';
?>
<main id="main" class="home-template">
    <section class="hero hero--noimage">
        <div class="wrapper hero__grid">
            <header class="hero__content hero__content--centered">
                <?php if ($home_hero['eyebrow'] !== '') : ?>
                    <p class="hero__eyebrow"><?php echo esc_html($home_hero['eyebrow']); ?></p>
                <?php endif; ?>
                <?php if ($home_hero['title'] !== '') : ?>
                    <h1<?php echo $home_hero_title_style; ?>><?php echo esc_html($home_hero['title']); ?></h1>
                <?php endif; ?>
                <?php if ($home_hero['text'] !== '') : ?>
                    <p><?php echo esc_html($home_hero['text']); ?></p>
                <?php endif; ?>
                <p class="hero__actions">
                    <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Ver notícias</a>
                    <a href="<?php echo esc_url(feicoop_programacao_archive_url()); ?>" class="btn btn--ghost">Ver programação</a>
                    <a href="<?php echo esc_url(feicoop_programacao_pdf_url()); ?>" class="btn btn--ghost" download>Baixar programação em PDF</a>
                    <?php if ($home_registration['button_url'] !== '' && $home_registration['button_label'] !== '') : ?>
                        <a href="<?php echo esc_url($home_registration['button_url']); ?>" class="btn btn--ghost"><?php echo esc_html($home_registration['button_label']); ?></a>
                    <?php endif; ?>
                </p>
                <dl class="hero__facts">
                    <div class="hero__fact">
                        <dt><?php echo esc_html($home_event['when_label']); ?></dt>
                        <dd><?php echo esc_html($home_event['when_value']); ?></dd>
                    </div>
                    <div class="hero__fact">
                        <dt><?php echo esc_html($home_event['where_label']); ?></dt>
                        <dd><?php echo esc_html($home_event['where_value']); ?></dd>
                    </div>
                    <div class="hero__fact">
                        <dt><?php echo esc_html($home_event['focus_label']); ?></dt>
                        <dd><?php echo esc_html($home_event['focus_value']); ?></dd>
                    </div>
                </dl>
            </header>
            <aside class="hero__panel">
                <?php if ($home_hero['panel_kicker'] !== '') : ?>
                    <p class="hero__panel-kicker"><?php echo esc_html($home_hero['panel_kicker']); ?></p>
                <?php endif; ?>
                <?php if ($home_hero['panel_title'] !== '') : ?>
                    <h2><?php echo esc_html($home_hero['panel_title']); ?></h2>
                <?php endif; ?>
                <?php if ($home_hero['panel_text'] !== '') : ?>
                    <p><?php echo esc_html($home_hero['panel_text']); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Acompanhar notícias</a>
            </aside>
        </div>
    </section>

    <section class="section" id="noticias">
        <div class="wrapper">
            <div class="news-summary">
                <div class="section__header">
                    <?php if ($home_news['kicker'] !== '') : ?>
                        <p class="section__kicker"><?php echo esc_html($home_news['kicker']); ?></p>
                    <?php endif; ?>
                    <?php if ($home_news['title'] !== '') : ?>
                        <h2><?php echo esc_html($home_news['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <div class="news-summary__actions">
                    <?php if ($home_news['text'] !== '') : ?>
                        <p class="section__text section__text--lead"><?php echo esc_html($home_news['text']); ?></p>
                    <?php endif; ?>
                    <a class="btn btn--ghost" href="<?php echo esc_url(feicoop_posts_page_url()); ?>">Ir para notícias</a>
                </div>
            </div>
            <div class="news-grid">
                <?php
                $latest = new WP_Query([
                    'post_type' => 'post',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                ]);

                if ($latest->have_posts()) {
                    while ($latest->have_posts()) {
                        $latest->the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('news-featured'); ?>>
                            <a class="news-featured__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php echo wp_kses_post(feicoop_post_feature_image_html(get_the_ID(), 'feicoop-card')); ?></a>
                            <div class="news-featured__content">
                                <div class="feed__meta">
                                    <time class="feed__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <span class="feed__author"><?php echo esc_html(get_the_author()); ?></span>
                                </div>
                                <h3 class="news-featured__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="news-featured__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
                                <a class="btn" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler notícia', 'feicoop'); ?></a>
                            </div>
                        </article>
                        <?php
                    }

                    wp_reset_postdata();
                } else {
                    get_template_part('template-parts/content', 'none');
                }
                ?>
            </div>
        </div>
    </section>

    <?php if (feicoop_home_registration_enabled()) : ?>
        <?php $registration_classes = 'homepage-registration' . (($home_registration['date_label'] === '' && $home_registration['date_value'] === '') ? ' homepage-registration--single' : ''); ?>
        <section class="section section--registration-callout">
            <div class="wrapper">
                <div class="<?php echo esc_attr($registration_classes); ?>">
                    <div class="homepage-registration__content">
                        <?php if ($home_registration['kicker'] !== '') : ?>
                            <p class="section__kicker"><?php echo esc_html($home_registration['kicker']); ?></p>
                        <?php endif; ?>
                        <?php if ($home_registration['title'] !== '') : ?>
                            <h2><?php echo esc_html($home_registration['title']); ?></h2>
                        <?php endif; ?>
                        <?php if ($home_registration['text'] !== '') : ?>
                            <p class="section__text section__text--lead"><?php echo esc_html($home_registration['text']); ?></p>
                        <?php endif; ?>
                        <?php if ($home_registration['button_url'] !== '' && $home_registration['button_label'] !== '') : ?>
                            <p class="homepage-registration__actions">
                                <a class="btn" href="<?php echo esc_url($home_registration['button_url']); ?>"><?php echo esc_html($home_registration['button_label']); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if ($home_registration['date_label'] !== '' || $home_registration['date_value'] !== '') : ?>
                        <aside class="homepage-registration__date" aria-label="<?php echo esc_attr($home_registration['date_label'] !== '' ? $home_registration['date_label'] : __('Data de abertura das inscrições', 'feicoop')); ?>">
                            <?php if ($home_registration['date_label'] !== '') : ?>
                                <span><?php echo esc_html($home_registration['date_label']); ?></span>
                            <?php endif; ?>
                            <?php if ($home_registration['date_value'] !== '') : ?>
                                <strong><?php echo esc_html($home_registration['date_value']); ?></strong>
                            <?php endif; ?>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($home_intro['kicker'] !== '' || $home_intro['title'] !== '' || $home_intro['text'] !== '') : ?>
        <section class="section section--intro">
            <div class="wrapper section__grid">
                <div>
                    <?php if ($home_intro['kicker'] !== '') : ?>
                        <p class="section__kicker"><?php echo esc_html($home_intro['kicker']); ?></p>
                    <?php endif; ?>
                    <?php if ($home_intro['title'] !== '') : ?>
                        <h2><?php echo esc_html($home_intro['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($home_intro['text'] !== '') : ?>
                    <div class="section__text section__text--lead"><?php echo nl2br(esc_html($home_intro['text'])); ?></div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="section section--quicklinks">
        <div class="wrapper quicklinks">
            <?php foreach ($home_quicklinks as $quicklink) : ?>
                <?php if ($quicklink['url'] === '' || $quicklink['label'] === '') : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <a class="quicklink" href="<?php echo esc_url($quicklink['url']); ?>">
                    <?php if ($quicklink['kicker'] !== '') : ?>
                        <span class="quicklink__kicker"><?php echo esc_html($quicklink['kicker']); ?></span>
                    <?php endif; ?>
                    <strong><?php echo esc_html($quicklink['label']); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section" id="projetos">
        <div class="wrapper">
            <div class="section__header">
                <p class="section__kicker"><?php esc_html_e('Projetos e redes', 'feicoop'); ?></p>
                <h2><?php esc_html_e('Núcleos que estruturam o portal', 'feicoop'); ?></h2>
            </div>
            <div class="projects-grid">
                <?php foreach ($home_projects as $project) : ?>
                    <?php if ($project['title'] === '') : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <article class="project-card">
                        <a href="<?php echo esc_url($project['url'] !== '' ? $project['url'] : home_url('/')); ?>">
                            <?php if ($project['image'] !== '') : ?>
                                <img src="<?php echo esc_url($project['image']); ?>" width="1600" height="1000" alt="<?php echo esc_attr($project['title']); ?>" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <div class="project-card__content">
                                <?php if ($project['eyebrow'] !== '') : ?>
                                    <p class="project-card__eyebrow"><?php echo esc_html($project['eyebrow']); ?></p>
                                <?php endif; ?>
                                <h3><?php echo esc_html($project['title']); ?></h3>
                                <?php if ($project['text'] !== '') : ?>
                                    <p><?php echo esc_html($project['text']); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($home_highlight['kicker'] !== '' || $home_highlight['title'] !== '' || $home_highlight['text'] !== '') : ?>
        <section class="section section--highlight">
            <div class="wrapper callout">
                <div>
                    <?php if ($home_highlight['kicker'] !== '') : ?>
                        <p class="section__kicker"><?php echo esc_html($home_highlight['kicker']); ?></p>
                    <?php endif; ?>
                    <?php if ($home_highlight['title'] !== '') : ?>
                        <h2><?php echo esc_html($home_highlight['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($home_highlight['text'] !== '') : ?>
                    <div class="section__text section__text--lead">
                        <p><?php echo esc_html($home_highlight['text']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>



    <section class="section">
        <div class="wrapper contact-block">
            <div>
                <p class="section__kicker">Contato</p>
                <h2>Informações principais do projeto</h2>
            </div>
            <div class="contact-block__grid">
                <div class="contact-item">
                    <strong><?php esc_html_e('Coordenação', 'feicoop'); ?></strong>
                    <p><?php echo esc_html($home_contact['coordinator']); ?></p>
                </div>
                <div class="contact-item">
                    <strong><?php esc_html_e('Telefones', 'feicoop'); ?></strong>
                    <p><?php echo esc_html($home_contact['phones']); ?></p>
                </div>
                <div class="contact-item">
                    <strong><?php esc_html_e('E-mail', 'feicoop'); ?></strong>
                    <?php if ($home_contact['email'] !== '') : ?>
                        <p><a href="mailto:<?php echo esc_attr($home_contact['email']); ?>"><?php echo esc_html($home_contact['email']); ?></a></p>
                    <?php endif; ?>
                </div>
                <div class="contact-item">
                    <strong><?php esc_html_e('Endereço', 'feicoop'); ?></strong>
                    <p><?php echo nl2br(esc_html($home_contact['address'])); ?></p>
                </div>
                <div class="contact-item">
                    <strong><?php esc_html_e('Redes sociais', 'feicoop'); ?></strong>
                    <p>
                        <?php if ($home_contact['facebook'] !== '') : ?><a href="<?php echo esc_url($home_contact['facebook']); ?>">Facebook</a><br><?php endif; ?>
                        <?php if ($home_contact['instagram'] !== '') : ?><a href="<?php echo esc_url($home_contact['instagram']); ?>">Instagram Feirão Colonial</a><br><?php endif; ?>
                        <?php if ($home_contact['instagram_rede'] !== '') : ?><a href="<?php echo esc_url($home_contact['instagram_rede']); ?>">Instagram Rede Esperança</a><?php endif; ?>
                    </p>
                </div>
                <div class="contact-item">
                    <strong><?php esc_html_e('YouTube', 'feicoop'); ?></strong>
                    <?php if ($home_contact['youtube'] !== '') : ?>
                        <p><a href="<?php echo esc_url($home_contact['youtube']); ?>"><?php esc_html_e('Canal oficial', 'feicoop'); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
