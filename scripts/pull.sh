#!/usr/bin/env bash
# 本番のバックアップをローカルの wp-env に取り込む。ローカルの DB と wp-content は本番の内容で置き換わる。
#   bash scripts/pull.sh           最新のバックアップを取り込む
#   bash scripts/pull.sh --fresh   先に本番のバックアップを取得してから取り込む
set -euo pipefail
source "$(dirname "$0")/lib.sh"
cd "$REPO_ROOT"

if [ "${1:-}" = --fresh ]; then
	bash scripts/backup.sh
fi

backup="$(find .local/backups -mindepth 1 -maxdepth 1 -type d -name '20*' 2>/dev/null | sort | tail -1 || true)"
if [ -z "$backup" ]; then
	echo "バックアップが無い。bash scripts/pull.sh --fresh で取得してから取り込む" >&2
	exit 1
fi
echo "取り込むバックアップ: $backup"

port="${WP_ENV_PORT:-$(node -p 'require("./.wp-env.json").port')}"
local_url="http://localhost:$port"
wp() { pnpm -s exec wp-env run cli wp "$@"; }

for dir in plugins themes uploads languages; do
	mkdir -p ".local/site/wp-content/$dir"
	rsync -a --delete "$backup/files/wp-content/$dir/" ".local/site/wp-content/$dir/"
done

mkdir -p .local/import
gunzip -c "$backup/db.sql.gz" >.local/import/db.sql
trap 'rm -f .local/import/db.sql' EXIT

wp db reset --yes
wp db import efoo-import/db.sql
wp search-replace "$SITE_URL" "$local_url" --all-tables --precise --report-changed-only
wp search-replace "${SITE_URL/https:/http:}" "$local_url" --all-tables --precise --report-changed-only

# 本番の管理者のパスワードはローカルでは分からないため、ローカル専用の管理者を用意する。
if wp user get admin --field=ID >/dev/null 2>&1; then
	wp user update admin --user_pass=password --role=administrator --skip-email
else
	wp user create admin admin@localhost.test --role=administrator --user_pass=password
fi
wp rewrite flush
wp cache flush

echo "完了: $local_url/ （管理画面 $local_url/wp-admin/ は admin / password）"
