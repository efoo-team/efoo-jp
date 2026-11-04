# efoo 2026 テーマ

トップページを刷新する自作テーマ。デザインカンバス「efoo.jp リデザイン案」の案A（Light Tech）を実装したものである。

## 構成

- `front-page.php` — トップページ本体。ヒーロー（タイピング・「前へ」の粒子集合・ライブ実行ターミナル・PR動画の物体検出風オーバーレイ）、二段マーキー、サービススライダー、強み、チーム、お知らせ、CTA、フッター
- `index.php` — トップページ以外の素のテンプレート（投稿・固定ページの本文をそのまま出す）
- `assets/css/main.css` — カラートークンと動きの定義。スクロール連動の出現は `animation-timeline: view()` を使い、未対応ブラウザは JS の IntersectionObserver で代替する
- `assets/js/main.js` — タイピング、粒子生成、ターミナルのループ、動画の検出枠（カット時刻・座標は実映像から採寸）、スライダー
- `assets/img/` — Lシフトの画面素材（l-shift リポジトリ由来）、エディタ画像、動画のフレーム

写真・PR動画は WordPress のメディア（`wp-content/uploads`）を参照する。ローカルでは `pnpm env:pull` 済みであれば同じパスで表示される。

## ローカルでの確認

`.wp-env.json` がこのテーマを wp-env にマウントしている。初回・設定変更後は wp-env の再起動が要る。

```sh
pnpm env:start
pnpm wp theme activate efoo-2026
```

戻すときは `pnpm wp theme activate inspiro`。

## 本番への反映

このテーマの配置と有効化は本番への書き込みのため、ユーザーの明示指示があるときだけ行う。配置は docroot の `wp-content/themes/efoo-2026` へのアップロード、有効化は `php ~/bin/wp --path=... theme activate efoo-2026`。有効化すると Inspiro から表示が切り替わり、トップページ以外は素のテンプレートになる。

## 既知の未実装・仮置き

- SNS リンク（X / Instagram / YouTube）は各サービスのトップへの仮リンク。実アカウントの URL に差し替える
- CTA の「お問い合わせはこちら」は `/contact/` 固定。実際の問い合わせページの URL に合わせる
- お知らせは最新の投稿5件を出す。カテゴリ別の出し分けは未実装
- モバイル表示は flex の折り返しによる簡易対応。ブレークポイントの作り込みは未実装
