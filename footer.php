<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_copyright = (string) get_theme_mod('feicoop_footer_copyright', 'Projeto Esperança/Cooesperança');
$footer_legal = (string) get_theme_mod(
    'feicoop_footer_legal',
    "CNPJ: 93.155.067/0001-86\nRazão Social: Cooperativa Mista dos Pequenos Produtores Rurais e Urbanos Vinculados ao Projeto Esperança Ltda (Cooesperança)"
);

$footer_legal_lines = [];

if ($footer_copyright !== '') {
    $footer_legal_lines[] = $footer_copyright;
}

if ($footer_legal !== '') {
    $footer_legal_lines[] = $footer_legal;
}

$contact = function_exists('feicoop_home_contact_fields') ? feicoop_home_contact_fields() : [];
?>
<!-- Tema ESPERANCA v<?php echo esc_html((string) wp_get_theme()->get('Version')); ?> -->
<footer class="<?php echo is_singular('post') ? 'footer footer--glued' : 'footer'; ?>">
    <div class="wrapper footer__grid">
        <div class="footer__col">
            <strong class="footer__title"><?php esc_html_e('Contato', 'feicoop'); ?></strong>
            <?php if (!empty($contact['address'])) : ?>
                <p class="footer__text"><?php echo nl2br(esc_html($contact['address'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($contact['phones'])) : ?>
                <p class="footer__text"><?php echo esc_html($contact['phones']); ?></p>
            <?php endif; ?>
            <?php if (!empty($contact['email'])) : ?>
                <p class="footer__text"><a href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a></p>
            <?php endif; ?>
        </div>

        <div class="footer__col">
            <strong class="footer__title"><?php esc_html_e('Navegação', 'feicoop'); ?></strong>
            <nav class="footer__nav" aria-label="<?php esc_attr_e('Footer menu', 'feicoop'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'footerMenu',
                    'container' => false,
                    'menu_class' => '',
                    'fallback_cb' => 'feicoop_footer_menu_fallback',
                    'depth' => 1,
                ]);
                ?>
            </nav>
        </div>

        <div class="footer__col">
            <strong class="footer__title"><?php esc_html_e('Redes sociais', 'feicoop'); ?></strong>
            <p class="footer__socials">
                <?php if (!empty($contact['facebook'])) : ?>
                    <a href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Facebook', 'feicoop'); ?></a>
                <?php endif; ?>
                <?php if (!empty($contact['youtube'])) : ?>
                    <a href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('YouTube', 'feicoop'); ?></a>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="wrapper footer__bottom">
        <?php if ($footer_legal_lines !== []) : ?>
            <p class="footer__legal"><?php echo nl2br(esc_html(implode("\n", $footer_legal_lines))); ?></p>
        <?php endif; ?>
    </div>

    <button id="backToTop" class="footer__bttop" aria-label="<?php esc_attr_e('Back to top', 'feicoop'); ?>" title="<?php esc_attr_e('Back to top', 'feicoop'); ?>">
        <svg width="20" height="20"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#toparrow')); ?>"></use></svg>
    </button>
</footer>
<script>
(function () {
    var banner = document.querySelector('.site-construction');
    if (!banner) {
        return;
    }
    var key = 'feicoop_construction_closed';
    if (window.localStorage && localStorage.getItem(key) === '1') {
        banner.style.display = 'none';
    }
    var btn = banner.querySelector('.site-construction__close');
    if (btn) {
        btn.addEventListener('click', function () {
            banner.style.display = 'none';
            if (window.localStorage) {
                localStorage.setItem(key, '1');
            }
        });
    }
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
