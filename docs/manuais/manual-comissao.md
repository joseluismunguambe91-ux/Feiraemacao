# Manual da Comissão Organizadora

Sistema de Gestão de Feira Gastronómica e Cultural Escolar

A Comissão Organizadora é quem gere o dia a dia de uma edição da feira: expositores, stands, atividades, gastronomia, inscrições e a agenda. A sua conta é criada pelo Administrador — se ainda não tem acesso, peça-lhe para a criar em "Utilizadores".

---

## 1. Entrar e escolher a edição em trabalho

Entre em `/login` com o email e senha que lhe foram atribuídos. Depois do login, chega ao **Painel** (`/painel`).

Como pode haver mais do que uma edição da feira (ex.: a de este ano em preparação e a do ano passado já arquivada), o sistema trabalha sempre sobre uma **"edição atual"**, escolhida através do seletor "Trocar Feira" no topo do Painel. Tudo o que criar ou aprovar fica sempre associado à edição selecionada no momento — confirme sempre qual é antes de começar a trabalhar, especialmente se houver mais do que uma edição em preparação ao mesmo tempo.

**Nota importante**: se a edição selecionada estiver no estado "arquivada", o sistema bloqueia qualquer alteração (mesmo para o Administrador) — escolha outra edição ou peça ao Administrador para reverter o estado se isso foi um engano.

---

## 2. Dashboard

A página inicial do Painel mostra, para a edição atual: número de expositores, stands, inscrições pendentes, atividades agendadas, e um gráfico com a evolução das inscrições. Use-o como ponto de partida diário para ver o que precisa da sua atenção (normalmente, inscrições pendentes por avaliar).

---

## 3. Avançar o estado da edição

Pode avançar o estado da edição atual (rascunho→publicada→em curso→encerrada→arquivada) através do botão "Avançar estado". **Não pode reverter** um estado (isso é exclusivo do Administrador) — confirme bem antes de avançar, sobretudo ao publicar (a partir daí a edição fica visível ao público).

---

## 4. Expositores e Stands

### Stands
"Stands" → "Novo Stand": número (único dentro da edição), localização, capacidade, categoria, responsável. Ao criar, o sistema gera automaticamente um **QR Code** — aceda a "Ver QR" na listagem para o descarregar/imprimir e colar fisicamente no stand. Quem o ler com o telemóvel vai diretamente à página pública desse stand.

### Expositores
"Expositores" → "Novo Expositor": associe ao professor responsável, turma, categoria, descrição, e opcionalmente um stand. **Um stand só pode ter um expositor** — se tentar atribuir um stand já ocupado, o sistema bloqueia com uma mensagem explicando o conflito. Pode também carregar fotos do expositor.

---

## 5. Atividades e Gastronomia

### Atividades criadas diretamente
Além das atividades que nascem de uma inscrição aprovada (secção 6), pode criar atividades diretamente em "Atividades" → "Nova Atividade": tipo (teatro, dança, música, poesia, ciências, artesanato, pintura, jogos, outro), título, descrição, responsável, participantes previstos, foto. Estas atividades ficam disponíveis em "Programação" → "Por agendar" até lhes atribuir data/hora (secção 7).

### Gastronomia
"Gastronomia" → "Novo Item": nome, categoria, descrição, preço, ingredientes, foto, disponibilidade e quantidade disponível. É apenas informativo — o sistema não processa pagamentos.

---

## 6. Inscrições: avaliar pedidos dos Professores

Menu "Inscrições" mostra a fila de pedidos submetidos pelos Professores, filtrável por estado (pendente/aprovada/rejeitada). Ao abrir uma inscrição pendente vê todos os detalhes: tipo de atividade, número de participantes, necessidades técnicas (palco, eletricidade, projetor, som, mesas, cadeiras) e o horário pretendido pelo professor.

### Aprovar
Preencha o **agendamento definitivo**: título da atividade, data, hora de início/fim, local e palco (obrigatório se a inscrição pedir palco). A data tem de estar dentro do período da edição. Ao aprovar:
- O sistema **verifica automaticamente conflitos de horário** no mesmo palco/data — se houver sobreposição com outra atividade já agendada, a aprovação é recusada com uma mensagem explicando o conflito; escolha outro horário ou palco.
- Sem conflito, o sistema cria a Atividade e o item de Programação automaticamente, e **notifica o professor** por email e dentro do sistema.

### Rejeitar
É **obrigatório** escrever um comentário a explicar o motivo — o professor vê esse comentário na notificação e na sua área.

Uma inscrição já avaliada (aprovada ou rejeitada) não pode ser reavaliada nem editada pelo professor — se for preciso corrigir algo depois de decidido, trate isso diretamente em "Atividades"/"Programação".

---

## 7. Programação

"Programação" mostra a agenda da edição atual, organizada por data/palco, mais uma secção "Atividades por agendar" (as criadas diretamente, secção 5, que ainda não têm horário).

### Agendar uma atividade por agendar
"Agendar" → escolha data, hora de início/fim, local, palco. Enquanto preenche, o formulário verifica o conflito em tempo real (sem esperar pelo envio) para o mesmo palco/data.

### Reorganizar um item já agendado
Abra o item na agenda → "Editar" → altere data/hora/local/palco. A mesma verificação de conflito aplica-se, ignorando o próprio item que está a editar (não se compara consigo mesmo).

---

## 8. Galeria e Patrocinadores

"Galeria" → "Novo Item": tipo foto (upload de imagem) ou vídeo (URL, ex. YouTube), categoria, título, ordem de exibição. "Patrocinadores" → "Novo Patrocinador": nome, logotipo, site, nível (ex. "ouro"/"prata"), ordem. Ambos alimentam diretamente as páginas públicas correspondentes.

---

## 9. Mensagens de Contacto

"Mensagens de Contacto" mostra as mensagens que os visitantes enviam pelo formulário público de contacto. Marque como lida depois de tratar de cada uma.

---

## 10. Relatórios

"Relatórios" → "Gerar Relatório": escolha o tipo e o formato (PDF ou Excel — na prática um ficheiro CSV, que abre normalmente no Excel/LibreOffice). A geração corre em segundo plano; recebe uma notificação quando estiver pronto, com um link de download. Se demorar muito mais do que o habitual, avise quem trata da manutenção do servidor — a geração depende de um processo (`queue:work`) estar sempre a correr.

---

## Perguntas frequentes

**Criei uma atividade mas ela não aparece na página pública.** Verifique se já tem um horário atribuído na Programação — só atividades agendadas aparecem na agenda pública, e confirme também que a edição está no estado "publicada" ou "em curso".

**Tentei editar um stand e recebi um erro de permissão.** A edição atual provavelmente está "arquivada" — escolha outra edição ativa em "Trocar Feira", ou peça ao Administrador para reverter o estado.

**Aprovei uma inscrição sem querer com o horário errado.** Não é possível desfazer a aprovação pela mesma tela; corrija o horário diretamente na Programação, editando o item já criado.
