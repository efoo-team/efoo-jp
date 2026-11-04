<?php
/**
 * お知らせの 1 行（サムネイル・日付・分類・タイトル）。ループの中で呼ぶ。
 * お知らせ一覧（news-list.php）と、記事詳細の「ほかの〈分類〉」が使う。
 */
$label = efoo_entry_term_label();
?>
<a class="news-row" href="<?php the_permalink(); ?>" style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px; padding: 20px; border-top: 1px solid #E2E7E3">
	<?php if ( has_post_thumbnail() ) { ?>
	<span class="news-thumb"><?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?></span>
	<?php } ?>
	<span class="en" style="color: #6B776F; font-size: 13px; letter-spacing: 0.08em"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
	<span style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: var(--accent); border: 1px solid var(--accent); border-radius: 999px; padding: 4px 12px"><?php echo esc_html( $label ); ?></span>
	<span class="news-title" style="flex: 1 1 400px; min-width: 0; font-size: 15px; color: #14201A"><?php the_title(); ?></span>
</a>
