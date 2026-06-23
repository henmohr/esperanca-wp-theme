<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="home-template">
    <?php while (have_posts()) : the_post(); ?>
        <section class="hero">
            <div class="wrapper hero__grid">
                <header class="hero__content">
                    <p class="hero__eyebrow"><?php echo esc_html(get_bloginfo('name')); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php endif; ?>
                    <p class="hero__actions">
                        <a href="<?php echo esc_url(feicoop_posts_page_url()); ?>" class="btn">Ver publicacoes</a>
                    </p>
                </header>
                <?php if (has_post_thumbnail()) : ?>
                    <aside class="hero__panel hero__panel--media">
                        <figure class="hero__panel-media">
                            <?php the_post_thumbnail('feicoop-hero'); ?>
                        </figure>
                    </aside>
                <?php else : ?>
                    <aside class="hero__panel">
                        <p class="hero__panel-kicker">Destaque</p>
                        <h2>Conteudo editavel no editor do WordPress</h2>
                        <p>Use este espaco para montar a home com blocos, textos, imagens, listas e chamadas para acao sem alterar o tema.</p>
                    </aside>
                <?php endif; ?>
            </div>
        </section>

        <section class="section section--intro">
            <div class="wrapper content__entry">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endwhile; ?>

    <section class="section" id="publicacoes">
        <div class="wrapper">
            <div class="section__header">
                <p class="section__kicker">Publicacoes</p>
                <h2>Conteudos recentes</h2>
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
</main>
<?php get_footer(); ?>
