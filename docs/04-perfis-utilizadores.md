# Etapa 4 — Perfis de Utilizadores

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel)

Esta etapa formaliza, numa matriz única e sem ambiguidade, o que cada perfil pode fazer em cada módulo. Serve de especificação direta para as Policies, Gates e middleware de papel que serão implementados na Etapa 8 — ainda sem código nesta etapa.

---

## 1. Perfis (revisão)

| Perfil | Tem conta de login | Âmbito de atuação |
|---|---|---|
| **Administrador** | Sim | Todo o sistema, todas as edições da feira |
| **Comissão Organizadora** | Sim | Operação do dia a dia da edição corrente |
| **Professor** | Sim | As suas próprias inscrições/expositores e alunos da turma |
| **Aluno** | Opcional (RF05) | Apenas consulta do próprio estado de participação |
| **Visitante** | Não (RN — sem login, Etapa 1) | Conteúdo público da edição publicada/em curso |

---

## 2. Clarificação necessária (revisão da Etapa 2)

Ao formalizar a matriz de permissões, foi identificada uma sobreposição entre a Etapa 1 (RF06 — só o Administrador gere a feira) e a Etapa 2 (UC13 — atribuiu à Comissão "publicar/despublicar edição"). Resolução adotada, que passa a ser a referência oficial:

- O **Administrador** é o único que pode **criar** uma edição, editar os seus dados institucionais (tema, datas, banner, regulamento) e **arquivá-la** definitivamente.
- A **Comissão Organizadora** pode **operar a transição de estado no dia a dia** dentro do ciclo normal (`publicada → em_curso → encerrada`), porque é quem está no terreno a coordenar o evento — mas nunca pode criar, arquivar, nem reverter uma edição já `em_curso` de volta a `rascunho`.
- Reverter um estado (ex.: despublicar por engano) exige sempre o Administrador — evita que a Comissão tire ao ar a página pública sem essa intenção.

---

## 3. Matriz de Permissões

Legenda: **Total** = criar/editar/eliminar sem restrição · **Gere** = criar/editar dentro da edição corrente · **Próprio** = só os registos associados ao utilizador · **Consulta** = só leitura · **Público** = acesso sem autenticação · **—** = sem acesso

| Módulo | Administrador | Comissão Organizadora | Professor | Aluno | Visitante |
|---|---|---|---|---|---|
| Gestão da Feira (criar/arquivar edição) | Total | Transição de estado (ver secção 2) | — | — | Público (edição ativa) |
| Utilizadores e Perfis | Total | — | Regista alunos da própria turma | — | — |
| Expositores | Total | Gere | Cria/edita o próprio enquanto `pendente` | — | Público |
| Stands | Total | Gere | — | — | Público (mapa, via QR Code) |
| Atividades | Total | Gere | Origem indireta via inscrição aprovada | Consulta | Público |
| Gastronomia | Total | Gere | — | — | Público (cardápio) |
| Inscrições (submissão) | Total | Consulta todas | Cria/edita a própria enquanto `pendente` | Consulta o próprio estado | — |
| Aprovação de Inscrições | Total | Aprova/rejeita | Consulta o resultado + comentário | — | — |
| Programação | Total | Gere (automática + manual) | Consulta | Consulta | Público |
| Dashboard | Total | Consulta (indicadores da edição) | — | — | — |
| Página Pública | — (não se autentica para isto) | — | — | — | Consulta livre |
| Galeria | Total | Gere | — | — | Público |
| Pesquisa | — | — | — | — | Uso livre |
| Relatórios (PDF/Excel) | Total | Gera | — | — | — |
| Notificações | Configura canais | Recebe + consulta | Recebe | Recebe (se tiver conta) | — |
| Auditoria | Consulta (Total) | — | — | — | — |
| Configurações do sistema | Total | — | — | — | — |

---

## 4. Regras de autorização contextual (base para Policies/Gates da Etapa 8)

Estas regras não aparecem numa matriz simples porque dependem do **estado** do registo, não só do papel do utilizador:

- **RC01** — Um Professor só pode editar ou eliminar a própria inscrição/expositor enquanto o estado for `pendente`; após `aprovada`/`rejeitada`, o registo passa a ser só de leitura para ele (integridade da decisão da Comissão).
- **RC02** — A Comissão só pode aprovar/rejeitar/gerir conteúdo dentro da edição da feira que estiver `publicada` ou `em_curso`; edições `arquivadas` ficam bloqueadas para escrita a todos os perfis, incluindo Administrador, exceto reversão explícita de arquivamento.
- **RC03** — Um Aluno só vê o estado de inscrições/atividades às quais está associado (via `inscricao_aluno`) ou conteúdo público — nunca cria nem edita nada.
- **RC04** — Visitante nunca autentica; todas as rotas públicas são acessíveis sem sessão (`guest` por definição, sem Policy aplicável).
- **RC05** — Só o Administrador pode reverter o estado de uma edição para trás no fluxo (ex.: `em_curso → publicada`); a Comissão só avança o fluxo.
- **RC06** — Um Professor só regista/consulta Alunos da(s) própria(s) turma(s) — nunca de turmas de outro professor.

Mapeamento direto para código (Etapa 8): RC01/RC03/RC06 → `InscricaoPolicy`, `ExpositorPolicy`, `AlunoPolicy` (autorização por instância); RC02/RC05 → Gate transversal `feira-editavel` (não pertence a um único Model); RC04 não precisa de Policy — ausência de middleware `auth` nas rotas públicas já resolve.

---

## 5. Autenticação e agrupamento de rotas por perfil

> **Nota (revisão da Etapa 5):** a tabela abaixo assumia prefixos estritamente por papel. Isso gerava duplicação de rotas/controllers para módulos partilhados entre Administrador e Comissão (Expositores, Stands, Atividades, Gastronomia, Galeria, Inscrições, Programação, Relatórios). A versão corrigida — com um prefixo `/painel` partilhado e autorização fina por Policy — está em [docs/05-modulos.md](05-modulos.md), secção 0. Esta tabela mantém-se válida apenas para os módulos verdadeiramente exclusivos de um papel.

Formaliza a organização de pastas já prevista na Etapa 2 (secção 5):

| Grupo de rotas | Middleware | Perfis com acesso |
|---|---|---|
| `/admin/*` | `auth`, `role:administrador` | Administrador |
| `/organizador/*` | `auth`, `role:comissao` | Comissão Organizadora |
| `/professor/*` | `auth`, `role:professor` | Professor |
| `/aluno/*` | `auth`, `role:aluno` | Aluno (só se tiver conta) |
| sem prefixo (público) | nenhum | Visitante — e também qualquer perfil autenticado, sem restrição |

Um utilizador com mais de um papel (decisão 1.1 da Etapa 3 — tabela `role_user`) acede a todos os grupos de rotas correspondentes aos papéis que possui, sem necessidade de múltiplas contas.

---

## 6. Estado desta etapa

Perfis de utilizadores formalizados numa matriz de permissões completa (16 módulos × 5 perfis), com 6 regras de autorização contextual mapeadas diretamente para as Policies/Gates que serão codificadas na Etapa 8, e o agrupamento de rotas por papel já alinhado com a arquitetura definida na Etapa 2. Foi também resolvida uma ambiguidade entre a Etapa 1 e a Etapa 2 sobre quem pode publicar/despublicar uma edição da feira.

Pendente: revisão do utilizador antes de avançar para a **Etapa 5 — Módulos** (o prompt original já detalha os campos de cada módulo; esta etapa consolida isso com o que já ficou definido nas Etapas 1–4, sem repetir).
