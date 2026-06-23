<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$items = get_posts([
    'post_type' => 'programacao',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby' => [
        'meta_value' => 'ASC',
        'title' => 'ASC',
    ],
    'meta_key' => '_feicoop_programacao_date',
    'order' => 'ASC',
]);

usort($items, static function (WP_Post $a, WP_Post $b): int {
    $a_date = (string) get_post_meta($a->ID, '_feicoop_programacao_date', true);
    $b_date = (string) get_post_meta($b->ID, '_feicoop_programacao_date', true);
    $a_time = (string) get_post_meta($a->ID, '_feicoop_programacao_time', true);
    $b_time = (string) get_post_meta($b->ID, '_feicoop_programacao_time', true);

    $a_key = ($a_date !== '' ? $a_date : '9999-12-31') . ' ' . ($a_time !== '' ? $a_time : '99:99');
    $b_key = ($b_date !== '' ? $b_date : '9999-12-31') . ' ' . ($b_time !== '' ? $b_time : '99:99');

    if ($a_key === $b_key) {
        return strcasecmp($a->post_title, $b->post_title);
    }

    return strcmp($a_key, $b_key);
});

$grouped = [];

foreach ($items as $item) {
    $date = (string) get_post_meta($item->ID, '_feicoop_programacao_date', true);
    $group_key = $date !== '' ? $date : 'sem-data';
    $grouped[$group_key][] = $item;
}

?>
<main class="programacao-archive">
    <div class="hero hero--noimage">
        <header class="hero__content hero__content--centered">
            <div class="wrapper">
                <h1><?php esc_html_e('Programação', 'feicoop'); ?></h1>
                <p class="page__desc"><?php esc_html_e('Agenda oficial da feira organizada por dia, horário e local.', 'feicoop'); ?></p>
            </div>
        </header>
    </div>

    <div class="wrapper programacao-archive__body">
        <?php if (!empty($grouped)) : ?>
            <?php foreach ($grouped as $group_key => $group_items) : ?>
                <section class="programacao-day">
                    <header class="programacao-day__header">
                        <p class="section__kicker"><?php esc_html_e('Dia', 'feicoop'); ?></p>
                        <h2>
                            <?php
                            if ($group_key === 'sem-data') {
                                esc_html_e('Sem data definida', 'feicoop');
                            } else {
                                echo esc_html(feicoop_programacao_format_date($group_key));
                            }
                            ?>
                        </h2>
                    </header>
                    <div class="programacao-day__list">
                        <?php foreach ($group_items as $item) : ?>
                            <?php
                            $post = $item;
                            setup_postdata($post);
                            get_template_part('template-parts/content', 'programacao');
                            ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
