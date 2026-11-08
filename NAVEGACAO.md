# Navegação do HorárioTEC

*Telas do MVP e fluxo principal do estudante*

Este documento descreve as telas necessárias para o aluno criar uma conta, organizar disciplinas e horários, consultar a grade semanal e exportá-la em PDF. O acesso é individual: cada aluno consulta e altera apenas os próprios dados.

## Mapa do fluxo

| Entrada | Caminho principal | Resultado |
|---|---|---|
| Sem sessão | Entrar → Cadastrar-se (se ainda não tiver conta) | Acesso à agenda após autenticação |
| Com sessão | Grade semanal ↔ Disciplinas ↔ Horários | Agenda pessoal organizada e atualizada |
| Da grade | Exportar PDF → baixar arquivo → continuar na grade | Cópia da agenda do aluno autenticado |
| Qualquer tela interna | Sair → Entrar | Sessão encerrada; páginas internas exigem novo login |

**Sequência de primeiro uso:** cadastrar conta → entrar → criar disciplinas → adicionar horários → consultar a grade. Se um novo horário se sobrepuser a outro no mesmo dia, o sistema recusa o salvamento e mantém a pessoa no formulário com uma explicação.

## Desenho simples para celular e computador

- **Computador:** cabeçalho com nome HorárioTEC e navegação; conteúdo central com largura confortável. Na grade, dias da semana em colunas e faixas de aula em blocos identificados pela disciplina.
- **Celular:** cabeçalho compacto; navegação em itens empilhados; formulários em uma coluna. A grade pode rolar horizontalmente para manter os seis dias legíveis, com o dia indicado em cada coluna.
- Ações de criar, editar, excluir, exportar e sair permanecem identificadas por texto. Confirmação antes de excluir disciplina ou horário; mensagens de erro e sucesso aparecem junto ao título da tela.
- As páginas usam carregamento tradicional: ao abrir uma tela ou enviar um formulário, o navegador indica a navegação/envio. Não é previsto carregamento parcial por JavaScript.

## Navegação comum

| Contexto | Itens disponíveis |
|---|---|
| Visitante | Entrar, Cadastrar-se |
| Aluno autenticado | Grade, Disciplinas, Horários, Sair |
| Operação concluída | Redirecionamento para a lista ou grade, com mensagem de resultado |

## 1. Cadastro de aluno

Permite criar a conta individual necessária para guardar uma agenda.

- **Campos:** nome (obrigatório, até 100 caracteres); usuário (obrigatório, até 50 caracteres e único); senha (obrigatória).
- **Botões e ações:** Criar conta; Entrar (leva à tela de login).
- **Mensagens:** “Conta criada. Entre para acessar sua agenda.”; “Este usuário já está em uso.”; “Preencha os campos obrigatórios.”; erro inesperado: “Não foi possível criar sua conta. Tente novamente.”

| Estado | Comportamento |
|---|---|
| Carregamento | Durante o envio, o navegador indica que a página está sendo carregada; evitar clique repetido enquanto aguarda. |
| Lista vazia | Não há lista nesta tela. |
| Sucesso | Conta gravada; redirecionar ao login e mostrar confirmação. |
| Erro | Manter os dados não sensíveis preenchidos, destacar o campo/motivo e permitir correção; nunca revelar detalhes técnicos. |

## 2. Login

Autentica o aluno e abre sua grade pessoal.

- **Campos:** usuário (obrigatório); senha (obrigatória).
- **Botões e ações:** Entrar; Cadastrar-se (leva ao cadastro).
- **Mensagens:** credenciais inválidas: “Usuário ou senha incorretos.”; validação: “Informe usuário e senha.”; erro inesperado: “Não foi possível entrar. Tente novamente.”

| Estado | Comportamento |
|---|---|
| Carregamento | Indicação do navegador enquanto o formulário é enviado. |
| Lista vazia | Não há lista nesta tela. |
| Sucesso | Sessão iniciada e redirecionamento para a grade semanal. |
| Erro | Continuar no login, limpar/ocultar a senha e exibir mensagem genérica sem confirmar se o usuário existe. |

## 3. Grade semanal

Visão principal após login; apresenta segunda a sábado e os horários da conta autenticada.

- **Campos:** sem campos de edição na grade. Cada bloco mostra disciplina, dia e hora de início e fim.
- **Botões e ações:** Exportar PDF; Disciplinas; Horários; Sair. Ações de criar/editar horários podem ser acessadas em Horários.
- **Mensagens:** agenda vazia: “Você ainda não tem horários cadastrados.” com orientação para abrir Horários; falha ao buscar: “Não foi possível carregar sua grade. Tente novamente.”; exportação: confirmação de download quando disponível.

| Estado | Comportamento |
|---|---|
| Carregamento | Navegação completa com indicador do navegador enquanto a grade é buscada. |
| Lista vazia | Mostrar os seis dias sem blocos e um convite claro para cadastrar disciplina e horário. |
| Sucesso | Mostrar blocos atualizados após recarregar; confirmação breve após voltar de uma gravação, se aplicável. |
| Erro | Preservar navegação e mostrar mensagem amigável; não exibir dados de outra conta. |

## 4. Disciplinas

Lista e administra as disciplinas do aluno. Criar e editar podem usar o mesmo formulário com ação identificada.

- **Campos:** nome da disciplina (obrigatório, até 100 caracteres). Na edição, o nome atual aparece preenchido.
- **Botões e ações:** Nova disciplina; Salvar; Cancelar; Editar; Excluir; Horários; Grade; Sair. Antes de excluir, pedir confirmação e avisar que os horários vinculados também serão removidos.
- **Mensagens:** sem disciplinas: “Nenhuma disciplina cadastrada.”; duplicada: “Já existe uma disciplina com esse nome.”; inválida: “Informe um nome para a disciplina.”; sucesso: “Disciplina salva.” / “Disciplina excluída. Os horários vinculados também foram removidos.”

| Estado | Comportamento |
|---|---|
| Carregamento | Indicação do navegador ao abrir, salvar ou excluir. |
| Lista vazia | Exibir estado vazio e botão Nova disciplina. |
| Sucesso | Atualizar a lista após salvar; voltar à lista após cancelar edição; após excluir, confirmar remoção e cascata. |
| Erro | Exibir validação junto ao formulário ou falha de gravação; manter a lista e os dados existentes. |

## 5. Horários de aula

Lista, cria, edita e exclui os horários vinculados às disciplinas próprias.

- **Campos:** disciplina (seleção das disciplinas do aluno); dia da semana (segunda a sábado); hora de início e hora de fim (formato 24 horas HH:MM). O fim deve ser posterior ao início.
- **Botões e ações:** Novo horário; Salvar; Cancelar; Editar; Excluir; Grade; Disciplinas; Sair. Exclusão pede confirmação.
- **Mensagens:** sem horários: “Nenhum horário cadastrado.”; sem disciplinas: “Cadastre uma disciplina antes de adicionar horários.”; intervalo inválido: “A hora de fim deve ser maior que a hora de início.”; conflito: “Este horário se sobrepõe a outro compromisso neste dia.”; sucesso: “Horário salvo.” / “Horário excluído.”

| Estado | Comportamento |
|---|---|
| Carregamento | Indicação do navegador ao carregar a lista ou enviar formulário. |
| Lista vazia | Mostrar convite para adicionar horário; se não houver disciplina, orientar primeiro a criar uma. |
| Sucesso | Lista atualizada após salvar ou excluir; a grade reflete a alteração ao ser aberta/recarregada. |
| Erro | Manter no formulário com valores informados e mensagem clara; conflito ou intervalo inválido não grava nenhum horário. |

## 6. Exportação em PDF

Entrega um arquivo com a grade semanal do aluno atualmente autenticado.

- **Campos:** sem campos. A ação usa a sessão atual e não aceita identificador de outro aluno.
- **Botões e ações:** Exportar PDF (na grade); ao concluir o download, o aluno pode voltar à grade pelo navegador.
- **Mensagens:** com horários: arquivo PDF com disciplinas, dias e faixas de horário; sem horários: PDF vazio acompanhado de aviso claro; falha: “Não foi possível gerar o PDF. Tente novamente.”

| Estado | Comportamento |
|---|---|
| Carregamento | O navegador indica a geração/transferência do arquivo; evitar submissões repetidas. |
| Lista vazia | Não há lista na tela de resposta; o conteúdo do PDF informa que a grade está vazia. |
| Sucesso | Iniciar download do PDF da própria agenda. |
| Erro | Exibir mensagem amigável e oferecer retorno à grade. |

## Regras de navegação e consistência

- Páginas internas exigem sessão. Ao sair, encerrar a sessão; uma tentativa posterior de abrir Grade, Disciplinas ou Horários retorna ao login.
- A lista e as operações de disciplina e horário mostram e alteram apenas dados do aluno autenticado, inclusive quando um identificador é alterado manualmente.
- Horários adjacentes são permitidos; sobreposições no mesmo dia são bloqueadas no salvamento. A mesma faixa em outro dia pode ser cadastrada.
- Excluir disciplina remove também seus horários e deve avisar isso antes da confirmação. Excluir horário não remove a disciplina.
- Não há telas de recuperação de senha por e-mail, compartilhamento, administração ou personalização avançada do PDF no MVP.
