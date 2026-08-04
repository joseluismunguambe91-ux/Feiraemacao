# Etapa 7 — Segurança

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel)

Esta etapa fixa, para cada um dos 10 itens pedidos no prompt original, o mecanismo Laravel exato e a regra específica deste projeto — para a Etapa 8 implementar sem ambiguidade nenhuma. Sem código nesta etapa (decisão do utilizador): autenticação real só faz sentido depois de existirem os Models `User`/`Role` e os Controllers, reservados para a Etapa 8.

---

## 1. Login

- **Mecanismo:** guard de sessão `web` do Laravel (scaffolding via Laravel Breeze na Etapa 8) — email + password.
- **Regras deste projeto:**
  - Conta com `users.ativo = false` (Etapa 3) não consegue entrar — mensagem genérica, sem indicar se é a senha ou a conta que está desativada.
  - Mensagem de erro de login sempre genérica ("credenciais inválidas") — nunca revelar se o email existe (evita enumeração de contas de professores/alunos).
  - Rate limiting: máximo 5 tentativas por minuto por combinação email+IP (`throttle` do Laravel), com bloqueio temporário progressivo.
  - Redirecionamento pós-login por papel (Etapa 4, secção 5): Administrador → `/admin`, Comissão → `/painel`, Professor → `/professor`, Aluno → `/aluno`. Utilizador com múltiplos papéis (tabela `role_user`) vai para o painel de maior privilégio.
  - Visitante nunca autentica (RC04, Etapa 4) — não há formulário de login na Página Pública.

## 2. Logout

- **Mecanismo:** rota `POST /logout` protegida por CSRF; invalida a sessão (`session()->invalidate()`) e regenera o token (`session()->regenerateToken()`) — previne session fixation.
- **Regra:** disponível em todos os layouts autenticados (`/admin`, `/painel`, `/professor`, `/aluno`), nunca via `GET` (evita logout acidental por pré-carregamento/crawler).

## 3. Recuperação de senha

- **Mecanismo:** fluxo padrão do Laravel — tabela `password_reset_tokens` (já criada na Etapa 3), Notification por email (RF34).
- **Regras deste projeto:**
  - Token expira em 60 minutos.
  - Rate limiting no pedido de reset (máx. 3 pedidos por hora por email) — evita usar o formulário para spam de email a terceiros.
  - Mensagem de confirmação idêntica quer o email exista ou não na base de dados (evita enumeração de contas).

## 4. Controlo de permissões

- **Mecanismo:** Policies por Model + Gate transversal `feira-editavel`, exatamente como mapeado na Etapa 4 (secção 4, RC01–RC06) e refinado na Etapa 5 (secção 0 — prefixo `/painel` partilhado com autorização fina).
- **Middleware de papel:** `role:administrador,comissao` (aceita lista, verifica `$user->roles->pluck('slug')`), aplicado aos grupos de rotas da Etapa 4/5.
- **Regra adicional:** qualquer ação bloqueada por Policy/Gate devolve HTTP 403 com página amigável própria (sem stack trace, sem detalhe técnico) — em produção, `APP_DEBUG=false` (já é o valor a configurar no deployment, distinto do `.env` de desenvolvimento criado na Etapa 3).

## 5. CSRF

- **Mecanismo:** já ativo por padrão — o middleware `VerifyCsrfToken` do Laravel protege todo o grupo `web` desde o scaffolding da Etapa 3; nenhuma rota deste sistema deve ser adicionada à lista de exceções (`$except`).
- **Regra deste projeto:** todo formulário Blade usa `@csrf`; todo pedido AJAX (SweetAlert2 + fetch/axios) envia o token via cabeçalho `X-CSRF-TOKEN`, lido de uma `<meta name="csrf-token">` no layout base — um único ponto de configuração JS reutilizado em todas as páginas (Etapa 8), em vez de repetir por formulário.

## 6. Validação de formulários

- **Mecanismo:** um Form Request por operação de escrita relevante (já previsto na organização de pastas da Etapa 2, secção 5).
- **Regra deste projeto:** as regras de validação espelham exatamente os tipos/enums/constraints do dicionário de dados (Etapa 3) — nunca duplicar valores à mão. Exemplos diretos:
  - `InscricaoStoreRequest`: `numero_participantes` → `required|integer|min:1` (espelha o `CHECK` da tabela `inscricoes`).
  - `GastronomiaItemRequest`: `preco` → `required|numeric|min:0` (espelha o `CHECK` da tabela `gastronomia_itens`).
  - `StandRequest`: `numero` → `required|string|max:20|unique:stands,numero,NULL,id,feira_id,{feira atual}` (espelha o `unique(feira_id, numero)`).
- Mensagens de validação em português (`APP_LOCALE=pt_PT`, já configurado na Etapa 3) — ficheiros de idioma publicados na Etapa 8 (`php artisan lang:publish`).

## 7. Proteção contra SQL Injection

- **Mecanismo:** Eloquent/Query Builder com *parameter binding* em 100% das queries de dados de utilizador — nenhuma interpolação direta de variável em SQL.
- **Exceção já existente e auditada:** os `DB::statement(...)` usados nas migrations da Etapa 3 (`CHECK` constraints em `inscricoes` e `gastronomia_itens`) não recebem input de utilizador — são strings fixas escritas no código, não parâmetros de formulário. Documentado aqui para não ser confundido com uma violação da regra.
- **Regra de code review para a Etapa 8:** proibir `whereRaw()`/`DB::raw()` com valor de utilizador concatenado diretamente na string; se for mesmo necessário SQL bruto, usar sempre bindings (`whereRaw('coluna = ?', [$valor])`).

## 8. Proteção contra XSS

- **Mecanismo:** Blade `{{ $variavel }}` escapa HTML por padrão em toda a Página Pública e painéis.
- **Regra deste projeto:** proibido `{!! !!}` (saída não escapada) em qualquer view deste sistema — nenhum requisito da Etapa 1 pede edição de HTML livre (sem WYSIWYG previsto), por isso não há caso de uso legítimo para desativar o escaping.
- **Uploads (fotos de expositores, atividades, galeria):** validação Laravel `image`/`mimes:jpg,png,webp` verifica o tipo real do ficheiro (não só a extensão), nome do ficheiro gerado pelo sistema (nunca o nome original do utilizador é usado no caminho), nunca servido como executável — mitigação direta do risco R6 da Etapa 1.
- **Cabeçalhos de segurança adicionais** (a configurar via middleware na Etapa 8): `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Content-Security-Policy` básica restringindo `script-src` a `'self'`.

## 9. Logs

- **Mecanismo:** logging técnico do Laravel, já configurado desde o scaffolding da Etapa 3 (`LOG_CHANNEL=stack`, `storage/logs/laravel.log`).
- **Regra deste projeto:** `LOG_LEVEL=debug` mantém-se em desenvolvimento; no deployment de produção passa a `error` (nota para a fase de implantação, fora do escopo desta etapa). Este log é técnico (exceções, erros de sistema) — **distinto** da Auditoria de negócio (item 10).

## 10. Auditoria

- **Mecanismo:** tabela `audit_logs` (Etapa 3) + um trait reutilizável `Auditavel` aplicado aos Models sensíveis (`Feira`, `Inscricao`, `User`, `Stand`) que escuta os eventos Eloquent `created`/`updated`/`deleted` e grava `dados_antigos`/`dados_novos` em JSON — evita repetir código de log em cada Controller (reutilização, Etapa 2).
- **Eventos de negócio explícitos** (não cobertos por um simples `updated` de Model, por isso registados diretamente no Service correspondente): aprovação/rejeição de inscrição, transição de estado da feira, alteração de papel de um utilizador.
- **Regra:** `audit_logs` nunca é editado nem eliminado (é imutável por definição, já sem `updated_at` desde a Etapa 3) — só o Administrador consulta (RC da Etapa 4).

---

## 11. Matriz de risco → mecanismo (retomando a Etapa 1)

| Risco (Etapa 1) | Mecanismo de mitigação nesta etapa |
|---|---|
| R1 — dados de menores (fotos/nomes de alunos) | Item 8 (validação de upload) + política de privacidade a redigir na Etapa 10 (documentação) |
| R6 — upload malicioso | Item 8 (validação de mime type real, nome gerado pelo sistema) |
| R3 — conflito de horário não detetado | Fora do âmbito da Segurança — já coberto por RN04/Service na Etapa 5 |
| — | CSRF (item 5) e XSS (item 8) cobrem RNF02 da Etapa 1 na íntegra |

---

## 12. Estado desta etapa

Os 10 itens de segurança do prompt original têm agora um mecanismo Laravel concreto e uma regra específica deste projeto, prontos para implementação direta na Etapa 8 sem decisões em aberto. Nenhum código foi escrito nesta etapa, por decisão explícita do utilizador — login/logout reais dependem dos Models `User`/`Role` e dos Controllers da Etapa 8.

Pendente: revisão do utilizador antes de avançar para a **Etapa 8 — Desenvolvimento**, onde finalmente entra código de aplicação (Models, Controllers, Policies, Services, Views).
