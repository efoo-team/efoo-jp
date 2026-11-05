<?php
/**
 * Plugin Name: efoo 問い合わせフォームの文言
 * Description: WPZOOM Forms がフォームの送信後に表示する英語の文言を日本語にする。プラグイン本体は変更しない。
 */

add_filter(
	'gettext',
	static function ( $translation, $text, $domain ) {
		// フォーム 239 は旧形式（ブロックで組んだフォーム）のため、送信後の通知は wpzoom-forms.php の
		// form_block_render() が出力する。フォームの投稿に文言（_form_success_message・_form_failure_message）が
		// 無いので、__( 'Thanks! We\'ve received your submission!', 'wpzoom-forms' ) と
		// __( 'Submission failed!', 'wpzoom-forms' ) の既定の文言が使われる。
		if ( 'wpzoom-forms' !== $domain ) {
			return $translation;
		}

		$replacements = array(
			"Thanks! We've received your submission!" => 'お問い合わせを送信しました。',
			'Submission failed!'                      => '送信できませんでした。お手数ですが、時間をおいてもう一度お試しください。',
		);

		return isset( $replacements[ $text ] ) ? $replacements[ $text ] : $translation;
	},
	10,
	3
);
