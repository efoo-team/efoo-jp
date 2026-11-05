<?php
/**
 * 共有したときの表示（OGP）。LINE・X・Facebook などがリンクのカードを作るときに読む
 * <meta> を wp_head に出す。あわせて検索エンジン向けの <meta name="description"> も出す。
 * OGP を出すプラグインは入れていないため、テーマで完結させる。
 */

/**
 * 会社の説明文。トップの OGP 説明と、固有の説明を持たないページ（404・検索結果・
 * 本文が空の記事）の既定に使う。トップのヒーロー（front-page.php）の見出しとリード文が元で、
 * 装飾の英字は含めない。ヒーローの文言を変えたらここも見直す。
 */
function efoo_ogp_site_description(): string {
	return '食の世界を、もっと前へ。efoo株式会社は、エンジニアリングとAIを軸に、食の現場の課題を解く会社です。自社プロダクトの開発・運用で磨いた技術力と、現場を知る料理人の知見を、ひとつのチームで提供します。';
}

/**
 * 共有カードに出す既定の画像（1200×630）。固有の画像を持たないページで使う。
 * URL に版が付かず保存期間が長いため、差し替えるときはファイル名を変える。
 *
 * @return array{url: string, width: int, height: int, alt: string}
 */
function efoo_ogp_default_image(): array {
	return array(
		'url'    => get_theme_file_uri( 'assets/img/og-default.png' ),
		'width'  => 1200,
		'height' => 630,
		'alt'    => 'efoo株式会社 食の世界を、もっと前へ。',
	);
}

/**
 * 説明文に使える文字列へ整える。HTML タグを除き、文字参照を戻し、空白を詰め、
 * 全角 120 字を超えるときは 120 字で切って「…」を付ける。
 */
function efoo_ogp_clean_text( string $text, int $max = 120 ): string {
	$text = html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' );
	$text = trim( (string) preg_replace( '/[\s\p{Z}]+/u', ' ', $text ) );
	if ( mb_strlen( $text ) > $max ) {
		$text = rtrim( mb_substr( $text, 0, $max ) ) . '…';
	}
	return $text;
}

/**
 * 記事・固定ページの説明文。抜粋があれば抜粋、無ければ本文の先頭。ショートコードと、
 * 抜粋に向かないブロックは本文から除く。パスワード保護の記事は本文を出さない。
 * 抜粋は get_the_excerpt() を通さない（英語の語数で切られ、日本語では全文が残るため）。
 */
function efoo_ogp_post_description( WP_Post $post ): string {
	if ( post_password_required( $post ) ) {
		return '';
	}
	$text = $post->post_excerpt;
	if ( '' === trim( $text ) ) {
		$text = excerpt_remove_blocks( strip_shortcodes( $post->post_content ) );
	}
	return efoo_ogp_clean_text( $text );
}

/**
 * アイキャッチの画像。登録済みの大きさのうち、幅 1200px 以上で最も小さいものを選ぶ。
 * 無ければ元の大きさ（full）。画像が無い・取れないときは null。
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function efoo_ogp_thumbnail( WP_Post $post ): ?array {
	$id = (int) get_post_thumbnail_id( $post );
	if ( ! $id ) {
		return null;
	}
	$size  = 'full';
	$width = 0;
	$meta  = wp_get_attachment_metadata( $id );
	if ( is_array( $meta ) && ! empty( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $name => $info ) {
			$w = (int) ( $info['width'] ?? 0 );
			if ( $w >= 1200 && ( 0 === $width || $w < $width ) ) {
				$size  = $name;
				$width = $w;
			}
		}
	}
	$src = wp_get_attachment_image_src( $id, $size );
	if ( ! $src ) {
		return null;
	}
	$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	return array(
		'url'    => $src[0],
		'width'  => (int) $src[1],
		'height' => (int) $src[2],
		'alt'    => '' !== $alt ? $alt : efoo_ogp_clean_text( get_the_title( $post ) ),
	);
}

/**
 * 表示中のページの正規の URL。404 は正規の URL が無いので null。
 * 記事・固定ページはコアが出す rel=canonical と同じ値（wp_get_canonical_url）を使う。
 * 一覧はコアが rel=canonical を出さないため、ページ送りを含めた現在のページの URL にする。
 */
function efoo_ogp_url(): ?string {
	if ( is_404() ) {
		return null;
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		$url = wp_get_canonical_url( get_queried_object_id() );
		return $url ? (string) $url : null;
	}
	return get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ), false );
}

/**
 * 表示中のページの OGP の値を決める。
 *
 * @return array{title: string, description: string, url: ?string, type: string, image: array{url: string, width: int, height: int, alt: string}}
 */
function efoo_ogp_data(): array {
	$description = efoo_ogp_site_description();
	$type        = 'website';
	$image       = null;

	if ( is_front_page() ) {
		// 静的なトップは固定ページ（Inspiro 時代の本文）だが、その本文や画像は共有カードに使わない。
		$description = efoo_ogp_site_description();
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$description = efoo_ogp_post_description( $post ) ?: $description;
			$image       = efoo_ogp_thumbnail( $post );
			if ( in_array( $post->post_type, array( 'post', 'portfolio_item' ), true ) ) {
				$type = 'article';
			}
		}
	} elseif ( is_post_type_archive( 'portfolio_item' ) || is_home() ) {
		$description = sprintf( 'efoo株式会社の%sの一覧です。プレスリリース、メディアへの掲載実績、シェフレピ店長ブログなどを掲載しています。', efoo_news_index_title() );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		// portfolio の分類は説明欄に並び順の数値を入れているため、分類の説明欄は使わない。
		$description = sprintf( 'efoo株式会社の「%s」の記事の一覧です。', single_term_title( '', false ) );
	}

	return array(
		'title'       => html_entity_decode( wp_get_document_title(), ENT_QUOTES, 'UTF-8' ),
		'description' => $description,
		'url'         => efoo_ogp_url(),
		'type'        => $type,
		'image'       => $image ?? efoo_ogp_default_image(),
	);
}

add_action( 'wp_head', function () {
	$d = efoo_ogp_data();

	$tags = array(
		array( 'name', 'description', $d['description'] ),
		array( 'property', 'og:title', $d['title'] ),
		array( 'property', 'og:description', $d['description'] ),
		array( 'property', 'og:type', $d['type'] ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:locale', 'ja_JP' ),
		array( 'property', 'og:image', $d['image']['url'], true ),
		array( 'property', 'og:image:width', (string) $d['image']['width'] ),
		array( 'property', 'og:image:height', (string) $d['image']['height'] ),
		array( 'property', 'og:image:alt', $d['image']['alt'] ),
		array( 'name', 'twitter:card', 'summary_large_image' ),
	);
	if ( $d['url'] ) {
		$tags[] = array( 'property', 'og:url', $d['url'], true );
	}

	foreach ( $tags as $tag ) {
		list( $attr, $key, $value ) = $tag;
		$content                    = ! empty( $tag[3] ) ? esc_url( $value ) : esc_attr( $value );
		printf( "<meta %s=\"%s\" content=\"%s\" />\n", $attr, esc_attr( $key ), $content ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
} );
