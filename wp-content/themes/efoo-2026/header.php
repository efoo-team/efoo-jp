<?php
/**
 * 共通ヘッダー。浮遊ピル型のナビ。トップではページ内アンカー、下層ではトップの各セクションへ飛ぶ。
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

<header style="position: sticky; top: 16px; z-index: 60; padding: 0 24px">
	<div class="float-nav" style="display: flex; flex-wrap: wrap; align-items: center; gap: 24px; max-width: 1100px; margin: 0 auto; padding: 10px 12px 10px 30px; box-sizing: border-box; border-radius: 999px; border: 1px solid rgba(226, 231, 227, 0.8); box-shadow: 0 8px 32px rgba(20, 32, 26, 0.08)">
	<a href="<?php echo esc_url( $efoo_home ); ?>" class="en" style="font-size: 26px; font-weight: 700; color: #14201A; letter-spacing: -0.02em">efoo<span class="logo-dot" style="color: var(--accent)">.</span></a>
	<nav style="display: flex; flex-wrap: wrap; gap: 28px; margin-left: auto; align-items: center">
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#services' ); ?>">Services</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#strength' ); ?>">Strength</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#team' ); ?>">Team</a>
		<a class="nav-link" href="<?php echo esc_url( efoo_news_index_url() ); ?>">Contents</a>
		<a class="nav-link" href="<?php echo esc_url( $efoo_prefix . '#contact' ); ?>">会社概要</a>
		<a class="pill-cta" href="<?php echo esc_url( efoo_contact_url() ); ?>" style="background: var(--accent); color: #FAFAF7; font-weight: 700; font-size: 14px; padding: 12px 26px; border-radius: 999px">お問い合わせ</a>
	</nav>
	</div>
</header>
