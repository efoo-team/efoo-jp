<?php
/**
 * カテゴリの絞り込みチップ。一覧系ページの見出し下に出す。
 */
$current = is_category() ? get_queried_object_id() : 0;
$cats    = get_categories( array( 'orderby' => 'count', 'order' => 'DESC' ) );
if ( ! $cats ) {
	return;
}
?>
<div style="display: flex; flex-wrap: wrap; gap: 10px">
	<a class="cat-chip<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( efoo_news_index_url() ); ?>">All</a>
	<?php foreach ( $cats as $cat ) { ?>
	<a class="cat-chip<?php echo $current === $cat->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
	<?php } ?>
</div>
