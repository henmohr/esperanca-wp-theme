<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
    <div class="hero hero--page <?php echo has_post_thumbnail() ? '' : 'hero--noimage'; ?>">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php the_title(); ?></h1>
                <?php feicoop_render_back_button(home_url('/'), __('Voltar ao início', 'feicoop')); ?>
            </div>
        </header>
        <?php if (has_post_thumbnail()) : ?>
            <figure class="hero__image">
                <?php the_post_thumbnail('feicoop-hero'); ?>
            </figure>
        <?php endif; ?>
    </div>

    <div class="entry-wrapper content__entry">
        <?php the_content(); ?>
    </div>
</article>
