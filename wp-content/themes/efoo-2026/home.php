<?php
/**
 * お知らせ一覧（投稿ページ）。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">CONTENTS</p>
		<h1 class="page-band-title">お知らせ・掲載実績</h1>
	</section>
	<div class="list-wrap" style="display: flex; flex-direction: column; gap: 36px">
		<?php get_template_part( 'template-parts/category-chips' ); ?>
		<?php get_template_part( 'template-parts/news-list' ); ?>
	</div>
</main>
<?php
get_footer();
