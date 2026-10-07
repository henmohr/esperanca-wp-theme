<?php
/**
 * Bloco de contato (grade de informações), usado na página /contato.
 * Os dados vêm do Customizer (seção FEICOOP Contatos).
 */
if (!defined('ABSPATH')) {
    exit;
}

$contact = feicoop_home_contact_fields();
?>
<div class="contact-block__grid">
    <div class="contact-item">
        <strong><?php esc_html_e('Coordenador do Projeto Esperança', 'feicoop'); ?></strong>
        <p><?php echo esc_html($contact['coordinator']); ?></p>
    </div>
    <div class="contact-item">
        <strong><?php esc_html_e('Telefone', 'feicoop'); ?></strong>
        <p><?php echo esc_html($contact['phones']); ?></p>
        <?php if ($contact['whatsapp'] !== '') : ?>
            <p class="contact-actions">
                <a class="btn contact-btn" href="<?php echo esc_url('https://wa.me/' . preg_replace('/\D/', '', $contact['whatsapp'])); ?>" target="_blank" rel="noopener noreferrer">
                    <svg width="18" height="18" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#whatsapp')); ?>"></use></svg>
                    <?php esc_html_e('Chamar no WhatsApp', 'feicoop'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <div class="contact-item">
        <strong><?php esc_html_e('E-mail', 'feicoop'); ?></strong>
        <?php if ($contact['email'] !== '') : ?>
            <p><a href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a></p>
            <p class="contact-actions">
                <a class="btn contact-btn" href="mailto:<?php echo esc_attr($contact['email']); ?>">
                    <?php esc_html_e('Enviar e-mail', 'feicoop'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <div class="contact-item">
        <strong><?php esc_html_e('Endereço', 'feicoop'); ?></strong>
        <p><?php echo nl2br(esc_html($contact['address'])); ?></p>
    </div>
    <div class="contact-item">
        <strong><?php esc_html_e('Redes sociais', 'feicoop'); ?></strong>
        <p class="contact-socials">
            <?php if ($contact['facebook'] !== '') : ?>
                <a class="contact-social" href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <svg width="20" height="20" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#facebook')); ?>"></use></svg>
                </a>
            <?php endif; ?>
            <?php if ($contact['instagram'] !== '') : ?>
                <a class="contact-social" href="<?php echo esc_url($contact['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Instagram Feirão EcoSol', 'feicoop'); ?>">
                    <svg width="20" height="20" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#instagram')); ?>"></use></svg>
                </a>
            <?php endif; ?>
            <?php if ($contact['youtube'] !== '') : ?>
                <a class="contact-social" href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('YouTube', 'feicoop'); ?>">
                    <svg width="20" height="20" aria-hidden="true"><use xlink:href="<?php echo esc_url(feicoop_asset_url('assets/svg/svg-map.svg#youtube')); ?>"></use></svg>
                </a>
            <?php endif; ?>
        </p>
    </div>
</div>
