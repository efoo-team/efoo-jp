<?php
/**
 * トップページ。デザインカンバス「efoo.jp リデザイン案」の案Aを実装したもの。
 */
$img     = get_theme_file_uri( 'assets/img' );
$uploads = content_url( 'uploads' );
get_header();
?>

<!-- ヒーロー -->
<section id="top" style="position: relative; margin-top: -62px; padding: 140px 32px 120px; box-sizing: border-box; overflow: hidden">
	<div aria-hidden="true" style="position: absolute; inset: 0; background-image: linear-gradient(rgba(21, 128, 61, 0.07) 1px, transparent 1px), linear-gradient(90deg, rgba(21, 128, 61, 0.07) 1px, transparent 1px); background-size: 56px 56px; mask-image: radial-gradient(ellipse 80% 70% at 50% 30%, #000 30%, transparent 75%)"></div>
	<div aria-hidden="true" class="glow" style="position: absolute; top: -140px; left: 50%; transform: translateX(-50%); width: 760px; height: 480px; background: radial-gradient(closest-side, rgba(21, 128, 61, 0.10), transparent); filter: blur(10px)"></div>
	<p class="en ghost" aria-hidden="true" style="position: absolute; top: 24px; right: -30px; margin: 0; font-size: 200px; font-weight: 700; letter-spacing: -0.04em; line-height: 0.95; text-align: right; user-select: none">TECH</p>
	<div aria-hidden="true" class="dot" style="width: 6px; height: 6px; top: 180px; left: 16%"></div>
	<div aria-hidden="true" class="dot" style="width: 4px; height: 4px; top: 320px; right: 14%; animation-delay: 1.6s"></div>
	<div aria-hidden="true" class="dot" style="width: 5px; height: 5px; top: 480px; left: 24%; animation-delay: 3.2s"></div>
	<div style="position: relative; max-width: 1240px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px">
		<div style="display: flex; flex-wrap: wrap; gap: 48px; align-items: center">
		<div style="flex: 1 1 520px; min-width: 0; display: flex; flex-direction: column; gap: 32px; align-items: flex-start">
			<p class="en" style="margin: 0; min-height: 22px; color: var(--accent); font-size: 15px; font-weight: 500; letter-spacing: 0.32em"><span id="hero-label"></span><span class="cursor" aria-hidden="true"></span></p>
			<h1 style="margin: 0; font-size: 84px; line-height: 1.18; font-weight: 900; letter-spacing: 0.01em; color: #14201A"><span class="char" style="animation-delay: .05s">食</span><span class="char" style="animation-delay: .1s">の</span><span class="char" style="animation-delay: .15s">世</span><span class="char" style="animation-delay: .2s">界</span><span class="char" style="animation-delay: .25s">を</span><span class="char" style="animation-delay: .3s">、</span><br><span class="char" style="animation-delay: .4s">も</span><span class="char" style="animation-delay: .45s">っ</span><span class="char" style="animation-delay: .5s">と</span><span style="position: relative; display: inline-block"><span id="pt-field" aria-hidden="true" style="position: absolute; left: 2%; top: 9%; width: 96%; height: 83%; pointer-events: none"></span><span class="word-assemble">前へ</span></span><span class="char" style="animation-delay: 2s">。</span></h1>
			<p class="reveal d3" style="margin: 0; max-width: 620px; font-size: 18px; line-height: 2; color: #44504A">エンジニアリングとAIを軸に、食の現場の課題を解く。自社プロダクトの開発・運用で磨いた技術力と、現場を知る料理人の知見を、ひとつのチームで提供します。</p>
			<div class="reveal d4" style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 8px">
				<a class="btn-primary" href="#contact">開発の相談をする<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></a>
				<a class="btn-ghost" href="#services">サービスを見る</a>
			</div>
		</div>
		<div class="reveal d4" style="flex: 1 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 20px">
			<div class="term" aria-hidden="true">
				<div class="term-head">
					<span class="term-dot" style="background: #E27878"></span>
					<span class="term-dot" style="background: #E2A478"></span>
					<span class="term-dot" style="background: #B4BE82"></span>
				</div>
				<p class="mono term-body"><span id="term-lines"></span><span class="term-line"><span style="color: #B4BE82">➜ </span><span style="color: #89B8C2">~/efoo </span><span style="color: #A093C7">❯ </span><span style="color: #C6C8D1" id="term-typed"></span><span class="cursor term-cursor" aria-hidden="true"></span></span></p>
			</div>
			<figure style="margin: 0; position: relative">
				<video id="pr-video" src="<?php echo esc_url( $uploads . '/2024/05/PR%E5%8B%95%E7%94%BB.mp4' ); ?>" poster="<?php echo esc_url( $uploads . '/2024/05/P8A9118-1024x683.jpg' ); ?>" autoplay muted loop playsinline aria-label="調理レッスンのPR動画" style="display: block; width: 100%; aspect-ratio: 2132 / 1200; object-fit: cover; background: #0D1310; border-radius: 20px; box-shadow: 0 24px 56px rgba(20, 32, 26, 0.16)"></video>
				<span class="cv-corner" aria-hidden="true" style="top: 10px; left: 10px; border-top-width: 1.5px; border-left-width: 1.5px"></span>
				<span class="cv-corner" aria-hidden="true" style="top: 10px; right: 10px; border-top-width: 1.5px; border-right-width: 1.5px"></span>
				<span class="cv-corner" aria-hidden="true" style="bottom: 10px; left: 10px; border-bottom-width: 1.5px; border-left-width: 1.5px"></span>
				<span class="cv-corner" aria-hidden="true" style="bottom: 10px; right: 10px; border-bottom-width: 1.5px; border-right-width: 1.5px"></span>
				<span id="cv-layer" aria-hidden="true"></span>
				<figcaption class="mono cv-chip"><span class="live-dot" aria-hidden="true"></span>AI VISION — LIVE</figcaption>
			</figure>
		</div>
		</div>
	</div>
	<div aria-hidden="true" class="en" style="position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 8px; color: #6B776F; font-size: 10px; letter-spacing: 0.3em">SCROLL<span class="scroll-line"></span></div>
</section>

<!-- マーキー -->
<div style="border-top: 1px solid #E2E7E3; border-bottom: 1px solid #E2E7E3; padding: 18px 0; display: flex; flex-direction: column; gap: 10px; overflow: hidden" aria-hidden="true">
	<div style="display: flex">
		<div class="marquee-inner en" style="color: #6B776F; font-size: 16px; font-weight: 500; letter-spacing: 0.22em; white-space: nowrap; padding-right: 64px">
			<span>CHEFREPI</span><span style="color: var(--accent)">●</span><span>L-SHIFT</span><span style="color: var(--accent)">●</span><span>AI WORKFLOW</span><span style="color: var(--accent)">●</span><span>EC SITE</span><span style="color: var(--accent)">●</span><span>CORPORATE SITE</span><span style="color: var(--accent)">●</span><span>BUSINESS SYSTEM</span><span style="color: var(--accent)">●</span>
			<span>CHEFREPI</span><span style="color: var(--accent)">●</span><span>L-SHIFT</span><span style="color: var(--accent)">●</span><span>AI WORKFLOW</span><span style="color: var(--accent)">●</span><span>EC SITE</span><span style="color: var(--accent)">●</span><span>CORPORATE SITE</span><span style="color: var(--accent)">●</span><span>BUSINESS SYSTEM</span><span style="color: var(--accent)">●</span>
			<span>CHEFREPI</span><span style="color: var(--accent)">●</span><span>L-SHIFT</span><span style="color: var(--accent)">●</span><span>AI WORKFLOW</span><span style="color: var(--accent)">●</span><span>EC SITE</span><span style="color: var(--accent)">●</span><span>CORPORATE SITE</span><span style="color: var(--accent)">●</span><span>BUSINESS SYSTEM</span><span style="color: var(--accent)">●</span>
			<span>CHEFREPI</span><span style="color: var(--accent)">●</span><span>L-SHIFT</span><span style="color: var(--accent)">●</span><span>AI WORKFLOW</span><span style="color: var(--accent)">●</span><span>EC SITE</span><span style="color: var(--accent)">●</span><span>CORPORATE SITE</span><span style="color: var(--accent)">●</span><span>BUSINESS SYSTEM</span><span style="color: var(--accent)">●</span>
		</div>
	</div>
	<div style="display: flex">
		<div class="marquee-inner marquee-rev en ghost" style="white-space: nowrap; padding-right: 64px">
			<span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span><span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span>
			<span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span><span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span>
			<span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span><span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span>
			<span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span><span>FOOD × TECHNOLOGY</span><span>UPDATE THE FOOD WORLD</span>
		</div>
	</div>
</div>

<!-- サービス（スライダー） -->
<section id="services" style="max-width: 1240px; margin: 0 auto; padding: 120px 32px 100px; box-sizing: border-box; display: flex; flex-direction: column; gap: 56px">
	<div class="sr" style="display: flex; flex-direction: column; gap: 14px">
		<p class="en" style="margin: 0; color: var(--accent); font-size: 14px; font-weight: 500; letter-spacing: 0.3em">OUR SERVICES</p>
		<h2 style="margin: 0; font-size: 44px; font-weight: 900; color: #14201A">自社サービスと、食に精通した開発</h2>
	</div>
	<div class="svc-wrap">
	<div class="svc-track">
		<article class="card sr" style="flex: 0 0 440px; scroll-snap-align: start; display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 20px; overflow: hidden; box-sizing: border-box">
			<div style="position: relative; overflow: hidden">
				<img class="srv-img" src="<?php echo esc_url( $uploads . '/2024/05/P8A9118-1024x683.jpg' ); ?>" alt="シェフレピのレッスン動画を見ながら調理する様子" style="display: block; width: 100%; height: 210px; object-fit: cover">
				<span class="en" aria-hidden="true" style="position: absolute; right: 14px; bottom: 2px; font-size: 68px; font-weight: 700; letter-spacing: -0.04em; color: transparent; -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.9)">01</span>
			</div>
			<div style="display: flex; flex-direction: column; gap: 16px; padding: 26px 28px 30px; flex-grow: 1">
			<h3 style="margin: 0; font-size: 26px; font-weight: 700; color: #14201A">シェフレピ<span class="en" style="display: block; margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--accent); letter-spacing: 0.2em">CHEFREPI</span></h3>
			<p style="margin: 0; font-size: 15px; line-height: 1.9; color: #44504A">140種類以上のシェフの料理が学べるオンライン料理教室。レッスン動画に加え、材料がすべて揃うミールキットも購入できます。</p>
			<div style="display: flex; flex-wrap: wrap; gap: 8px">
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">動画レッスン</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">ミールキット</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">EC販売</span>
			</div>
			<a href="https://chefrepi.com/" style="margin-top: auto; display: inline-flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: #14201A">公式サイトへ<span class="card-arrow" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; border: 1px solid #C6CEC9"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7"></path><path d="M9 7h8v8"></path></svg></span></a>
			</div>
		</article>
		<article class="card sr sr2" style="flex: 0 0 440px; scroll-snap-align: start; display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 20px; overflow: hidden; box-sizing: border-box">
			<div style="position: relative; overflow: hidden">
				<div class="srv-img" style="position: relative; height: 210px; background: linear-gradient(135deg, #E9F3EB, #D7EBDD); overflow: hidden" role="img" aria-label="Lシフトの画面（シフト管理のWeb画面、スマホの打刻画面、承認通知）">
					<div style="position: absolute; top: -14px; left: -30px; width: 118%; border-radius: 10px; box-shadow: 0 16px 40px rgba(20, 50, 30, 0.16); overflow: hidden; transform: rotate(-1.5deg)"><img src="<?php echo esc_url( $img . '/desktop-shifts.webp' ); ?>" alt="" style="display: block; width: 100%; opacity: 0.92; margin-top: -4%"></div>
					<div class="phone" style="--pw: 118px; position: absolute; right: 100px; top: 26px; --phone-shadow: 0 18px 36px rgba(20, 50, 30, 0.35)" aria-hidden="true">
						<div class="phone-screen">
							<div class="phone-status">
								<span class="phone-time">9:41</span>
								<span class="phone-island"></span>
								<svg class="phone-icons" viewBox="0 0 68 12" fill="currentColor"><rect x="0" y="8" width="3" height="4" rx="0.8"></rect><rect x="4.6" y="5.6" width="3" height="6.4" rx="0.8"></rect><rect x="9.2" y="3" width="3" height="9" rx="0.8"></rect><rect x="13.8" y="0.4" width="3" height="11.6" rx="0.8"></rect><path d="M30 1a11 11 0 0 1 8 3.4l-1.5 1.5A8.9 8.9 0 0 0 30 3.1a8.9 8.9 0 0 0-6.5 2.8L22 4.4A11 11 0 0 1 30 1zm0 4.2a6.8 6.8 0 0 1 5 2.2l-1.5 1.5a4.7 4.7 0 0 0-7 0L25 7.4a6.8 6.8 0 0 1 5-2.2zm0 4.2a2.6 2.6 0 0 1 2 .9L30 12.3l-2-2a2.6 2.6 0 0 1 2-.9z"></path><rect x="43.5" y="0.9" width="20" height="10.2" rx="3" fill="none" stroke="currentColor" stroke-opacity="0.4"></rect><rect x="45.2" y="2.6" width="14" height="6.8" rx="1.6"></rect><path d="M65.2 4.2c.9.3 1.5 1 1.5 1.8s-.6 1.5-1.5 1.8z" fill-opacity="0.45"></path></svg>
							</div>
							<img src="<?php echo esc_url( $img . '/phone-clock.webp' ); ?>" alt="">
							<div class="phone-home"><span></span></div>
						</div>
					</div>
					<div style="position: absolute; left: 16px; bottom: 14px; display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.95); border-radius: 10px; padding: 9px 14px; box-shadow: 0 10px 26px rgba(20, 50, 30, 0.18)">
						<span style="display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: #15803D; color: #FFFFFF"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4 10-10"></path></svg></span>
						<span style="font-size: 11px; font-weight: 700; color: #14201A">シフト申請が承認されました</span>
					</div>
					<span class="en" aria-hidden="true" style="position: absolute; right: 14px; bottom: 2px; font-size: 68px; font-weight: 700; letter-spacing: -0.04em; color: transparent; -webkit-text-stroke: 1.5px rgba(21, 128, 61, 0.55)">02</span>
				</div>
			</div>
			<div style="display: flex; flex-direction: column; gap: 16px; padding: 26px 28px 30px; flex-grow: 1">
			<h3 style="margin: 0; font-size: 26px; font-weight: 700; color: #14201A">Lシフト<span class="en" style="display: block; margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--accent); letter-spacing: 0.2em">L-SHIFT</span></h3>
			<p style="margin: 0; font-size: 15px; line-height: 1.9; color: #44504A">LINEを活用した新しいシフト管理プラットフォーム。スタッフは使い慣れたLINEで、管理者は直感的なWeb画面で。煩雑な業務を自動化します。</p>
			<div style="display: flex; flex-wrap: wrap; gap: 8px">
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">シフト管理</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">LINE連携</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">勤怠・打刻</span>
			</div>
			<a href="https://l-shift.com/" style="margin-top: auto; display: inline-flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: #14201A">サービス詳細<span class="card-arrow" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; border: 1px solid #C6CEC9"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7"></path><path d="M9 7h8v8"></path></svg></span></a>
			</div>
		</article>
		<article class="card sr sr3" style="flex: 0 0 440px; scroll-snap-align: start; display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 20px; overflow: hidden; box-sizing: border-box">
			<div style="position: relative; overflow: hidden">
				<img class="srv-img" src="<?php echo esc_url( $img . '/dev-editor.jpg' ); ?>" alt="LシフトのTypeScriptコードを開いたエディタ画面" style="display: block; width: 100%; height: 210px; object-fit: cover; object-position: 10% 42%">
				<span class="en" aria-hidden="true" style="position: absolute; right: 14px; bottom: 2px; font-size: 68px; font-weight: 700; letter-spacing: -0.04em; color: transparent; -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.9)">03</span>
			</div>
			<div style="display: flex; flex-direction: column; gap: 16px; padding: 26px 28px 30px; flex-grow: 1">
			<h3 style="margin: 0; font-size: 26px; font-weight: 700; color: #14201A">システム開発<span class="en" style="display: block; margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--accent); letter-spacing: 0.2em">DEVELOPMENT</span></h3>
			<p style="margin: 0; font-size: 15px; line-height: 1.9; color: #44504A">食とシステムに精通したチームが、飲食・食品企業の課題を解くシステムを開発。AIワークフロー、ECサイト、コーポレートサイト、業務システムまで。</p>
			<div style="display: flex; flex-wrap: wrap; gap: 8px">
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">AIワークフロー</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">ECサイト</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">業務システム</span>
			</div>
			<a href="#contact" style="margin-top: auto; display: inline-flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: #14201A">相談する<span class="card-arrow" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; border: 1px solid #C6CEC9"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7"></path><path d="M9 7h8v8"></path></svg></span></a>
			</div>
		</article>
		<article class="card sr sr3" style="flex: 0 0 440px; scroll-snap-align: start; display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 20px; overflow: hidden; box-sizing: border-box">
			<div style="position: relative; overflow: hidden">
				<img class="srv-img" src="<?php echo esc_url( $img . '/video-frame.jpg' ); ?>" alt="制作したPR動画のワンシーン（ステーキの調理）" style="display: block; width: 100%; height: 210px; object-fit: cover">
				<span aria-hidden="true" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 54px; height: 54px; border-radius: 50%; background: rgba(250, 250, 247, 0.9); display: inline-flex; align-items: center; justify-content: center; color: #14201A"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5l12 7-12 7z"></path></svg></span>
				<span class="en" aria-hidden="true" style="position: absolute; right: 14px; bottom: 2px; font-size: 68px; font-weight: 700; letter-spacing: -0.04em; color: transparent; -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.9)">04</span>
			</div>
			<div style="display: flex; flex-direction: column; gap: 16px; padding: 26px 28px 30px; flex-grow: 1">
			<h3 style="margin: 0; font-size: 26px; font-weight: 700; color: #14201A">動画制作<span class="en" style="display: block; margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--accent); letter-spacing: 0.2em">VIDEO PRODUCTION</span></h3>
			<p style="margin: 0; font-size: 15px; line-height: 1.9; color: #44504A">サービスや店舗の魅力を伝える動画を、企画から撮影・編集まで一貫して制作。シェフレピのレッスン動画やPR動画で培った、食の映像表現を活かします。</p>
			<div style="display: flex; flex-wrap: wrap; gap: 8px">
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">企画・構成</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">撮影</span>
				<span style="font-size: 11px; font-weight: 500; color: #44504A; background: #F1F4F0; border-radius: 999px; padding: 5px 12px">編集</span>
			</div>
			<a href="#contact" style="margin-top: auto; display: inline-flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: #14201A">相談する<span class="card-arrow" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; border: 1px solid #C6CEC9"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7"></path><path d="M9 7h8v8"></path></svg></span></a>
			</div>
		</article>
	</div>
	<div aria-hidden="true" class="svc-fade"></div>
	</div>
	<div class="sr" style="display: flex; align-items: center; gap: 28px">
		<div style="flex: 1 1 auto; height: 2px; background: #E2E7E3; border-radius: 999px; overflow: hidden"><div id="svc-bar" style="height: 100%; background: var(--accent); width: 25%; transition: width .25s ease"></div></div>
		<div style="display: flex; gap: 10px">
			<button type="button" id="svc-prev" class="nav-btn" aria-label="前のサービスへ" style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #C6CEC9; background: #FFFFFF; color: #14201A; display: inline-flex; align-items: center; justify-content: center; cursor: pointer"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"></path></svg></button>
			<button type="button" id="svc-next" class="nav-btn" aria-label="次のサービスへ" style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #C6CEC9; background: #FFFFFF; color: #14201A; display: inline-flex; align-items: center; justify-content: center; cursor: pointer"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"></path></svg></button>
		</div>
	</div>
</section>

<!-- 強み -->
<section id="strength" style="background: #F1F4F0; border-top: 1px solid #E2E7E3; border-bottom: 1px solid #E2E7E3">
	<div style="max-width: 1240px; margin: 0 auto; padding: 110px 32px; box-sizing: border-box; display: flex; flex-wrap: wrap; gap: 64px">
		<div class="sr" style="flex: 1 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 18px">
			<p class="en" style="margin: 0; color: var(--accent); font-size: 14px; font-weight: 500; letter-spacing: 0.3em">WHY EFOO</p>
			<h2 style="margin: 0; font-size: 42px; line-height: 1.4; font-weight: 900; color: #14201A">現場を知る料理人と、<br>作れるエンジニアが<br>同じチームにいる。</h2>
			<p style="margin: 0; font-size: 16px; line-height: 2; color: #44504A">要件を聞くだけの開発会社ではありません。厨房と経営の現場を経験したメンバーが、業務の文脈ごと理解して設計します。</p>
		</div>
		<div class="sr" style="flex: 1 1 480px; min-width: 0; display: flex; flex-direction: column; gap: 20px">
			<div style="display: flex; gap: 24px; padding: 28px; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 14px">
				<span class="en" style="color: var(--accent); font-size: 18px; font-weight: 700">01</span>
				<div style="display: flex; flex-direction: column; gap: 8px">
					<h3 style="margin: 0; font-size: 19px; font-weight: 700; color: #14201A">食の現場理解 × 技術力</h3>
					<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">CEOは元フランス料理人、CTOはシェフレピを立ち上げたエンジニア。発注者の言葉と現場の実態を翻訳なしで理解します。</p>
				</div>
			</div>
			<div style="display: flex; gap: 24px; padding: 28px; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 14px">
				<span class="en" style="color: var(--accent); font-size: 18px; font-weight: 700">02</span>
				<div style="display: flex; flex-direction: column; gap: 8px">
					<h3 style="margin: 0; font-size: 19px; font-weight: 700; color: #14201A">自社サービスで磨いた開発力</h3>
					<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">シェフレピとLシフトを自ら開発・運営。企画から運用まで、事業として回してきた経験をそのまま提供します。</p>
				</div>
			</div>
			<div style="display: flex; gap: 24px; padding: 28px; background: #FFFFFF; border: 1px solid #E2E7E3; border-radius: 14px">
				<span class="en" style="color: var(--accent); font-size: 18px; font-weight: 700">03</span>
				<div style="display: flex; flex-direction: column; gap: 8px">
					<h3 style="margin: 0; font-size: 19px; font-weight: 700; color: #14201A">AIを前提にした設計</h3>
					<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">AIワークフローの構築を含め、これからの業務に合わせたシステムを提案。作って終わりではなく、改善まで伴走します。</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- チーム -->
<section id="team" style="max-width: 1240px; margin: 0 auto; padding: 120px 32px 100px; box-sizing: border-box; display: flex; flex-direction: column; gap: 56px">
	<div class="sr" style="display: flex; flex-direction: column; gap: 14px">
		<p class="en" style="margin: 0; color: var(--accent); font-size: 14px; font-weight: 500; letter-spacing: 0.3em">OUR TEAM</p>
		<h2 style="margin: 0; font-size: 44px; font-weight: 900; color: #14201A">食とテクノロジーの両方のプロ</h2>
	</div>
	<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 28px">
		<article class="sr" style="display: flex; flex-direction: column; gap: 18px">
			<div class="tphoto"><img src="<?php echo esc_url( $uploads . '/2024/05/2-1024x683.jpg' ); ?>" alt="山本 篤の写真" style="display: block; width: 100%; height: 300px; object-fit: cover"></div>
			<div style="display: flex; flex-direction: column; gap: 6px">
				<p class="en" style="margin: 0; color: var(--accent); font-size: 12px; font-weight: 500; letter-spacing: 0.24em">CEO</p>
				<h3 style="margin: 0; font-size: 22px; font-weight: 700; color: #14201A">山本 篤</h3>
				<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">辻調理師専門学校を卒業後、フランス料理店で修業。店舗の立ち上げ・運営を経てエンジニアリングを学び、efooを創業。</p>
			</div>
		</article>
		<article class="sr sr2" style="display: flex; flex-direction: column; gap: 18px">
			<div class="tphoto"><img src="<?php echo esc_url( $uploads . '/2024/05/1-1-1024x683.jpg' ); ?>" alt="阿部 圭史の写真" style="display: block; width: 100%; height: 300px; object-fit: cover"></div>
			<div style="display: flex; flex-direction: column; gap: 6px">
				<p class="en" style="margin: 0; color: var(--accent); font-size: 12px; font-weight: 500; letter-spacing: 0.24em">CTO</p>
				<h3 style="margin: 0; font-size: 22px; font-weight: 700; color: #14201A">阿部 圭史</h3>
				<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">仙台高専・東北大学大学院で研究とシステム開発に従事。創業当初から参画し、シェフレピの開発を牽引して2021年にサービスイン。</p>
			</div>
		</article>
		<article class="sr sr3" style="display: flex; flex-direction: column; gap: 18px">
			<div class="tphoto"><img src="<?php echo esc_url( $uploads . '/2024/05/IMG_6274-1024x683.jpg' ); ?>" alt="池田 亮太の写真" style="display: block; width: 100%; height: 300px; object-fit: cover"></div>
			<div style="display: flex; flex-direction: column; gap: 6px">
				<p class="en" style="margin: 0; color: var(--accent); font-size: 12px; font-weight: 500; letter-spacing: 0.24em">OPERATIONS MANAGER</p>
				<h3 style="margin: 0; font-size: 22px; font-weight: 700; color: #14201A">池田 亮太</h3>
				<p style="margin: 0; font-size: 14px; line-height: 1.9; color: #44504A">調理師専門学校を卒業後、ホテルやD2Cブランドで料理と店舗運営を経験。飲食業に活かせる技術を身につけ、オペレーションを統括。</p>
			</div>
		</article>
	</div>
</section>

<!-- コンテンツ（お知らせ） -->
<section id="contents" style="background: #F1F4F0; border-top: 1px solid #E2E7E3">
	<div style="max-width: 1240px; margin: 0 auto; padding: 110px 32px; box-sizing: border-box; display: flex; flex-direction: column; gap: 48px">
		<div style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 24px">
			<div class="sr" style="display: flex; flex-direction: column; gap: 14px">
				<p class="en" style="margin: 0; color: var(--accent); font-size: 14px; font-weight: 500; letter-spacing: 0.3em">CONTENTS</p>
				<h2 style="margin: 0; font-size: 44px; font-weight: 900; color: #14201A">お知らせ・掲載実績</h2>
			</div>
			<a href="<?php echo esc_url( efoo_news_index_url() ); ?>" style="margin-left: auto; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #14201A">一覧を見る<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></a>
		</div>
		<div style="display: flex; flex-direction: column">
			<?php
			$news = new WP_Query(
				array(
					'post_type'           => efoo_news_post_types(),
					'posts_per_page'      => 5,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $news->have_posts() ) {
				while ( $news->have_posts() ) {
					$news->the_post();
					$label = efoo_entry_term_label();
					?>
			<a class="news-row sr" href="<?php the_permalink(); ?>" style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px; padding: 20px; border-top: 1px solid #E2E7E3">
				<?php if ( has_post_thumbnail() ) { ?>
				<span class="news-thumb"><?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?></span>
				<?php } ?>
				<span class="en" style="color: #6B776F; font-size: 13px; letter-spacing: 0.08em"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
				<span style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: var(--accent); border: 1px solid var(--accent); border-radius: 999px; padding: 4px 12px"><?php echo esc_html( $label ); ?></span>
				<span class="news-title" style="flex: 1 1 400px; min-width: 0; font-size: 15px; color: #14201A"><?php the_title(); ?></span>
			</a>
					<?php
				}
				wp_reset_postdata();
			}
			?>
			<span style="border-top: 1px solid #E2E7E3"></span>
		</div>
	</div>
</section>

<!-- CTA -->
<section id="contact" style="position: relative; padding: 140px 32px; box-sizing: border-box; overflow: hidden">
	<img aria-hidden="true" src="<?php echo esc_url( $uploads . '/2024/08/R63447-scaled.jpg' ); ?>" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover">
	<div aria-hidden="true" style="position: absolute; inset: 0; background: rgba(10, 15, 13, 0.74)"></div>
	<div class="sr" style="position: relative; max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 28px; text-align: center">
		<p class="en" style="margin: 0; color: #86EFAC; font-size: 14px; font-weight: 500; letter-spacing: 0.3em">CONTACT</p>
		<h2 style="margin: 0; font-size: 52px; line-height: 1.4; font-weight: 900; color: #FFFFFF">食 × テクノロジーの課題、<br>一緒に解きませんか。</h2>
		<p style="margin: 0; font-size: 16px; line-height: 2; color: #D7DEDA">Webサイト制作からAIワークフロー・業務システムまで。まずは課題をお聞かせください。</p>
		<a class="btn-primary" href="<?php echo esc_url( efoo_contact_url() ); ?>" style="margin-top: 8px; font-size: 16px; padding: 20px 44px">お問い合わせはこちら<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></a>
	</div>
</section>

<?php
get_footer();
