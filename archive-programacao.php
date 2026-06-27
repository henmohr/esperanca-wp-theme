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
$tracks = [];

foreach ($items as $item) {
    $fields = feicoop_programacao_meta_fields($item->ID);
    $date = $fields['date'];
    $track = $fields['track'] !== '' ? $fields['track'] : __('Sem trilha', 'feicoop');
    $group_key = $date !== '' ? $date : 'sem-data';

    $grouped[$group_key][$track][] = $item;
    $tracks[$track] = true;
}

$track_names = array_keys($tracks);
sort($track_names, SORT_NATURAL | SORT_FLAG_CASE);
foreach ($grouped as &$date_tracks) {
    ksort($date_tracks, SORT_NATURAL | SORT_FLAG_CASE);
}
unset($date_tracks);

$track_palette = [
    '#4a8779',
    '#b0aa5c',
    '#d17f4d',
    '#8d6bb8',
    '#3f7cac',
    '#c05771',
];

$get_track_style = static function (string $track_name) use ($track_palette): string {
    $slug = sanitize_title($track_name);
    $index = abs((int) crc32($slug)) % count($track_palette);
    $accent = $track_palette[$index];

    return sprintf(
        '--track-accent: %1$s; --track-accent-soft: color-mix(in srgb, %1$s 14%, white); --track-accent-border: color-mix(in srgb, %1$s 26%, white);',
        $accent
    );
};

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
        <?php if (!empty($track_names)) : ?>
            <nav class="programacao-tracks" aria-label="<?php esc_attr_e('Trilhas da programação', 'feicoop'); ?>">
                <button type="button" class="programacao-tracks__item is-active" data-track-filter="all"><?php esc_html_e('Todas', 'feicoop'); ?></button>
                <?php foreach ($track_names as $track_name) : ?>
                    <button type="button" class="programacao-tracks__item" data-track-filter="<?php echo esc_attr(sanitize_title($track_name)); ?>" style="<?php echo esc_attr($get_track_style($track_name)); ?>"><?php echo esc_html($track_name); ?></button>
                <?php endforeach; ?>
            </nav>
            <div class="programacao-legend" aria-label="<?php esc_attr_e('Legenda das trilhas', 'feicoop'); ?>">
                <p class="programacao-legend__title"><?php esc_html_e('Legenda', 'feicoop'); ?></p>
                <div class="programacao-legend__list">
                    <?php foreach ($track_names as $track_name) : ?>
                        <span class="programacao-legend__item" style="<?php echo esc_attr($get_track_style($track_name)); ?>">
                            <span class="programacao-legend__swatch" aria-hidden="true"></span>
                            <span class="programacao-legend__label"><?php echo esc_html($track_name); ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($grouped)) : ?>
            <?php foreach ($grouped as $group_key => $group_items) : ?>
                <section class="programacao-day" id="<?php echo esc_attr($group_key === 'sem-data' ? 'programacao-dia-sem-data' : 'programacao-dia-' . $group_key); ?>">
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
                        <?php foreach ($group_items as $track_name => $track_items) : ?>
                            <section class="programacao-track" data-track="<?php echo esc_attr(sanitize_title($track_name)); ?>" style="<?php echo esc_attr($get_track_style($track_name)); ?>">
                                <header class="programacao-track__header">
                                    <h3><?php echo esc_html($track_name); ?></h3>
                                </header>
                                <div class="programacao-track__list">
                                    <?php foreach ($track_items as $item) : ?>
                                        <?php
                                        $post = $item;
                                        setup_postdata($post);
                                        get_template_part('template-parts/content', 'programacao');
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <?php wp_reset_postdata(); ?>
            <script>
                (function () {
                    var buttons = document.querySelectorAll('.programacao-tracks [data-track-filter]');
                    var tracks = document.querySelectorAll('.programacao-track');
                    var days = document.querySelectorAll('.programacao-day');

                    if (!buttons.length || !tracks.length || !days.length) {
                        return;
                    }

                    function setActive(button) {
                        buttons.forEach(function (item) {
                            item.classList.toggle('is-active', item === button);
                        });
                    }

                    function applyFilter(filter) {
                        tracks.forEach(function (track) {
                            var value = track.getAttribute('data-track');
                            var show = filter === 'all' || value === filter;
                            track.classList.toggle('is-hidden', !show);
                        });

                        days.forEach(function (day) {
                            var visibleTracks = day.querySelectorAll('.programacao-track:not(.is-hidden)');

                            day.classList.toggle('is-hidden', visibleTracks.length === 0);
                        });
                    }

                    buttons.forEach(function (button) {
                        button.addEventListener('click', function () {
                            var filter = this.getAttribute('data-track-filter') || 'all';
                            setActive(this);
                            applyFilter(filter);
                        });
                    });

                    applyFilter('all');
                })();
            </script>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
