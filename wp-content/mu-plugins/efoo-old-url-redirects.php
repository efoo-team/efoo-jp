<?php
/**
 * Plugin Name: efoo 旧 URL の転送
 * Description: 投稿タイプ portfolio_item は階層型で登録されているため、WordPress は slug を変えても旧 slug を _wp_old_slug に保存せず、旧 URL を転送しない。旧パスから新パスへの対応表をこのファイルで持ち、301 で転送する。
 */

add_action(
	'template_redirect',
	static function () {
		// 404 のときだけ動かす。存在するページの表示には影響しない。
		if ( ! is_404() ) {
			return;
		}

		$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

		// 旧パス（先頭と末尾の / を除いた形） => 新パス。
		// portfolio_item の slug を変えたら、旧 slug の行をここに足す。
		$redirects = array(
			'project/test1'        => '/project/chefrepi-economy/',
			'project/test1/file-4' => '/project/chefrepi-economy/file-4/',
		);

		if ( ! isset( $redirects[ $path ] ) ) {
			return;
		}

		wp_safe_redirect( home_url( $redirects[ $path ] ), 301, 'efoo-old-url' );
		exit;
	},
	1
);
