<?php
if (!defined('ABSPATH')) {
    exit;
}

$fields = feicoop_programacao_meta_fields(get_the_ID());
$date = feicoop_programacao_format_date($fields['date']);
$time = feicoop_programacao_format_time($fields['time']);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('programacao-card'); ?>>
    <div class="programacao-card__meta">
        <?php if ($date !== '') : ?>
            <time class="programacao-card__date" datetime="<?php echo esc_attr($fields['date']); ?>"><?php echo esc_html($date); ?></time>
        <?php endif; ?>
        <?php if ($time !== '') : ?>
            <span class="programacao-card__time"><?php echo esc_html($time); ?></span>
        <?php endif; ?>
        <?php if ($fields['location'] !== '') : ?>
            <span class="programacao-card__location"><?php echo esc_html($fields['location']); ?></span>
        <?php endif; ?>
        <?php if ($fields['track'] !== '') : ?>
            <span class="programacao-card__track"><?php echo esc_html($fields['track']); ?></span>
        <?php endif; ?>
        <?php if ($fields['featured']) : ?>
            <span class="programacao-card__badge"><?php esc_html_e('Destaque', 'feicoop'); ?></span>
        <?php endif; ?>
    </div>
    <div class="programacao-card__content">
        <h3 class="programacao-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="programacao-card__excerpt"><?php echo wp_kses_post(feicoop_excerpt()); ?></div>
        <a class="btn btn--ghost programacao-card__readmore" href="<?php the_permalink(); ?>"><?php esc_html_e('Ver detalhes', 'feicoop'); ?></a>
    </div>
</article>
