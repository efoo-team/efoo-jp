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
