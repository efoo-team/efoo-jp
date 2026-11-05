#!/usr/bin/env bash
# リポジトリの wp-content/mu-plugins/ を本番の同じパスへ同期する。リポジトリに無いファイルは本番から消す。
# 送る前に一時ディレクトリへ写してディレクトリ 755・ファイル 644 に揃え、その権限を本番へ写す。
#   bash scripts/deploy-mu-plugins.sh --dry-run   転送内容の確認だけ
#   bash scripts/deploy-mu-plugins.sh --yes       同期する
set -euo pipefail
source "$(dirname "$0")/lib.sh"
cd "$REPO_ROOT"

case "${1:-}" in
--dry-run) dry=(--dry-run) ;;
--yes) dry=() ;;
*)
	echo "--dry-run か --yes を指定する" >&2
	exit 1
	;;
esac

php_lint wp-content/mu-plugins/*.php
tmp="$(mktemp -d)"
# shellcheck disable=SC2064 # tmp は確定済みの値を埋め込む
trap "rm -rf '$tmp'" EXIT
cp -Rp wp-content/mu-plugins "$tmp/"
normalize_perms "$tmp/mu-plugins"
remote "mkdir -p $DOCROOT/wp-content/mu-plugins"
# -i で、権限や mtime だけの差も種類が分かるように表示する。
rsync -rlptvi --delete "${dry[@]}" -e "ssh ${SSH_OPTS[*]}" "$tmp/mu-plugins/" "$SSH_HOST:$DOCROOT/wp-content/mu-plugins/"
if [ ${#dry[@]} -eq 0 ]; then
	remote_wp plugin list --status=must-use
	ng=0
	remote_check_perms "$DOCROOT/wp-content/mu-plugins" || ng=1
	code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/")"
	echo "$SITE_URL/ -> $code"
	[ "$code" = 200 ] || ng=1
	exit "$ng"
fi
