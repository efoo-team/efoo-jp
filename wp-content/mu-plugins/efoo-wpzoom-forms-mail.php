<?php
/**
 * Plugin Name: efoo 問い合わせフォームの通知メール
 * Description: WPZOOM Forms の問い合わせ通知メールの件名・本文・差出人を、公式サイトからの問い合わせだと分かる形にする。プラグイン本体は変更しない。
 */

// 通知メールは wpzoom-forms.php の action_form_post() が wp_mail() で送る。
// パスワード再設定など、ほかの wp_mail() に影響しないよう、この送信処理の最中だけを対象にする。
$efoo_is_contact_mail = static function () {
	return doing_action( 'admin_post_wpzf_submit' ) || doing_action( 'admin_post_nopriv_wpzf_submit' );
};

// 差出人のアドレスは受信者と同じ chefree@efoo.jp にする。実在が確かで、送信失敗の通知も受け取れるため。
$efoo_contact_from = 'efoo.jp お問い合わせフォーム <chefree@efoo.jp>';

add_filter(
	'wp_mail',
	static function ( $args ) use ( $efoo_is_contact_mail, $efoo_contact_from ) {
		if ( ! $efoo_is_contact_mail() ) {
			return $args;
		}

		// 件名：action_form_post() と同じ式で組み立てた末尾の「 -- サイト名」だけを外し、目印を先頭に付ける。
		$subject = (string) $args['subject'];
		$suffix  = sprintf( __( ' -- %s', 'wpzoom-forms' ), sanitize_text_field( get_bloginfo( 'name' ) ) );
		if ( '' !== $suffix && substr( $subject, -strlen( $suffix ) ) === $suffix ) {
			$subject = substr( $subject, 0, -strlen( $suffix ) );
		}
		$args['subject'] = '【efoo.jp お問い合わせ】' . $subject;

		// 本文：HTML の <body> の直後に、どこから届いたかを 1 行入れる。
		$notice = '<p>efoo.jp のお問い合わせフォーム（' . esc_html( home_url( '/contact-us/' ) ) . '）から送信されました。</p>';
		$body   = (string) $args['message'];
		$count  = 0;
		$body   = preg_replace( '/<body[^>]*>/i', '$0' . $notice, $body, 1, $count );
		$args['message'] = $count ? $body : $notice . (string) $args['message'];

		// 差出人：From だけを置き換え、Reply-To（送信者のアドレス）は残す。ヘッダーは文字列でも配列でも渡される。
		$headers = $args['headers'];
		$lines   = is_array( $headers ) ? $headers : preg_split( '/\r\n|\r|\n/', (string) $headers );
		$lines   = array_values(
			array_filter(
				$lines,
				static function ( $line ) {
					return '' !== trim( $line ) && ! preg_match( '/^\s*from\s*:/i', $line );
				}
			)
		);
		$lines[] = 'From: ' . $efoo_contact_from;
		$args['headers'] = is_array( $headers ) ? $lines : implode( "\r\n", $lines );

		return $args;
	}
);

// WordPress は setFrom() で Sender（エンベロープ送信者、Return-Path になる）を設定しない。
// 本番の sendmail_path にも -f が無いため、そのままではサーバー側のアドレスが Return-Path になり、
// From の efoo.jp と DMARC の SPF alignment が揃わない。efoo.jp の SPF は Web サーバーを含むので、From と同じにすれば揃う。
add_action(
	'phpmailer_init',
	static function ( $phpmailer ) use ( $efoo_is_contact_mail ) {
		if ( $efoo_is_contact_mail() ) {
			$phpmailer->Sender = $phpmailer->From;
		}
	}
);
