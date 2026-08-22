#!/usr/bin/env bash
# Minifica CSS/JS do tema em um diretório de staging (não altera os fontes).
# Uso: minify-assets.sh <dir-do-tema>
#
# Usa npx (lightningcss para CSS, terser para JS). Se não houver rede/npm,
# termina com aviso sem quebrar o empacotamento.
set -euo pipefail

THEME_DIR="${1:?Informe o diretório do tema}"

if [ ! -d "${THEME_DIR}" ]; then
  echo "Diretório do tema não encontrado: ${THEME_DIR}" >&2
  exit 1
fi

cd "${THEME_DIR}"

# --- CSS -------------------------------------------------------------------
if npx --yes lightningcss-cli --version >/dev/null 2>&1; then
  while IFS= read -r -d '' f; do
    npx --yes lightningcss-cli --minify -o "${f}.tmp" "${f}"
    mv "${f}.tmp" "${f}"
  done < <(find assets/css -maxdepth 1 -name '*.css' -not -name '*.min.css' -print0)
  echo "CSS minificado com lightningcss."
else
  echo "AVISO: lightningcss indisponível (sem npm/rede?) — CSS não minificado." >&2
fi

# --- JS --------------------------------------------------------------------
if npx --yes terser --version >/dev/null 2>&1; then
  while IFS= read -r -d '' f; do
    npx --yes terser "${f}" -o "${f}.tmp" --compress --mangle
    mv "${f}.tmp" "${f}"
  done < <(printf '%s\0' assets/js/scripts.js assets/js/banner-carousel.js)
  echo "JS minificado com terser."
else
  echo "AVISO: terser indisponível (sem npm/rede?) — JS não minificado." >&2
fi
