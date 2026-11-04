# efoo 2026 テーマ

efoo株式会社のコーポレートサイトの自作テーマ。デザインカンバス「efoo.jp リデザイン案」の案A（Light Tech）を実装したものである。トップページのほか、お知らせ一覧・詳細、固定ページ、404、検索結果の専用テンプレートを持つ。

## 構成

- `front-page.php` — トップページ。ヒーロー（タイピング・「前へ」の粒子集合・ライブ実行ターミナル・PR動画の物体検出風オーバーレイ）、二段マーキー、サービススライダー、強み、チーム、お知らせの最新5件、CTA
- `header.php`・`footer.php` — 共通のヘッダー（浮遊ピル型の追従ナビ）とフッター。ヘッダーとフッターから、会社概要・お問い合わせ・お知らせ一覧へ 1 クリックでたどれる
- `home.php`・`archive.php`・`taxonomy-portfolio.php` — お知らせ一覧（投稿ページ、portfolio_item のアーカイブ `/project/`、portfolio タクソノミーなどの分類のアーカイブ）。分類の絞り込みチップとページ送り付き
- `single.php` — 投稿・ポートフォリオの詳細。アイキャッチはタイトルを重ねたヒーローの背景にする
- `page.php` — 固定ページ（会社概要・お問い合わせなど）。本文はブロック・プラグインの出力をそのまま載せる
- `404.php`・`search.php` — ページが見つからないときと、サイト内検索の結果（0 件のときは見つからなかったことを出す）
- `index.php` — どのテンプレートにも当たらないときの素のテンプレート
- `template-parts/` — お知らせ一覧の行（`news-list.php`）、分類の絞り込みチップ（`category-chips.php`）、検索フォーム（`search-form.php`）
- `functions.php` — テーマの機能（title-tag・アイキャッチ・フィードのリンク）、アセットの読み込み、URL を引く関数
- `assets/css/main.css` — カラー・余白・ヘッダーの高さのトークン、レイアウトのクラス、動きの定義、狭い画面のブレークポイント
- `assets/js/main.js` — メニューの開閉、タイピング、粒子生成、ターミナルのループ、動画の検出枠（カット時刻・座標は実映像から採寸）、スライダー
- `assets/img/` — Lシフトの画面素材（l-shift リポジトリ由来）、エディタ画像、動画のフレーム

写真・PR動画は WordPress のメディア（`wp-content/uploads`）を参照する。ローカルでは `pnpm env:pull` 済みであれば同じパスで表示される。

## データとリンクの決まり

- お知らせ・掲載実績の記事は、wpzoom-portfolio の投稿タイプ `portfolio_item` と、その分類 `portfolio` に入っている。トップのお知らせは通常の投稿と `portfolio_item` を合わせて新しい順に出す
- お知らせ一覧の URL は `efoo_news_index_url()` が `portfolio_item` のアーカイブから引く（無ければ投稿ページ、それも無ければトップ）
- 会社概要は固定ページのスラッグ `company`、お問い合わせは `contact-us`（無ければ `contact`、タイトル「お問い合わせ」の順）から URL を引く（`efoo_company_url()`・`efoo_contact_url()`）。会社概要のページが無いときは、ヘッダーとフッターにリンクを出さない
- フッターの SNS（X・Instagram・YouTube）は、シェフレピの公式アカウントへリンクする

## 画面幅と動き

- ブレークポイントは 2 段である。860px 以下ではヘッダーのナビを畳み、メニューボタンで開閉する（Esc・メニュー外のクリック・メニュー内のリンクで閉じる）。640px 以下をスマホ幅とし、余白・見出し・サービスのカード幅・チームの列数を詰める
- テンプレートの見た目はほとんどがインラインの `style` にある。インラインは media query で上書きできないため、幅で変わる指定だけを `main.css` のクラスに持つ。幅で変わる指定を足すときは、インラインではなくクラスに書く
- 追従ヘッダーの位置と高さは `--header-top`・`--header-h` で持ち、アンカーの着地位置（`scroll-padding-top`）もこの値から決める
- スクロール連動の出現は `animation-timeline: view()` を使い、未対応ブラウザは JS の IntersectionObserver で代替する。`prefers-reduced-motion: reduce` では出現の演出を適用せず、最初から表示する

## ローカルでの確認

`.wp-env.json` がこのテーマを wp-env にマウントしている。初回・設定変更後は wp-env の再起動が要る。

```sh
pnpm env:start
pnpm wp theme activate efoo-2026
```

戻すときは `pnpm wp theme activate inspiro`。

## 本番への反映

このテーマの配置と有効化は本番への書き込みのため、ユーザーの明示指示があるときだけ行う。配置は docroot の `wp-content/themes/efoo-2026` へのアップロード、有効化は `php ~/bin/wp --path=... theme activate efoo-2026`。有効化すると、サイト全体の表示が Inspiro からこのテーマに切り替わる。
