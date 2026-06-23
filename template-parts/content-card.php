<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('feed__item feed__item--card'); ?>>
    <a class="feed__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php echo wp_kses_post(feicoop_post_feature_image_html(get_the_ID(), 'feicoop-card')); ?></a>
    <div class="feed__content">
        <div class="feed__meta">
            <time class="feed__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            <span class="feed__author"><?php echo esc_html(get_the_author()); ?></span>
        </div>
        <h3 class="feed__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="feed__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
        <a class="btn btn--ghost feed__readmore" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'feicoop'); ?></a>
    </div>
</article>
