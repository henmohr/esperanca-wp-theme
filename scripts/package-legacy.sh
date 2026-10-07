#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/dist"
ZIP_FILE="${OUT_DIR}/esperanca-wp-theme-legacy.zip"
STAGING_DIR="${OUT_DIR}/staging-legacy"

mkdir -p "${OUT_DIR}"
rm -f "${ZIP_FILE}"
rm -rf "${STAGING_DIR}"

# Empacota apenas os ativos pesados do acervo (imagens e anexos), mantendo a
# estrutura assets/legacy/... para que o usuário extraia dentro da pasta do
# tema via cPanel/FTP e as pastas se fundam corretamente.
mkdir -p "${STAGING_DIR}/assets/legacy"
cd "${ROOT_DIR}"
cp -R assets/legacy/img "${STAGING_DIR}/assets/legacy/img"
cp -R assets/legacy/files "${STAGING_DIR}/assets/legacy/files"

cd "${STAGING_DIR}"
zip -r "${ZIP_FILE}" .

printf 'Created %s\n' "${ZIP_FILE}"
