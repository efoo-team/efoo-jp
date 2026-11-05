# AGENTS.md

efoo株式会社のコーポレートサイト https://efoo.jp/ を運用・改善するリポジトリ。サイトは本番サーバー上の WordPress で、ローカルでは wp-env で本番と同じ内容を再現して確かめる。

リポジトリで管理するのは、自作のコード（`wp-content/mu-plugins/` と自作テーマ `wp-content/themes/efoo-2026/`）、本番の `.htaccess` に書き足す設定（`server/htaccess/`）、ローカル環境の設定（`.wp-env.json`）、本番の取得・点検・反映のスクリプトである。配布物のテーマ・プラグイン、ページの内容、アップロード画像は本番が正本であり、Git では管理しない。ローカルへは本番のバックアップから取り込む。

## ローカル環境

### 初めて環境を作るとき

リポジトリの直下で `pnpm env:setup`（`bash scripts/setup.sh`）を実行する。前提の確認、`pnpm install`、`wp-env start`、本番の内容の取り込みまでを一度に行い、何度実行してもよい。終わると http://localhost:8890/ でサイトが開ける。管理画面 http://localhost:8890/wp-admin/ には `admin` / `password` でログインできる。

前提と、満たされていないときの対処は次のとおりである。`scripts/setup.sh` は、満たされていない前提を名指しして止まる。

| 前提 | 対処 |
| --- | --- |
| Docker が起動している | macOS では OrbStack か Docker Desktop を入れて起動する。どちらも無ければ OrbStack（`brew install --cask orbstack`）を入れる |
| Node.js 22.13 以上 | wp-env 12 の要件。mise・nvm・Homebrew など、その環境で使われている方法で入れる |
| pnpm | `corepack enable pnpm`。版は `package.json` の `packageManager` で固定している |
| 本番の内容（下記） | 本番へ SSH で接続できるか、`.local/backups/<日時>/` にバックアップがあること |

### 本番の内容を手に入れる

ローカルのサイトの中身（DB、テーマ、プラグイン、画像）は、本番のバックアップから取り込む。手に入れ方は 2 通りある。

- **本番へ SSH で接続できる場合**：`scripts/setup.sh` が自動でバックアップを取得する。接続には、サーバーに公開鍵が登録されている必要がある。鍵が `~/.ssh` の既定の名前（`id_ed25519`・`id_rsa` など）でなければ、`EFOO_SSH_KEY=~/.ssh/<鍵のファイル>` を付けて実行するか、`~/.ssh/config` に次を書いて毎回の指定を省く（スクリプトは接続先をこのホスト名で指定するため、この設定が効く）。

  ```
  Host www1068.onamae.ne.jp
    IdentityFile ~/.ssh/<鍵のファイル>
  ```

  接続を確かめるコマンドは `ssh -p 8022 -o IdentitiesOnly=yes r0884593@www1068.onamae.ne.jp true` である。鍵が既定の名前でなく、`~/.ssh/config` にも書いていない場合は、`-i ~/.ssh/<鍵のファイル>` を付ける。公開鍵の登録は、お名前.com のサーバーの管理者に依頼する。
- **SSH で接続できない場合**：接続できる人に、`pnpm backup` で取得したフォルダ `.local/backups/<日時>/` を丸ごと受け取る。受け取ったフォルダを、同じ名前でこのリポジトリの `.local/backups/` に置いてから `pnpm env:setup` を実行する。このフォルダには、DB のパスワード（`files/wp-config.php`）と、問い合わせなどの DB の内容が入っている。受け渡しは公開の場所を通さずに行う。

### よく使うコマンド

```sh
pnpm env:start          # wp-env を起動（http://localhost:8890/）
pnpm env:stop
pnpm env:pull --fresh   # 本番のバックアップを取得して、ローカルへ取り込み直す
pnpm env:pull           # 取得済みの最新のバックアップを取り込み直す
pnpm wp <args>          # ローカルの wp-cli
```

### 環境の仕組みと注意点

- **版を本番に揃える**：WordPress の版（`core`）と PHP の版（`phpVersion`）は、`.wp-env.json` で本番に揃えてある。本番の WordPress を更新したら、`core` の URL も合わせる。
- **ポート**：8890 を使う。8888・8889 は、ほかのプロジェクトの wp-env が使っていることが多いためである。8890 が使用中なら、`WP_ENV_PORT=<空いているポート>` を付けて `pnpm env:start` と `pnpm env:pull` を実行する。取り込み時の URL の置き換えも、そのポートで行われる。
- **取り込み直すと消えるもの**：`scripts/pull.sh` は、ローカルの DB と `.local/site/wp-content/` の plugins・themes・uploads・languages を、バックアップの内容で置き換える。そのうえで URL を `https://efoo.jp` から `http://localhost:<ポート>` に置き換える。ローカルの管理画面で行った変更は、取り込み直すと消える。
- **mu-plugin はすぐ反映される**：`wp-content/mu-plugins/` はリポジトリのディレクトリをそのまま割り当てているため、編集はすぐローカルに反映される。
- **wp-cli の出力に出る警告**：ローカルは `WP_DEBUG` が有効なため、`Deprecated` や `Notice`（insert-headers-and-footers、Inspiro が原因）が出る。動作には影響しない。

## サーバー

| 項目 | 値 |
| --- | --- |
| ホスティング | お名前.com レンタルサーバー（RS） |
| SSH | `ssh -p 8022 -o IdentitiesOnly=yes r0884593@www1068.onamae.ne.jp` |
| docroot | `/home/r0884593/public_html/efoo.jp` |
| PHP | 8.5 |
| DB | `mysql3.onamae.ne.jp`（接続情報は docroot の `wp-config.php`） |
| wp-cli | `php ~/bin/wp --path=/home/r0884593/public_html/efoo.jp`（版は `scripts/lib.sh` の `WP_CLI_VERSION`） |
| ログ | `~/logs/efoo.jp/`（access_log・error_log）、`~/logs/php_error_log` |
| DNS・メール | ネームサーバーと MX はお名前.com。`www.efoo.jp` は DNS に登録がない |

ssh-agent に鍵が多いと、`IdentitiesOnly` なしの接続は `Too many authentication failures` で切断される。スクリプトは `scripts/lib.sh` の `SSH_OPTS` でこれを付けている。接続先は `EFOO_SSH_HOST`、鍵は `EFOO_SSH_KEY` で変えられる。

本番のほかにステージング環境は無い。変更はローカルで確かめてから本番へ入れる。

## 本番を扱うコマンド

```sh
pnpm check                       # 読み取りのみの点検。問題があれば終了コード 1
pnpm backup                      # DB と docroot 全体を .local/backups/<日時>/ に取得
bash scripts/install-wp-cli.sh   # サーバーの ~/bin/wp に wp-cli を導入（導入済みなら何もしない）
```

`scripts/check.sh` が点検する項目は次のとおりである。

- WordPress 本体：更新の有無、公式チェックサムとの照合
- プラグイン・テーマ：版、更新の有無、プラグインの公式チェックサムとの照合
- 管理者の一覧
- `wp-content/uploads` 内の実行可能ファイルの有無
- 直近 14 日に変更されたファイル
- トップページの HTTP ステータス

公式配布物と異なるプラグインのファイルは、`scripts/known-modified.tsv` に同じ sha256 で載っていれば許容する。cache-clear-onamae はお名前.com 独自のプラグインのため、照合の対象外である。

`scripts/backup.sh` はサーバーへ書き込まない。docroot は前回のバックアップとの差分だけを転送し、変わっていないファイルはハードリンクで共有する。バックアップには `wp-config.php`（DB のパスワード）が含まれ、`.local/` は Git の管理外である。

## 本番を変更するときの手順

1. `pnpm env:pull --fresh` で本番の最新状態をローカルに取り込み、ローカルで変更して表示を確かめる。
2. `pnpm backup` で本番のバックアップを取る（手順 1 で取得していれば不要）。
3. 本番を変更する。プラグインやテーマの更新は `php ~/bin/wp --path=... plugin update <name>` のように wp-cli で行う。`wp-content/mu-plugins/` の変更は `bash scripts/deploy-mu-plugins.sh --dry-run` で確かめてから `--yes` で反映する。自作テーマ `wp-content/themes/efoo-2026/` の変更は、main へマージしてから `bash scripts/deploy-theme.sh --dry-run` で確かめ、`--yes` で反映する（使い方はテーマの README の「本番への反映」）。静的ファイルの保存期間（`server/htaccess/efoo-cache.conf`）の変更は、`bash scripts/deploy-htaccess.sh --dry-run` で確かめてから `--yes` で反映する（下の「静的ファイルの保存期間」）。
4. `pnpm check` で点検し、https://efoo.jp/ の表示を確かめる。
5. 公式配布物と異なるファイルを意図して作ったときは、`scripts/known-modified.tsv` に登録する。

本番への書き込み（更新・削除・設定変更・ファイル配置）は、ユーザーの明示指示があるときだけ行う。

## 静的ファイルの保存期間

本番の docroot の `.htaccess` の `# BEGIN efoo cache`〜`# END efoo cache` の区間が、CSS・JS・画像・動画・フォントの応答に `Cache-Control: max-age` と `Expires` を付け、ブラウザに保存させる期間を決める。区間の中身の正本は `server/htaccess/efoo-cache.conf` であり、本番の `.htaccess` を直接編集しても、次の反映で上書きされる。

| ファイル | 保存期間 |
| --- | --- |
| URL に `?ver=` が付いた CSS・JS | 1 年（`immutable` も付ける） |
| URL に `?ver=` が付かない CSS・JS | 付けない |
| 画像（テーマの `assets/img/` とアップロードした画像）・動画 | 30 日 |
| フォント | 1 年 |
| HTML | 付けない |

前段の nginx も、応答の `max-age` の間ファイルを保持して返す。このため、同じファイル名のまま中身を差し替えた画像や動画は、nginx のキャッシュを消すまで本番でも古い内容が返り、消したあとも、すでに取得したブラウザには最大 30 日古い内容が残る。サーバーから消したファイルも、nginx のキャッシュを消すまで返ることがある。nginx のキャッシュは、サーバーで `curl 'http://127.0.0.1:9080/clearcache?cwd=/home/r0884593/public_html/efoo.jp'` を実行して消す（`scripts/deploy-theme.sh --yes` と `scripts/deploy-htaccess.sh --yes` は、これを実行する）。写真や動画（テーマの `assets/img/` の画像を含む）を差し替えるときは、ファイル名を変える。CSS・JS は `?ver=` が変われば別の URL になるため、この扱いは要らない。

区間の反映は `scripts/deploy-htaccess.sh` で行う。

```sh
bash scripts/deploy-htaccess.sh --dry-run            # 本番の .htaccess と、書き換えた後との差分を表示するだけ
bash scripts/deploy-htaccess.sh --yes                # 書き換え、nginx のキャッシュを消して、応答ヘッダーを確かめる
bash scripts/deploy-htaccess.sh --dry-run --remove   # 区間の中身を空にした場合の差分を表示するだけ
bash scripts/deploy-htaccess.sh --yes --remove       # 区間の中身を空にして保存期間を外し、応答ヘッダーに保存期間が無いことを確かめる
```

- 書き換えは、サーバーの wp-cli から WordPress の `insert_with_markers()` で行う。区間がまだ無ければ `.htaccess` の末尾（`# END WordPress` の後）に足し、区間の外は変えない。WordPress がパーマリンクの保存で書き換えるのは `# BEGIN WordPress` の区間だけなので、この区間は残る。`insert_with_markers()` は末尾に改行を付けずに書くため、区間を初めて足すときの `--dry-run` の差分には、`# END WordPress` の行が `-`（`\ No newline at end of file` 付き）と `+` の両方に出る。行の中身は同じで、後ろに行が続くようになっただけであり、書き換えに害はない
- `--yes` は、書き換える前の `.htaccess` を、サーバーの `~/htaccess-before-deploy/efoo.jp-<日時>` と手元の `.local/htaccess-before-deploy/efoo.jp-<日時>` に写す。書き換えを始めてから、書き換えた内容が差分のとおりであることと、トップページが 200 を返すことを確かめ終えるまでの間に終わると（書き換えや読み戻しの失敗、内容の不一致、`.htaccess` の誤りによる 500 など、中断を含む）、サーバーの写しに戻して終了コード 1 で終わる。写しに戻せなかったときは、そのことと、手で戻すコマンドを表示する
- 保存期間を外すときは `--remove` を付ける。区間の中身を空にし（`# BEGIN efoo cache` と `# END efoo cache` の行だけが残る）、区間の外は変えない。`--remove` を付けない反映では、`server/htaccess/efoo-cache.conf` が空、または空行とコメント行だけのとき、本番に接続する前に誤りとして止める。設定ファイルを空にしても保存期間は外れない。外したあと設定を戻すときは、`--remove` を付けずに `--yes` で反映する。書き換える前の写しに `.htaccess` 全体を戻す方法もあるが、写した後に WordPress が `# BEGIN WordPress` の区間を書き換えていれば、その変更も戻ってしまう
- `--yes` は、書き換えた後に nginx のキャッシュを消し、トップページ（`Cache-Control` なし）、トップページの HTML から拾った URL の `Cache-Control` を確かめる。対象は、テーマ efoo-2026 の `assets/css/main.css` と `assets/js/main.js` の `?ver=` 付きの URL（1 年と `immutable`）、同じ `main.css` の `?ver=` 無しの URL（なし）、PR 動画の mp4（30 日）、テーマのフォント（`wp_head` の preload に出る `assets/fonts/` の woff2。1 年）、テーマの画像とアップロードした画像（30 日）である。どれかの URL がトップページの HTML に無いときも、確かめられなかったものとして列挙する。満たさないものがあれば列挙し、終了コード 1 で終わる。区間に差分が無いときは書き換えず、nginx のキャッシュの消去と確認だけを行う。`--remove` のときは、同じ URL のどれにも `Cache-Control` が無いことを確かめる

## テーマ

本番で有効なテーマは、自作テーマ `wp-content/themes/efoo-2026/` である。リポジトリで管理しており、ローカルでは `.wp-env.json` のマッピングで wp-env に載る。ローカルの有効なテーマは、取り込んだ本番の DB と同じく efoo-2026 である。配置と確認の手順はテーマの README を見る。

本番には Inspiro（WPZOOM、wordpress.org 配布版）が無効のまま残っている。以下の「Inspiro」節は、Inspiro へ戻すとき、または Inspiro を更新するときの参考である。

### Inspiro（無効のまま残しているテーマ）

Inspiro は classic theme なので、block theme のように Site Editor の変更を DB に保存して親テーマの更新から守る仕組みは使えない。ただし、Inspiro で有効だったときの見た目の調整はすべて DB にあり、テーマのファイルには手を入れていない。具体的には次の 3 つである。

- Customizer の設定（option `theme_mods_inspiro`）
- 追加 CSS（`custom_css` の投稿）
- グローバルスタイル（`wp_global_styles` の投稿）

このため、Inspiro はそのまま更新できる。

子テーマは、PHP テンプレートの上書きや `functions.php` への処理の追加が必要になったときに作る。子テーマを有効にすると、上の 3 つは子テーマ用の空の値から始まるため、親テーマの値を子テーマへ複製する作業が要る。

Inspiro 2.x への更新では、見出しのフォント（Montserrat から Onest へ）やメニューの見た目など、表示が変わる。更新はローカルで表示を確かめてから行う。

## wpzoom-portfolio のカテゴリ順

ポートフォリオブロックは、カテゴリの絞り込みをカテゴリの「説明」欄の値の順に並べる。ブロックを含むのは、固定ページ `homepage`（ID 95）と投稿タイプ `portfolio_layout` の `contents`（ID 310）である。有効な efoo-2026 のトップページ（`front-page.php`）はこのブロックを使わず、お知らせの絞り込みチップ（`category-chips.php`）も件数順で並べるため、現在の本番のトップページとお知らせ一覧には、この並び順は表れない。Inspiro へ戻したときのトップページに表れる。この並び順は mu-plugin の `wp-content/mu-plugins/efoo-portfolio-category-order.php` が決める。この mu-plugin はプラグイン本体を変更しないので、wpzoom-portfolio を公式の新しい版へ更新しても並び順は保たれる。カテゴリの並びを変えるには、管理画面のポートフォリオのカテゴリで「説明」欄の値（`1`、`2`、`3`）を変える。

本番の wpzoom-portfolio 1.4.10 には、同じ並び替えをプラグイン本体に直接書き込んだ改修も残っている（差分は `patches/wpzoom-portfolio-1.4.10.diff`）。mu-plugin が同じ順を決めるため、wpzoom-portfolio は公式の版へ更新してよい。更新したら `scripts/known-modified.tsv` から wpzoom-portfolio の行を消し、`patches/` も消す。

## 問い合わせフォーム

お問い合わせページ（固定ページ `contact-us`、ID 248）は、wpzoom-forms のフォーム（ID 239、post_type `wpzf-form`）を埋め込んでいる。フォーム 239 は、wpzoom-forms 2.x でも旧形式（本文のブロックで項目を組んだフォーム。投稿メタ `_wpzf_schema` が無い）のままである。旧形式のフォームは、表示を `form_block_render()`、送信の処理を `action_form_post()`（2.x の送信処理 `WPZOOM_Forms_Submission_Handler::handle()` から呼ばれる）が行う。下の 2 つの mu-plugin と、テーマの送信ボタンの中央寄せは、この旧形式の経路を前提にしている。

### フォームを変えるとき

フォーム 239 は、管理画面のフォーム編集画面（Form Builder）で保存しない。保存すると `_wpzf_schema` が作られて新しい形式に移り、次のように変わる。開くだけなら形式は変わらない。

- 項目名：本文のブロックの `name` の値になり、「メールアドレス」は「Email」、「問い合わせ内容」は「Message」になる。
- 送信ボタン：「Submit」になる（送信ボタンのブロックに `name` が無いため）。
- 送信後の文言：「Thanks! We've received your submission.」「Sorry, something went wrong. Please try again.」になり、mu-plugin の日本語が当たらない。

項目名や送信ボタンの「送信する」は、フォーム 239 の本文（`post_content`）に保存されたブロックの値である。変えるときは、wp-cli で本文をファイルに書き出し、ブロックの値を書き換えて、本文を更新する。項目名を変えるときは、ブロックの属性の `name`（通知メールの本文に出る項目名）と `label`、ブロックの HTML の `<label>` の文字をそろえて書き換える。送信ボタンの文字は `<input type="submit">` の `value` である。

```sh
pnpm -s wp post get 239 --field=post_content 2>/dev/null > .local/form-239.html   # 書き出す（.local/ は Git の管理外）
pnpm -s wp post update 239 - --user=admin < .local/form-239.html                  # 書き換えた本文で更新する
```

`pnpm wp` の出力には pnpm と wp-env の表示が混ざるため、書き出しには `-s` と `2>/dev/null` を付ける。本番では、ローカルで書き換えて表示を確かめた本文を、SSH 越しにサーバーの wp-cli へ渡して更新する（`ssh -p 8022 -o IdentitiesOnly=yes r0884593@www1068.onamae.ne.jp 'php ~/bin/wp --path=/home/r0884593/public_html/efoo.jp post update 239 - --user=<管理者のユーザー名>' < .local/form-239.html`）。更新すると、フォーム 239 のリビジョンが 1 件増える。

### 送信後の文言

フォームの送信後に表示する「Thanks! We've received your submission!」「Submission failed!」は、mu-plugin の `wp-content/mu-plugins/efoo-wpzoom-forms-ja.php` が gettext フィルター（テキストドメイン `wpzoom-forms`）で、「お問い合わせを送信しました。」「送信できませんでした。お手数ですが、時間をおいてもう一度お試しください。」にする。フォーム 239 に投稿メタ `_form_success_message`・`_form_failure_message` が無いため、プラグインは `__()` を通した既定の文言を出し、この置き換えが当たる。置き換えはプラグインの原文と一致したときだけ当たるので、wpzoom-forms を更新したら、ローカルで送信後の文言を確かめる。

### 送信ボタンの位置

送信ボタンは、テーマの `wp-content/themes/efoo-2026/assets/css/main.css` の `.wpzoom-forms_form .wp-block-column:has(> input[type="submit"])` が、ボタンを置いた列の中央に置く。

### 問い合わせメールの件名・本文・差出人

問い合わせフォームの通知メール（chefree@efoo.jp 宛て）は、mu-plugin の `wp-content/mu-plugins/efoo-wpzoom-forms-mail.php` が、公式サイトからの問い合わせだと分かる形にする。対象はフォーム送信の処理（`admin_post_wpzf_submit`・`admin_post_nopriv_wpzf_submit`）の最中に `wp_mail()` で送るメールだけで、パスワード再設定などのメールには影響しない。

- 件名：送信者が件名の欄に入力した値の先頭に `【efoo.jp お問い合わせ】` を付ける。プラグインが組み立てた件名（末尾に「 - サイト名」が付く）は使わず、送信された値から組み立て直す。件名の欄は、フォームが hidden の `wpzf_subject` で送る欄の名前で決まる。件名の欄が空なら `【efoo.jp お問い合わせ】` だけになり、件名の欄が無いフォームでは、フォームの投稿メタ `_form_subject`（フォーム 239 では「New Form Submission」）を使う。
- 本文：冒頭に「efoo.jp のお問い合わせフォーム（`/contact-us/` の URL）から送信されました。」の 1 行を入れる。
- 差出人：From は `efoo.jp お問い合わせフォーム <chefree@efoo.jp>`、Reply-To は送信者のアドレスである。エンベロープ送信者（Return-Path）も From と同じ chefree@efoo.jp にし、efoo.jp の SPF と揃える。
