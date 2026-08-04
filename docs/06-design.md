# Etapa 6 — Design

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel + Blade + Bootstrap 5)

Prévia visual publicada (paleta, tipografia, componentes, mockups de painel/programação/stands): ver o artefacto gerado nesta etapa. Este documento é a especificação escrita, oficial e duradoura — a prévia visual expira com a sessão; este ficheiro não.

---

## 1. Porquê esta paleta e não uma "cream + serif + terracotta" genérica

O prompt original pedia amarelo/laranja/branco/cinza claro/verde, "moderno... sem que se note que usei IA". Paletas quentes com bege/creme + serif + terracota são hoje o "piloto automático" de muita geração de design — por isso, em vez de suavizar os tons pedidos até ao bege, foram escolhidos tons **saturados e específicos da feira**: milho, brasa, capim — cada um com trabalho funcional definido, não decorativo. O neutro claro tem um viés verde (capim), não creme/pêssego, para não cair nesse cliché. O uso do tipo serifado (Fraunces) é deliberadamente restrito a títulos — nunca a parágrafos inteiros.

---

## 2. Paleta de cores

| Nome | Hex (claro) | Hex (escuro) | Uso |
|---|---|---|---|
| Milho | `#FFC42B` | `#FFD35C` | Cor de marca principal — CTAs primários, destaques, hero |
| Brasa | `#E85D2C` | `#FF8659` | Acento — botões secundários, links, elementos "em curso" |
| Capim (forte) | `#2F8F5B` | `#4FBE86` | Verde decorativo — ícones, grandes números |
| Capim (texto) | `#256B44` | `#56C088` | Verde para texto/badge — garante AA sobre fundo claro/tinta |
| Âmbar | `#8A5A0E` | `#E3AC52` | Semântico — estado "pendente"/"reservado" (distinto do Milho) |
| Tijolo | `#7A2415` | `#E8846E` | Semântico — estado "rejeitada"/"erro" (distinto da Brasa) |
| Tinta | `#241B10` | `#F5EFE1` | Texto principal |
| Papel | `#FFFEFB` | `#1B1712` | Branco — fundo de cartões/superfícies |
| Cinza-claro | `#F2F4EE` | `#211B13` | Fundo de secção — viés verde, não creme |
| Linha | `#E3E0D3` | `#3A3327` | Bordas e divisórias |

**Regra de contraste:** texto tinta sobre milho = 10.4:1; texto tinta sobre brasa = 5.0:1; texto branco sobre capim-texto = 6.4:1. Todos acima do mínimo AA (4.5:1) para texto normal — validado por cálculo de luminância relativa (WCAG 2.1), não por inspeção visual.

**Cor semântica é separada da cor de marca:** Milho/Brasa são identidade, não significado. Estado usa sempre a trinca Capim (bom) / Âmbar (à espera) / Tijolo (rejeitado/erro) / cinza neutro (inativo) — nunca o Milho ou a Brasa para comunicar estado, para não confundir "marca" com "sinal".

---

## 3. Tipografia

| Papel | Tipo de letra | Peso(s) | Uso |
|---|---|---|---|
| Display | Fraunces | SemiBold 600 (normal) + 500 (itálico) | Títulos, hero, etiquetas de secção — nunca parágrafos |
| Corpo | Karla | 400–700 (fonte variável) | Texto corrido, formulários, tabelas, botões |

Ambas via Google Fonts, a auto-hospedar em `public/fonts/` na Etapa 8 (evita dependência de CDN externo e melhora tempo de carregamento — RNF01). Números tabulares (`font-variant-numeric: tabular-nums`) em preços, horários e contadores do dashboard para alinhamento vertical correto.

**Escala tipográfica:** Hero `clamp(2.35rem, 1.5rem + 3.4vw, 4rem)` · Secção `clamp(1.75rem, 1.35rem + 1.7vw, 2.5rem)` · Corpo `1rem`/1.6 · Legenda `0.875rem`.

---

## 4. Componentes

- **Botões:** `btn--ink` (preenchido tinta, ação primária tipo "Aprovar"), `btn--capim` (ação de confirmação positiva tipo "Publicar"), `btn--outline` (ação secundária/cancelar). Nunca botão preenchido a milho/brasa com texto branco (falha contraste) — se precisar do tom de marca num botão, o texto é sempre tinta.
- **Badges de estado:** par tom-claro-de-fundo + tom-forte-de-texto da mesma família semântica (secção 2), sempre acompanhado de um ponto (`::before`) além da cor — não depende só de cor (acessibilidade a daltonismo).
- **Campos de formulário:** `<label>` sempre visível (nunca só placeholder), anel de foco a milho (`box-shadow` 3px), mensagens de ajuda em cinza-suave abaixo do campo.
- **Cartão de estatística (dashboard):** número em Fraunces com `tabular-nums`, borda esquerda de 4px colorida por categoria (milho/brasa/capim) — nunca cor semântica aqui, é categorização, não estado.
- **Gráfico mínimo:** barras com animação de entrada (`scaleY`, respeitando `prefers-reduced-motion`), cor por série — usado no dashboard para inscrições por estado (Etapa 5, módulo 1).
- **Sidebar do painel:** clara (Papel), não escura — o item ativo do menu ganha fundo Milho + texto Tinta. Deliberadamente diferente do AdminLTE clássico (sidebar escura), para manter a leitura "alegre" pedida no prompt em vez de um tom sóbrio-corporativo.
- **Calendário de programação:** tabela com colunas por palco e células com "chips" coloridos por tipo de atividade (RF15) — permite à Comissão detetar sobreposição visualmente antes mesmo de ler a hora (reforça RN04/RC da Etapa 4).
- **Mapa de stands:** grelha de cartões coloridos pelo mesmo código semântico dos badges (ocupado=capim, reservado=âmbar, disponível=papel, inativo=cinza) — reaproveita o mesmo sistema de cor da fila de aprovação, sem vocabulário visual novo.

---

## 5. Layout

- **Página pública:** hero em gradiente milho→brasa com texto tinta (nunca branco sobre laranja, por contraste), seguido de secções alternadas papel/cinza-claro, sempre alinhadas à esquerda — sem tudo centrado.
- **`/painel` (Administrador + Comissão):** sidebar clara fixa (15rem) + conteúdo principal com stat tiles no topo e gráfico/tabela abaixo — grid de 2 colunas que colapsa para 1 em ecrãs `<760px`.
- **Formulário de inscrição:** campos agrupados em passos lógicos (dados do responsável → tipo de atividade → necessidades técnicas → participantes/fotos), rótulos sempre visíveis, confirmação final via SweetAlert2 (Etapa 8).
- **Responsividade:** breakpoint único de simplificação em 760px é suficiente para este sistema (poucos layouts verdadeiramente complexos); grids usam `auto-fit`/`auto-fill` com `minmax()` em vez de breakpoints manuais adicionais, reduzindo CSS a manter.

---

## 6. Acessibilidade (RNF04, retomando a Etapa 1)

- Contraste AA+ validado por cálculo em toda combinação texto/fundo (secção 2).
- Foco visível: contorno sólido cor tinta, `outline-offset`, em todo elemento interativo — nunca removido.
- Estado sempre comunicado por forma **e** cor (ponto + texto no badge), nunca só por cor.
- `prefers-reduced-motion` respeitado: animações de entrada e crescimento de gráfico desativadas quando o utilizador pedir menos movimento.
- Rótulos de formulário permanentes, nunca dependentes de placeholder (falha comum de leitura por leitores de ecrã).

---

## 7. Estado desta etapa

Paleta com 10 tons nomeados e validados por contraste (claro + escuro), par tipográfico Fraunces/Karla com escala definida, biblioteca de componentes (botões, badges, campos, stat tiles, gráfico, sidebar, calendário, mapa de stands) e conceito de layout documentados. Prévia visual publicada como artefacto para validação antes da implementação real em Blade/Bootstrap 5 (Etapa 8) — a prévia usa fontes embutidas via data URI só para fins de demonstração isolada; a aplicação real fará self-host dos ficheiros de fonte.

Pendente: revisão do utilizador (incluindo o artefacto visual) antes de avançar para a **Etapa 7 — Segurança**.
