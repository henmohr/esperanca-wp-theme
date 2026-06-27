<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$home_hero = feicoop_home_hero_fields();
$home_event = feicoop_home_event_fields();
$home_registration = feicoop_home_registration_fields();
?>
<main class="home-template">
    <section class="hero hero--noimage">
        <div class="wrapper hero__grid">
            <header class="hero__content hero__content--centered">
                <?php if ($home_hero['eyebrow'] !== '') : ?>
                    <p class="hero__eyebrow"><?php echo esc_html($home_hero['eyebrow']); ?></p>
                <?php endif; ?>
                <?php if ($home_hero['title'] !== '') : ?>
                    <h1><?php echo esc_html($home_hero['title']); ?></h1>
                <?php endif; ?>
                <?php if ($home_hero['text'] !== '') : ?>
                    <p><?php echo esc_html($home_hero['text']); ?></p>
                <?php endif; ?>
                <p class="hero__actions">
                    <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Ver notícias</a>
                    <a href="<?php echo esc_url(feicoop_programacao_archive_url()); ?>" class="btn btn--ghost">Ver programação</a>
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

    <section class="section section--intro">
        <div class="wrapper section__grid">
            <div>
                <p class="section__kicker">Projeto Esperança/Cooesperança</p>
                <h2>Organização popular, economia solidária e articulação em rede</h2>
            </div>
            <div class="section__text section__text--lead">
                <p>O Projeto Esperança/Cooesperança articula experiências de economia popular e solidária, agricultura familiar, comércio justo e cooperativismo em Santa Maria e na região central do Rio Grande do Sul.</p>
                <p>Seu trabalho conecta grupos urbanos e rurais, promove circulação de renda no território e fortalece iniciativas coletivas comprometidas com a autogestão, a cooperação e o bem viver.</p>
            </div>
        </div>
    </section>

    <section class="section section--quicklinks">
        <div class="wrapper quicklinks">
            <a class="quicklink" href="<?php echo esc_url(feicoop_page_url('quem-somos', '/quem-somos.html')); ?>">
                <span class="quicklink__kicker">Institucional</span>
                <strong>Quem somos</strong>
            </a>
            <a class="quicklink" href="<?php echo esc_url(feicoop_page_url('historia', '/historia.html')); ?>">
                <span class="quicklink__kicker">Memória</span>
                <strong>História</strong>
            </a>
            <a class="quicklink" href="<?php echo esc_url(feicoop_page_url('rede-esperanca', '/rede-esperanca.html')); ?>">
                <span class="quicklink__kicker">Rede</span>
                <strong>Rede Esperança</strong>
            </a>
            <a class="quicklink" href="<?php echo esc_url(feicoop_page_url('feirao-colonial', '/feirao-colonial.html')); ?>">
                <span class="quicklink__kicker">Comercialização</span>
                <strong>Feirão Colonial</strong>
            </a>
        </div>
    </section>

    <section class="section" id="projetos">
        <div class="wrapper">
            <div class="section__header">
                <p class="section__kicker">Projetos e redes</p>
                <h2>Núcleos que estruturam o portal</h2>
            </div>
            <div class="projects-grid">
                <article class="project-card">
                    <a href="<?php echo esc_url(feicoop_page_url('quem-somos', '/quem-somos.html')); ?>">
                        <img src="<?php echo esc_url(feicoop_asset_url('assets/img/Card_Home_Projeto_Esperanca_Cooesperanca_FEICOOP_Santa_Maria_RS-3.png')); ?>" alt="Projeto Esperança/Cooesperança" loading="lazy">
                        <div class="project-card__content">
                            <p class="project-card__eyebrow">Articulação</p>
                            <h3>Projeto Esperança/Cooesperança</h3>
                            <p>Espaço onde acontece o Feirão Colonial e de onde parte a articulação anual da FEICOOP.</p>
                        </div>
                    </a>
                </article>
                <article class="project-card">
                    <a href="<?php echo esc_url(feicoop_page_url('rede-esperanca', '/rede-esperanca.html')); ?>">
                        <img src="<?php echo esc_url(feicoop_asset_url('assets/img/Card_Home_Rede_Esperanca_Projeto_Esperanca_Cooesperanca_FEICOOP_Santa_Maria_RS-3.png')); ?>" alt="Rede Esperança" loading="lazy">
                        <div class="project-card__content">
                            <p class="project-card__eyebrow">Rede territorial</p>
                            <h3>Rede Esperança</h3>
                            <p>Rede de empreendimentos solidários vinculados ao projeto e conectados a processos nacionais de articulação.</p>
                        </div>
                    </a>
                </article>
                <article class="project-card">
                    <a href="<?php echo esc_url(feicoop_page_url('feirao-colonial', '/feirao-colonial.html')); ?>">
                        <img src="<?php echo esc_url(feicoop_asset_url('assets/img/Card_Home_O_Feirao_Colonial_Projeto_Esperanca_Cooesperanca_FEICOOP_Santa_Maria_RS.png')); ?>" alt="Feirão Colonial" loading="lazy">
                        <div class="project-card__content">
                            <p class="project-card__eyebrow">Comercialização</p>
                            <h3>Feirão Colonial</h3>
                            <p>Comercialização solidária, alimentação e agroecologia em atividade permanente aos sábados.</p>
                        </div>
                    </a>
                </article>
                <article class="project-card">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="<?php echo esc_url(feicoop_asset_url('assets/img/Imagem_Postagem_FEICOOP_Projeto_Esperanca_Cooesperanca_Santa_Maria_RS-2.png')); ?>" alt="FEICOOP" loading="lazy">
                        <div class="project-card__content">
                            <p class="project-card__eyebrow">Evento anual</p>
                            <h3>FEICOOP</h3>
                            <p>Feira internacional realizada anualmente em Santa Maria, reunindo cooperativismo, cultura e economia solidária.</p>
                        </div>
                    </a>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--highlight">
        <div class="wrapper callout">
            <div>
                <p class="section__kicker">Memória e agenda</p>
                <h2>Um espaço para reunir história, programação, redes parceiras e notícias da feira</h2>
            </div>
            <div class="section__text section__text--lead">
                <p>A FEICOOP reúne iniciativas do campo e da cidade em torno da cooperação, da comercialização solidária e da troca de saberes entre grupos e comunidades.</p>
            </div>
        </div>
    </section>

    <section class="section" id="noticias">
        <div class="wrapper">
            <div class="news-summary">
                <div class="section__header">
                    <p class="section__kicker">Notícias</p>
                    <h2>Últimas publicações</h2>
                </div>
                <div class="news-summary__actions">
                    <p class="section__text section__text--lead">Acompanhe as atualizações mais recentes da feira, os comunicados oficiais e as matérias que também aparecem na página completa de notícias.</p>
                    <a class="btn btn--ghost" href="<?php echo esc_url(feicoop_posts_page_url()); ?>">Ir para notícias</a>
                </div>
            </div>
            <div class="news-grid">
                <?php
                $latest = new WP_Query([
                    'post_type' => 'post',
                    'posts_per_page' => 4,
                    'post_status' => 'publish',
                ]);

                if ($latest->have_posts()) {
                    $featured_rendered = false;
                    $news_list_open = false;

                    while ($latest->have_posts()) {
                        $latest->the_post();

                        if (!$featured_rendered) {
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
                            $featured_rendered = true;
                            continue;
                        }

                        if (!$news_list_open) {
                            echo '<div class="news-grid__list feed feed--cards">';
                            $news_list_open = true;
                        }

                        get_template_part('template-parts/content', 'card');
                    }

                    if ($news_list_open) {
                        echo '</div>';
                    }

                    wp_reset_postdata();
                } else {
                    get_template_part('template-parts/content', 'none');
                }
                ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrapper contact-block">
            <div>
                <p class="section__kicker">Contato</p>
                <h2>Informações principais do projeto</h2>
            </div>
            <div class="contact-block__grid">
                <div class="contact-item">
                    <strong>Coordenação</strong>
                    <p>José Carlos Peranconi</p>
                </div>
                <div class="contact-item">
                    <strong>Telefones</strong>
                    <p>José Carlos Peranconi: 55 99974 4567</p>
                </div>
                <div class="contact-item">
                    <strong>E-mail</strong>
                    <p><a href="mailto:feicoopsantamaria@gmail.com">feicoopsantamaria@gmail.com</a></p>
                </div>
                <div class="contact-item">
                    <strong>Endereço</strong>
                    <p>Rua Heitor Campos, s/n<br>Medianeira, Santa Maria - RS<br>CEP 97060-290</p>
                </div>
                <div class="contact-item">
                    <strong>Redes sociais</strong>
                    <p><a href="https://www.facebook.com/share/18i1BbrmgR/">Facebook</a><br><a href="https://www.instagram.com/feirao.ecosol/">Instagram Feirão Colonial</a><br><a href="https://www.instagram.com/redeesperancacooesperanca/">Instagram Rede Esperança</a></p>
                </div>
                <div class="contact-item">
                    <strong>YouTube</strong>
                    <p><a href="https://www.youtube.com/channel/UC9fE3YsQNza8UpiYULNHIZw">Canal oficial</a></p>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
