# AGENTS.md

efoo株式会社のコーポレートサイト https://efoo.jp/ を運用・改善するリポジトリ。サイトは本番サーバー上の WordPress で、ローカルでは wp-env で本番と同じ内容を再現して確かめる。

リポジトリで管理するのは、自作のコード（`wp-content/mu-plugins/` と自作テーマ `wp-content/themes/efoo-2026/`）、ローカル環境の設定（`.wp-env.json`）、本番の取得・点検のスクリプトである。配布物のテーマ・プラグイン、ページの内容、アップロード画像は本番が正本であり、Git では管理しない。ローカルへは本番のバックアップから取り込む。

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
3. 本番を変更する。プラグインやテーマの更新は `php ~/bin/wp --path=... plugin update <name>` のように wp-cli で行う。`wp-content/mu-plugins/` の変更は `bash scripts/deploy-mu-plugins.sh --dry-run` で確かめてから `--yes` で反映する。自作テーマ `wp-content/themes/efoo-2026/` の変更は、main へマージしてから `bash scripts/deploy-theme.sh --dry-run` で確かめ、`--yes` で反映する（使い方はテーマの README の「本番への反映」）。
4. `pnpm check` で点検し、https://efoo.jp/ の表示を確かめる。
5. 公式配布物と異なるファイルを意図して作ったときは、`scripts/known-modified.tsv` に登録する。

本番への書き込み（更新・削除・設定変更・ファイル配置）は、ユーザーの明示指示があるときだけ行う。

## テーマ

本番で有効なテーマは Inspiro（WPZOOM、wordpress.org 配布版）である。トップページを刷新する自作テーマ `wp-content/themes/efoo-2026/` をリポジトリで管理しており、ローカルでは `.wp-env.json` のマッピングで wp-env に載る（確認・本番反映の手順はテーマの README を見る）。未有効化のため、以下の Inspiro の記述が現在の本番の状態である。

テーマは Inspiro（WPZOOM、wordpress.org 配布版）である。Inspiro は classic theme なので、block theme のように Site Editor の変更を DB に保存して親テーマの更新から守る仕組みは使えない。ただし、このサイトの見た目の調整はすべて DB にあり、テーマのファイルには手を入れていない。具体的には次の 3 つである。

- Customizer の設定（option `theme_mods_inspiro`）
- 追加 CSS（`custom_css` の投稿）
- グローバルスタイル（`wp_global_styles` の投稿）

このため、Inspiro はそのまま更新できる。

子テーマは、PHP テンプレートの上書きや `functions.php` への処理の追加が必要になったときに作る。子テーマを有効にすると、上の 3 つは子テーマ用の空の値から始まるため、親テーマの値を子テーマへ複製する作業が要る。

Inspiro 2.x への更新では、見出しのフォント（Montserrat から Onest へ）やメニューの見た目など、表示が変わる。更新はローカルで表示を確かめてから行う。

## wpzoom-portfolio のカテゴリ順

トップページのポートフォリオブロックは、カテゴリの絞り込みをカテゴリの「説明」欄の値の順に並べる。この並び順は mu-plugin の `wp-content/mu-plugins/efoo-portfolio-category-order.php` が決める。この mu-plugin はプラグイン本体を変更しないので、wpzoom-portfolio を公式の新しい版へ更新しても並び順は保たれる。カテゴリの並びを変えるには、管理画面のポートフォリオのカテゴリで「説明」欄の値（`1`、`2`、`3`）を変える。

本番の wpzoom-portfolio 1.4.10 には、同じ並び替えをプラグイン本体に直接書き込んだ改修も残っている（差分は `patches/wpzoom-portfolio-1.4.10.diff`）。mu-plugin が同じ順を決めるため、wpzoom-portfolio は公式の版へ更新してよい。更新したら `scripts/known-modified.tsv` から wpzoom-portfolio の行を消し、`patches/` も消す。

## 問い合わせフォームの文言

フォームの送信後に表示する「Submitted successfully!」「Submission failed!」は、mu-plugin の `wp-content/mu-plugins/efoo-wpzoom-forms-ja.php` が gettext フィルター（テキストドメイン `wpzoom-forms`）で日本語にする。プラグイン本体は変更しないので、wpzoom-forms を更新しても文言は保たれる。

送信ボタンの「送信する」はプラグインのコードではなく、フォームの投稿（ID 239、post_type `wpzf-form`）の本文に保存された `wpzoom-forms/submit-field` ブロックの値である。変えるにはフォーム編集画面の送信ボタンの「Name」欄で変える。

## 問い合わせメールの件名・本文・差出人

問い合わせフォームの通知メール（chefree@efoo.jp 宛て）は、mu-plugin の `wp-content/mu-plugins/efoo-wpzoom-forms-mail.php` が、公式サイトからの問い合わせだと分かる形にする。対象はフォーム送信の処理（`admin_post_wpzf_submit`・`admin_post_nopriv_wpzf_submit`）の最中に送るメールだけで、パスワード再設定などのメールには影響しない。プラグイン本体は変更しないので、wpzoom-forms を更新しても保たれる。

- 件名：送信者が入力した件名の先頭に `【efoo.jp お問い合わせ】` を付け、プラグインが末尾に足す「 -- サイト名」は外す。
- 本文：冒頭に「efoo.jp のお問い合わせフォーム（`/contact-us/` の URL）から送信されました。」の 1 行を入れる。
- 差出人：From は `efoo.jp お問い合わせフォーム <chefree@efoo.jp>`、Reply-To は送信者のアドレスである。エンベロープ送信者（Return-Path）も From と同じ chefree@efoo.jp にし、efoo.jp の SPF と揃える。
