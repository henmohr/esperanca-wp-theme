<?php
/**
 * Title: FEICOOP Page
 * Slug: feicoop/feicoop-page
 * Categories: feicoop
 * Description: Estrutura padrão da página FEICOOP (/feicoop/): chamada, PDF da programação, trajetória e cartas de encerramento.
 */
?>
<!-- wp:paragraph {"className":"hero__eyebrow"} -->
<p class="hero__eyebrow">32ª FEICOOP</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Feira Internacional do Cooperativismo e da Economia Solidária</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"feicoop-hero__subtitle"} -->
<p class="feicoop-hero__subtitle">Portal institucional do Projeto Esperança/Cooesperança para divulgar a feira, suas redes, a memória do movimento e as novidades da programação.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"feicoop-facts","layout":{"type":"constrained"}} -->
<div class="wp-block-group feicoop-facts"><!-- wp:paragraph -->
<p><strong>Quando:</strong> Em breve</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Onde:</strong> Santa Maria, RS</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Foco:</strong> Economia solidária, cooperativismo e redes</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:feicoop/programacao-pdf /-->

<!-- wp:feicoop/feicoop-history /-->

<!-- wp:group {"tagName":"section","className":"section section--registration-callout","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section--registration-callout"><!-- wp:paragraph {"className":"section__kicker"} -->
<p class="section__kicker">Inscrições</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Abertura das inscrições</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"section__text section__text--lead"} -->
<p class="section__text section__text--lead">Reserve a data e acompanhe os canais oficiais para acessar o formulário.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(feicoop_page_url('inscricoes')); ?>">Ir para inscrições</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section section--highlight","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section--highlight"><!-- wp:paragraph {"className":"section__kicker"} -->
<p class="section__kicker">Memória e agenda</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Um espaço para reunir história, programação, redes parceiras e notícias da feira</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"section__text section__text--lead"} -->
<p class="section__text section__text--lead">A FEICOOP reúne iniciativas do campo e da cidade em torno da cooperação, da comercialização solidária e da troca de saberes entre grupos e comunidades.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:feicoop/cartas-encerramento /-->
