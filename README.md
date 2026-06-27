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

## Adicionar itens na programação

A programação usa um tipo de conteúdo próprio no painel do WordPress.

1. Vá em `Programação` no menu lateral do administrador.
2. Clique em `Adicionar item de programação`.
3. Preencha o título e o conteúdo do item.
4. Na caixa `Detalhes da programação`, informe:
   - `Data`
   - `Horário`
   - `Local`
   - `Faixa / categoria`
5. Marque `Destacar este item na programação` se quiser dar ênfase ao item na listagem.
6. Publique o item.

Depois disso, o item passa a aparecer automaticamente no arquivo de programação do site.

### Dica de organização

- Use a `Faixa / categoria` para separar as trilhas da programação.
- Prefira nomes curtos e consistentes para cada trilha.
- Itens com a mesma trilha aparecem agrupados e com cor própria no arquivo de programação.

## Colocar programação no menu

Se quiser mostrar a programação na navegação principal:

1. Vá em `Appearance > Menus` ou `Appearance > Editor`, dependendo da instalação.
2. Abra o menu principal usado no site.
3. Adicione a página `Programação`, se ela existir como página dedicada, ou o link do arquivo de programação.
4. Salve o menu.

O tema também inclui a programação no menu padrão quando não há menu configurado.

## Configurar os patrocinadores do topo

O carrossel do topo da home foi pensado para exibir logos ou imagens dos patrocinadores.

1. Vá em `Pages > Home` e edite a página inicial.
2. Abra a caixa `Patrocinadores do topo`.
3. Clique em `Enviar ou selecionar imagens`.
4. No modal da biblioteca de mídia, você pode:
   - fazer upload de novos arquivos;
   - selecionar imagens já enviadas;
   - reorganizar a ordem antes de confirmar.
5. Se precisar, use `Remover` para apagar apenas uma imagem do preview.
6. Use `Limpar` para remover todas de uma vez.
7. Salve a página.

O preview do bloco tem altura limitada e rolagem interna, para não ocupar a tela inteira quando houver muitas imagens.

### Formato recomendado das imagens

Para que os patrocinadores apareçam corretamente no carrossel do topo:

- prefira logos horizontais ou faixas curtas, não imagens muito altas;
- use PNG com fundo transparente ou SVG, quando possível;
- evite artes com muito espaço vazio ao redor do logo;
- mantenha a arte em proporção horizontal, porque o banner trabalha com altura fixa;
- se o logo for vertical, ele pode parecer pequeno dentro do carrossel.

Se a marca do patrocinador precisar aparecer maior, a melhor solução é preparar uma versão horizontal específica para o topo.
