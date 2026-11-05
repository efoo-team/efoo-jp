#!/usr/bin/env bash
# origin/main の wp-content/themes/efoo-2026/ を本番の同じパスへ同期する。origin/main に無いファイルは本番から消す。
# どのチェックアウト・ブランチで実行しても、作業ツリーではなく fetch した origin/main を送る（scripts/lib.sh の extract_deploy_ref）。
#   bash scripts/deploy-theme.sh --dry-run   転送内容の確認だけ
#   bash scripts/deploy-theme.sh --yes       同期し、nginx のキャッシュを消して、本番を確かめる
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"

THEME=wp-content/themes/efoo-2026

# 本番のテーマを確かめる。本番へは書き込まない。$1 は extract_deploy_ref の展開先で、assets/ の URL の一覧に使う。
# 満たさないものを列挙し、1 つでもあれば 1 を返す。
verify_theme() {
	local ng=0 path code
	remote_check_perms "$DOCROOT/$THEME" || ng=1
	while IFS= read -r path; do
		code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/$THEME/$path")"
		if [ "$code" != 200 ]; then
			echo "$SITE_URL/$THEME/$path -> $code"
			ng=1
		fi
	done < <(cd "$1/$THEME" && find assets -type f | sort)
	[ "$ng" = 1 ] || echo "assets: すべて 200"
	code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/")"
	echo "$SITE_URL/ -> $code"
	[ "$code" = 200 ] || ng=1
	return "$ng"
}

# 送ったあとの処理。キャッシュの消去が失敗しても本番の確認まで行い、どちらかが失敗していれば 1 を返す。
finish_deploy() {
	local ng=0
	# 画像は ?ver= が付かず、HTML のキャッシュには古い ?ver= が残るため、送ったあとに nginx のキャッシュを消す。
	clear_cache || ng=1
	verify_theme "$1" || ng=1
	return "$ng"
}

main() {
	local tmp
	read_mode "${1:-}"
	tmp="$(mktemp -d)"
	# shellcheck disable=SC2064 # tmp は確定済みの値を埋め込む
	trap "rm -rf '$tmp'" EXIT
	extract_deploy_ref "$tmp" "$THEME"
	sync_to_remote "$tmp" "$THEME"
	if [ "$MODE" = yes ]; then
		finish_deploy "$tmp"
	fi
}

if [ "${BASH_SOURCE[0]}" = "$0" ]; then
	main "$@"
fi
