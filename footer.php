<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="footer">
    <div class="wrapper footer__grid">
        <div class="footer__brand">
            <div class="footer__seal">
                <img src="<?php echo esc_url(feicoop_asset_url('assets/img/selo-rodape-projeto-esperanca-cooesperanca-feicoop-santa-maria-rs.png')); ?>" alt="Selo Rodape Projeto Esperanca Cooesperanca FEICOOP Santa Maria RS" width="350" height="350" loading="lazy" decoding="async">
            </div>
            <p class="footer__lead">Projeto Esperanca/Cooesperanca, economia solidaria, cooperativismo e articulacao em rede em Santa Maria e na regiao central do Rio Grande do Sul.</p>
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

        <?php $social = feicoop_social_links(); ?>
        <?php if (array_filter($social)) : ?>
            <div class="footer__social" aria-label="<?php esc_attr_e('Social links', 'feicoop'); ?>">
                <?php if (!empty($social['facebook'])) : ?><a href="<?php echo esc_url($social['facebook']); ?>" aria-label="Facebook"><svg><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#facebook')); ?>"></use></svg></a><?php endif; ?>
                <?php if (!empty($social['x'])) : ?><a href="<?php echo esc_url($social['x']); ?>" aria-label="X"><svg><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#twitter')); ?>"></use></svg></a><?php endif; ?>
                <?php if (!empty($social['instagram'])) : ?><a href="<?php echo esc_url($social['instagram']); ?>" aria-label="Instagram"><svg><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#instagram')); ?>"></use></svg></a><?php endif; ?>
                <?php if (!empty($social['linkedin'])) : ?><a href="<?php echo esc_url($social['linkedin']); ?>" aria-label="LinkedIn"><svg><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#linkedin')); ?>"></use></svg></a><?php endif; ?>
                <?php if (!empty($social['youtube'])) : ?><a href="<?php echo esc_url($social['youtube']); ?>" aria-label="YouTube"><svg><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#youtube')); ?>"></use></svg></a><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <button id="backToTop" class="footer__bttop" aria-label="<?php esc_attr_e('Back to top', 'feicoop'); ?>" title="<?php esc_attr_e('Back to top', 'feicoop'); ?>">
        <svg width="20" height="20"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#toparrow')); ?>"></use></svg>
    </button>
</footer>
<?php wp_footer(); ?>
</body>
</html>
