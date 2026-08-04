# Etapa 9 — Testes (plano de testes, cobertura e checklist de validação)

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel 12)

Esta etapa construiu uma suite de testes automatizados (PHPUnit) sobre o código entregue na Etapa 8, cobrindo a lógica de negócio mais sensível (Services) e os fluxos ponta-a-ponta mais críticos (autenticação, papéis, ciclo de vida da feira, inscrição→aprovação, programação, página pública, gestão de utilizadores). Este documento é o plano de testes exigido pelo Prompt Mestre e regista também o que os testes automáticos encontraram por si só.

---

## 1. Estratégia de testes

| Nível | Ferramenta | O que cobre | Onde vive |
|---|---|---|---|
| Unitário | PHPUnit, sem HTTP, `RefreshDatabase` só quando o Service toca a BD | Regras de negócio isoladas dos Services: máquina de estados da feira (RN02), deteção de conflito de agenda (RN04), aprovação/rejeição de inscrição | `tests/Unit/Services/` |
| Integração (Feature) | PHPUnit + `Illuminate\Foundation\Testing` (`get`/`post`/`actingAs`/`assertDatabaseHas`) | Rotas reais, middleware (`auth`, `role`, `feira.contexto`), Form Requests, Gates, notificações (`Notification::fake()`), fluxos completos | `tests/Feature/` |

**Ambiente de teste**: SQLite em memória (`phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`), `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`, `SESSION_DRIVER=array`, `CACHE_STORE=array`, `APP_URL=http://localhost`. Isto mantém os testes rápidos (suite completa em ~11s) e isolados do MySQL/XAMPP usado em desenvolvimento manual — a um custo: SQLite e MySQL não se comportam da mesma forma em todas as comparações, o que a secção 3 detalha.

**Dados de teste**: Model Factories (`database/factories/`) para as 8 entidades operacionais mais usadas nos testes (`Feira`, `Stand`, `Expositor`, `Atividade`, `GastronomiaItem`, `Inscricao`, `ProgramacaoItem`, `User`), com estados nomeados onde o fluxo depende deles (`Feira::publicada()`, `::emCurso()`, `::arquivada()`; `Inscricao::aprovada()`, `::rejeitada()`). Um trait de apoio, `tests\Concerns\CriaUtilizadoresComPapel`, cria utilizadores já com o papel correto atribuído (`criarAdministrador()`, `criarComissao()`, `criarProfessor()`, `criarAluno()`), evitando repetir a mesma sequência de `Role::firstOrCreate()` + `attach()` em cada teste.

---

## 2. Cobertura entregue

**53 testes, 150 assertions, todos a passar** (`php artisan test`, ~11s).

### Testes unitários (15)

| Ficheiro | Cobre |
|---|---|
| `FeiraEstadoTransicaoTest` | Sequência completa `rascunho→publicada→em_curso→encerrada→arquivada`; bloqueio de avanço além de `arquivada`; RN02 (bloqueia publicar com outra edição já ativa); reversão de estado incluindo desarquivar; bloqueio de reversão antes de `rascunho` |
| `ConflitoAgendaVerificadorTest` | Deteção de sobreposição no mesmo palco/data; ausência de conflito em palco diferente; horários adjacentes não sobrepostos; ausência de palco nunca gera conflito; item a ignorar-se a si próprio ao reorganizar |
| `InscricaoAprovacaoServiceTest` | Aprovar cria Atividade + Item de Programação e notifica o professor; aprovar bloqueia com conflito de horário (RN04); aprovar/rejeitar bloqueiam inscrição já avaliada; rejeitar grava comentário obrigatório e notifica |

### Testes de integração (38)

| Ficheiro | Cobre |
|---|---|
| `Auth/LoginTest` (6) | Redireciono por papel (admin→`/painel`, professor→`/professor/inscricoes`); credenciais inválidas com mensagem genérica; conta inativa bloqueada; logout; visitante redirecionado das páginas protegidas |
| `AcessoPorPapelTest` (4) | Áreas exclusivas do Administrador, área partilhada `/painel`, área exclusiva do Professor, auditoria exclusiva do Administrador |
| `Admin/UtilizadorTest` (5) | RF03: Administrador cria utilizador com papel; utilizador novo consegue entrar com o papel atribuído; Comissão não gere utilizadores; Administrador não se autoelimina; email duplicado rejeitado |
| `Painel/FeiraGestaoTest` (5) | Criação de edição; Comissão bloqueada de criar; Comissão avança mas não reverte estado; RN02 bloqueia segunda edição ativa; Gate `feira-editavel` bloqueia edição de feira arquivada |
| `Painel/ExpositorStandTest` (2) | Criação de stand com QR token automático; RN03 (mesmo stand não pode ter dois expositores) |
| `Painel/ProgramacaoTest` (3) | Agendamento direto sem conflito; RN04 bloqueia agendamento sobreposto; endpoint AJAX de verificação de conflito |
| `InscricaoFluxoTest` (6) | Submissão pelo professor + notificação à Comissão; bloqueio sem feira ativa; aprovação com agendamento + notificação ao professor; rejeição exige comentário; RC01 (só se edita enquanto pendente); professor não acede a inscrição de outro |
| `Publico/PaginasPublicasTest` (7) | As 10 páginas públicas respondem sem feira ativa; respondem com conteúdo com feira ativa; página de stand via QR token (e 404 com token inválido); pesquisa encontra atividade pelo título; formulário de contacto grava mensagem e valida campos obrigatórios |

### O que fica fora desta suite (por natureza do teste automatizado)

- Verificação visual do design (paleta, tipografia, responsividade) — coberta na Etapa 6/8 por inspeção manual no browser, não por PHPUnit.
- Geração real de PDF/CSV (`RelatorioGerado`) e envio real de email — testados manualmente na Fase 8.7 com `queue:work`; os testes automáticos usam `Notification::fake()` e não invocam o Job de geração de ficheiro.
- Área do Aluno, CRUD de Expositor/Alunos pelo Professor, UI de Configurações — não têm testes porque a própria funcionalidade não existe ainda (gap já registado em `docs/08-desenvolvimento.md`, secção 3).

---

## 3. Bugs reais encontrados pelos testes automáticos

O valor mais concreto desta etapa: dois bugs de portabilidade entre bases de dados que sobreviveram a todas as verificações manuais por `curl` contra MySQL ao longo da Etapa 8, e que só apareceram quando a mesma lógica correu sobre SQLite (o motor usado nos testes).

1. **Comparação de datas por igualdade** (`ConflitoAgendaVerificador`, `PublicoController::inicio()`): o código usava `where('data', $string)` para comparar um atributo `date`-cast. O Eloquent serializa esse atributo como `"2026-09-12 00:00:00"` ao gravar; a coluna `DATE` do MySQL trunca isto automaticamente ao armazenar, mascarando o problema — mas o SQLite guarda o texto tal como recebe, pelo que uma comparação `where('data', '2026-09-12')` nunca encontrava a linha gravada. **Corrigido** substituindo por `whereDate('data', $data)`, o helper do Laravel que gera SQL específico do motor para comparar só a parte da data.
2. **Comparação de horas** (`ConflitoAgendaVerificador`): mesmo depois de mudar para `whereTime()`, um teste continuava a falhar. Investigação com `dump($query->toSql())` revelou que a gramática SQLite do Laravel formata a **coluna** com segundos (`strftime('%H:%M:%S', hora_fim)`) mas não normaliza o **valor** do outro lado da comparação — um valor vindo de um `<input type="time">` como `"10:15"` nunca é igual, maior ou menor que `"10:15:00"` de forma previsível em comparação de texto. O MySQL mascarava isto porque a coluna `TIME` coage o literal automaticamente. **Corrigido** normalizando ambos os extremos com `Carbon::parse($valor)->format('H:i:s')` antes de montar a query.

Ambos os bugs afetavam RN04 (deteção de conflito de horário) e a home pública — funcionalidade que tinha passado em todas as verificações manuais da Etapa 8 precisamente porque essas verificações corriam sempre sobre MySQL. Fica registado como justificação prática de porque esta etapa exige testes automatizados reais, mesmo depois de verificação manual extensa.

---

## 4. Checklist de validação manual

Testes automatizados cobrem lógica e fluxos HTTP; esta checklist cobre o que só um humano num browser consegue confirmar, a repetir antes de qualquer entrega/demonstração:

- [ ] Login/logout e recuperação de senha funcionam visualmente (mensagens, estados de erro, redirecionamento) nos três papéis principais.
- [ ] Paleta, tipografia e espaçamento da Etapa 6 aplicados de forma consistente em `/painel` e na página pública, em ecrã largo e em telemóvel.
- [ ] Seletor de "feira atual" funciona quando há várias edições em estados diferentes (`rascunho`, `publicada`, `arquivada`).
- [ ] Criação de uma edição completa: Feira → Stands → Expositores → Gastronomia → Atividades → Inscrições → Aprovação → Programação, tudo através da interface, sem atalhos de `tinker`/seed.
- [ ] QR Code de um stand aponta para a página pública correta desse stand quando lido por um telemóvel real.
- [ ] Geração de relatório (PDF e CSV) com `php artisan queue:work` a correr — confirmar que o ficheiro gerado abre e tem os dados esperados.
- [ ] Notificação por email chega (com `MAIL_MAILER` real configurado, não `log`) quando uma inscrição é avaliada.
- [ ] Página pública completa (as 10 páginas) sem nenhuma feira publicada mostra estados vazios coerentes, não erros.
- [ ] Auditoria (`/painel/auditoria`) regista corretamente uma criação, edição e eliminação feitas na sessão de validação.
- [ ] Gate `feira-editavel`: tentar editar Expositores/Stands/Atividades/Gastronomia/Programação numa edição `arquivada` está bloqueado na interface (não só na API), incluindo para o Administrador.

---

## 5. Como correr a suite

```
php artisan test
```

Para correr só uma camada:
```
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

---

## 6. Estado desta etapa

Etapa 9 concluída: 53 testes automatizados (15 unitários + 38 de integração, 150 assertions), todos a passar, cobrindo os Services de negócio mais sensíveis e os fluxos ponta-a-ponta principais dos 5 perfis. Dois bugs reais de portabilidade entre bases de dados foram encontrados e corrigidos durante a escrita desta suite (secção 3). Checklist de validação manual definida (secção 4) para o que fica fora do alcance de testes automatizados.

Pendente: revisão do utilizador antes de avançar para a **Etapa 10 — Documentação** (manuais de utilizador por perfil, documentação técnica, guia de instalação/deployment).
