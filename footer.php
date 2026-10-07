<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_copyright = (string) get_theme_mod('feicoop_footer_copyright', 'Projeto Esperança/Cooesperança');
$footer_legal = (string) get_theme_mod(
    'feicoop_footer_legal',
    "CNPJ: 93.155.067/0001-86\nRazão Social: Cooperativa Mista dos Pequenos Produtores Rurais e Urbanos Vinculados ao Projeto Esperança Ltda (Cooesperança)"
);

// Nome e informações legais no mesmo bloco, com a mesma formatação.
$footer_legal_lines = [];

if ($footer_copyright !== '') {
    $footer_legal_lines[] = $footer_copyright;
}

if ($footer_legal !== '') {
    $footer_legal_lines[] = $footer_legal;
}
?>
<!-- Tema ESPERANCA v<?php echo esc_html((string) wp_get_theme()->get('Version')); ?> -->
<footer class="<?php echo is_singular('post') ? 'footer footer--glued' : 'footer'; ?>">
    <div class="wrapper footer__grid">
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
                <?php if ($footer_legal_lines !== []) : ?>
                    <p class="footer__legal"><?php echo nl2br(esc_html(implode("\n", $footer_legal_lines))); ?></p>
                <?php endif; ?>
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
