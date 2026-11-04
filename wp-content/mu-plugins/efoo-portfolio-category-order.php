<?php
/**
 * Plugin Name: efoo ポートフォリオのカテゴリ順
 * Description: WPZOOM Portfolio のポートフォリオブロックが表示するカテゴリの絞り込みを、カテゴリの「説明」欄の値の順に並べる。
 */

add_filter(
	'get_terms_args',
	static function ( array $args, array $taxonomies ): array {
		// ポートフォリオブロックのカテゴリ一覧は、get_categories() に show_option_all と
		// use_desc_for_title を付けて portfolio タクソノミーを取得する。その呼び出しだけを対象にする。
		if ( array( 'portfolio' ) === array_values( $taxonomies )
			&& isset( $args['show_option_all'], $args['use_desc_for_title'] )
			&& 'name' === $args['orderby'] ) {
			$args['orderby'] = 'description';
		}
		return $args;
	},
	10,
	2
);
