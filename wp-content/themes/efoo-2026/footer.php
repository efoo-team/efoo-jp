<?php
/**
 * 共通フッター。
 */
?>
<footer style="border-top: 1px solid #E2E7E3; background: #FAFAF7">
	<div style="max-width: 1240px; margin: 0 auto; padding: 64px 32px 48px; box-sizing: border-box; display: flex; flex-wrap: wrap; gap: 48px; align-items: flex-start">
		<div style="display: flex; flex-direction: column; gap: 16px; flex: 1 1 280px; min-width: 0">
			<span class="en" style="font-size: 24px; font-weight: 700; color: #14201A">efoo<span class="logo-dot" style="color: var(--accent)">.</span></span>
			<p style="margin: 0; font-size: 13px; line-height: 1.9; color: #6B776F">efoo株式会社<br>Food × Technology</p>
		</div>
		<nav style="display: flex; gap: 48px; flex-wrap: wrap">
			<div style="display: flex; flex-direction: column; gap: 12px">
				<span class="en" style="font-size: 12px; letter-spacing: 0.2em; color: #6B776F">SERVICES</span>
				<a href="https://chefrepi.com/" style="font-size: 14px">シェフレピ</a>
				<a href="https://l-shift.com/" style="font-size: 14px">Lシフト</a>
				<a href="<?php echo esc_url( home_url( '/#services' ) ); ?>" style="font-size: 14px">システム開発</a>
			</div>
			<div style="display: flex; flex-direction: column; gap: 12px">
				<span class="en" style="font-size: 12px; letter-spacing: 0.2em; color: #6B776F">COMPANY</span>
				<a href="<?php echo esc_url( home_url( '/#team' ) ); ?>" style="font-size: 14px">チーム</a>
				<a href="<?php echo esc_url( efoo_news_index_url() ); ?>" style="font-size: 14px">お知らせ</a>
				<a href="<?php echo esc_url( efoo_contact_url() ); ?>" style="font-size: 14px">お問い合わせ</a>
			</div>
		</nav>
		<div style="display: flex; gap: 16px; margin-left: auto">
			<a class="sns-btn" href="https://x.com/" aria-label="X（旧Twitter）" style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border: 1px solid #C6CEC9; border-radius: 50%"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 4l16 16"></path><path d="M20 4L4 20"></path></svg></a>
			<a class="sns-btn" href="https://www.instagram.com/" aria-label="Instagram" style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border: 1px solid #C6CEC9; border-radius: 50%"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="0.5"></circle></svg></a>
			<a class="sns-btn" href="https://www.youtube.com/" aria-label="YouTube" style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border: 1px solid #C6CEC9; border-radius: 50%"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4"></rect><path d="M10 9l5 3-5 3z"></path></svg></a>
		</div>
	</div>
	<p class="en" aria-hidden="true" style="max-width: 1240px; margin: 0 auto; padding: 0 32px; box-sizing: border-box; font-size: 220px; font-weight: 700; line-height: 0.85; letter-spacing: -0.05em; color: #EDF1EE; user-select: none">efoo<span style="color: #DCEFE2">.</span></p>
	<p class="en" style="max-width: 1240px; margin: 0 auto; padding: 0 32px 40px; box-sizing: border-box; font-size: 12px; color: #6B776F; letter-spacing: 0.06em">© efoo Inc. All rights reserved.</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
