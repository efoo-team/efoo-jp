// efoo 2026 トップページの演出。タイピング・粒子集合・動画の物体検出風オーバーレイ・サービススライダー
(function () {
	'use strict';

	var REDUCE = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function wait(ms) {
		return new Promise(function (res) { setTimeout(res, ms); });
	}

	// ページ内アンカーだけを滑らかにスクロールする。CSS の scroll-behavior を全体に
	// 掛けるとページ遷移時のスクロール復元までアニメーションしてしまうため、クリック時に限る
	document.addEventListener('click', function (e) {
		var a = e.target.closest ? e.target.closest('a[href*="#"]') : null;
		if (!a) { return; }
		var url = new URL(a.href, location.href);
		if (url.pathname !== location.pathname || url.origin !== location.origin || !url.hash) { return; }
		var target = document.querySelector(url.hash);
		if (!target) { return; }
		e.preventDefault();
		target.scrollIntoView({ behavior: REDUCE ? 'auto' : 'smooth', block: 'start' });
		history.pushState(null, '', url.hash);
	});

	// 狭い画面のメニュー開閉。Esc・メニュー外のクリック・メニュー内のリンクで閉じる
	var siteHeader = document.querySelector('.site-header');
	var navToggle = siteHeader && siteHeader.querySelector('.nav-toggle');
	if (navToggle) {
		var setNavOpen = function (open) {
			siteHeader.classList.toggle('is-open', open);
			navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			navToggle.setAttribute('aria-label', open ? 'メニューを閉じる' : 'メニューを開く');
		};
		navToggle.addEventListener('click', function () {
			setNavOpen(navToggle.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && siteHeader.classList.contains('is-open')) {
				setNavOpen(false);
				navToggle.focus();
			}
		});
		document.addEventListener('click', function (e) {
			if (!siteHeader.classList.contains('is-open')) { return; }
			if (!siteHeader.contains(e.target) || e.target.closest('.site-nav a')) { setNavOpen(false); }
		});
		// ナビを畳む幅（main.css の max-width: 860px と同じ値）を出たら、閉じた状態に戻す
		var navCollapsed = window.matchMedia && window.matchMedia('(max-width: 860px)');
		if (navCollapsed) {
			var onNavMedia = function (e) { if (!e.matches) { setNavOpen(false); } };
			if (navCollapsed.addEventListener) { navCollapsed.addEventListener('change', onNavMedia); }
			else if (navCollapsed.addListener) { navCollapsed.addListener(onNavMedia); }
		}
	}

	// スクロール連動出現のフォールバック（animation-timeline 未対応ブラウザ）。
	// 動きを減らす設定では CSS が出現の演出を適用しないため、ここでも何もしない
	if (!REDUCE && !(window.CSS && CSS.supports && CSS.supports('animation-timeline: view()'))) {
		document.documentElement.classList.add('no-sdt');
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (e) {
					if (e.isIntersecting) {
						e.target.classList.add('sr-in');
						io.unobserve(e.target);
					}
				});
			}, { rootMargin: '0px 0px -15% 0px' });
			document.querySelectorAll('.sr').forEach(function (el) { io.observe(el); });
		} else {
			document.querySelectorAll('.sr').forEach(function (el) { el.classList.add('sr-in'); });
		}
	}

	// ヒーローのラベルのタイピング
	var label = document.getElementById('hero-label');
	var LABEL_TEXT = 'TECHNOLOGY × FOOD';
	if (label) {
		if (REDUCE) {
			label.textContent = LABEL_TEXT;
		} else {
			(async function () {
				await wait(350);
				for (var i = 1; i <= LABEL_TEXT.length; i++) {
					label.textContent = LABEL_TEXT.slice(0, i);
					await wait(55 + Math.random() * 45);
				}
			})();
		}
	}

	// 「前へ」の粒子集合。座標は見出しのグリフからサンプリング済み
	var PT_TARGETS = [[8.5,3.9],[11.2,3.8],[13.2,4.3],[14.6,4.5],[16.7,4.4],[29.9,3.8],[32.7,5.0],[34.4,4.0],[37.4,5.2],[11.1,8.8],[13.9,8.2],[15.9,8.6],[16.9,8.3],[28.1,9.4],[30.1,9.1],[33.0,8.7],[35.0,8.2],[-0.7,12.9],[2.5,13.3],[4.1,13.5],[6.5,13.1],[9.2,13.7],[10.5,13.5],[13.2,14.0],[15.7,13.1],[18.3,12.8],[19.5,13.8],[21.3,13.4],[23.3,13.7],[26.7,13.5],[29.0,13.1],[30.9,13.6],[32.9,13.3],[35.5,14.1],[37.1,13.7],[38.6,13.7],[41.8,14.2],[44.2,13.1],[45.7,13.7],[-0.8,17.8],[1.7,17.3],[3.7,18.3],[6.0,17.5],[8.6,18.5],[10.3,17.8],[13.2,18.5],[15.8,18.5],[17.1,17.7],[19.4,18.5],[22.6,17.3],[23.5,17.4],[25.8,17.9],[28.6,17.5],[29.8,17.7],[32.6,18.0],[35.7,18.2],[37.2,18.1],[39.6,17.2],[42.2,18.3],[44.3,18.4],[45.7,17.7],[67.1,18.1],[69.2,17.2],[71.7,17.3],[-0.3,21.6],[1.4,21.8],[3.7,22.1],[5.8,22.9],[8.9,21.8],[10.5,22.1],[12.9,21.7],[15.9,23.1],[17.4,22.3],[19.0,21.7],[21.6,22.0],[24.6,21.8],[25.5,23.1],[28.5,21.8],[30.7,21.6],[32.8,23.1],[35.6,22.7],[36.8,22.1],[38.8,22.8],[41.6,22.8],[43.4,21.9],[46.4,23.1],[66.1,22.8],[68.3,22.7],[69.5,22.4],[71.9,21.6],[73.6,22.0],[-0.4,27.1],[2.9,26.7],[5.1,27.6],[7.3,26.6],[8.3,26.4],[10.4,26.3],[13.3,27.5],[15.8,26.8],[17.7,27.3],[19.0,27.1],[22.5,27.3],[24.4,26.8],[25.7,27.3],[28.1,27.3],[31.4,26.6],[32.6,27.5],[35.3,26.3],[36.6,26.3],[40.0,27.3],[41.0,27.3],[44.5,27.1],[45.7,26.9],[62.8,26.0],[66.3,27.1],[67.8,27.5],[69.8,27.4],[72.7,26.4],[73.9,26.5],[76.1,27.0],[1.8,31.2],[3.8,31.9],[6.3,31.2],[8.9,31.9],[10.8,32.0],[13.1,31.3],[15.3,30.5],[17.4,30.8],[18.9,31.8],[21.3,31.2],[37.5,31.4],[39.1,31.3],[41.6,31.7],[43.1,31.4],[60.8,30.9],[63.8,31.3],[65.7,31.7],[68.4,31.2],[70.1,31.3],[72.2,31.6],[74.2,31.3],[76.5,32.0],[79.0,31.9],[2.9,35.4],[4.5,36.5],[7.1,35.2],[8.1,35.7],[10.2,35.3],[12.4,36.0],[15.8,36.4],[16.9,36.1],[19.9,35.2],[22.5,36.5],[25.8,36.5],[28.3,35.7],[31.4,36.3],[32.2,35.6],[37.2,35.5],[38.9,35.5],[41.9,35.0],[43.8,35.7],[58.2,35.5],[61.4,35.8],[62.7,36.5],[66.0,36.5],[67.1,35.4],[69.2,36.2],[71.8,35.2],[74.2,36.4],[77.0,35.4],[78.1,36.4],[81.0,36.1],[1.5,39.5],[4.7,40.1],[5.9,40.9],[9.0,40.7],[10.3,40.8],[12.4,40.8],[15.2,40.0],[17.6,40.9],[19.3,39.6],[21.9,39.8],[25.6,39.7],[27.7,39.7],[30.3,39.9],[33.2,39.9],[37.2,39.7],[39.1,39.5],[41.1,39.4],[44.1,40.3],[56.3,40.2],[59.7,39.6],[61.7,40.1],[63.4,40.8],[65.4,40.2],[68.1,41.0],[71.9,40.8],[74.6,40.4],[76.4,40.0],[78.0,39.6],[80.2,40.6],[82.7,39.7],[1.5,45.2],[5.0,45.0],[6.2,44.3],[8.4,44.6],[16.9,44.6],[19.3,45.4],[22.6,44.8],[25.8,45.4],[28.1,44.5],[29.8,44.5],[32.7,44.7],[36.7,44.7],[38.6,44.3],[40.9,44.5],[43.0,43.9],[56.5,44.3],[59.2,44.7],[61.6,44.9],[63.7,45.3],[65.4,44.4],[75.1,44.1],[76.9,44.9],[78.0,45.2],[81.5,44.9],[83.4,45.2],[84.7,44.7],[2.2,49.7],[4.9,49.7],[6.7,49.8],[9.0,49.5],[17.1,48.4],[19.1,48.9],[21.2,49.7],[26.3,49.4],[28.6,49.5],[30.6,48.4],[33.3,49.6],[37.2,49.2],[39.6,48.5],[41.9,48.8],[43.0,48.8],[55.0,48.7],[57.2,49.9],[59.0,49.0],[61.2,49.5],[63.8,49.3],[76.7,48.5],[78.1,48.8],[81.3,48.8],[83.2,48.4],[84.5,48.8],[87.7,49.5],[2.5,53.3],[4.4,53.6],[6.5,53.0],[9.4,53.2],[11.7,54.3],[12.3,53.6],[15.8,54.4],[17.4,53.3],[19.2,54.3],[21.4,53.8],[25.7,53.7],[29.1,53.0],[31.1,53.6],[33.4,54.0],[36.7,54.3],[39.3,52.9],[40.7,53.6],[43.6,53.3],[51.9,53.4],[54.4,54.2],[56.0,54.0],[59.6,53.0],[61.9,54.0],[64.0,53.3],[78.5,53.5],[81.7,53.8],[82.8,53.5],[84.9,52.9],[86.8,54.2],[89.3,54.3],[1.8,57.7],[4.4,57.6],[6.4,58.8],[9.4,58.6],[11.1,58.8],[13.8,58.2],[15.7,57.4],[17.9,58.0],[20.1,58.3],[21.5,57.4],[26.9,57.5],[28.4,57.9],[30.3,58.5],[33.5,57.7],[37.4,57.8],[39.4,57.9],[41.0,57.6],[43.2,58.8],[52.5,57.7],[55.3,58.9],[56.8,57.5],[58.5,57.4],[60.9,57.4],[80.5,57.7],[83.2,58.7],[85.6,58.0],[87.3,58.1],[89.4,57.8],[91.1,57.7],[2.9,62.0],[4.4,62.8],[7.1,62.1],[8.4,62.2],[17.3,62.5],[20.4,63.1],[22.5,61.8],[25.5,62.9],[29.0,62.5],[30.7,61.8],[32.6,63.3],[37.7,63.1],[40.1,62.2],[40.9,62.0],[43.8,62.9],[55.4,62.9],[57.1,63.0],[58.9,62.7],[82.3,63.0],[84.8,63.2],[87.7,62.3],[89.0,62.2],[92.0,62.9],[93.4,61.9],[2.2,67.2],[4.2,66.6],[6.7,66.3],[8.4,67.0],[11.7,67.3],[13.7,67.0],[14.9,66.6],[18.2,67.4],[19.4,66.3],[21.9,67.3],[26.1,66.7],[28.7,67.7],[30.2,66.3],[32.5,66.9],[37.5,66.6],[39.8,67.4],[41.5,66.6],[44.5,66.7],[55.2,66.6],[56.4,67.5],[84.9,67.8],[87.4,66.5],[89.2,66.9],[92.1,67.8],[93.4,66.9],[95.7,67.8],[1.6,70.8],[3.7,71.3],[7.2,72.1],[9.1,72.3],[11.6,71.2],[12.6,72.2],[15.7,70.8],[17.7,71.3],[19.5,71.2],[21.3,70.7],[25.9,71.3],[29.1,70.9],[31.3,71.0],[32.6,72.0],[37.7,71.4],[38.6,71.5],[41.3,72.2],[43.2,71.3],[88.1,70.8],[89.5,72.0],[92.2,70.8],[93.2,70.8],[96.8,71.1],[98.8,72.1],[1.9,75.6],[5.1,76.2],[6.2,76.3],[8.4,75.6],[10.1,76.4],[13.8,76.2],[16.0,75.2],[17.1,75.9],[20.4,76.7],[21.7,75.6],[26.1,76.0],[29.1,75.5],[31.1,76.4],[33.3,76.4],[37.3,75.7],[39.1,75.8],[42.0,75.3],[43.2,76.4],[87.0,75.3],[88.9,76.1],[91.5,76.7],[94.6,76.8],[95.8,75.3],[97.7,76.0],[2.5,80.4],[3.9,80.3],[6.7,80.7],[9.1,81.0],[17.7,79.8],[20.2,80.1],[22.0,80.2],[37.5,80.0],[38.9,80.0],[41.0,81.1],[43.8,80.2],[89.5,81.2],[91.8,80.0],[94.5,80.7],[97.0,79.8],[2.1,85.4],[4.9,85.6],[5.8,84.6],[8.1,84.4],[18.2,85.0],[20.4,84.7],[22.4,84.8],[30.2,85.4],[37.9,84.3],[39.5,85.1],[41.1,84.7],[43.1,84.4],[93.6,85.1],[96.4,84.4],[1.4,89.1],[4.7,88.9],[6.3,88.9],[9.2,89.5],[12.4,88.7],[15.1,89.5],[17.7,88.7],[19.1,89.7],[21.7,89.0],[30.3,90.1],[32.5,89.5],[34.7,89.3],[37.7,90.2],[39.1,88.9],[41.9,88.9],[42.9,90.0],[96.1,89.9],[2.0,94.5],[4.3,93.3],[5.8,93.9],[9.0,94.5],[12.5,94.1],[15.1,93.9],[16.9,93.5],[19.7,94.5],[21.2,93.8],[31.1,94.6],[32.3,93.3],[35.7,94.6],[37.1,93.1],[40.0,93.7],[42.2,94.0],[44.2,93.3],[2.6,97.9],[4.2,98.9],[7.1,97.8],[8.3,98.2],[15.3,98.1],[16.9,97.9],[20.0,99.0],[29.9,98.4],[33.2,97.6],[35.5,97.7],[37.3,98.4],[39.5,98.0],[41.4,98.5]];

	var ptField = document.getElementById('pt-field');
	if (ptField && !REDUCE) {
		var ptColors = ['#15803D', '#22A355', '#4ADE80', '#86EFAC'];
		var frag = document.createDocumentFragment();
		PT_TARGETS.forEach(function (t, i) {
			var ang = Math.random() * Math.PI * 2;
			var dist = 70 + Math.random() * 240;
			var dx = Math.cos(ang) * dist;
			var dy = Math.sin(ang) * dist;
			var j = Math.random() * 70 - 35;
			var s = document.createElement('span');
			s.className = 'pt';
			var size = 2 + (i % 3);
			s.style.cssText = 'left:' + t[0] + '%;top:' + t[1] + '%;width:' + size + 'px;height:' + size + 'px;background:' + ptColors[i % 4]
				+ ';--dx:' + Math.round(dx) + 'px;--dy:' + Math.round(dy) + 'px'
				+ ';--mx:' + Math.round(dx * 0.45 - Math.sin(ang) * j) + 'px;--my:' + Math.round(dy * 0.45 + Math.cos(ang) * j) + 'px'
				+ ';--dur:' + (1.6 + Math.random() * 0.6).toFixed(2) + 's;--delay:' + (0.1 + Math.random() * 0.4).toFixed(2) + 's'
				+ ';--o:' + (0.8 + Math.random() * 0.2).toFixed(2) + ';--r:' + (Math.random() < 0.3 ? '1px' : '50%');
			frag.appendChild(s);
		});
		ptField.appendChild(frag);
	}

	// ターミナルのライブ実行演出（Iceberg 配色・Starship 風プロンプト）
	var termLines = document.getElementById('term-lines');
	var termTyped = document.getElementById('term-typed');
	if (termLines && termTyped) {
		var FG = '#C6C8D1', GRAY = '#6B7089', GREEN = '#B4BE82', CYAN = '#89B8C2', BLUE = '#84A0C6', YELLOW = '#E2A478', PURPLE = '#A093C7';
		var SP = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
		var PROMPT = [['➜ ', GREEN], ['~/efoo ', CYAN], ['❯ ', PURPLE]];
		var model = [];

		var renderTerm = function () {
			termLines.innerHTML = model.map(function (line) {
				return '<span class="term-line">' + line.map(function (seg) {
					return '<span style="color:' + seg[1] + '">' + seg[0] + '</span>';
				}).join('') + '</span>';
			}).join('');
		};
		var push = function (segs) { model.push(segs); renderTerm(); };
		var replaceLast = function (segs) { model[model.length - 1] = segs; renderTerm(); };

		var typeCmd = async function (cmd) {
			for (var i = 0; i <= cmd.length; i++) {
				termTyped.textContent = cmd.slice(0, i);
				await wait(26 + Math.random() * 38);
			}
			await wait(260);
			push(PROMPT.concat([[cmd, FG]]));
			termTyped.textContent = '';
		};
		var spin = async function (lbl, ms) {
			push([]);
			var start = Date.now();
			var f = 0;
			while (Date.now() - start < ms) {
				replaceLast([[SP[f % SP.length] + ' ', CYAN], [lbl + ' …', FG]]);
				f++;
				await wait(80);
			}
			replaceLast([['✓ ', GREEN], [lbl + ' ', FG], ['(' + (ms / 1000).toFixed(1) + 's)', YELLOW]]);
		};

		if (REDUCE) {
			model = [
				PROMPT.concat([['efoo run ai-workflow --kitchen', FG]]),
				[['✓ ', GREEN], ['analyzing ingredients ', FG], ['(0.9s)', YELLOW]],
				[['✓ ', GREEN], ['generating prep steps ', FG], ['(1.1s)', YELLOW]],
				PROMPT.concat([['efoo deploy chefrepi --production', FG]]),
				[['✓ ', GREEN], ['building assets ', FG], ['(0.9s)', YELLOW]],
				[['  ↳ ', GRAY], ['42 modules', BLUE], [' · ', GRAY], ['1.2 MB', YELLOW]],
				[['✓ ', GREEN], ['deploying to production ', FG], ['(1.3s)', YELLOW]],
				[['● ', GREEN], ['live', GREEN], [' · production', GRAY]]
			];
			renderTerm();
		} else {
			(async function () {
				await wait(600);
				for (;;) {
					model = [];
					renderTerm();
					termTyped.textContent = '';
					await typeCmd('efoo run ai-workflow --kitchen');
					await spin('analyzing ingredients', 900);
					await spin('generating prep steps', 1100);
					await wait(500);
					await typeCmd('efoo deploy chefrepi --production');
					await spin('building assets', 900);
					push([['  ↳ ', GRAY], ['42 modules', BLUE], [' · ', GRAY], ['1.2 MB', YELLOW]]);
					await spin('deploying to production', 1300);
					push([['● ', GREEN], ['live', GREEN], [' · production', GRAY]]);
					await wait(3200);
				}
			})();
		}
	}

	// PR 動画の物体検出風オーバーレイ。カット時刻と枠の軌道は映像から採寸済み
	var video = document.getElementById('pr-video');
	var cvLayer = document.getElementById('cv-layer');
	if (video && cvLayer && !REDUCE) {
		var TL = [
			{ s: 0, e: 1.13, b: [
				['steak', '#4ADE80', [25, 8, 42, 54], [27, 12, 41, 54], 0.97],
				['knife', '#A78BFA', [30, 4, 47, 52], [35, 8, 45, 40], 0.91],
				['steak', '#4ADE80', [56, 60, 34, 35], [56, 62, 34, 33], 0.88],
				['hand', '#38BDF8', [0, 14, 32, 58], [2, 24, 34, 54], 0.86]
			] },
			{ s: 1.13, e: 2.34, b: [
				['knife', '#A78BFA', [0, 0, 70, 32], [0, 6, 55, 50], 0.84],
				['steak', '#4ADE80', [30, 10, 45, 65], [34, 16, 46, 64], 0.95]
			] },
			{ s: 2.34, e: 3.47, b: [
				['tablet', '#22D3EE', [46, 0, 52, 54], [47, 0, 52, 54], 0.96],
				['plate', '#E2A478', [20, 45, 76, 42], [22, 42, 74, 44], 0.93],
				['hand', '#38BDF8', [0, 6, 28, 44], [6, 20, 40, 46], 0.9]
			] },
			{ s: 3.47, e: 4.7, b: [
				['potato', '#FBBF24', [0, 0, 44, 62], [0, 2, 36, 74], 0.95],
				['steak', '#4ADE80', [44, 0, 54, 56], [46, 0, 52, 64], 0.97],
				['hand', '#38BDF8', [24, 30, 52, 64], [58, 74, 42, 26], 0.92, 4.2]
			] },
			{ s: 4.7, e: 6.5, b: [
				['plate', '#E2A478', [10, 34, 38, 42], [10, 34, 38, 42], 0.88],
				['bowl', '#F472B6', [12, 0, 34, 20], [12, 0, 34, 20], 0.85],
				['glass', '#A78BFA', [42, 0, 26, 20], [42, 0, 26, 20], 0.84],
				['spoon', '#22D3EE', [52, 62, 38, 32], [52, 62, 38, 32], 0.8]
			] }
		];
		var vis = {};
		var lastSeg = -2;
		var lastDrift = 0;
		(async function () {
			for (;;) {
				if (video.playbackRate !== 0.85) { video.playbackRate = 0.85; }
				if (video.paused) {
					video.muted = true;
					var pr = video.play();
					if (pr && pr.catch) { pr.catch(function () {}); }
				}
				var ok = video.duration && !isNaN(video.duration) && video.readyState >= 2;
				var tv = ok ? (video.currentTime % video.duration) : -1;
				var seg = null;
				var segIdx = -1;
				for (var i = 0; i < TL.length; i++) {
					if (tv >= TL[i].s && tv < TL[i].e) { seg = TL[i]; segIdx = i; break; }
				}
				var now = Date.now();
				var snap = segIdx !== lastSeg;
				if (snap || now - lastDrift > 380) {
					var html = '';
					(seg ? seg.b : []).forEach(function (b, j) {
						var bEnd = b[5] || seg.e;
						if (tv > bEnd) { return; }
						var p = Math.min(1, Math.max(0, (tv - seg.s) / (bEnd - seg.s)));
						var key = segIdx + ':' + j;
						var cur = vis[key];
						if (snap || cur === undefined) { cur = Math.random() < 0.92; }
						else { cur = cur ? (Math.random() > 0.015) : (Math.random() < 0.6); }
						vis[key] = cur;
						if (!cur) { return; }
						var f = b[2];
						var t4 = b[3];
						var l = (f[0] + (t4[0] - f[0]) * p + Math.random() * 0.4 - 0.2).toFixed(1);
						var t = (f[1] + (t4[1] - f[1]) * p + Math.random() * 0.4 - 0.2).toFixed(1);
						var w = (f[2] + (t4[2] - f[2]) * p + Math.random() * 0.4 - 0.2).toFixed(1);
						var h = (f[3] + (t4[3] - f[3]) * p + Math.random() * 0.4 - 0.2).toFixed(1);
						var conf = (b[4] + Math.random() * 0.01 - 0.005).toFixed(2);
						html += '<span class="cv-box ' + (snap ? 'cv-snap' : 'cv-slow') + '" style="left:' + l + '%;top:' + t + '%;width:' + w + '%;height:' + h + '%;border-color:' + b[1] + '">'
							+ '<span class="cv-tag mono" style="background:' + b[1] + '">' + b[0] + ' ' + conf + '</span></span>';
					});
					cvLayer.innerHTML = html;
					lastSeg = segIdx;
					lastDrift = now;
				}
				await wait(140);
			}
		})();
	}
	if (video && REDUCE) {
		video.removeAttribute('autoplay');
		video.pause();
	}

	// サービススライダー。1枚ずつ送り、ゲージはスクロール量に連動。
	// 停止位置の列は「各カードの左端（scroll-snap-align: start の位置）を末尾 max で頭打ちにしたもの」に
	// 末尾 max を加えた昇順の列で、ボタン操作とスクロール位置の判定の両方がこの列を使う。
	// カード幅は画面幅で変わるため、列は使うたびに DOM から求める
	var track = document.querySelector('.svc-track');
	var bar = document.getElementById('svc-bar');
	if (track) {
		var stops = function () {
			var max = track.scrollWidth - track.clientWidth;
			var origin = track.getBoundingClientRect().left - track.scrollLeft;
			var list = [];
			Array.prototype.forEach.call(track.children, function (card) {
				var at = Math.min(max, Math.max(0, Math.round(card.getBoundingClientRect().left - origin)));
				if (!list.length || at - list[list.length - 1] > 1) { list.push(at); }
			});
			if (!list.length || max - list[list.length - 1] > 1) { list.push(max); }
			return list;
		};
		// 送り中（smooth スクロールの途中）は、現在位置ではなく送り先を基準にして連打に追従する
		var target = null;
		var go = function (dir) {
			var list = stops();
			var base = target !== null ? target : track.scrollLeft;
			var to = null;
			if (dir > 0) {
				for (var i = 0; i < list.length; i++) { if (list[i] > base + 1) { to = list[i]; break; } }
			} else {
				for (var j = list.length - 1; j >= 0; j--) { if (list[j] < base - 1) { to = list[j]; break; } }
			}
			if (to === null) { return; }
			target = to;
			track.scrollTo({ left: to, behavior: REDUCE ? 'auto' : 'smooth' });
		};
		var settle = function () { target = null; };
		if ('onscrollend' in window) {
			track.addEventListener('scrollend', settle);
		} else {
			var settleTimer = 0;
			track.addEventListener('scroll', function () {
				clearTimeout(settleTimer);
				settleTimer = setTimeout(settle, 150);
			});
		}
		var prev = document.getElementById('svc-prev');
		var next = document.getElementById('svc-next');
		if (prev) { prev.addEventListener('click', function () { go(-1); }); }
		if (next) { next.addEventListener('click', function () { go(1); }); }
		if (bar) {
			var drawBar = function () {
				var max = track.scrollWidth - track.clientWidth;
				var p = max > 0 ? (track.scrollLeft / max) : 0;
				bar.style.width = Math.round(25 + p * 75) + '%';
			};
			track.addEventListener('scroll', drawBar, { passive: true });
			window.addEventListener('resize', drawBar);
			drawBar();
		}
	}
})();
