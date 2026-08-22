<?php
/**
 * Title: FEICOOP Home Publications
 * Slug: feicoop/home-publications
 * Categories: feicoop-home, feicoop
 * Description: Publications section with a query loop for the home page.
 */
?>
<!-- wp:group {"tagName":"section","anchor":"publicacoes","className":"section","layout":{"type":"constrained"}} -->
<section class="wp-block-group section" id="publicacoes">
	<!-- wp:group {"className":"wrapper","layout":{"type":"constrained"}} -->
	<div class="wp-block-group wrapper">
		<!-- wp:group {"className":"section__header","layout":{"type":"constrained"}} -->
		<div class="wp-block-group section__header">
			<!-- wp:paragraph {"className":"section__kicker"} -->
			<p class="section__kicker">Publicacoes</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading -->
			<h2>Conteudos recentes</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"feed feed--cards"} -->
		<div class="wp-block-query feed feed--cards">
			<!-- wp:post-template -->
			<!-- wp:group {"tagName":"article","className":"feed__item feed__item--card","layout":{"type":"constrained"}} -->
			<article class="wp-block-group feed__item feed__item--card">
				<!-- wp:post-featured-image {"isLink":true,"className":"feed__image"} /-->

				<!-- wp:group {"className":"feed__content","layout":{"type":"constrained"}} -->
				<div class="wp-block-group feed__content">
					<!-- wp:group {"className":"feed__meta","layout":{"type":"constrained"}} -->
					<div class="wp-block-group feed__meta">
						<!-- wp:post-date /-->
						<!-- wp:post-author-name /-->
					</div>
					<!-- /wp:group -->

					<!-- wp:post-title {"level":3,"isLink":true,"className":"feed__title"} /-->
					<!-- wp:post-excerpt {"moreText":"Ler mais","className":"feed__excerpt"} /-->
				</div>
				<!-- /wp:group -->
			</article>
			<!-- /wp:group -->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:query -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
