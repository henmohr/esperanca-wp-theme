<?php
if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}

$comment_count = get_comments_number();
?>
<section class="content__comments">
    <div class="entry-wrapper">
        <?php if (have_comments()) : ?>
            <h2 class="h4">
                <?php
                printf(
                    esc_html(_nx('%s comentário', '%s comentários', $comment_count, 'comments title', 'feicoop')),
                    esc_html(number_format_i18n($comment_count))
                );
                ?>
            </h2>
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

        <?php
        comment_form([
            'title_reply' => __('Deixe um comentário', 'feicoop'),
            'label_submit' => __('Enviar comentário', 'feicoop'),
            'comment_field' => sprintf(
                '<p class="comment-form-comment"><label for="comment">%s</label><textarea id="comment" name="comment" cols="45" rows="8" required="required"></textarea></p>',
                esc_html__('Comentário', 'feicoop')
            ),
        ]);
        ?>
    </div>
</section>
