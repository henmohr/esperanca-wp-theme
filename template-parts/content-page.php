<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
    <div class="hero hero--page <?php echo has_post_thumbnail() ? '' : 'hero--noimage'; ?>">
        <header class="hero__content">
            <div class="wrapper">
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?><p class="page__desc"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
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

    <?php if (feicoop_page_has_children(get_the_ID())) : ?>
        <div class="subpages">
            <div class="entry-wrapper">
                <h2 class="h6 subpages__title"><?php esc_html_e('Paginas filhas', 'feicoop'); ?></h2>
                <ul class="subpages__list">
                    <?php
                    wp_list_pages([
                        'child_of' => get_the_ID(),
                        'title_li' => '',
                        'sort_column' => 'menu_order,post_title',
                    ]);
                    ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php comments_template(); ?>
</article>
