<?php
/**
 * どのテンプレートにも当たらない場合の素のテンプレート。
 */
get_header();
?>
<main>
	<section class="page-band">
		<h1 class="page-band-title"><?php echo is_search() ? '検索結果' : 'efoo株式会社'; ?></h1>
	</section>
	<div class="list-wrap">
		<?php get_template_part( 'template-parts/news-list' ); ?>
	</div>
</main>
<?php
get_footer();
