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
