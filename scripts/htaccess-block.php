<?php
/**
 * 本番 docroot の .htaccess の「# BEGIN <区間名>」〜「# END <区間名>」を、WordPress の insert_with_markers() で書き換える。
 * scripts/deploy-htaccess.sh が、サーバーの wp-cli に標準入力で渡して実行する。
 *   wp eval-file - <render|write> set <区間名> <中身の base64>   区間の中身を設定の内容にする
 *   wp eval-file - <render|write> remove <区間名>                 区間の中身を空にする（マーカー行だけを残す）。区間がまだ無ければ何もしない
 *   render  書き換えた後の .htaccess の全文を出力する。サーバーの一時ファイルの写しで書き換え、docroot の .htaccess には書かない
 *   write   docroot の .htaccess を書き換える
 * 区間がまだ無ければ、insert_with_markers() は .htaccess の末尾に足す。
 *
 * @package efoo-jp
 */

require_once ABSPATH . 'wp-admin/includes/misc.php';

$efoo_mode   = $args[0] ?? '';
$efoo_action = $args[1] ?? '';
if ( ! in_array( $efoo_mode, array( 'render', 'write' ), true ) ) {
	WP_CLI::error( "動作は render か write: {$efoo_mode}" );
}

if ( 'remove' === $efoo_action && count( $args ) === 3 ) {
	$efoo_marker = $args[2];
	$efoo_lines  = array();
} elseif ( 'set' === $efoo_action && count( $args ) === 4 ) {
	$efoo_marker  = $args[2];
	$efoo_content = base64_decode( $args[3], true );
	if ( false === $efoo_content ) {
		WP_CLI::error( '区間の中身を読めない' );
	}
	$efoo_lines = explode( "\n", rtrim( str_replace( "\r\n", "\n", $efoo_content ), "\n" ) );
	// 空行とコメント行だけの中身は保存期間を外すことになる。外すのは remove で明示したときだけにする。
	$efoo_directives = array_filter(
		$efoo_lines,
		static function ( $line ) {
			$line = trim( $line );
			return '' !== $line && '#' !== $line[0];
		}
	);
	if ( array() === $efoo_directives ) {
		WP_CLI::error( '区間の中身に、空行とコメント行のほかの行が無い。中身を空にするときは remove を使う' );
	}
} else {
	WP_CLI::error( '引数は <render|write> set <区間名> <中身の base64> か <render|write> remove <区間名>' );
}

// insert_with_markers() は区間の先頭に「WordPress フィルターによってのみ修正が可能」という説明を入れる。
// この区間の説明は中身のコメントに書いてあるため、この区間に限って入れない。
add_filter(
	'insert_with_markers_inline_instructions',
	static function ( $instructions, $marker ) use ( $efoo_marker ) {
		return $marker === $efoo_marker ? array() : $instructions;
	},
	10,
	2
);

$efoo_target = ABSPATH . '.htaccess';
if ( ! is_file( $efoo_target ) ) {
	WP_CLI::error( "{$efoo_target} が無い" );
}

// 区間が無いのに remove するときは、マーカー行だけの区間を足さずに、そのままにする。
$efoo_noop = 'remove' === $efoo_action && ! str_contains( (string) file_get_contents( $efoo_target ), "# BEGIN {$efoo_marker}" );

if ( 'render' === $efoo_mode && $efoo_noop ) {
	readfile( $efoo_target );
} elseif ( 'write' === $efoo_mode && $efoo_noop ) {
	WP_CLI::log( '区間が無いため、何もしない' );
} elseif ( 'render' === $efoo_mode ) {
	$efoo_copy = tempnam( sys_get_temp_dir(), 'efoo-htaccess-' );
	if ( false === $efoo_copy || ! copy( $efoo_target, $efoo_copy ) ) {
		WP_CLI::error( '一時ファイルへ写せない' );
	}
	$efoo_ok   = insert_with_markers( $efoo_copy, $efoo_marker, $efoo_lines );
	$efoo_data = file_get_contents( $efoo_copy );
	unlink( $efoo_copy );
	if ( ! $efoo_ok || false === $efoo_data ) {
		WP_CLI::error( '一時ファイルの写しを書き換えられない' );
	}
	echo $efoo_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- .htaccess の全文をそのまま出す。
} else {
	if ( ! insert_with_markers( $efoo_target, $efoo_marker, $efoo_lines ) ) {
		WP_CLI::error( "{$efoo_target} を書き換えられない" );
	}
}
