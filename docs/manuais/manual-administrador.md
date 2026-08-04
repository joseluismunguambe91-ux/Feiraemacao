# Manual do Administrador

Sistema de Gestão de Feira Gastronómica e Cultural Escolar

O Administrador tem acesso total ao sistema: cria e gere as edições da feira, cria as contas de todos os outros utilizadores, e tem acesso exclusivo à auditoria. Além disto, partilha com a Comissão Organizadora toda a área operacional do dia a dia (`/painel`) — descrita em detalhe no [Manual da Comissão Organizadora](manual-comissao.md), que vale a pena ler também.

---

## 1. Entrar no sistema

Aceda a `/login` e entre com o seu email e senha. Se acabou de instalar o sistema, a conta de arranque é:

- Email: `admin@feiraemacao.local`
- Senha: `MudarNo1Acesso!`

**Troque esta senha imediatamente** — vá a "Utilizadores", edite a sua própria conta e defina uma senha nova. Nunca deixe a senha de arranque ativa fora de um ambiente de testes local.

Se esquecer a senha, use "Esqueci a senha" no ecrã de login para receber um link de recuperação por email.

---

## 2. Gerir edições da feira

Menu "Feiras" (só visível para o Administrador). Cada edição da feira (ex.: "Feira 2026", "Feira 2027") é independente: tem os seus próprios expositores, stands, atividades, gastronomia e inscrições.

### Criar uma nova edição
1. "Feiras" → "Nova Feira".
2. Preencha tema, descrição, datas de início/fim, hora de abertura/encerramento, local.
3. Pode já carregar banner, logotipo e regulamento (PDF) — ou fazê-lo mais tarde ao editar.
4. Uma edição nova nasce sempre no estado **rascunho** — invisível na página pública.

### O ciclo de vida de uma edição
Uma edição avança sempre pela mesma sequência:

```
rascunho → publicada → em curso → encerrada → arquivada
```

- **rascunho**: em preparação, só visível em `/painel`.
- **publicada**: já aparece na página pública, ainda não começou.
- **em curso**: a decorrer.
- **encerrada**: terminou, mas os dados continuam consultáveis e editáveis.
- **arquivada**: fechada em definitivo — **nenhuma edição, nem sequer pelo Administrador, pode ser modificada numa feira arquivada.**

**Só pode existir uma edição ativa** (publicada ou em curso) de cada vez — o sistema bloqueia com uma mensagem clara se tentar publicar uma segunda edição enquanto outra já está ativa. Feche ou arquive a anterior primeiro.

- **Avançar o estado**: botão "Avançar estado" na listagem ou na ficha da feira — tanto o Administrador como a Comissão podem fazê-lo.
- **Reverter o estado** (ex.: voltar de "publicada" para "rascunho", ou desarquivar): **exclusivo do Administrador**, botão "Reverter estado". Não é possível reverter antes de "rascunho".

### Editar dados da edição
"Feiras" → escolher a edição → "Editar". Pode alterar tema, datas, local e substituir banner/logotipo/regulamento a qualquer momento, exceto se a edição já estiver arquivada.

---

## 3. Gerir utilizadores

Menu "Utilizadores" (exclusivo do Administrador) — é aqui que se criam as contas de Comissão Organizadora e de Professor. Não existe registo público de conta: só o Administrador cria contas.

### Criar uma conta
1. "Utilizadores" → "Novo Utilizador".
2. Preencha nome, email, telefone (opcional), senha inicial.
3. Marque a caixa "Ativo" (uma conta inativa não consegue entrar, mesmo com a senha certa).
4. Escolha um ou mais papéis: Administrador, Comissão Organizadora ou Professor. (Aluno tem tabela própria mas ainda não tem interface de criação de conta associada — ver secção de limitações no fim deste manual.)
5. Guardar. Comunique a senha inicial ao utilizador por um canal seguro; ele pode trocá-la depois em qualquer altura via "Esqueci a senha".

### Editar ou desativar uma conta
"Utilizadores" → escolher a pessoa → "Editar". Deixe o campo senha em branco para manter a senha atual, ou preencha para a substituir. Desmarcar "Ativo" bloqueia o acesso sem eliminar a conta nem o histórico associado.

### Eliminar uma conta
Disponível na listagem, exceto para a **sua própria conta** — o sistema impede que um Administrador se elimine a si próprio, para nunca se ficar sem acesso.

---

## 4. Auditoria

Menu "Auditoria" (exclusivo do Administrador). Mostra o histórico de criações, edições e eliminações feitas em toda a aplicação: quem fez o quê, quando, e a que registo. Use para investigar uma alteração inesperada ou confirmar que uma ação (ex.: aprovação de inscrição, publicação de edição) foi mesmo realizada por quem devia.

---

## 5. O resto do sistema

Como Administrador, você tem acesso a tudo o que a Comissão Organizadora tem em `/painel`: Expositores, Stands (com QR Code), Atividades, Gastronomia, Inscrições (aprovar/rejeitar), Programação, Galeria, Patrocinadores, Mensagens de Contacto e Relatórios. Esses fluxos estão descritos passo a passo no [Manual da Comissão Organizadora](manual-comissao.md) — não são repetidos aqui para evitar duplicação, mas aplicam-se a si da mesma forma.

---

## 6. Antes de qualquer evento real (checklist rápida)

- Senha de arranque do Administrador trocada.
- Contas de Comissão e Professores criadas com os papéis corretos.
- Edição da feira criada com todos os dados (banner, regulamento) e no estado certo (só "publicada" quando estiver mesmo pronta para o público ver).
- Um processo de fila (`php artisan queue:work`) a correr, para os Relatórios e o email de aprovação de inscrições funcionarem — questão técnica, confirme com quem faz a manutenção do servidor.

## Limitações conhecidas nesta versão

- Não existe ainda uma área própria para o Aluno consultar o seu estado de participação (a programação pública já mostra a agenda, mas não uma vista personalizada).
- O Professor ainda não regista o seu próprio Expositor nem os seus Alunos pela interface — atualmente é a Comissão/Administrador que o faz em seu nome, em `/painel`.
- A tabela de Configurações do sistema existe mas ainda não tem um ecrã de gestão.
