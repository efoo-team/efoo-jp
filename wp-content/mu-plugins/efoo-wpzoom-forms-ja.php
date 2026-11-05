<?php
/**
 * Plugin Name: efoo 問い合わせフォームの文言
 * Description: WPZOOM Forms がフォームの送信後に表示する英語の文言を日本語にする。プラグイン本体は変更しない。
 */

add_filter(
	'gettext',
	static function ( $translation, $text, $domain ) {
		// 送信後の通知は wpzoom-forms.php の form_block_render() が
		// __( 'Submitted successfully!', 'wpzoom-forms' ) と
		// __( 'Submission failed!', 'wpzoom-forms' ) で出力する。
		if ( 'wpzoom-forms' !== $domain ) {
			return $translation;
		}

		$replacements = array(
			'Submitted successfully!' => 'お問い合わせを送信しました。',
			'Submission failed!'      => '送信できませんでした。お手数ですが、時間をおいてもう一度お試しください。',
		);

		return isset( $replacements[ $text ] ) ? $replacements[ $text ] : $translation;
	},
	10,
	3
);
