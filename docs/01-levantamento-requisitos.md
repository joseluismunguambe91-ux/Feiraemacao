# Etapa 1 — Levantamento de Requisitos

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel)

---

## 0. Premissas confirmadas com o cliente

Estas decisões foram tomadas antes do levantamento final porque alteram diretamente a modelagem de dados e as regras de negócio das etapas seguintes:

| Decisão | Opção escolhida | Impacto principal |
|---|---|---|
| Multi-edição da feira | **Suporte a múltiplas edições desde o início** (ex.: Feira 2026, Feira 2027) | Toda a tabela operacional (expositores, stands, atividades, inscrições, gastronomia, galeria) referencia uma `feira_id`. Nada é global entre edições. |
| Inscrição de alunos | **Sempre mediada pelo professor responsável** | Aluno não submete inscrições sozinho; pode ter conta apenas para consulta do estado da sua participação. |
| Gastronomia | **Apenas informativa** (sem pagamento online) | Sem gateway de pagamento nesta fase; preço é exibido, pagamento é presencial. |
| Conta de Visitante | **Sem login — acesso público total** | Página pública 100% acessível sem registo; não há autenticação de visitante nesta fase. |

---

## 1. Requisitos Funcionais (RF)

### 1.1 Autenticação e Perfis
- **RF01** — O sistema deve autenticar utilizadores com os perfis: Administrador, Comissão Organizadora, Professor e Aluno. O Visitante não autentica (acesso público).
- **RF02** — O sistema deve permitir recuperação de senha via email.
- **RF03** — O Administrador pode criar, editar, desativar e atribuir papéis (roles) a qualquer conta.
- **RF04** — O Professor pode registar Alunos da(s) sua(s) turma(s) (registo administrativo; não implica necessariamente conta de login própria para o aluno).
- **RF05** — O Aluno, quando tiver conta, pode apenas consultar a programação pública e o estado das inscrições em que está associado — não pode criar/editar inscrições.

### 1.2 Gestão da Feira (edição)
- **RF06** — Criar, editar, eliminar (soft delete) edições da feira: tema, descrição, datas de início/fim, horário, local, banner, logótipo, regulamento (PDF/texto), estado.
- **RF07** — Estado da feira segue um fluxo: `rascunho → publicada → em curso → encerrada → arquivada`.
- **RF08** — Apenas uma edição pode estar em estado `publicada` ou `em curso` de cada vez (para não confundir a página pública).
- **RF09** — Todos os módulos operacionais (expositores, stands, atividades, gastronomia, inscrições, programação, galeria) devem estar sempre associados a uma edição de feira específica.

### 1.3 Gestão de Expositores
- **RF10** — CRUD de expositores: professor responsável, turma, categoria, descrição, fotografias, stand atribuído, estado (pendente/ativo/inativo).
- **RF11** — Um expositor pertence a exatamente uma edição da feira e, opcionalmente, a um stand.

### 1.4 Gestão de Stands
- **RF12** — CRUD de stands: número, localização, capacidade, responsável, categoria, estado, QR Code (gerado automaticamente).
- **RF13** — Um stand só pode estar atribuído a um expositor de cada vez, dentro da mesma edição da feira.
- **RF14** — O QR Code do stand deve apontar para uma página pública com informação do expositor/atividade associada.

### 1.5 Gestão de Atividades
- **RF15** — CRUD de atividades, com tipo (teatro, dança, música, poesia, ciências, artesanato, pintura, jogos, outro), título, descrição, horário, local, palco, duração, responsável, participantes, fotografia, estado.
- **RF16** — O sistema deve impedir/alertar sobreposição de horário entre duas atividades no mesmo palco/local.

### 1.6 Gestão de Gastronomia
- **RF17** — CRUD de itens de gastronomia: nome, categoria, descrição, preço, fotografia, ingredientes (opcional), disponibilidade (dia/horário/quantidade).
- **RF18** — Preço e disponibilidade são apenas informativos — sem carrinho de compras nem checkout.

### 1.7 Inscrição Online
- **RF19** — Formulário de inscrição preenchido pelo Professor, contendo: nome/apelido do responsável, tipo (professor/aluno representado), turma, telefone, email, tipo de atividade, descrição, nº de participantes, necessidades técnicas (palco, eletricidade, projetor, som, mesa, cadeiras), horário pretendido, duração, fotografias, observações.
- **RF20** — Toda inscrição nasce em estado `pendente`.

### 1.8 Aprovação de Inscrições
- **RF21** — A Comissão Organizadora pode aprovar ou rejeitar uma inscrição, com comentário obrigatório em caso de rejeição.
- **RF22** — Uma inscrição aprovada gera automaticamente (ou disponibiliza para geração) uma entrada na Programação.
- **RF23** — O autor da inscrição (professor) deve ser notificado da mudança de estado.

### 1.9 Programação
- **RF24** — Gerar agenda automaticamente a partir das inscrições aprovadas, respeitando horário/local/palco pretendidos.
- **RF25** — Permitir reorganização manual da agenda (drag-and-drop ou formulário) pela Comissão Organizadora, com validação de conflitos.
- **RF26** — A programação de uma edição em estado `publicada`/`em curso` é visível publicamente.

### 1.10 Dashboard
- **RF27** — Mostrar, por edição de feira: nº de inscritos, expositores, atividades, stands, professores, alunos, visitantes (estimados/registados via check-in, se aplicável), próximas apresentações, e gráficos (inscrições por estado, atividades por tipo, etc.) via Chart.js.

### 1.11 Página Pública
- **RF28** — Páginas públicas sem necessidade de login: Início, Sobre a Feira, Programação, Atividades, Gastronomia, Expositores, Mapa, Galeria, Patrocinadores, Pesquisa, Contacto.
- **RF29** — O conteúdo público reflete sempre a edição da feira que estiver `publicada`/`em curso`.

### 1.12 Galeria
- **RF30** — CRUD de fotos e vídeos organizados por categoria e por edição da feira.

### 1.13 Pesquisa
- **RF31** — Pesquisa pública unificada sobre: atividades, pratos de gastronomia, professores, turmas, stands — com resultados filtráveis por tipo.

### 1.14 Relatórios
- **RF32** — Geração de relatórios em PDF e Excel: participantes, atividades, expositores, gastronomia, programação — filtráveis por edição da feira.
- **RF33** — Geração de relatórios pesados deve correr em background (Jobs/Queues) quando o volume de dados justificar, notificando o utilizador quando o ficheiro estiver pronto.

### 1.15 Notificações
- **RF34** — Notificações por email e notificações internas (sino/painel) para eventos-chave: nova inscrição, mudança de estado da inscrição, publicação da programação.
- **RF35** — Estrutura de notificação desacoplada (Laravel Notification Channels) preparada para adicionar um canal WhatsApp no futuro, sem alterar os pontos de disparo já existentes.

### 1.16 Auditoria
- **RF36** — Registar log de auditoria para ações sensíveis: aprovação/rejeição de inscrições, alteração de estado da feira, eliminação de registos, alteração de permissões.

---

## 2. Requisitos Não Funcionais (RNF)

- **RNF01 — Desempenho**: páginas públicas devem carregar em menos de 2s em condições normais; listagens administrativas paginadas (nunca carregar tabelas inteiras).
- **RNF02 — Segurança**: proteção CSRF, validação server-side via Form Requests, prevenção de SQL Injection (Eloquent/Query Builder parametrizado), prevenção de XSS (escaping do Blade, sanitização de uploads), rate limiting em formulários públicos (inscrição, contacto, pesquisa).
- **RNF03 — Privacidade e dados de menores**: fotografias e dados de alunos (menores de idade) exigem consentimento do encarregado de educação; política de privacidade publicada; dados de alunos nunca expostos publicamente sem anonimização/consentimento.
- **RNF04 — Usabilidade e acessibilidade**: interface responsiva (mobile-first), contraste adequado (WCAG AA), navegação por teclado nas áreas administrativas.
- **RNF05 — Escalabilidade**: arquitetura modular (Services/Repositories quando fizer sentido) para permitir crescer para escolas maiores e múltiplas feiras simultâneas no futuro.
- **RNF06 — Disponibilidade**: o sistema deve tolerar o pico de acessos durante os dias do evento (cache de páginas públicas, otimização de queries do dashboard).
- **RNF07 — Manutenibilidade**: código organizado por domínio, nomenclatura consistente, sem duplicação, testes automatizados nas regras de negócio críticas.
- **RNF08 — Compatibilidade**: suporte aos browsers modernos mais usados (Chrome, Edge, Firefox, Safari) e últimos 2 anos de versões mobile.
- **RNF09 — Idioma**: interface em português (pt), sem necessidade de multi-idioma nesta fase.
- **RNF10 — Backup e recuperação**: rotina de backup da base de dados e dos ficheiros enviados (uploads), com plano mínimo de recuperação.
- **RNF11 — Auditoria e logs**: logs de erro e de auditoria persistidos e consultáveis pelo Administrador.

---

## 3. Regras de Negócio (RN)

- **RN01** — Toda entidade operacional (expositor, stand, atividade, gastronomia, inscrição, galeria, relatório) pertence a exatamente uma edição de feira.
- **RN02** — Apenas uma edição da feira pode estar `publicada` ou `em curso` simultaneamente.
- **RN03** — Um stand só pode estar associado a um expositor por vez, dentro da mesma edição.
- **RN04** — Não pode haver duas atividades no mesmo palco/local com horários sobrepostos.
- **RN05** — Uma inscrição só pode ser submetida por um Professor (em nome próprio ou de uma turma/aluno).
- **RN06** — Uma inscrição só transita de `pendente` para `aprovada`/`rejeitada` por ação da Comissão Organizadora, e uma rejeição exige comentário.
- **RN07** — Uma inscrição aprovada é o único tipo de inscrição que pode gerar entrada na Programação.
- **RN08** — Um Aluno nunca cria, edita ou submete inscrições — apenas consulta.
- **RN09** — Preços de gastronomia são meramente informativos; o sistema não processa pagamentos.
- **RN10** — Conteúdo da Página Pública reflete sempre a edição corrente (`publicada`/`em curso`); edições `arquivadas` ficam disponíveis apenas em modo histórico/consulta (sem inscrição ativa).
- **RN11** — Ações de eliminação em entidades com histórico (inscrições, expositores, atividades) usam soft delete — nunca eliminação física, para preservar auditoria e relatórios.

---

## 4. Melhorias Sugeridas (a validar antes de implementar)

| # | Melhoria | Justificação |
|---|---|---|
| M1 | Check-in de visitantes via QR Code à entrada | Permite contabilizar visitantes reais no Dashboard sem exigir conta/login, mantendo a decisão de "sem login para visitante". |
| M2 | Certificados de participação em PDF gerados automaticamente | Alto valor percebido para professores/alunos, baixo custo de implementação reaproveitando a geração de PDF já prevista nos relatórios. |
| M3 | Painel de check-in em tempo real por stand (Comissão Organizadora) | Ajuda a gestão operacional no dia do evento; pode ser um módulo simples reaproveitando o QR Code do stand. |
| M4 | Avaliação/feedback pós-feira (visitantes e participantes) | Gera dados úteis para a edição seguinte e fecha o ciclo de reutilização do sistema. |
| M5 | Modo "pré-visualização" da edição antes de publicar | Permite à Comissão rever a página pública antes de a tornar visível, reduzindo risco de publicar conteúdo incompleto. |

Nenhuma destas melhorias está incluída no escopo atual — ficam registadas para validação e priorização posterior.

---

## 5. Riscos Identificados

| # | Risco | Mitigação proposta |
|---|---|---|
| R1 | Dados de menores (alunos): nomes, turmas e fotografias são dados sensíveis. | Consentimento explícito do encarregado de educação no ato de inscrição; política de privacidade; nunca expor dados de alunos na página pública sem anonimização. |
| R2 | Modelagem multi-edição mal desenhada agora obriga a reescrever a base de dados mais tarde. | Incluir `feira_id` em todas as tabelas operacionais desde a primeira migration (Etapa 3). |
| R3 | Conflitos de horário/palco/stand não detetados na aprovação da inscrição. | Validação de sobreposição obrigatória no momento da aprovação e da reorganização manual da agenda. |
| R4 | Volume de uploads (fotos/vídeos da galeria, expositores, atividades) pode sobrecarregar armazenamento. | Definir disco de storage (local vs. S3-compatible), limites de tamanho/tipo de ficheiro, e processamento assíncrono de imagens (resize/thumbnail) via Jobs. |
| R5 | Geração de relatórios PDF/Excel volumosos pode bloquear o servidor web. | Executar em Jobs/Queues, notificando o utilizador quando o ficheiro estiver pronto para download. |
| R6 | Upload de ficheiros malicioso (ex.: SVG com script, ficheiro disfarçado). | Validar mime type real (não só extensão), limitar tamanho, sanitizar nomes de ficheiro, nunca servir uploads como executáveis. |
| R7 | Expectativa futura de integração com WhatsApp depende de API paga de terceiros (Meta Business API/Twilio). | Nesta fase, apenas desenhar a interface de notificação de forma desacoplada (Notification Channel), sem implementar o canal real. |
| R8 | Reorganização manual da agenda por utilizador humano pode introduzir inconsistências. | Validação em tempo real (AJAX) ao mover atividades na agenda, bloqueando sobreposições antes de gravar. |

---

## 6. Estado desta etapa

Levantamento de requisitos concluído com base nas premissas confirmadas na secção 0. Pendente: revisão e aprovação do utilizador antes de avançar para a **Etapa 2 — Modelagem do Sistema** (fluxograma, casos de uso, diagrama de módulos).
