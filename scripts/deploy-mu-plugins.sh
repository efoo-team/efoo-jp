#!/usr/bin/env bash
# origin/main の wp-content/mu-plugins/ を本番の同じパスへ同期する。origin/main に無いファイルは本番から消す。
# どのチェックアウト・ブランチで実行しても、作業ツリーではなく fetch した origin/main を送る（scripts/lib.sh の extract_deploy_ref）。
#   bash scripts/deploy-mu-plugins.sh --dry-run   転送内容の確認だけ
#   bash scripts/deploy-mu-plugins.sh --yes       同期し、本番を確かめる
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"

MU_PLUGINS=wp-content/mu-plugins

# 本番の mu-plugin を確かめる。本番へは書き込まない。満たさないものを列挙し、1 つでもあれば 1 を返す。
verify_mu_plugins() {
	local ng=0 code
	remote_wp plugin list --status=must-use || ng=1
	remote_check_perms "$DOCROOT/$MU_PLUGINS" || ng=1
	code="$(curl -s -o /dev/null -w '%{http_code}' -m 20 "$SITE_URL/")"
	echo "$SITE_URL/ -> $code"
	[ "$code" = 200 ] || ng=1
	return "$ng"
}

main() {
	local tmp
	read_mode "${1:-}"
	tmp="$(mktemp -d)"
	# shellcheck disable=SC2064 # tmp は確定済みの値を埋め込む
	trap "rm -rf '$tmp'" EXIT
	extract_deploy_ref "$tmp" "$MU_PLUGINS"
	sync_to_remote "$tmp" "$MU_PLUGINS"
	if [ "$MODE" = yes ]; then
		verify_mu_plugins
	fi
}

if [ "${BASH_SOURCE[0]}" = "$0" ]; then
	main "$@"
fi
