<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="<?php echo is_singular('post') ? 'footer footer--glued' : 'footer'; ?>">
    <div class="wrapper footer__grid">
        <div class="footer__brand">
            <div class="footer__seal">
                <img src="<?php echo esc_url(feicoop_asset_url('assets/img/selo-rodape-projeto-esperanca-cooesperanca-feicoop-santa-maria-rs.png')); ?>" alt="Selo Rodape Projeto Esperanca Cooesperanca FEICOOP Santa Maria RS" width="350" height="350" loading="lazy" decoding="async">
            </div>
        </div>

        <div>
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

            <div class="footer__copyright">
                <p><?php echo esc_html(get_theme_mod('feicoop_footer_copyright', 'Projeto Esperança/Cooesperança')); ?></p>
            </div>
        </div>
    </div>

    <button id="backToTop" class="footer__bttop" aria-label="<?php esc_attr_e('Back to top', 'feicoop'); ?>" title="<?php esc_attr_e('Back to top', 'feicoop'); ?>">
        <svg width="20" height="20"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#toparrow')); ?>"></use></svg>
    </button>
</footer>
<?php wp_footer(); ?>
</body>
</html>
