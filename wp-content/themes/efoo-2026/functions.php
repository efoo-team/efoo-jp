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
 * お知らせで扱う投稿タイプ。通常の投稿に加え、現サイトがお知らせ・掲載実績を
 * 保存している wpzoom-portfolio の portfolio_item を含める。
 */
function efoo_news_post_types(): array {
	$types = array( 'post' );
	if ( post_type_exists( 'portfolio_item' ) ) {
		$types[] = 'portfolio_item';
	}
	return $types;
}

/**
 * お知らせの記事が属する分類の名称。portfolio タクソノミー → カテゴリの順に見る。
 */
function efoo_entry_term_label( ?WP_Post $post = null ): string {
	$post = get_post( $post );
	foreach ( array( 'portfolio', 'category' ) as $tax ) {
		if ( ! taxonomy_exists( $tax ) ) {
			continue;
		}
		$terms = get_the_terms( $post, $tax );
		if ( $terms && ! is_wp_error( $terms ) ) {
			return $terms[0]->name;
		}
	}
	return 'お知らせ';
}

/**
 * お知らせ一覧のURL。お知らせ・掲載実績の記事は portfolio_item に入っているため、
 * そのアーカイブ（wpzoom-portfolio の設定ではスラッグ project）を正とする。
 * portfolio_item が無ければ投稿ページ、それも無ければトップ。
 */
function efoo_news_index_url(): string {
	if ( post_type_exists( 'portfolio_item' ) ) {
		$link = get_post_type_archive_link( 'portfolio_item' );
		if ( $link ) {
			return (string) $link;
		}
	}
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		return (string) get_permalink( $posts_page );
	}
	return home_url( '/' );
}

/**
 * 公開中の固定ページのURLを、候補のスラッグの順に探す。見つからなければ null。
 */
function efoo_page_url( array $slugs ): ?string {
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			return (string) get_permalink( $page );
		}
	}
	return null;
}

/**
 * 会社概要ページのURL（スラッグ company）。ページが無ければ null で、呼び出し側はリンクを出さない。
 */
function efoo_company_url(): ?string {
	return efoo_page_url( array( 'company' ) );
}

/**
 * お問い合わせページのURL。スラッグ contact-us → contact → タイトル「お問い合わせ」の順に探し、無ければトップのCTAへ。
 */
function efoo_contact_url(): string {
	$url = efoo_page_url( array( 'contact-us', 'contact' ) );
	if ( $url ) {
		return $url;
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
