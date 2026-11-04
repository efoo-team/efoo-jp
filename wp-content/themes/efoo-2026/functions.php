<?php
/**
 * efoo 2026 テーマ。
 * トップページ（front-page.php）が本体。スタイルとスクリプトはテーマ内の assets を使う。
 */

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
} );

/**
 * お知らせ一覧のURL。投稿ページが設定されていればそれを、無ければ投稿数の多いカテゴリのアーカイブを使う。
 */
function efoo_news_index_url(): string {
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		return (string) get_permalink( $posts_page );
	}
	$cats = get_categories(
		array(
			'number'  => 1,
			'orderby' => 'count',
			'order'   => 'DESC',
		)
	);
	return $cats ? (string) get_category_link( $cats[0] ) : home_url( '/' );
}

/**
 * お問い合わせページのURL。スラッグ contact → タイトル「お問い合わせ」の順に探し、無ければトップのCTAへ。
 */
function efoo_contact_url(): string {
	foreach ( array( 'contact-us', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			return (string) get_permalink( $page );
		}
	}
	$q = new WP_Query(
		array(
			'post_type'      => 'page',
			'title'          => 'お問い合わせ',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		)
	);
	if ( $q->posts ) {
		return (string) get_permalink( $q->posts[0] );
	}
	return home_url( '/#contact' );
}

add_action( 'wp_enqueue_scripts', function () {
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_style(
		'efoo-fonts',
		'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Noto+Sans+JP:wght@400;500;700;900&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'efoo-main', get_theme_file_uri( 'assets/css/main.css' ), array(), $ver );
	wp_enqueue_script(
		'efoo-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		$ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
} );
