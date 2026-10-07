#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/dist"
ZIP_FILE="${OUT_DIR}/esperanca-wp-theme.zip"
STAGING_DIR="${OUT_DIR}/staging"
THEME_DIR="${STAGING_DIR}/esperanca-wp-theme"

mkdir -p "${OUT_DIR}"
rm -f "${ZIP_FILE}"
rm -rf "${STAGING_DIR}"

# Copia o tema para um staging (excluindo git, dist e afins), dentro de uma
# pasta de nível superior com o nome do tema. O WordPress espera que o ZIP de
# um tema tenha um único diretório contendo o style.css na raiz desse diretório.
# Os ativos pesados do acervo (assets/legacy/*) vão num pacote separado
# (scripts/package-legacy.sh) para manter o tema leve e fácil de enviar.
mkdir -p "${THEME_DIR}"
cd "${ROOT_DIR}"
tar \
  --exclude="./.git" \
  --exclude="./.github" \
  --exclude="./dist" \
  --exclude="./.DS_Store" \
  --exclude="./scripts" \
  --exclude="./assets/legacy/files" \
  --exclude="./assets/legacy/img" \
  -cf - . | (cd "${THEME_DIR}" && tar -xf -)

# Minifica CSS/JS no staging (não altera os fontes do tema).
bash "${ROOT_DIR}/scripts/minify-assets.sh" "${THEME_DIR}"

cd "${STAGING_DIR}"
zip -r "${ZIP_FILE}" esperanca-wp-theme

printf 'Created %s\n' "${ZIP_FILE}"
