<?php
/**
 * efoo 2026 テーマ。
 * トップページ（front-page.php）が本体。スタイルとスクリプトはテーマ内の assets を使う。
 */

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/*
 * 本文中の裸の URL をリンクにする。oEmbed に解決できず URL の文字列だけが残った埋め込みや、
 * プレスリリース末尾の会社概要の URL が対象。oEmbed（優先度 8）の後に走らせる。
 */
add_filter( 'the_content', 'make_clickable', 12 );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
} );

/**
 * お知らせ一覧の名前。一覧の画面の見出し（archive.php）、トップのお知らせの見出し
 * （front-page.php）、ブラウザのタブの名前で同じ語を使う。
 */
function efoo_news_index_title(): string {
	return 'お知らせ・掲載実績';
}

/*
 * お知らせ一覧（portfolio_item のアーカイブ /project/）のタブの名前。投稿タイプのラベルは
 * wpzoom-portfolio の「Portfolio」なので、画面の見出しと同じ語に置き換える。
 */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_post_type_archive( 'portfolio_item' ) ) {
		$parts['title'] = efoo_news_index_title();
	}
	return $parts;
} );

/**
 * トップのお知らせに出す投稿タイプ。お知らせ・掲載実績はすべて wpzoom-portfolio の
 * portfolio_item に入っているため、portfolio_item だけを出す（通常の投稿には同じ告知の
 * 複製があり、混ぜると同じ告知が 2 件並ぶ）。プラグインが無効で portfolio_item が
 * 無いときだけ通常の投稿を出す。通常の投稿の詳細・検索・フィードはこの関数を使わない。
 */
function efoo_news_post_types(): array {
	return post_type_exists( 'portfolio_item' ) ? array( 'portfolio_item' ) : array( 'post' );
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
 * 記事が属する portfolio の分類（先頭の 1 件）。分類が無い記事・通常の投稿は null。
 */
function efoo_entry_term( ?WP_Post $post = null ): ?WP_Term {
	if ( ! taxonomy_exists( 'portfolio' ) ) {
		return null;
	}
	$terms = get_the_terms( get_post( $post ), 'portfolio' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * 記事詳細での記事の役割。portfolio の term ID で決め、CSS のクラス名（entry--<役割>）と
 * 記事の終わりの出し分けに使う（slug はパーセントエンコードされた日本語のため使わない）。
 * 対応表に無い分類・分類の無い記事・通常の投稿は 'post' で、共通の見た目にする。
 */
function efoo_entry_role( ?WP_Post $post = null ): string {
	$roles = array(
		16 => 'press', // プレスリリース
		19 => 'media', // 掲載実績
		18 => 'blog',  // シェフレピ店長ブログ
	);
	$term = efoo_entry_term( $post );
	return ( $term && isset( $roles[ $term->term_id ] ) ) ? $roles[ $term->term_id ] : 'post';
}

/**
 * 記事詳細にアイキャッチを出すか。本文にアイキャッチと同じ添付画像が入っている記事では出さない。
 * ブロックエディタは画像に class="wp-image-<添付 ID>" を書くため、本文にその文字列があるかで判定する
 * （HTML は解析しない）。ID の後ろに数字が続くもの（ID 49 に対する wp-image-492 など）は別の画像として扱う。
 */
function efoo_entry_shows_cover( ?WP_Post $post = null ): bool {
	$post     = get_post( $post );
	$thumb_id = (int) get_post_thumbnail_id( $post );
	if ( ! $post || ! $thumb_id ) {
		return false;
	}
	return ! preg_match( '/wp-image-' . $thumb_id . '(?![0-9])/', $post->post_content );
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
	/*
	 * CSS と JS の版（?ver=）はそれぞれのファイルの更新日時。内容を変えれば URL が変わるので、
	 * nginx と閲覧者のブラウザに残った古いファイルを使わずに済む。
	 */
	$css_ver = (string) filemtime( get_theme_file_path( 'assets/css/main.css' ) );
	$js_ver  = (string) filemtime( get_theme_file_path( 'assets/js/main.js' ) );
	// Space Grotesk はテーマから配信する（main.css の @font-face）。ここでは Noto Sans JP と JetBrains Mono だけを読む
	wp_enqueue_style(
		'efoo-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;900&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'efoo-main', get_theme_file_uri( 'assets/css/main.css' ), array(), $css_ver );
	wp_enqueue_script(
		'efoo-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
} );

/*
 * Google Fonts の CSS とフォントのファイルは別のホストから来るため、接続を先に張っておく。
 * フォントのファイルは CORS で取得されるので、fonts.gstatic.com には crossorigin を付ける。
 */
add_filter( 'wp_resource_hints', function ( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}, 10, 2 );

/*
 * Space Grotesk のファイルを、スタイルシート（wp_head の優先度 8）より前に取りに行かせる。
 * main.css を読み終えてから取りに行くと、トップのヒーローの英字が後から差し替わって横にずれる。
 * URL は main.css の @font-face の url() と同じにする（違うと 2 回取得する）。
 */
add_action( 'wp_head', function () {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/space-grotesk-v22-latin.woff2' ) )
	);
}, 3 );
