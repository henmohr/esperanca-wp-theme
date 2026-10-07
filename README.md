# esperanca-wp-theme

WordPress theme ported from the Publii FEICOOP theme.

[![Package theme](https://github.com/henmohr/esperanca-wp-theme/actions/workflows/package-theme.yml/badge.svg?branch=main)](https://github.com/henmohr/esperanca-wp-theme/actions/workflows/package-theme.yml)
[![Releases](https://img.shields.io/github/v/release/henmohr/esperanca-wp-theme?label=release)](https://github.com/henmohr/esperanca-wp-theme/releases)

## Download

O release contém dois arquivos:

- **`esperanca-wp-theme.zip`** — o tema (~5 MB), pronto para instalar.
- **`esperanca-wp-theme-legacy.zip`** — o acervo do site antigo (PDFs e imagens, ~73 MB), mantido à parte para não estourar o limite de upload do WordPress.

Para instalar:

1. Baixe os dois ZIPs na [página de Releases](https://github.com/henmohr/esperanca-wp-theme/releases).
2. Envie `esperanca-wp-theme.zip` em `Appearance > Themes > Add New > Upload Theme`.
3. Envie `esperanca-wp-theme-legacy.zip` via cPanel (File Manager) ou FTP para a pasta do tema (`wp-content/themes/esperanca-wp-theme/`) e extraia dentro dela.
4. Ative o tema (ou recarregue o painel) — o acervo antigo é importado automaticamente em lotes.

Para gerar os pacotes localmente: `scripts/package-theme.sh` (tema) e `scripts/package-legacy.sh` (acervo).

## Atualizações automáticas

O tema se auto atualiza a partir dos releases do GitHub, sem depender de plugins externos.

- A biblioteca [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) (em `inc/plugin-update-checker/`) consulta o release mais recente do repositório.
- Quando a versão publicada é maior que a instalada, o aviso aparece em `Appearance > Themes`, com o botão "Atualizar agora" — igual a um tema do WordPress.org.
- A atualização baixa apenas o `esperanca-wp-theme.zip` (tema) do release. O pacote do acervo não é rebaixado, pois o conteúdo já fica na biblioteca de mídia após a primeira importação.

### Como lançar uma nova versão

1. Incremente o número em `Version` no `style.css`.
2. Envie o commit para a branch `main` (ou crie uma tag `vX.Y.Z`).
3. O workflow `Package theme` cria ou atualiza o release automaticamente com o ZIP.

> **Importante:** o número do `style.css` precisa ser incrementado a cada lançamento. A comparação de versão é feita pelo `Version` do `style.css` publicado no release — se ele não mudar, o WordPress não detecta atualização.

A verificação usa a API pública do GitHub (sem token) e roda no máximo a cada 12 horas; também é disparada ao visitar a tela de temas ou de atualizações.

## Configurar a página de notícias

Este tema usa a página de posts do WordPress como o arquivo principal de notícias.

Ao ativar o tema, ele cria automaticamente as páginas base usadas na navegação:

- `Início`
- `Quem somos`
- `História`
- `Rede Esperança`
- `Feirão Colonial`
- `Contato`
- `Inscrições`
- `Notícias`

As páginas são criadas apenas se ainda não existirem, para não sobrescrever conteúdo já editado.

Depois disso:

- As novas notícias aparecem automaticamente em `Notícias`, com a mais recente em destaque.
- O link para notícias usado pelo tema aponta para essa página.
- Se você adicionar a página ao menu principal, ela já vai abrir o arquivo editorial de notícias.

## Adicionar itens na programação

A programação usa um tipo de conteúdo próprio no painel do WordPress. O tema também pode criar automaticamente os itens iniciais da 32ª FEICOOP a partir da programação oficial, para que você já encontre conteúdo publicado ao ativar o tema.

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

Se você quiser usar a programação oficial como base, basta revisar os itens já criados e ajustar textos, horários ou locais no painel.

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

## Configurar os patrocinadores da Início

O carrossel da página `Início` foi pensado para exibir logos ou imagens dos patrocinadores.

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

Esse bloco fica dentro da página `Início`, que o tema cria e usa como página inicial quando a instalação ainda não tem uma front page definida.

### Formato recomendado das imagens

Para que os patrocinadores apareçam corretamente no carrossel do topo:

- prefira logos horizontais ou faixas curtas, não imagens muito altas;
- use PNG com fundo transparente ou SVG, quando possível;
- evite artes com muito espaço vazio ao redor do logo;
- mantenha a arte em proporção horizontal, porque o banner trabalha com altura fixa;
- se o logo for vertical, ele pode parecer pequeno dentro do carrossel.

Se a marca do patrocinador precisar aparecer maior, a melhor solução é preparar uma versão horizontal específica para o topo.
