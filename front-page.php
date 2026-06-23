<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="home-template">
    <section class="hero hero--noimage">
        <div class="wrapper hero__grid">
            <header class="hero__content hero__content--centered">
                <h2 class="hero__eyebrow">32ª FEICOOP</h2>
                <h2>Feira Internacional do Cooperativismo e da Economia Solidária</h2>
                <p>Portal institucional do Projeto Esperança/Cooesperança para divulgar a feira, suas redes, a memória do movimento e as novidades da programação.</p>
                <p>&nbsp;</p>
                <p class="hero__actions">
                    <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Ver publicações</a>
                    <a href="https://inscricoes.esperancacooesperanca.org.br/" class="btn btn--ghost">Ir para inscrições</a>
                </p>
                <ul class="hero__facts">
                    <li><strong>Quando</strong><span>10 a 12 de julho de 2026</span></li>
                    <li><strong>Onde</strong><span>Santa Maria, RS</span></li>
                    <li><strong>Foco</strong><span>Economia solidária, cooperativismo e redes</span></li>
                </ul>
            </header>
            <aside class="hero__panel">
                <p class="hero__panel-kicker">32ª FEICOOP</p>
                <h2>A maior feira de economia solidária da América Latina</h2>
                <p>Encontro anual de articulação, formação, comercialização solidária e troca de expêriencias entre grupos, redes, cooperativas e comunidades.</p>
                <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Acompanhar notícias</a>
            </aside>
        </div>
    </section>

    <section class="section section--registration-callout">
        <div class="wrapper">
            <div class="homepage-registration">
                <div class="homepage-registration__content">
                    <p class="section__kicker">Inscrições</p>
                    <h2>Abertura das inscrições em 1º de maio</h2>
                    <p class="section__text section__text--lead">Reserve a data e acompanhe os canais oficiais para acessar o formulário.</p>
                    <p class="homepage-registration__actions">
                        <a class="btn" href="https://inscricoes.esperancacooesperanca.org.br/">Ir para inscrições</a>
                    </p>
                </div>
                <aside class="homepage-registration__date" aria-label="Data de abertura das inscrições">
                    <span>Data de abertura</span>
                    <strong>1º de maio</strong>
                </aside>
            </div>
        </div>
    </section>

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

    <section class="section" id="publicacoes">
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
                <p></p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrapper">
            <div class="section__header">
                <p class="section__kicker">Publicações</p>
                <h2>Conteúdos recentes</h2>
            </div>
            <div class="feed feed--cards">
                <?php
                $latest = new WP_Query([
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                ]);

                if ($latest->have_posts()) {
                    while ($latest->have_posts()) {
                        $latest->the_post();
                        get_template_part('template-parts/content', 'card');
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
