<?php
if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>
<section class="content__comments">
    <div class="entry-wrapper">
        <?php if (have_comments()) : ?>
            <h2 class="h4"><?php comments_number(); ?></h2>
            <ol class="comment-list">
                <?php
                wp_list_comments([
                    'style' => 'ol',
                    'short_ping' => true,
                ]);
                ?>
            </ol>
            <?php the_comments_navigation(); ?>
        <?php endif; ?>

        <?php comment_form(); ?>
    </div>
</section>
