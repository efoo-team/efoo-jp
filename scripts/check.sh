#!/usr/bin/env bash
# 本番の状態を読み取りだけで点検する。問題が 1 件でもあれば終了コード 1。
set -uo pipefail
source "$(dirname "$0")/lib.sh"

status=0
fail() { echo "NG: $*"; status=1; }

echo "== WordPress 本体"
remote_wp core version --extra
remote_wp core check-update
remote_wp core verify-checksums || fail "本体ファイルが公式チェックサムと一致しない"

echo "== プラグイン"
remote_wp plugin list --fields=name,status,version,update_version,auto_update
# 公式配布物と異なるファイルのうち、known-modified.tsv に同じ sha256 で載っているものは許容する。
mismatches="$(remote_wp plugin verify-checksums --all --format=csv 2>/dev/null | tail -n +2)"
while IFS=, read -r plugin file message; do
	[ -n "$plugin" ] || continue
	if [ "$plugin" = hello ]; then path="$DOCROOT/wp-content/plugins/$file"; else path="$DOCROOT/wp-content/plugins/$plugin/$file"; fi
	actual="$(remote "sha256sum '$path' 2>/dev/null | cut -d' ' -f1" </dev/null)"
	expected="$(awk -F'\t' -v p="$plugin" -v f="$file" '$1 == p && $2 == f { print $3 }' "$REPO_ROOT/scripts/known-modified.tsv")"
	if [ -n "$expected" ] && [ "$expected" = "$actual" ]; then
		echo "既知の改変: $plugin/$file"
	else
		fail "公式配布物と異なる: $plugin/$file ($message)"
	fi
done <<<"$mismatches"

echo "== テーマ"
remote_wp theme list --fields=name,status,version,update_version

echo "== 管理者"
remote_wp user list --role=administrator --fields=ID,user_login,user_email,user_registered

echo "== uploads 内の実行可能ファイル"
found="$(remote "find $DOCROOT/wp-content/uploads -type f \\( -iname '*.php*' -o -iname '*.phtml' -o -iname '*.phar' -o -name '.htaccess' \\)")"
if [ -n "$found" ]; then
	echo "$found"
	fail "uploads に実行可能ファイルがある"
else
	echo "なし"
fi

echo "== 直近 14 日に変更されたファイル（翻訳ファイルを除く）"
remote "cd $DOCROOT && find . -type f -mtime -14 ! -path './wp-content/languages/*' ! -path './wp-content/uploads/*' -printf '%TY-%Tm-%Td %TH:%TM %p\n' | sort"

echo "== HTTP"
code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/")"
echo "$SITE_URL/ -> $code"
[ "$code" = 200 ] || fail "$SITE_URL/ が 200 を返さない"

exit "$status"
