#!/usr/bin/env bash
# 本番の DB と docroot 全体を .local/backups/<日時>/ に取得する（サーバーへは書き込まない）。
# docroot は前回のバックアップとの差分だけを転送し、変わっていないファイルはハードリンクで共有する。
set -euo pipefail
source "$(dirname "$0")/lib.sh"

backups="$REPO_ROOT/.local/backups"
stamp="$(date +%Y%m%d-%H%M%S)"
dest="$backups/$stamp"
prev="$(find "$backups" -mindepth 1 -maxdepth 1 -type d -name '20*' 2>/dev/null | sort | tail -1 || true)"
mkdir -p "$dest/files"

echo "DB を取得: $dest/db.sql.gz"
remote "php \$HOME/bin/wp --path=$DOCROOT db export - --single-transaction --default-character-set=utf8mb4 | gzip" >"$dest/db.sql.gz"
gzip -t "$dest/db.sql.gz"

echo "docroot を取得: $dest/files/"
link_dest=()
if [ -n "$prev" ] && [ -d "$prev/files" ]; then
	link_dest=(--link-dest="$prev/files")
fi
rsync -a "${link_dest[@]}" -e "ssh ${SSH_OPTS[*]}" "$SSH_HOST:$DOCROOT/" "$dest/files/"

remote_wp core version >"$dest/core-version.txt"
remote_wp plugin list --format=csv >"$dest/plugins.csv"
remote_wp theme list --format=csv >"$dest/themes.csv"

du -sh "$dest"
echo "完了: $dest"
