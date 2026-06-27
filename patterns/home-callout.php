<?php
/**
 * Title: FEICOOP Home Callout
 * Slug: feicoop/home-callout
 * Categories: feicoop-home, feicoop
 * Description: Highlight section with registration or event message.
 */
?>
<!-- wp:group {"tagName":"section","className":"section section--registration-callout","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section--registration-callout">
	<!-- wp:group {"className":"wrapper","layout":{"type":"constrained"}} -->
	<div class="wp-block-group wrapper">
		<!-- wp:group {"className":"homepage-registration","layout":{"type":"constrained"}} -->
		<div class="wp-block-group homepage-registration">
			<!-- wp:group {"className":"homepage-registration__content","layout":{"type":"constrained"}} -->
			<div class="wp-block-group homepage-registration__content">
				<!-- wp:paragraph {"className":"section__kicker"} -->
				<p class="section__kicker">Inscricoes</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading -->
					<h2>Abertura das inscricoes</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"section__text section__text--lead"} -->
				<p class="section__text section__text--lead">Acompanhe o anuncio oficial e os detalhes do processo de participacao na programacao da feira.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"homepage-registration__date","layout":{"type":"constrained"}} -->
			<div class="wp-block-group homepage-registration__date">
				<!-- wp:paragraph -->
				<p>Data de abertura</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"fontSize":"x-large"} -->
					<p class="has-x-large-font-size"><strong>Em breve</strong></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(feicoop_page_url('inscricoes')); ?>">Ver pagina de inscricoes</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
