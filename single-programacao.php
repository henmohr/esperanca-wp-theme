<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="programacao-single">
    <?php while (have_posts()) : the_post(); ?>
        <?php $fields = feicoop_programacao_meta_fields(get_the_ID()); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('content programacao-single__article'); ?>>
            <div class="hero hero--page <?php echo has_post_thumbnail() ? '' : 'hero--noimage'; ?>">
                <header class="hero__content hero__content--centered">
                    <div class="wrapper">
                        <p class="hero__eyebrow"><?php esc_html_e('Programação', 'feicoop'); ?></p>
                        <h1><?php the_title(); ?></h1>
                    </div>
                </header>
                <?php if (has_post_thumbnail()) : ?>
                    <figure class="hero__image">
                        <?php the_post_thumbnail('feicoop-hero'); ?>
                    </figure>
                <?php endif; ?>
            </div>

            <div class="entry-wrapper content__entry">
                <div class="programacao-single__meta">
                    <?php if ($fields['date'] !== '') : ?>
                        <div class="programacao-single__meta-item">
                            <strong><?php esc_html_e('Data', 'feicoop'); ?></strong>
                            <span><?php echo esc_html(feicoop_programacao_format_date($fields['date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($fields['time'] !== '') : ?>
                        <div class="programacao-single__meta-item">
                            <strong><?php esc_html_e('Horário', 'feicoop'); ?></strong>
                            <span><?php echo esc_html(feicoop_programacao_format_time($fields['time'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($fields['location'] !== '') : ?>
                        <div class="programacao-single__meta-item">
                            <strong><?php esc_html_e('Local', 'feicoop'); ?></strong>
                            <span><?php echo esc_html($fields['location']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($fields['track'] !== '') : ?>
                        <div class="programacao-single__meta-item">
                            <strong><?php esc_html_e('Faixa', 'feicoop'); ?></strong>
                            <span><?php echo esc_html($fields['track']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
