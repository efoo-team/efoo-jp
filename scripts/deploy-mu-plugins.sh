#!/usr/bin/env bash
# リポジトリの wp-content/mu-plugins/ を本番の同じパスへ同期する。リポジトリに無いファイルは本番から消す。
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

for f in wp-content/mu-plugins/*.php; do
	php -l "$f" >/dev/null 2>&1 || pnpm -s exec wp-env run cli php -l "/var/www/html/$f"
done
remote "mkdir -p $DOCROOT/wp-content/mu-plugins"
rsync -rlptv --delete "${dry[@]}" -e "ssh ${SSH_OPTS[*]}" wp-content/mu-plugins/ "$SSH_HOST:$DOCROOT/wp-content/mu-plugins/"
if [ ${#dry[@]} -eq 0 ]; then
	remote_wp plugin list --status=must-use
	code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/")"
	echo "$SITE_URL/ -> $code"
	[ "$code" = 200 ]
fi
