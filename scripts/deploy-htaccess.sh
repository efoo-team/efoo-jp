#!/usr/bin/env bash
# 本番 docroot の .htaccess の「# BEGIN efoo cache」〜「# END efoo cache」を、server/htaccess/efoo-cache.conf の内容にする。
# 区間がまだ無ければ .htaccess の末尾に足す。区間の外（# BEGIN WordPress の区間など）は変えない。
#   bash scripts/deploy-htaccess.sh --dry-run   書き換えた後との差分を表示するだけ（本番の .htaccess には書かない）
#   bash scripts/deploy-htaccess.sh --yes       書き換え、nginx のキャッシュを消して、ファイルの種類ごとの応答ヘッダーを確かめる
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"

MARKER='efoo cache'
CONF="$REPO_ROOT/server/htaccess/efoo-cache.conf"
HELPER="$REPO_ROOT/scripts/htaccess-block.php"
HTACCESS="$DOCROOT/.htaccess"

# server/htaccess/efoo-cache.conf の保存期間と揃える。
YEAR_IMMUTABLE='max-age=31536000, immutable'
YEAR='max-age=31536000'
MONTH='max-age=2592000'

# 作業用の一時ディレクトリ。
TMP=
# 書き換える前に写した .htaccess のサーバー上の場所と手元の場所。ROLLBACK_FROM が空でない間に終わると、on_exit がそこから戻す。
ROLLBACK_FROM=
LOCAL_SAVED=

# サーバーの wp-cli で scripts/htaccess-block.php を実行する。$1 は render か write。
run_helper() {
	local encoded
	encoded="$(base64 <"$CONF" | tr -d '\n')"
	remote_wp eval-file - "$1" "$MARKER" "$encoded" <"$HELPER"
}

# $1 の URL の HTTP ステータスを出力する。
http_code() {
	curl -s -o /dev/null -w '%{http_code}' -m 20 "$1" || true
}

# $1 の URL の Cache-Control が $2 と一致するかを確かめる。$2 が空なら、Cache-Control が無いことを確かめる。
expect_cache_control() {
	local url="$1" want="$2" headers code got
	headers="$(curl -sI -m 20 "$url" | tr -d '\r')" || true
	code="$(printf '%s\n' "$headers" | awk 'NR == 1 { print $2 }')"
	got="$(printf '%s\n' "$headers" | awk -F': ' 'tolower($1) == "cache-control" { print $2 }')"
	if [ "$code" = 200 ] && [ "$got" = "$want" ]; then
		echo "OK  ${want:-（Cache-Control なし）}  $url"
	else
		echo "NG  $code Cache-Control: ${got:-（なし）}（期待: ${want:-（なし）}）  $url"
		return 1
	fi
}

# トップの HTML から、$1 の拡張表現に最初に一致する URL を出力する。
first_url() {
	grep -oE "$1" "$2" | head -1 || true
}

# 書き換えた後の本番を確かめる。本番へは書き込まない。満たさないものを列挙し、1 つでもあれば 1 を返す。
# 種類ごとの代表の URL は、そのときのトップの HTML から拾う。フォントはトップから読み込まないため、WordPress 本体に入っている固定の URL を使う。
verify() {
	local ng=0 html css js theme_img upload_img name url
	html="$(mktemp)"
	curl -s -m 20 -o "$html" "$SITE_URL/" || true
	expect_cache_control "$SITE_URL/" '' || ng=1
	css="$(first_url "$SITE_URL/[^\"' ]+\\.css\\?ver=[^\"' ]+" "$html")"
	js="$(first_url "$SITE_URL/[^\"' ]+\\.js\\?ver=[^\"' ]+" "$html")"
	theme_img="$(first_url "$SITE_URL/wp-content/themes/[^\"' ?]+\\.(jpe?g|png|webp)" "$html")"
	upload_img="$(first_url "$SITE_URL/wp-content/uploads/[^\"' ?]+\\.(jpe?g|png|webp)" "$html")"
	rm -f "$html"
	for name in css js theme_img upload_img; do
		if [ -z "${!name}" ]; then
			echo "NG  トップの HTML に $name の代表になる URL が無い"
			ng=1
		fi
	done
	if [ -n "$css" ]; then
		expect_cache_control "$css" "$YEAR_IMMUTABLE" || ng=1
		# 同じ CSS を ?ver= 無しで取ると、保存期間は付かない。
		expect_cache_control "${css%%\?*}" '' || ng=1
	fi
	[ -z "$js" ] || expect_cache_control "$js" "$YEAR_IMMUTABLE" || ng=1
	for url in "$theme_img" "$upload_img"; do
		[ -z "$url" ] || expect_cache_control "$url" "$MONTH" || ng=1
	done
	expect_cache_control "$SITE_URL/wp-includes/fonts/dashicons.woff2" "$YEAR" || ng=1
	return "$ng"
}

# 書き換える前に写しておいた .htaccess（サーバー上の ROLLBACK_FROM）に戻し、戻った内容が写しと一致するかを確かめる。
# 戻せなかった、または戻ったことを確かめられなかったときは、手で戻すコマンドを出して 1 を返す。
rollback() {
	local url
	url="$SITE_URL/?efoo-htaccess-check=restored-$(date +%s)"
	echo "$HTACCESS を、書き換える前の写し $ROLLBACK_FROM に戻す"
	if remote "cp '$ROLLBACK_FROM' '$HTACCESS'" && remote "cat '$HTACCESS'" >"$TMP/restored" && cmp -s "$LOCAL_SAVED" "$TMP/restored"; then
		echo "戻した"
		clear_cache || true
		echo "戻した後の $url -> $(http_code "$url")"
		return 0
	fi
	echo "戻せなかった、または戻ったことを確かめられなかった。次のコマンドで手で戻し、$SITE_URL/ が 200 を返すことを確かめる:"
	echo "  ssh ${SSH_OPTS[*]} $SSH_HOST \"cp '$ROLLBACK_FROM' '$HTACCESS'\""
	echo "サーバーの写しが使えないときは、手元の写し $LOCAL_SAVED の内容を $HTACCESS に書く"
	return 1
}

# 終わるときの処理。書き換えを始めてから、確かめ終える前に終わった（途中のコマンドの失敗や中断を含む）ときは、写しに戻して終了コード 1 にする。
on_exit() {
	local status=$?
	set +e
	trap - EXIT INT TERM HUP
	if [ -n "$ROLLBACK_FROM" ]; then
		echo "書き換えを始めてから、確かめ終える前に終わった（終了コード ${status}）"
		rollback
		status=1
	fi
	[ -z "$TMP" ] || rm -rf "$TMP"
	exit "$status"
}

main() {
	local mode stamp saved code ng=0
	case "${1:-}" in
	--dry-run) mode=dry ;;
	--yes) mode=yes ;;
	*)
		echo "--dry-run か --yes を指定する" >&2
		exit 1
		;;
	esac

	TMP="$(mktemp -d)"
	trap on_exit EXIT
	trap 'exit 1' INT TERM HUP
	php_lint "$HELPER"
	remote "cat '$HTACCESS'" >"$TMP/current"
	run_helper render >"$TMP/new"
	echo "$HTACCESS の差分（- が今、+ が書き換えた後。何も出なければ差分なし）:"
	if diff -u --label "${HTACCESS}（今）" --label "${HTACCESS}（書き換えた後）" "$TMP/current" "$TMP/new"; then
		echo "差分なし"
	fi
	[ "$mode" = yes ] || return 0

	if ! cmp -s "$TMP/current" "$TMP/new"; then
		stamp="$(date +%Y%m%d-%H%M%S)"
		saved="\$HOME/htaccess-before-deploy/efoo.jp-$stamp"
		saved="$(remote "mkdir -p \$HOME/htaccess-before-deploy && cp -p '$HTACCESS' $saved && echo $saved")"
		LOCAL_SAVED="$REPO_ROOT/.local/htaccess-before-deploy/efoo.jp-$stamp"
		mkdir -p "$(dirname "$LOCAL_SAVED")"
		cp "$TMP/current" "$LOCAL_SAVED"
		echo "書き換える前の .htaccess の写し: サーバーの ${saved}、手元の $LOCAL_SAVED"

		# ここから、下で ROLLBACK_FROM を空に戻すまでの間に終わると、on_exit が写しに戻す。
		ROLLBACK_FROM="$saved"
		run_helper write
		remote "cat '$HTACCESS'" >"$TMP/written"
		if ! cmp -s "$TMP/new" "$TMP/written"; then
			echo "書き換えた後の $HTACCESS が、差分で示した内容と一致しない"
			exit 1
		fi
		# nginx のキャッシュに無い URL で、Apache が .htaccess を読めているか（500 にならないか）を確かめる。
		code="$(http_code "$SITE_URL/?efoo-htaccess-check=$stamp")"
		echo "$SITE_URL/?efoo-htaccess-check=$stamp -> $code"
		[ "$code" = 200 ] || exit 1
		ROLLBACK_FROM=
	fi

	# nginx は応答の max-age の間ファイルを保持するため、書き換える前の応答を消してから確かめる。
	clear_cache || ng=1
	verify || ng=1
	exit "$ng"
}

if [ "${BASH_SOURCE[0]}" = "$0" ]; then
	main "$@"
fi
