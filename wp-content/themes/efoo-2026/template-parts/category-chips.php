<?php
/**
 * 分類の絞り込みチップ。portfolio タクソノミーを正とし、
 * 同名の通常カテゴリは出さない（現サイトは両方に「プレスリリース」があるため）。
 */
$current = ( is_category() || is_tax( 'portfolio' ) ) ? get_queried_object_id() : 0;
$seen    = array();
$terms   = array();
foreach ( array( 'portfolio', 'category' ) as $tax ) {
	if ( ! taxonomy_exists( $tax ) ) {
		continue;
	}
	$found = get_terms(
		array(
			'taxonomy'   => $tax,
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	if ( ! $found || is_wp_error( $found ) ) {
		continue;
	}
	foreach ( $found as $term ) {
		if ( in_array( $term->name, $seen, true ) ) {
			continue;
		}
		$seen[]  = $term->name;
		$terms[] = $term;
	}
}
if ( ! $terms ) {
	return;
}
?>
<div style="display: flex; flex-wrap: wrap; gap: 10px">
	<?php foreach ( $terms as $term ) { ?>
	<a class="cat-chip<?php echo $current === $term->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
	<?php } ?>
</div>
