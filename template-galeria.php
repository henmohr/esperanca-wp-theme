<?php
/*
Template Name: Galeria de fotos
*/
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$gallery_ids = feicoop_gallery_ids();
?>
<main id="main" class="page page--galeria">
    <?php while (have_posts()) : the_post(); ?>
        <div class="hero hero--noimage">
            <header class="hero__content hero__content--centered">
                <div class="wrapper">
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
                </div>
            </header>
        </div>

        <div class="wrapper gallery-page">
            <?php if ($gallery_ids !== []) : ?>
                <div class="gallery-grid">
                    <?php foreach ($gallery_ids as $image_id) : ?>
                        <?php
                        $full = (string) wp_get_attachment_image_url($image_id, 'full');
                        $alt = (string) get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        ?>
                        <a class="gallery-grid__item" href="<?php echo esc_url($full); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (trim((string) get_the_content()) !== '') : ?>
                <div class="entry-wrapper content__entry gallery-page__content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
