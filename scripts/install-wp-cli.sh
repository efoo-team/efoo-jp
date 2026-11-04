#!/usr/bin/env bash
# サーバーの ~/bin/wp に wp-cli を導入する。版は lib.sh の WP_CLI_VERSION で固定し、
# 公式の sha512 と照合してから置く。導入済みで版が同じなら何もしない。
set -euo pipefail
source "$(dirname "$0")/lib.sh"

remote bash -s -- "$WP_CLI_VERSION" <<'REMOTE'
set -euo pipefail
version="$1"
base="https://github.com/wp-cli/wp-cli/releases/download/v${version}/wp-cli-${version}.phar"
mkdir -p "$HOME/bin"
if [ -f "$HOME/bin/wp" ] && php "$HOME/bin/wp" --version 2>/dev/null | grep -qx "WP-CLI ${version}"; then
	echo "WP-CLI ${version} は導入済み"
	exit 0
fi
tmp="$(mktemp "$HOME/bin/wp.XXXXXX")"
trap 'rm -f "$tmp"' EXIT
curl -fsSL -o "$tmp" "$base"
expected="$(curl -fsSL "${base}.sha512" | awk '{print $1}')"
actual="$(sha512sum "$tmp" | awk '{print $1}')"
if [ "$expected" != "$actual" ]; then
	echo "sha512 が一致しない: expected=$expected actual=$actual" >&2
	exit 1
fi
chmod 755 "$tmp"
mv "$tmp" "$HOME/bin/wp"
trap - EXIT
php "$HOME/bin/wp" --version
REMOTE
