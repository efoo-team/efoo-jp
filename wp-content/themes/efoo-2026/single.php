<?php
/**
 * 投稿・ポートフォリオ（お知らせ・掲載実績）の詳細。
 * アイキャッチがあれば、タイトルを重ねたヒーローの背景にする。本文の冒頭に
 * 同じ画像が入っている記事が多いため、本文の上にはアイキャッチを並べない。
 */
get_header();
while ( have_posts() ) {
	the_post();
	$label = efoo_entry_term_label();
	$cover = has_post_thumbnail();
	?>
<main>
	<article>
		<section class="page-band<?php echo $cover ? ' page-band--cover' : ''; ?>">
			<?php
			if ( $cover ) {
				the_post_thumbnail(
					'full',
					array(
						'class'   => 'page-band-cover',
						'alt'     => '',
						'sizes'   => '100vw',
						'loading' => 'eager',
					)
				);
				?>
			<span class="page-band-shade" aria-hidden="true"></span>
			<?php } ?>
			<div class="page-band-body">
				<div style="display: flex; align-items: center; gap: 16px; justify-content: center">
					<span class="en entry-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
					<span class="entry-term"><?php echo esc_html( $label ); ?></span>
				</div>
				<h1 class="page-band-title entry-title"><?php the_title(); ?></h1>
			</div>
		</section>
		<div class="prose">
			<?php the_content(); ?>
		</div>
		<div class="entry-foot">
			<a class="btn-ghost" href="<?php echo esc_url( efoo_news_index_url() ); ?>">← 一覧へ戻る</a>
		</div>
	</article>
</main>
	<?php
}
get_footer();
