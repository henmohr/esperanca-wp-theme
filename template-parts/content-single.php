<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
    <div class="hero <?php echo has_post_thumbnail() ? '' : 'hero--noimage'; ?>">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php the_title(); ?></h1>
                <div class="feed__meta content__meta">
                    <time class="feed__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <span class="feed__author"><?php the_author_posts_link(); ?></span>
                </div>
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

    <footer class="content__footer">
        <div class="entry-wrapper">
            <?php the_tags('<ul class="content__tag"><li>', '</li><li>', '</li></ul>'); ?>

            <div class="content__actions">
                <div class="content__share">
                    <button class="btn btn--icon content__share-button js-content__share-button" type="button">
                        <svg width="20" height="20" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#share')); ?>"></use></svg>
                        <span><?php esc_html_e('Compartilhar', 'feicoop'); ?></span>
                    </button>
                    <div class="content__share-popup js-content__share-popup">
                        <a class="js-share" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_permalink()); ?>">Facebook</a>
                        <a class="js-share" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode(get_permalink()); ?>">X</a>
                        <a class="js-share" href="https://wa.me/?text=<?php echo rawurlencode(get_the_title() . ' ' . get_permalink()); ?>">WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php comments_template(); ?>
</article>
