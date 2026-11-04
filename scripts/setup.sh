#!/usr/bin/env bash
# ローカル環境を一度に用意する: 前提の確認 → 依存の導入 → wp-env 起動 → 本番の内容の取り込み。
# 本番へ SSH で接続できればバックアップを取得してから取り込み、接続できなければ
# .local/backups/ にある最新のバックアップを取り込む。何度実行してもよい。
set -euo pipefail
source "$(dirname "$0")/lib.sh"
cd "$REPO_ROOT"

die() { echo "セットアップを中止: $*" >&2; exit 1; }

docker info >/dev/null 2>&1 || die "Docker が起動していない。OrbStack か Docker Desktop を起動してから再実行する"
node -e 'const [a,b]=process.versions.node.split(".").map(Number); process.exit(a>22||(a===22&&b>=13)?0:1)' \
	|| die "Node.js 22.13 以上が要る（現在 $(node -v 2>/dev/null || echo 未導入)）"
command -v pnpm >/dev/null 2>&1 || die "pnpm が無い。corepack enable pnpm を実行してから再実行する"

pnpm install --frozen-lockfile
pnpm exec wp-env start

if remote true 2>/dev/null; then
	bash scripts/pull.sh --fresh
elif find .local/backups -mindepth 1 -maxdepth 1 -type d -name '20*' 2>/dev/null | grep -q .; then
	echo "本番へ SSH で接続できないため、取得済みの最新のバックアップを取り込む"
	bash scripts/pull.sh
else
	die "本番へ SSH で接続できず、バックアップも無い。AGENTS.md の「本番の内容を手に入れる」を参照する"
fi
