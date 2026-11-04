<?php
/**
 * ページが見つからないとき（存在しない URL・記事）。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">404 NOT FOUND</p>
		<h1 class="page-band-title">ページが見つかりません</h1>
	</section>
	<div class="list-wrap" style="max-width: 820px; display: flex; flex-direction: column; gap: 32px">
		<p style="margin: 0; font-size: 16px; line-height: 2; color: var(--muted)">お探しのページは、移動または削除された可能性があります。URL をお確かめのうえ、トップページやお知らせ一覧からお探しください。</p>
		<?php get_template_part( 'template-parts/search-form' ); ?>
		<div style="display: flex; flex-wrap: wrap; gap: 16px">
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">トップページへ</a>
			<a class="btn-ghost" href="<?php echo esc_url( efoo_news_index_url() ); ?>">お知らせ一覧へ</a>
		</div>
	</div>
</main>
<?php
get_footer();
