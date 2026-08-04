# Etapa 10 — Documentação técnica

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel 12)

Última etapa do prompt mestre. Este documento é a documentação técnica completa do sistema (estrutura de pastas, rotas, controllers, models, migrations, seeders, factories, policies/gates, middleware, services, notificações). Os manuais por perfil de utilizador vivem em `docs/manuais/` (Administrador, Comissão Organizadora, Professor, Visitante).

---

## 1. Visão geral e stack

| Componente | Escolha |
|---|---|
| Linguagem/Framework | PHP 8.2, Laravel 12 |
| Base de dados | MySQL 8 (produção/desenvolvimento via XAMPP), SQLite em memória (testes automatizados) |
| Frontend | Blade + Bootstrap 5 (compilado via Vite/Sass, paleta como variáveis nativas do Bootstrap), SweetAlert2, Chart.js |
| Autenticação | `Auth`/`Password` facades diretamente (sem Breeze/Jetstream) |
| Autorização | Middleware de papel (`role:`) + Gate único (`feira-editavel`) — sem Policies por Model, decisão registada na secção 5 |
| PDF | `barryvdh/laravel-dompdf` |
| QR Code | `simplesoftwareio/simple-qrcode` (SVG) |
| Filas | Driver `database`, processadas por `php artisan queue:work` |
| Notificações | Canais `database` + `mail` |

---

## 2. Estrutura de pastas relevante

```
app/
  Http/
    Controllers/
      Admin/          -- área exclusiva do Administrador (/admin)
      Auth/            -- login, logout, recuperação de senha
      Painel/          -- área partilhada Administrador+Comissão (/painel)
      Professor/       -- área exclusiva do Professor (/professor)
      PublicoController.php   -- as 10 páginas públicas sem login
      NotificacaoController.php
    Middleware/
      EnsureUserHasRole.php    -- alias "role:papel1,papel2"
      DefinirFeiraAtual.php    -- alias "feira.contexto"
    Requests/          -- um Form Request por operação de escrita, agrupados por área
  Models/
    Concerns/
      PertenceAFeira.php   -- trait: relação feira() + scope daFeiraAtual
      Auditavel.php        -- trait: grava em audit_logs em created/updated/deleted
    (20 models — ver secção 4)
  Services/
    FeiraContexto.php            -- resolve/guarda a "feira atual" em sessão
    FeiraEstadoTransicao.php     -- motor do ciclo de vida da feira (RN02)
    ConflitoAgendaVerificador.php -- deteção de sobreposição de horário/palco (RN04)
    InscricaoAprovacaoService.php -- aprovar/rejeitar inscrição (transacional)
  Notifications/
    NovaInscricaoSubmetida.php   -- database, para Comissão+Administrador
    InscricaoAvaliada.php        -- database+mail, para o professor
    RelatorioConcluido.php       -- database, para quem pediu o relatório
  Jobs/
    GerarRelatorioJob.php        -- assíncrono, gera PDF ou CSV
  Providers/
    AppServiceProvider.php       -- define o Gate feira-editavel

routes/
  web.php        -- agrega todos os ficheiros abaixo
  publico.php    -- 10 páginas públicas, sem middleware de autenticação
  auth.php       -- login/logout/recuperação de senha
  admin.php      -- prefixo /admin, role:administrador
  painel.php     -- prefixo /painel, role:administrador,comissao + feira.contexto
  professor.php  -- prefixo /professor, role:professor

database/
  migrations/    -- 23 ficheiros, uma tabela por ficheiro (ver secção 6)
  seeders/       -- RoleSeeder, AdminUserSeeder, DatabaseSeeder
  factories/     -- 8 factories para testes (Feira, Stand, Expositor, Atividade,
                    GastronomiaItem, Inscricao, ProgramacaoItem, User)

resources/views/
  layouts/       -- layouts.painel (sidebar Admin/Comissão), layouts.publico, layouts.professor
  admin/         -- feiras, utilizadores, auditoria
  painel/        -- expositores, stands, atividades, gastronomia, inscricoes,
                    programacao, galeria, patrocinadores, mensagens-contacto, relatorios
  professor/inscricoes/
  publico/       -- as 10 páginas públicas
  relatorios/pdf/ -- templates dompdf
  components/, partials/

tests/
  Unit/Services/     -- FeiraEstadoTransicao, ConflitoAgendaVerificador, InscricaoAprovacaoService
  Feature/            -- Auth, AcessoPorPapel, Admin, Painel, Publico, InscricaoFluxo
  Concerns/CriaUtilizadoresComPapel.php

docs/
  01 a 09  -- um documento por etapa anterior
  10-documentacao.md  -- este ficheiro
  manuais/  -- um manual por perfil de utilizador
```

---

## 3. Rotas

### Públicas (`routes/publico.php`) — sem autenticação, sempre refletem a edição `publicada`/`em_curso` (RN10)

| Método | URI | Nome | Descrição |
|---|---|---|---|
| GET | `/` | `publico.inicio` | Home: hero, destaques do dia |
| GET | `/sobre` | `publico.sobre` | Sobre a feira/edição atual |
| GET | `/programacao` | `publico.programacao` | Agenda pública |
| GET | `/atividades` | `publico.atividades` | Lista de atividades |
| GET | `/gastronomia` | `publico.gastronomia` | Cardápio |
| GET | `/expositores` | `publico.expositores` | Lista de expositores/turmas |
| GET | `/mapa` | `publico.mapa` | Mapa dos stands |
| GET | `/stand/{qrToken}` | `publico.stand` | Página individual de um stand (destino do QR Code) |
| GET | `/galeria` | `publico.galeria` | Fotos/vídeos |
| GET | `/patrocinadores` | `publico.patrocinadores` | Lista de patrocinadores |
| GET | `/pesquisa` | `publico.pesquisa` | Pesquisa unificada (atividades, gastronomia, expositores, stands) |
| GET | `/contacto` | `publico.contacto` | Formulário de contacto |
| POST | `/contacto` | `publico.contacto.store` | Grava mensagem (`throttle:5,1`) |

### Autenticação (`routes/auth.php`)

| Método | URI | Nome | Middleware |
|---|---|---|---|
| GET/POST | `/login` | `login` | `guest` |
| GET/POST | `/esqueci-senha` | `password.request`/`password.email` | `guest` (+`throttle:3,60` no POST) |
| GET/POST | `/redefinir-senha` | `password.reset`/`password.store` | `guest` |
| POST | `/logout` | `logout` | `auth` |

### Administrador (`routes/admin.php`, prefixo `/admin`, `role:administrador`)

| Recurso | Rotas | Observação |
|---|---|---|
| `feiras` | `Route::resource` exceto `show` | CRUD de edições |
| `feiras/{feira}/reverter-estado` | POST | Reversão de estado, exclusiva do Administrador |
| `utilizadores` | `Route::resource` exceto `show`, parâmetro `utilizador` | RF03 |
| `auditoria` | GET | Consulta de `audit_logs` |

### Painel partilhado (`routes/painel.php`, prefixo `/painel`, `role:administrador,comissao` + `feira.contexto`)

| Recurso | Rotas | Observação |
|---|---|---|
| `/` | GET | Dashboard |
| `trocar-feira` | GET/POST | Seletor de edição em sessão |
| `feiras/{feira}/avancar-estado` | POST | Avanço de estado (Admin+Comissão) |
| `expositores`, `stands`, `atividades`, `gastronomia`, `galeria`, `patrocinadores` | `Route::resource` exceto `show`, parâmetro singular explícito | Ver nota sobre singularização em português (secção 5) |
| `stands/{stand}/qr` | GET | SVG do QR Code |
| `inscricoes` | GET index/show + `aprovar`/`rejeitar` (POST) | Fila de avaliação |
| `programacao` | GET index, `verificar-conflito` (POST, AJAX), `agendar/{atividade}` (GET/POST), `{item}/editar` (GET), `{item}` (PUT) | RN04 |
| `mensagens-contacto` | GET index + `marcar-lida` (POST) | Inbox do formulário de contacto |
| `relatorios` | GET index, POST store, GET `{relatorio}/download` | Gera assincronamente via `GerarRelatorioJob` |

### Professor (`routes/professor.php`, prefixo `/professor`, `role:professor`)

| Recurso | Rotas | Observação |
|---|---|---|
| `inscricoes` | `index`, `create`, `store`, `edit`, `update` (sem `destroy`/`show`) | RC01: só edita enquanto `pendente` |

### Partilhada (`routes/web.php`)

| Método | URI | Nome | Observação |
|---|---|---|---|
| POST | `notificacoes/{id}/marcar-lida` | `notificacoes.marcar-lida` | Qualquer utilizador autenticado; sino partilhado entre `layouts.painel` e `layouts.professor` |

---

## 4. Models (20)

| Model | Relações principais | Nota |
|---|---|---|
| `User` | `belongsToMany(Role)`, `hasMany` como `professor_id`/`responsavel_id` noutras tabelas | `hasRole()`/`hasAnyRole()`, `SoftDeletes` |
| `Role` | `belongsToMany(User)` via `role_user` | 4 papéis fixos (Visitante não tem conta) |
| `Feira` | `hasMany` de todas as entidades operacionais | Coluna gerada `estado_ativo` (STORED) + índice único garantindo RN02 ao nível da BD |
| `Aluno` | `belongsToMany(Inscricao)` via `inscricao_aluno` | |
| `Stand` | `belongsTo(Feira)`, `hasOne(Expositor)` | `qr_token` gerado automaticamente na criação |
| `Expositor` | `belongsTo(Feira, Stand, User professor)`, `hasMany(ExpositorFoto)` | RN03: 1 stand → 1 expositor |
| `ExpositorFoto` | `belongsTo(Expositor)` | |
| `Inscricao` | `belongsTo(Feira, User professor)`, `belongsToMany(Aluno)`, `hasMany(InscricaoFoto)`, `hasOne(Atividade)` | Estado `pendente`/`aprovada`/`rejeitada` |
| `InscricaoFoto` | `belongsTo(Inscricao)` | |
| `Atividade` | `belongsTo(Feira, Inscricao?, User responsavel)`, `hasMany(ProgramacaoItem)` | Origem direta (Comissão) OU via inscrição aprovada |
| `ProgramacaoItem` | `belongsTo(Feira, Atividade)` | Entidade de agendamento, separada do conteúdo (`Atividade`) |
| `GastronomiaItem` | `belongsTo(Feira)` | CHECK `preco >= 0` na BD |
| `GaleriaItem` | `belongsTo(Feira)` | `tipo` foto/vídeo |
| `Patrocinador` | `belongsTo(Feira)` | |
| `MensagemContacto` | — | Inbox do formulário público |
| `RelatorioGerado` | `belongsTo(User solicitante)` | Gerado por `GerarRelatorioJob` |
| `AuditLog` | `belongsTo(User)`, polimórfico ao registo auditado | Gravado pelo trait `Auditavel` |
| `Configuracao` | — | Chave/valor, sem UI de gestão ainda (gap conhecido) |

**Traits reutilizáveis** (`app/Models/Concerns/`):
- `PertenceAFeira`: relação `feira()` + scope `daFeiraAtual()`, usado pelas 9 entidades operacionais para nunca vazar dados entre edições.
- `Auditavel`: hooks `created`/`updated`/`deleted` que gravam em `audit_logs`.

---

## 5. Autorização: middleware e Gate (sem Policies)

Decisão consolidada nas Etapas 4/8: como há só 4 papéis com contas e a maioria das regras de autorização é "que papéis podem aceder a este prefixo de rota" (não "pode este utilizador editar este registo específico"), a autorização vive em:

1. **`role:papel1,papel2`** (`EnsureUserHasRole`) — aplicado a grupos de rotas inteiros (`/admin`, `/painel`, `/professor`).
2. **`feira.contexto`** (`DefinirFeiraAtual`) — resolve a "feira atual" da sessão (`FeiraContexto`), partilha-a com a view e injeta-a nos Form Requests que precisam validar contra o intervalo de datas da edição (`StandRequest`, `ProgramacaoItemRequest`, `InscricaoAprovacaoRequest`).
3. **Gate `feira-editavel`** (`AppServiceProvider::boot()`) — `Gate::define('feira-editavel', fn (User $user, ?Feira $feira) => $feira !== null && $feira->estado !== 'arquivada')`. Verificado explicitamente no início de cada método de escrita dos controllers operacionais via `Controller::assegurarFeiraEditavel()`, bloqueando qualquer alteração numa edição arquivada — mesmo para o Administrador (RC02).
4. Verificações pontuais dentro dos próprios controllers para regras que não cabem num Gate genérico (ex.: `Professor\InscricaoController` só permite editar a própria inscrição e só enquanto `pendente` — RC01).

**Gotcha de nomes de rota em português**: `Route::resource()` tenta adivinhar o parâmetro singular a partir do nome do recurso via `Str::singular()`, e falha sistematicamente com palavras portuguesas terminadas em vogal/ditongo (`gastronomia`→`gastronomium`, `expositores`→`expositore`, `galeria`→`galerium`, `patrocinadores`→`patrocinadore`, `utilizadores`→`utilizadore`). Todas as rotas deste projeto usam `->parameters([...])` explícito — ao adicionar um novo `Route::resource()` com nome em português, correr `Str::singular('nome-do-recurso')` mentalmente (ou no `tinker`) antes de assumir que o Laravel acerta.

---

## 6. Base de dados (23 migrations)

Dicionário de dados completo em `docs/03-modelagem-base-dados.md`; resumo das tabelas e das duas garantias impostas ao nível da BD (não só na aplicação):

- **`feiras.estado_ativo`**: coluna `STORED GENERATED` (`CASE WHEN estado IN ('publicada','em_curso') THEN 'ATIVA' ELSE NULL END`) + índice único → impossível existir duas edições ativas em simultâneo (RN02), mesmo que a aplicação tivesse um bug.
- **CHECK constraints MySQL-only** (preço ≥ 0 em `gastronomia_itens`, número de participantes ≥ 1 em `inscricoes`): envolvidas em `if (DB::connection()->getDriverName() === 'mysql')` nas migrations, porque a sintaxe `ALTER TABLE ... ADD CONSTRAINT ... CHECK` não existe no SQLite usado pelos testes (Etapa 9).

Tabelas: `users`, `roles`, `role_user`, `configuracoes`, `feiras`, `alunos`, `stands`, `expositores`, `expositor_fotos`, `inscricoes`, `inscricao_fotos`, `inscricao_aluno`, `atividades`, `programacao_itens`, `gastronomia_itens`, `galeria_itens`, `patrocinadores`, `mensagens_contacto`, `relatorios_gerados`, `audit_logs`, `notifications`, mais `cache`/`jobs` (infraestrutura Laravel).

---

## 7. Seeders e dados de arranque

| Seeder | O que faz |
|---|---|
| `RoleSeeder` | Cria os 4 papéis fixos (`administrador`, `comissao`, `professor`, `aluno`) |
| `AdminUserSeeder` | Cria `admin@feiraemacao.local` / `MudarNo1Acesso!` com o papel Administrador — **trocar esta senha antes de qualquer ambiente não-local** |
| `DatabaseSeeder` | Chama os dois acima |

---

## 8. Serviços de negócio (Services)

- **`FeiraContexto`**: resolve a edição "em trabalho" numa sessão que não tem `{feira}` na URL — por defeito a edição `estado_ativo`, ou na sua falta a mais recente em `rascunho`.
- **`FeiraEstadoTransicao`**: motor do ciclo `rascunho→publicada→em_curso→encerrada→arquivada`, valida RN02 com mensagem amigável antes de a constraint da BD rejeitar, permite reverter um passo (exceto antes de `rascunho`) — reversão exclusiva do Administrador, avanço partilhado com a Comissão.
- **`ConflitoAgendaVerificador`**: única fonte de verdade para RN04 (sem sobreposição no mesmo palco/data), partilhada entre a aprovação de inscrição, o agendamento direto e a verificação AJAX em tempo real. Usa `whereDate()`/`whereTime()` com normalização explícita via `Carbon` — ver [[feiraemacao-sqlite-mysql-portability]] na memória do projeto para o porquê.
- **`InscricaoAprovacaoService`**: aprovar cria `Atividade`+`ProgramacaoItem` numa transação e valida conflito antes de commitar; rejeitar exige `comentario_avaliacao` (RN06). Ambos bloqueiam reavaliação de uma inscrição já decidida.

---

## 9. Notificações e filas

| Notificação | Canais | Destinatário | Disparada por |
|---|---|---|---|
| `NovaInscricaoSubmetida` | database | Administrador + Comissão | Submissão de inscrição pelo Professor |
| `InscricaoAvaliada` | database + mail | Professor | Aprovação ou rejeição |
| `RelatorioConcluido` | database | Quem pediu o relatório | `GerarRelatorioJob` ao terminar |

`GerarRelatorioJob` corre na fila `database` — em produção precisa de `php artisan queue:work` permanente (ex.: via Supervisor). Gera PDF real (`barryvdh/laravel-dompdf`) ou CSV (não `.xlsx` — decisão registada na Etapa 8 para não trazer PhpSpreadsheet só por formatação; CSV abre nativamente em Excel/LibreOffice).

---

## 10. Instalação e deployment

```
composer install
npm install && npm run build
cp .env.example .env   # depois configurar DB_* para MySQL, APP_LOCALE=pt_PT
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Para relatórios assíncronos, correr em paralelo: `php artisan queue:work`.

**Checklist antes de qualquer ambiente que não seja local** (detalhado em `docs/08-desenvolvimento.md`, secção 3):
- `APP_DEBUG=false`, `APP_ENV=production`.
- Trocar a senha de `admin@feiraemacao.local` criada pelo `AdminUserSeeder`.
- `LOG_LEVEL=error` ou `warning`.
- Configurar `MAIL_MAILER` real (não `log`) para `InscricaoAvaliada` chegar de facto por email.
- Processo `queue:work` permanente para os Relatórios serem gerados.

---

## 11. Gaps conhecidos (herdados da Etapa 8, ainda não resolvidos)

- Área do Aluno: login com papel "aluno" cai na página pública, sem uma área de consulta dedicada ao próprio estado de participação.
- Professor não gere o próprio Expositor nem regista Alunos via interface — só a Comissão o faz em `/painel`.
- Tabela `configuracoes` existe mas não tem UI de gestão.

Estes gaps não bloqueiam o uso do sistema para o fluxo principal (Administrador/Comissão organizam, Professor inscreve, Visitante consulta) mas devem ser considerados antes de dar por fechado o âmbito completo do prompt mestre original.

---

## 12. Estado desta etapa

Etapa 10 concluída — documentação técnica completa (este ficheiro) e manuais de utilizador por perfil (`docs/manuais/`). **As 10 etapas do prompt mestre estão concluídas.**
