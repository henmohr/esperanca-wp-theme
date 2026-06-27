# feicoop-wp-template

WordPress theme ported from the Publii FEICOOP theme.

[![Package theme](https://github.com/henmohr/feicoop-wp-template/actions/workflows/package-theme.yml/badge.svg?branch=main)](https://github.com/henmohr/feicoop-wp-template/actions/workflows/package-theme.yml)
[![Releases](https://img.shields.io/github/v/release/henmohr/feicoop-wp-template?label=release)](https://github.com/henmohr/feicoop-wp-template/releases)

## Download

To get a ZIP package ready to upload in WordPress:

1. Open the repository on GitHub.
2. Go to the [Releases page](https://github.com/henmohr/feicoop-wp-template/releases) and download the latest ZIP asset, or run the `Package theme` workflow in `Actions`.
3. If you want to build it locally, run `scripts/package-theme.sh`.

The ZIP includes the theme root files only, so it can be uploaded directly in `Appearance > Themes > Add New > Upload Theme` in WordPress.

## Configurar a página de notícias

Este tema usa a página de posts do WordPress como o arquivo principal de notícias.

1. Crie uma página chamada `Notícias` em `Pages > Add New`.
2. Vá em `Settings > Reading`.
3. Em `Your homepage displays`, mantenha a home como página inicial estática, se for o caso.
4. Em `Posts page`, selecione a página `Notícias`.
5. Salve as alterações.

Depois disso:

- As novas notícias aparecem automaticamente em `Notícias`, com a mais recente em destaque.
- O link para notícias usado pelo tema aponta para essa página.
- Se você adicionar a página ao menu principal, ela já vai abrir o arquivo editorial de notícias.
