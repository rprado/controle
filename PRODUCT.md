# PRODUCT.md — HorárioTEC

## 1. Visão do produto

**HorárioTEC** é um sistema web para estudantes do ensino técnico federal
organizarem seus horários de aula em uma grade semanal, com autenticação
individual e exportação em PDF. O sistema **impede** que o aluno cadastre
horários que se sobreponham no mesmo dia.

- **Público-alvo:** estudantes do ensino técnico federal.
- **Uso:** individual (cada aluno acessa apenas a própria agenda).
- **Problema resolvido:** montar e manter uma grade de horários sem choques,
  de forma simples e consultável (tela e PDF).

## 2. Escopo confirmado

| Item | Definição |
|---|---|
| Uso | Individual, com login |
| Cadastro | Aluno se cadastra sozinho |
| Dados gerenciados | Apenas disciplinas e horários de aula |
| Regra de negócio | Apenas evitar choques de horário |
| Comportamento no choque | Sistema **bloqueia** o salvamento |
| Plataforma | Navegador web |
| Stack | PHP + MySQL (PDO) |
| Exportação | PDF da agenda do aluno logado |
| Prazo | Um semestre (~16 semanas) |

## 3. Funcionalidades essenciais (MVP)

O MVP é composto por **5 funcionalidades**. Nada além disso é essencial.

### F1. Cadastro e login de aluno
**História de usuário**
> Como aluno do ensino técnico federal, quero criar minha conta e entrar no
> sistema, para que minha agenda fique salva e acessível apenas por mim.

**Critérios de aceitação**
- [ ] Cadastro aceita nome, usuário (único) e senha; dados válidos criam a conta.
- [ ] Usuário já existente exibe erro e não cria duplicata.
- [ ] Senha é gravada com hash (nunca em texto puro).
- [ ] Login correto abre a agenda; login incorreto exibe erro e não autentica.
- [ ] Sem sessão ativa, páginas internas redirecionam para o login.
- [ ] Logout encerra a sessão e bloqueia o acesso posterior.

**Dependências:** nenhuma.

---

### F2. CRUD de disciplinas
**História de usuário**
> Como aluno autenticado, quero cadastrar, listar, editar e excluir minhas
> disciplinas, para organizar os horários em torno delas.

**Critérios de aceitação**
- [ ] Disciplina com nome válido aparece na lista do aluno logado.
- [ ] Cada aluno vê apenas as próprias disciplinas.
- [ ] Editar o nome atualiza a lista e mantém horários vinculados.
- [ ] Excluir disciplina remove (ou bloqueia, de forma consistente) seus horários.
- [ ] Nome vazio ou duplicado para o mesmo aluno é rejeitado com mensagem.

**Dependências:** F1.

---

### F3. CRUD de horários de aula
**História de usuário**
> Como aluno autenticado, quero cadastrar os horários de cada disciplina
> (dia e faixa de horário), para montar minha grade semanal.

**Critérios de aceitação**
- [ ] Cadastro exige disciplina do próprio aluno, dia da semana, hora de início e fim.
- [ ] Hora de fim maior que a de início é obrigatória; senão, erro e nada é salvo.
- [ ] Horário salvo aparece na lista/grade do aluno logado.
- [ ] Editar e excluir refletem imediatamente na grade.
- [ ] Aluno não consegue criar/editar horário em disciplina de outro aluno (bloqueio no servidor).

**Dependências:** F1, F2.

---

### F4. Detecção e bloqueio de choque de horário
**História de usuário**
> Como aluno, quero que o sistema impeça cadastrar dois horários que se
> sobreponham no mesmo dia, para não montar uma grade impossível.

**Critérios de aceitação**
- [ ] Horário que se sobrepõe a outro do mesmo aluno no mesmo dia é **bloqueado**, com mensagem de conflito.
- [ ] Sobreposição = `inicio_novo < fim_existente` **e** `inicio_existente < fim_novo`, no mesmo dia.
- [ ] Horários adjacentes (ex.: 09:10–10:00 e 10:00–10:50) **não** são choque.
- [ ] Mesmo horário em dias diferentes **não** é choque.
- [ ] Checagem ocorre **no servidor** (não só no JavaScript).
- [ ] Após o bloqueio, o horário conflitante não aparece na grade nem no banco.

**Dependências:** F3.

---

### F5. Grade semanal e exportação em PDF
**História de usuário**
> Como aluno autenticado, quero ver minha grade semanal e baixá-la em PDF,
> para consultar meus horários offline e imprimir.

**Critérios de aceitação**
- [ ] Grade exibe segunda a sábado com os horários do aluno logado, identificando a disciplina.
- [ ] Grade reflete o estado atual do banco ao recarregar.
- [ ] "Exportar PDF" gera arquivo com a grade do aluno logado (não de outro).
- [ ] PDF contém disciplinas e faixas de horário visíveis na tela e abre em leitor de PDF.
- [ ] Aluno sem horários recebe grade/PDF vazio sem erro (ou aviso claro).

**Dependências:** F1, F2, F3.

## 4. Funcionalidades futuras (fora do MVP)

| # | Funcionalidade | Motivo da exclusão |
|---|---|---|
| N1 | Blocos de estudo pessoal / metas de horas | Escopo confirmado: só disciplinas e horários de aula |
| N2 | Priorização por proximidade de prova / peso | Regra única é só evitar choque |
| N3 | Recuperação de senha por e-mail | Exige e-mail/serviço externo |
| N4 | Layout/filtros personalizados no PDF | PDF simples já atende |
| N5 | Painel administrativo / gestão de contas | Aluno se cadastra sozinho |
| N6 | Notificações/lembretes | Fora do prazo semestral |
| N7 | Compartilhamento de agenda entre alunos | Uso individual confirmado |

## 5. Mapa de dependências
F1 (cadastro/login)
└─> F2 (disciplinas)
└─> F3 (horários)
└─> F4 (bloqueio de choque)
└─> F5 (grade + PDF)


- **F1** é pré-requisito de todas.
- **F2** depende de F1.
- **F3** depende de F1 + F2.
- **F4** depende de F3 (regra aplicada no salvamento).
- **F5** depende de F1 + F2 + F3.

## 6. Cronograma sugerido (16 semanas)

| Semanas | Entrega |
|---|---|
| 1–2 | Modelagem do banco + protótipo estático da grade |
| 3–4 | F1 — cadastro de usuário + login/sessão |
| 5–6 | F2 — CRUD de disciplinas |
| 7–8 | F3 — CRUD de horários |
| 9–12 | F4 — detecção e bloqueio de choque |
| 13–14 | F5 — grade + exportação PDF |
| 15–16 | Testes, documentação e apresentação |

## 7. Fora de escopo (global)

- Aplicativo mobile nativo.
- Multiusuário compartilhado (agendas coletivas).
- Integração com sistemas acadêmicos da instituição.
- Qualquer funcionalidade das seções 4 e N1–N7.

## 8. Definição de pronto (Definition of Done) do MVP

O MVP é considerado concluído quando:
1. F1 a F5 estão implementadas e atendem a todos os critérios de aceitação.
2. Um aluno consegue, do zero: cadastrar-se → logar → criar disciplinas →
   criar horários → ser bloqueado ao tentar um choque → ver a grade → exportar PDF.
3. A checagem de choque é validada no servidor.
4. Um segundo aluno não acessa dados do primeiro.
5. Documentação curta de uso e apresentação final entregues.