#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/dist"
ZIP_FILE="${OUT_DIR}/feicoop-wp-template.zip"
STAGING_DIR="${OUT_DIR}/staging"

mkdir -p "${OUT_DIR}"
rm -f "${ZIP_FILE}"
rm -rf "${STAGING_DIR}"

# Copia o tema para um staging (excluindo git, dist e afins).
mkdir -p "${STAGING_DIR}"
cd "${ROOT_DIR}"
tar \
  --exclude="./.git" \
  --exclude="./.github" \
  --exclude="./dist" \
  --exclude="./.DS_Store" \
  --exclude="./scripts" \
  -cf - . | (cd "${STAGING_DIR}" && tar -xf -)

# Minifica CSS/JS no staging (não altera os fontes do tema).
bash "${ROOT_DIR}/scripts/minify-assets.sh" "${STAGING_DIR}"

cd "${STAGING_DIR}"
zip -r "${ZIP_FILE}" .

printf 'Created %s\n' "${ZIP_FILE}"
