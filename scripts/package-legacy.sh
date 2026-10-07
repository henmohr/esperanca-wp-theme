#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/dist"
ARCHIVE_FILE="${OUT_DIR}/esperanca-wp-theme-legacy.tar.gz"
STAGING_DIR="${OUT_DIR}/staging-legacy"

mkdir -p "${OUT_DIR}"
rm -f "${ARCHIVE_FILE}"
rm -rf "${STAGING_DIR}"

# Empacota apenas os ativos pesados do acervo (imagens e anexos), mantendo a
# estrutura assets/legacy/... para que o usuário extraia dentro da pasta do
# tema via cPanel/FTP e as pastas se fundam corretamente.
#
# Usa .tar.gz (e não .zip) de propósito: o auto-update baixa o ".zip" do tema
# filtrando pelo sufixo, e manter o acervo em .tar.gz evita que ele seja
# confundido com o pacote do tema.
mkdir -p "${STAGING_DIR}/assets/legacy"
cd "${ROOT_DIR}"
cp -R assets/legacy/img "${STAGING_DIR}/assets/legacy/img"
cp -R assets/legacy/files "${STAGING_DIR}/assets/legacy/files"

cd "${STAGING_DIR}"
tar -czf "${ARCHIVE_FILE}" .

printf 'Created %s\n' "${ARCHIVE_FILE}"
