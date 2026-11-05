# shellcheck shell=bash
# 各スクリプトが読み込む共通設定。

# ~/.ssh/config に依存しないよう接続先を直接指定する。鍵は EFOO_SSH_KEY で指定でき、
# 未指定なら ssh の既定の鍵（~/.ssh/id_ed25519・~/.ssh/id_rsa など）を使う。
SSH_HOST="${EFOO_SSH_HOST:-r0884593@www1068.onamae.ne.jp}"
# ssh-agent に鍵が多いと、サーバーの認証試行回数上限で切断されるため、IdentitiesOnly を付ける。
SSH_OPTS=(-p 8022 -o BatchMode=yes -o IdentitiesOnly=yes -o LogLevel=ERROR -o StrictHostKeyChecking=accept-new)
if [ -n "${EFOO_SSH_KEY:-}" ]; then
	SSH_OPTS+=(-i "$EFOO_SSH_KEY")
fi
DOCROOT=/home/r0884593/public_html/efoo.jp
SITE_URL=https://efoo.jp
WP_CLI_VERSION=2.12.0

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
# 本番へ送る内容の正本。どのチェックアウト・ブランチで実行しても、送るのは fetch した origin/main の内容であり、作業ツリーではない。
DEPLOY_REF=origin/main

remote() {
	ssh "${SSH_OPTS[@]}" "$SSH_HOST" "$@"
}

# サーバー上で wp-cli を実行する。引数はそのまま wp に渡す。
remote_wp() {
	local args
	printf -v args '%q ' "$@"
	remote "php \$HOME/bin/wp --path=$DOCROOT $args"
}

# PHP ファイルの構文を確かめる。手元に php が無ければ、本番と同じ版のサーバーの php に標準入力で渡す（サーバーには書き込まない）。
php_lint() {
	local f out
	for f in "$@"; do
		if command -v php >/dev/null 2>&1; then
			out="$(php -l "$f" 2>&1)" || { echo "$f: $out" >&2; return 1; }
		else
			out="$(remote php -l <"$f" 2>&1)" || { echo "$f: $out" >&2; return 1; }
		fi
	done
}

# 送る前のディレクトリの権限を、ディレクトリ 755・ファイル 644 に揃える。
# 手元の umask（077 など）で作られた権限が rsync -p で本番へ写り、Web サーバーが読めなくなるのを防ぐ。
normalize_perms() {
	find "$1" -type d -exec chmod 755 {} +
	find "$1" -type f -exec chmod 644 {} +
}

# 本番のディレクトリ配下で、ディレクトリ 755・ファイル 644 でないものを列挙する。1 件でもあれば、または調べられなければ 1 を返す。
remote_check_perms() {
	local bad
	bad="$(remote "find $1 \\( -type d ! -perm 755 \\) -o \\( -type f ! -perm 644 \\)")" || {
		echo "権限を調べられない: $1"
		return 1
	}
	if [ -n "$bad" ]; then
		echo "ディレクトリ 755・ファイル 644 でないもの:"
		printf '%s\n' "$bad" | sed 's/^/  /'
		return 1
	fi
	echo "権限: $1 配下はすべてディレクトリ 755・ファイル 644"
}

# 本番の nginx のキャッシュを消す。URL は $1 で差し替えられ、省略時は本番の消去 URL である。
# 応答が 200 でなければ（curl 自体の失敗で 000 になる場合を含む）、手で消す方法を出して 1 を返す。
clear_cache() {
	local url="${1:-http://127.0.0.1:9080/clearcache?cwd=$DOCROOT}" code
	code="$(remote "curl -s -o /dev/null -w '%{http_code}' -m 20 '$url'")" || code=000
	[ -n "$code" ] || code=000
	echo "nginx のキャッシュの消去 -> $code"
	if [ "$code" != 200 ]; then
		echo "キャッシュを消せなかった。古い内容が残るため、サーバーに ssh で入り、次を実行して手で消す:"
		echo "  curl '$url'"
		return 1
	fi
}

# 本番へ送るスクリプトの 1 つめの引数を読み、--dry-run なら MODE=dry、--yes なら MODE=yes にする。
# どちらでもなければ使い方を出して終わる。$2 は使い方の文の後ろに足す説明である。
read_mode() {
	case "${1:-}" in
	--dry-run) MODE=dry ;;
	--yes) MODE=yes ;;
	*)
		echo "--dry-run か --yes を指定する${2:-}" >&2
		exit 1
		;;
	esac
}

# origin/main を fetch し、そのコミットの $2（リポジトリ直下からのパス）を $1 へ展開する。送るコミットを表示する。
# fetch か展開に失敗したら（オフライン、$2 が origin/main に無いなど）、送る内容が決まらないため終わる。
# 作業ツリーの $2 に、未コミットの変更（未追跡のファイルを含む）か、HEAD にあって origin/main に無いコミットがあれば、それが送られないことを表示する。
extract_deploy_ref() {
	local dest="$1" path="$2" changes commits
	git -C "$REPO_ROOT" fetch -q origin main || {
		echo "origin の main を fetch できない。送る内容を origin/main に揃えられないため、止める" >&2
		exit 1
	}
	echo "送るコミット: $(git -C "$REPO_ROOT" log -1 --format='%h %s' "$DEPLOY_REF")"
	changes="$(git -C "$REPO_ROOT" status --porcelain --untracked-files=all -- "$path")"
	if [ -n "$changes" ]; then
		echo "作業ツリーの $path に未コミットの変更がある。これは送られない（送るのは $DEPLOY_REF である）:"
		printf '%s\n' "$changes" | sed 's/^/  /'
	fi
	commits="$(git -C "$REPO_ROOT" log --format='%h %s' "$DEPLOY_REF..HEAD" -- "$path")"
	if [ -n "$commits" ]; then
		echo "$path を変える、$DEPLOY_REF に無いコミットがある。これは main へマージするまで送られない:"
		printf '%s\n' "$commits" | sed 's/^/  /'
	fi
	git -C "$REPO_ROOT" archive "$DEPLOY_REF" "$path" | tar -x -C "$dest" || {
		echo "$DEPLOY_REF の $path を取り出せない" >&2
		exit 1
	}
}

# extract_deploy_ref で $1 へ展開した $2 を、本番の同じパスへ同期する。$2 に無いファイルは本番から消す。MODE が dry なら差分を表示するだけである。
# 送る前に、ディレクトリ 755・ファイル 644 に揃え、全 PHP ファイルの構文を確かめる。
sync_to_remote() {
	local src="$1/$2" dest="$DOCROOT/$2" f php_files=() dry=()
	normalize_perms "$src"
	while IFS= read -r -d '' f; do
		php_files+=("$f")
	done < <(find "$src" -name '*.php' -type f -print0)
	[ ${#php_files[@]} -eq 0 ] || php_lint "${php_files[@]}"

	if [ "$MODE" = yes ]; then
		remote "mkdir -p $dest"
	else
		dry=(--dry-run)
		if ! remote "test -d $dest"; then
			echo "送り先 $dest は本番にまだ無い（--yes で作る）。以下はすべて新規として表示される"
		fi
	fi
	# git archive の mtime はコミットの時刻で本番と一致しないため、内容の差は checksum で判定し、mtime は送らない。
	# -p で、揃えた権限を本番の既存ファイルにも写す。-i で権限だけの差も表示する。
	echo "rsync の差分（itemize。行頭 < は本番へ送る行、. は内容を送らず権限などの属性だけを変える行。c か s を含む f の行が内容の差、p を含む行が権限の差（< でも . でも、f でも d でも出る）。c・s・p を含まず T または t だけの行は時刻の違いで書き換えではない。*deleting は削除）:"
	rsync -rlpci --delete "${dry[@]}" -e "ssh ${SSH_OPTS[*]}" "$src/" "$SSH_HOST:$dest/"
}
