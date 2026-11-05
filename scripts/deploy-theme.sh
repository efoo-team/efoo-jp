#!/usr/bin/env bash
# origin/main の wp-content/themes/efoo-2026/ を本番の同じパスへ同期する。origin/main に無いファイルは本番から消す。
# 作業ツリーではなく origin/main を送るのは、本番のテーマを main と一致させるためである。
#   bash scripts/deploy-theme.sh --dry-run   転送内容の確認だけ
#   bash scripts/deploy-theme.sh --yes       同期し、nginx のキャッシュを消して、本番を確かめる
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"

THEME=wp-content/themes/efoo-2026
REF=origin/main

# origin/main のテーマを、ディレクトリ 755・ファイル 644 にして $1 へ展開する。
extract_theme() {
	git -C "$REPO_ROOT" fetch -q origin main
	echo "送るコミット: $(git -C "$REPO_ROOT" log -1 --format='%h %s' "$REF")"
	git -C "$REPO_ROOT" archive "$REF" "$THEME" | tar -x -C "$1"
	normalize_perms "$1/$THEME"
}

# 本番のテーマを確かめる。本番へは書き込まない。$1 は extract_theme の展開先で、assets/ の URL の一覧に使う。
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

# 本番の nginx のキャッシュを消す。URL は $1 で差し替えられ、省略時は本番の消去 URL である。
# 応答が 200 でなければ（curl 自体の失敗で 000 になる場合を含む）、手で消す方法を出して 1 を返す。
clear_cache() {
	local url="${1:-http://127.0.0.1:9080/clearcache?cwd=$DOCROOT}" code
	code="$(remote "curl -s -o /dev/null -w '%{http_code}' -m 20 '$url'")" || code=000
	[ -n "$code" ] || code=000
	echo "nginx のキャッシュの消去 -> $code"
	if [ "$code" != 200 ]; then
		echo "キャッシュを消せなかった。古い HTML が残るため、サーバーに ssh で入り、次を実行して手で消す:"
		echo "  curl '$url'"
		return 1
	fi
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
	local dry tmp f php_files=()
	case "${1:-}" in
	--dry-run) dry=(--dry-run) ;;
	--yes) dry=() ;;
	*)
		echo "--dry-run か --yes を指定する" >&2
		exit 1
		;;
	esac

	tmp="$(mktemp -d)"
	# shellcheck disable=SC2064 # tmp は確定済みの値を埋め込む
	trap "rm -rf '$tmp'" EXIT
	extract_theme "$tmp"
	while IFS= read -r -d '' f; do
		php_files+=("$f")
	done < <(find "$tmp/$THEME" -name '*.php' -type f -print0)
	php_lint "${php_files[@]}"

	if [ ${#dry[@]} -eq 0 ]; then
		remote "mkdir -p $DOCROOT/$THEME"
	elif ! remote "test -d $DOCROOT/$THEME"; then
		echo "送り先 $DOCROOT/$THEME は本番にまだ無い（--yes で作る）。以下はすべて新規として表示される"
	fi
	# git archive の mtime は本番と一致しないため、内容の差は checksum で判定し、mtime は送らない。
	# -p で、揃えた権限を本番の既存ファイルにも写す。-i で権限だけの差も表示する。
	echo "rsync の差分（itemize。行頭 <f のうち c・s を含む行が内容の差、p を含む行が権限の差。T だけの行は時刻の違いで書き換えではない）:"
	rsync -rlpci --delete "${dry[@]}" -e "ssh ${SSH_OPTS[*]}" "$tmp/$THEME/" "$SSH_HOST:$DOCROOT/$THEME/"
	if [ ${#dry[@]} -eq 0 ]; then
		finish_deploy "$tmp"
	fi
}

if [ "${BASH_SOURCE[0]}" = "$0" ]; then
	main "$@"
fi
