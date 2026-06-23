<?php
/**
 * Title: FEICOOP Home Quicklinks
 * Slug: feicoop/home-quicklinks
 * Categories: feicoop-home, feicoop
 * Description: Four quick links for the home page.
 */
?>
<!-- wp:group {"tagName":"section","className":"section section--quicklinks","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section--quicklinks">
	<!-- wp:group {"className":"wrapper quicklinks","layout":{"type":"constrained"}} -->
	<div class="wp-block-group wrapper quicklinks">
		<?php
		$links = [
			[
				'kicker' => 'Institucional',
				'title' => 'Quem somos',
				'url' => feicoop_page_url('quem-somos'),
			],
			[
				'kicker' => 'Memoria',
				'title' => 'Historia',
				'url' => feicoop_page_url('historia'),
			],
			[
				'kicker' => 'Rede',
				'title' => 'Rede Esperanca',
				'url' => feicoop_page_url('rede-esperanca'),
			],
			[
				'kicker' => 'Comercializacao',
				'title' => 'Feirao Colonial',
				'url' => feicoop_page_url('feirao-colonial'),
			],
		];

		foreach ($links as $link) :
		?>
			<a class="quicklink" href="<?php echo esc_url($link['url']); ?>">
				<span class="quicklink__kicker"><?php echo esc_html($link['kicker']); ?></span>
				<strong><?php echo esc_html($link['title']); ?></strong>
			</a>
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
