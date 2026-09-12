<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="posts posts--archive">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <?php
                // O WordPress embrulha o título do arquivo em <span> desde o 5.5
                // (general-template.php). Escapar a string inteira fazia as tags
                // aparecerem como texto: "Categoria: <span>Acervo</span>".
                // Aqui permitimos apenas o span do core e escapamos o resto.
                ?>
                <h1><?php echo wp_kses(get_the_archive_title(), ['span' => []]); ?></h1>
                <p class="page__desc"><?php echo wp_kses_post(get_the_archive_description()); ?></p>
            </div>
        </header>
    </div>

    <div class="wrapper feed">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
