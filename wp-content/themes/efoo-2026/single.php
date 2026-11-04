<?php
/**
 * 投稿の詳細。
 */
get_header();
while ( have_posts() ) {
	the_post();
	$label = efoo_entry_term_label();
	?>
<main>
	<article>
		<section class="page-band">
			<div style="display: flex; align-items: center; gap: 16px; justify-content: center">
				<span class="en" style="color: #6B776F; font-size: 13px; letter-spacing: 0.08em"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
				<span style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: var(--accent); border: 1px solid var(--accent); border-radius: 999px; padding: 4px 12px"><?php echo esc_html( $label ); ?></span>
			</div>
			<h1 class="page-band-title entry-title"><?php the_title(); ?></h1>
		</section>
		<div class="prose">
			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?>
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
