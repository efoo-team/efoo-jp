<?php
/**
 * 検索結果。0 件のときは一覧の代わりに、見つからなかったことと次の行き先を出す。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">SEARCH</p>
		<h1 class="page-band-title">「<?php echo esc_html( get_search_query( false ) ); ?>」の検索結果</h1>
	</section>
	<div class="list-wrap" style="display: flex; flex-direction: column; gap: 36px">
		<?php get_template_part( 'template-parts/search-form' ); ?>
		<?php
		get_template_part(
			'template-parts/news-list',
			null,
			array( 'empty' => '「' . get_search_query( false ) . '」に一致する記事は見つかりませんでした。別のキーワードで検索するか、お知らせ一覧をご覧ください。' )
		);
		?>
		<?php if ( ! have_posts() ) { ?>
		<div><a class="btn-ghost" href="<?php echo esc_url( efoo_news_index_url() ); ?>">お知らせ一覧へ</a></div>
		<?php } ?>
	</div>
</main>
<?php
get_footer();
