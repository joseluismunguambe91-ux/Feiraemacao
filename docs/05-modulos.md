# Etapa 5 — Módulos

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel)

Esta etapa consolida, módulo a módulo, tudo o que já ficou definido nas Etapas 1–4 (requisitos, arquitetura, base de dados, perfis) num formato diretamente utilizável na Etapa 8: rotas previstas, campos (por referência ao dicionário de dados), ações, regras de negócio aplicáveis e componentes de UI. Não repete o detalhe já registado — referencia pelos códigos (RF, RN, UC, RC) e nomes de tabela já definidos.

---

## 0. Correção de arquitetura antes de prosseguir (revisão da Etapa 4)

Ao desenhar as rotas de cada módulo, a tabela de "agrupamento de rotas por perfil" da Etapa 4 (secção 5) revelou um problema: vários módulos (Expositores, Stands, Atividades, Gastronomia, Galeria, Inscrições, Programação, Relatórios) são geridos tanto pelo **Administrador** como pela **Comissão Organizadora** (matriz da Etapa 4, secção 3: `Total` vs. `Gere`). Prefixar por papel (`/admin/expositores` **e** `/organizador/expositores`) obrigaria a duplicar controllers e views para o mesmo recurso — o que viola diretamente a regra do projeto "evitar duplicação de código".

**Correção adotada:** os módulos partilhados entre Administrador e Comissão passam a viver sob um único prefixo `/painel/*`, com uma única Policy por Model a decidir, dentro dessa área, quais ações cada papel pode executar (ex.: eliminar definitivamente só é permitido ao Administrador, mesmo estando os dois papéis na mesma rota de listagem). Os prefixos `/admin/*`, `/professor/*` e `/aluno/*` ficam reservados apenas para o que é **exclusivo** de cada papel.

| Prefixo | Middleware | Conteúdo | Perfis |
|---|---|---|---|
| `/admin` | `auth`, `role:administrador` | Utilizadores, Papéis, Feiras (criar/editar dados institucionais/arquivar), Auditoria, Configurações | Administrador |
| `/painel` | `auth`, `role:administrador,comissao` | Dashboard, Expositores, Stands, Atividades, Gastronomia, Galeria, Patrocinadores, Inscrições (listagem/aprovação), Programação, Relatórios | Administrador + Comissão (afinado por Policy) |
| `/professor` | `auth`, `role:professor` | As próprias Inscrições/Expositores, Alunos da própria turma | Professor |
| `/aluno` | `auth`, `role:aluno` | Consulta do estado da própria participação | Aluno (se tiver conta) |
| sem prefixo | nenhum | Página Pública, Pesquisa, Contacto, Mapa (QR Code) | Visitante (e qualquer perfil autenticado) |

---

## 1. Dashboard

- **Objetivo:** visão consolidada da edição corrente para decisão rápida (RF27).
- **Rota:** `GET /painel` (ou `/painel/dashboard`).
- **Perfis:** Administrador (todas as edições), Comissão (só a edição ativa) — Etapa 4, matriz.
- **Dados exibidos:** contadores (inscritos, expositores, atividades, stands, professores, alunos, visitantes/check-ins se M1 for adotada), próximas apresentações (via `programacao_itens`), gráficos Chart.js (inscrições por estado, atividades por tipo, ocupação de stands).
- **Regras aplicáveis:** RN01 (tudo filtrado por `feira_id` da edição ativa).
- **UI:** cartões de indicador (stat tiles), 2–3 gráficos, tabela de "próximas apresentações".

---

## 2. Gestão da Feira

- **Objetivo:** ciclo de vida da edição da feira (RF06–RF09).
- **Rotas:**

| Método | Rota | Ação | Perfil |
|---|---|---|---|
| GET | `/admin/feiras` | Listar edições | Administrador |
| POST | `/admin/feiras` | Criar edição | Administrador |
| PUT | `/admin/feiras/{feira}` | Editar dados institucionais | Administrador |
| DELETE | `/admin/feiras/{feira}` | Arquivar (soft delete) | Administrador |
| POST | `/painel/feiras/{feira}/avancar-estado` | Avançar estado (rascunho→publicada→em_curso→encerrada) | Administrador + Comissão |
| POST | `/admin/feiras/{feira}/reverter-estado` | Reverter estado | Administrador (RC05) |

- **Campos:** tabela `feiras` (dicionário, Etapa 3, secção 3.5).
- **Regras aplicáveis:** RN02 (só uma ativa — reforçado por `estado_ativo` na BD), RC02, RC05.
- **UI:** formulário com upload de banner/logótipo/regulamento, timeline visual do estado atual, aviso destacado se já existir edição ativa.

---

## 3. Gestão dos Expositores

- **Objetivo:** cadastro de expositores e associação a stand (RF10–RF11).
- **Rotas:** `GET|POST /painel/expositores`, `PUT|DELETE /painel/expositores/{expositor}`, `POST /professor/expositores` (professor cria o seu, estado nasce `pendente`).
- **Campos:** tabela `expositores` + `expositor_fotos` (Etapa 3, 3.7–3.8).
- **Regras aplicáveis:** RN01, RN03 (1 stand → 1 expositor), RC01 (professor só edita o próprio enquanto `pendente`).
- **UI:** listagem com filtro por categoria/estado, formulário com upload múltiplo de fotos, seletor de stand disponível.

---

## 4. Gestão da Gastronomia

- **Objetivo:** cardápio informativo (RF17–RF18).
- **Rotas:** `GET|POST /painel/gastronomia`, `PUT|DELETE /painel/gastronomia/{item}`.
- **Campos:** tabela `gastronomia_itens` (Etapa 3, 3.11).
- **Regras aplicáveis:** RN01, RN09 (sem pagamento), `CHECK (preco >= 0)` já garantido na BD.
- **UI:** listagem em grelha (foto + nome + preço), toggle de disponibilidade.

---

## 5. Gestão das Atividades

- **Objetivo:** catálogo de atividades culturais (RF15–RF16).
- **Rotas:** `GET|POST /painel/atividades`, `PUT|DELETE /painel/atividades/{atividade}`.
- **Campos:** tabela `atividades` (Etapa 3, 3.9). Nasce diretamente (Comissão) ou via `inscricao_id` preenchido (Service de aprovação).
- **Regras aplicáveis:** RN01, RN04 (sem sobreposição — validada no Service, não na BD), RN07 (só nasce de inscrição aprovada quando tem essa origem).
- **UI:** listagem filtrável por tipo/estado; indicação visual de "origem: direta" vs. "origem: inscrição".

---

## 6. Gestão dos Stands

- **Objetivo:** alocação física e QR Code (RF12–RF14).
- **Rotas:** `GET|POST /painel/stands`, `PUT|DELETE /painel/stands/{stand}`, `GET /stand/{qr_token}` (pública).
- **Campos:** tabela `stands` (Etapa 3, 3.6).
- **Regras aplicáveis:** RN01, RN03, `unique(feira_id, numero)` já garantido na BD.
- **UI:** planta/lista de stands com estado por cor (disponível/reservado/ocupado/inativo), botão "gerar QR Code".

---

## 7. Inscrição Online

- **Objetivo:** formulário de submissão pelo Professor (RF19–RF20).
- **Rotas:** `GET /professor/inscricoes/criar`, `POST /professor/inscricoes`, `GET|PUT /professor/inscricoes/{inscricao}` (só enquanto `pendente`, RC01).
- **Campos:** tabela `inscricoes` + `inscricao_fotos` + `inscricao_aluno` (Etapa 3, 3.12–3.14).
- **Regras aplicáveis:** RN05 (só professor submete), RN08 (aluno nunca submete), `CHECK (numero_participantes >= 1)` já garantido na BD.
- **UI:** formulário longo em passos (dados do responsável → tipo de atividade → necessidades técnicas → participantes → fotos/observações), com SweetAlert2 de confirmação ao submeter.

---

## 8. Aprovação das Inscrições

- **Objetivo:** fila de decisão da Comissão (RF21–RF23).
- **Rotas:** `GET /painel/inscricoes` (fila `pendente`), `POST /painel/inscricoes/{inscricao}/aprovar`, `POST /painel/inscricoes/{inscricao}/rejeitar`.
- **Campos:** `inscricoes.estado`, `comentario_avaliacao`, `avaliado_por`, `avaliado_em`.
- **Regras aplicáveis:** RN06 (comentário obrigatório se rejeitada), RN07 (aprovação → `InscricaoAprovacaoService` valida conflito de horário/palco/stand e gera `atividades` + `programacao_itens`), RC02.
- **UI:** listagem em fila com badges de estado, modal SweetAlert2 para comentário de rejeição, aviso inline se o Service detetar conflito de horário ao aprovar (RF16/RN04).

---

## 9. Programação

- **Objetivo:** agenda consolidada da feira (RF24–RF26).
- **Rotas:** `GET /painel/programacao` (vista de gestão, drag-and-drop), `PUT /painel/programacao/{item}` (reorganizar), `GET /programacao` (pública).
- **Campos:** tabela `programacao_itens` (Etapa 3, 3.10).
- **Regras aplicáveis:** RN04 (validação de conflito via AJAX antes de gravar, R8 da Etapa 1), RN01.
- **UI:** vista de calendário/grelha por palco e hora (biblioteca de calendário + Bootstrap 5), feedback imediato de conflito com SweetAlert2.

---

## 10. Página Pública

- **Objetivo:** vitrine da feira sem login (RF28–RF29).
- **Rotas (sem prefixo, sem auth):** `/`, `/sobre`, `/programacao`, `/atividades`, `/gastronomia`, `/expositores`, `/mapa`, `/galeria`, `/patrocinadores`, `/pesquisa`, `/contacto`.
- **Regras aplicáveis:** RN10 (reflete sempre a edição `publicada`/`em_curso`; edições `arquivadas` acessíveis só em modo histórico de consulta, sem inscrição ativa).
- **UI:** layout público próprio (paleta da Etapa 6), navegação fixa, hero banner da edição ativa.

---

## 11. Galeria

- **Objetivo:** fotos e vídeos por categoria (RF30).
- **Rotas:** `GET|POST /painel/galeria`, `DELETE /painel/galeria/{item}`, `GET /galeria` (pública).
- **Campos:** tabela `galeria_itens` (Etapa 3, 3.15).
- **UI:** grelha com filtro por categoria/tipo, lightbox para ampliar foto/vídeo.

---

## 12. Pesquisa

- **Objetivo:** busca unificada pública (RF31).
- **Rota:** `GET /pesquisa?q=...`.
- **Âmbito:** atividades, gastronomia, professores (nome, sem dados sensíveis — RNF03), turmas, stands.
- **UI:** campo de pesquisa com resultados agrupados por tipo, sem necessidade de autenticação.

---

## 13. Relatórios

- **Objetivo:** exportações PDF/Excel (RF32–RF33).
- **Rotas:** `GET /painel/relatorios` (formulário de filtros), `POST /painel/relatorios` (dispara Job), `GET /painel/relatorios/{relatorio}/download`.
- **Campos:** tabela `relatorios_gerados` (Etapa 3, 3.18).
- **Regras aplicáveis:** geração pesada corre em Job/Queue (R5 da Etapa 1); notificação ao concluir (RF34).
- **UI:** formulário de filtros + lista de relatórios já gerados com estado (`processando`/`concluido`/`falhou`) e botão de download quando pronto.

---

## 14. Notificações

- **Objetivo:** email + interno, estrutura preparada para WhatsApp (RF34–RF35).
- **Eventos que disparam notificação:** nova inscrição (→ Comissão), inscrição aprovada/rejeitada (→ Professor), programação publicada (→ todos os inscritos aprovados), relatório concluído (→ quem pediu).
- **Canais:** `mail`, `database` (sino no painel); `whatsapp` desenhado como Notification Channel próprio, sem implementação nesta fase (R7 da Etapa 1).
- **UI:** ícone de sino no layout do `/painel`, `/admin`, `/professor`, `/aluno` com contagem de não lidas.

---

## 15. Módulos transversais (introduzidos nas Etapas 2–4)

| Módulo | Objetivo | Rota | Regras |
|---|---|---|---|
| Auditoria | Log imutável de ações sensíveis (RF36) | `GET /admin/auditoria` | RC02, tabela `audit_logs` |
| Configurações | Parâmetros gerais do sistema | `GET|PUT /admin/configuracoes` | tabela `configuracoes` |

---

## 16. Estado desta etapa

Todos os 14 módulos do prompt original consolidados com rotas, campos (por referência), regras de negócio e componentes de UI previstos, mais os 2 módulos transversais já introduzidos. Foi também corrigida a estrutura de rotas por perfil definida na Etapa 4, substituindo prefixos duplicados por um `/painel` partilhado com autorização fina por Policy — evita duplicação de controllers/views para o mesmo recurso.

Pendente: revisão do utilizador antes de avançar para a **Etapa 6 — Design** (paleta amarelo/laranja/branco/cinza claro/verde, inspiração Eventbrite/Material/AdminLTE/Tabler, sem emojis).
