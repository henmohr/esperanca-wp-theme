<?php
/*
Template Name: Contato
*/
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="page page--contato">
    <?php while (have_posts()) : the_post(); ?>
        <div class="hero hero--noimage">
            <header class="hero__content hero__content--centered">
                <div class="wrapper">
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
                </div>
            </header>
        </div>

        <div class="wrapper contact-block">
            <?php get_template_part('template-parts/contact', 'grid'); ?>

            <?php if (trim((string) get_the_content()) !== '') : ?>
                <div class="entry-wrapper content__entry contact-page__content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
