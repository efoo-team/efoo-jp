<?php
/**
 * 共通ヘッダー。浮遊ピル型のナビ。トップではページ内アンカー、下層ではトップの各セクションへ飛ぶ。
 * 狭い画面ではナビを畳み、メニューボタン（main.js）で開閉する。
 */
$efoo_home   = home_url( '/' );
$efoo_prefix = is_front_page() ? '' : $efoo_home;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
	<div class="float-nav">
	<a href="<?php echo esc_url( $efoo_home ); ?>" class="en" style="font-size: 26px; font-weight: 700; color: #14201A; letter-spacing: -0.02em">efoo<span class="logo-dot" style="color: var(--accent)">.</span></a>
	<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="メニューを開く"><span class="nav-toggle-bars" aria-hidden="true"></span></button>
	<nav id="site-nav" class="site-nav" aria-label="メインメニュー">
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#services' ); ?>">Services</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#strength' ); ?>">Strength</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#team' ); ?>">Team</a>
		<a class="nav-link" href="<?php echo esc_url( efoo_news_index_url() ); ?>">Contents</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#contact' ); ?>">会社概要</a>
		<a class="pill-cta" href="<?php echo esc_url( efoo_contact_url() ); ?>" style="background: var(--accent); color: #FAFAF7; font-weight: 700; font-size: 14px; padding: 12px 26px; border-radius: 999px">お問い合わせ</a>
	</nav>
	</div>
</header>
