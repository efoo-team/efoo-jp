<?php
/**
 * 固定ページ（お問い合わせ・会社概要など）。本文はブロック／プラグインの出力をそのまま載せる。
 */
get_header();
while ( have_posts() ) {
	the_post();
	?>
<main>
	<article>
		<section class="page-band">
			<p class="en page-band-label">EFOO</p>
			<h1 class="page-band-title"><?php the_title(); ?></h1>
		</section>
		<div class="prose" style="padding-bottom: 110px">
			<?php the_content(); ?>
		</div>
	</article>
</main>
	<?php
}
get_footer();
