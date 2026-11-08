# PLANO.md — HorárioTEC

Plano de implementação do MVP descrito em `PRODUCT.md`, pensado para quem está
aprendendo PHP. A ideia é avançar **uma tarefa pequena por vez**, sempre com uma
verificação concreta antes de marcar o checkbox.

## Como usar este plano

1. Leia `PRODUCT.md` (o quê) e `AGENTS.md` (regras) antes de começar.
2. Faça **uma tarefa por vez**, na ordem das fases.
3. Uma tarefa só termina quando a coluna **Verificar** passar.
4. Marque `[x]` no plano e faça um commit pequeno por tarefa.
5. Se uma tarefa levar mais de ~45 minutos, pare e peça ajuda: ela está grande
   demais ou faltou algum pré-requisito.

---

## 1. Estrutura de arquivos proposta

Estrutura simples, sem framework e sem pasta `public/`. Cada funcionalidade tem
uma página principal; a lógica fica no topo do arquivo e o HTML abaixo.

```text
controle/
├── AGENTS.md                 # regras do projeto (leia antes de codar)
├── PLANO.md                  # este plano
├── PRODUCT.md                # especificação do produto
├── README.md                 # como instalar e usar (Fase 6)
├── .gitignore                # segredos e pastas geradas
│
├── index.php                 # entrada: manda para login ou grade
├── cadastro.php              # F1: criar conta
├── login.php                 # F1: entrar
├── logout.php                # F1: sair
│
├── disciplinas.php           # F2: listar/criar/editar/excluir disciplinas
├── horarios.php              # F3/F4: listar/criar/editar/excluir horários
├── grade.php                 # F5: grade semanal do aluno
├── exportar_pdf.php          # F5: download do PDF
│
├── config/
│   ├── banco.exemplo.php     # modelo de configuração (vai para o Git)
│   └── banco.php             # dados reais de conexão (NÃO vai para o Git)
│
├── includes/
│   ├── funcoes.php           # e(), redirecionar(), mensagens flash
│   ├── sessao.php            # sessão, usuário logado, exigir_login(), CSRF
│   ├── regras.php            # regras de negócio puras (choque de horário)
│   ├── topo.php              # cabeçalho + menu comum
│   └── rodape.php            # rodapé comum
│
├── assets/
│   └── estilo.css            # um único CSS, sem framework
│
├── sql/
│   └── schema.sql            # criação das tabelas
│
├── tests/
│   ├── run.php               # executa todos os testes
│   ├── regras_test.php       # testes da regra de choque (sem banco)
│   ├── banco_test.php        # testes de isolamento entre alunos (com banco)
│   └── roteiro_manual.md     # passo a passo de teste manual (Fase 6)
│
├── composer.json             # só para instalar o Dompdf (Fase 5)
├── composer.lock
└── vendor/                   # bibliotecas instaladas (NÃO vai para o Git)
```

**Por que assim?**

- Poucas pastas e nomes em português: fácil de achar as coisas.
- Sem MVC, sem rotas, sem framework: uma página PHP = uma tela.
- `includes/` evita repetir cabeçalho, sessão e conexão.
- `regras.php` guarda a regra de choque em funções puras, fáceis de testar.
- `tests/` usa o banco `controle_teste`, separado do banco real.

---

## 2. Decisões fixas (para não decidir no meio do caminho)

| Assunto | Decisão |
|---|---|
| Dias da semana | Inteiro de **1 a 6** (1 = segunda, 6 = sábado) |
| Horário | Texto `HH:MM` (24h), gravado como `TIME` no MySQL |
| Duração | `hora_fim` deve ser **maior** que `hora_inicio` (sem aula passando da meia-noite) |
| Choque | `inicio_novo < fim_existente` **e** `inicio_existente < fim_novo`, no mesmo dia |
| Exclusão de disciplina | Remove os horários junto (`ON DELETE CASCADE`), avisando na tela |
| Nome de disciplina duplicado | Rejeitar duplicata **do mesmo aluno**; outro aluno pode ter o mesmo nome |
| Senha | Sempre `password_hash()` / `password_verify()` |
| Banco (dev) | `controle`; banco de testes: `controle_teste` |
| Dados de outro aluno | Nunca confiar no formulário; filtrar sempre por `usuario_id` da sessão |

### Tabelas previstas em `sql/schema.sql`

```text
usuarios
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
  nome          VARCHAR(100) NOT NULL
  usuario       VARCHAR(50)  NOT NULL UNIQUE
  senha_hash    VARCHAR(255) NOT NULL
  criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP

disciplinas
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
  usuario_id    INT UNSIGNED NOT NULL  -> usuarios(id) ON DELETE CASCADE
  nome          VARCHAR(100) NOT NULL
  UNIQUE (usuario_id, nome)

horarios
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
  disciplina_id INT UNSIGNED NOT NULL  -> disciplinas(id) ON DELETE CASCADE
  dia_semana    TINYINT UNSIGNED NOT NULL      -- 1 a 6
  hora_inicio   TIME NOT NULL
  hora_fim      TIME NOT NULL
  INDEX (disciplina_id, dia_semana)
```

Tudo em `InnoDB` e `utf8mb4`. O dono do horário é descoberto pela disciplina
(`JOIN disciplinas ... WHERE usuario_id = ?`), evitando dado duplicado.

---

## 3. Fases e tarefas

### Fase 0 — Preparação do ambiente

#### [ ] T0.1 — Proteger o repositório
**Fazer:** revogar o token do GitHub que aparece na URL do remote; reconfigurar
o remote sem credencial; criar `.gitignore` com `config/banco.php`, `vendor/`,
`.env`, `*.log` e arquivos de IDE.
**Verificar:** `git remote -v` não mostra token; `git check-ignore -v config/banco.php vendor/` aponta as regras; `git status` limpo.

#### [ ] T0.2 — Criar a conexão PDO
**Fazer:** criar `config/banco.exemplo.php` (sem senha real) e copiar para
`config/banco.php`; função que devolve um `PDO` com `ERRMODE_EXCEPTION` e
`charset=utf8mb4`.
**Verificar:** `php -l config/banco.php` sem erros; um comando PHP que inclui o
arquivo e executa `SELECT 1` imprime `conexao ok`.

#### [ ] T0.3 — Escrever `sql/schema.sql`
**Fazer:** criar as três tabelas conforme a seção 2, com chaves estrangeiras e
índices. Validação de horário é obrigatória no PHP; `CHECK` é opcional.
**Verificar:** importar em `controle_teste`; `SHOW TABLES;` mostra as 3 tabelas;
`DESCRIBE horarios;` mostra as colunas esperadas.

#### [ ] T0.4 — Criar os bancos
**Fazer:** criar `controle` e `controle_teste`; importar o schema nos dois.
**Verificar:** `SHOW DATABASES;` lista os dois; `SELECT COUNT(*) FROM usuarios;`
funciona em ambos.

#### [ ] T0.5 — Helpers básicos
**Fazer:** em `includes/funcoes.php`, criar `e()` (escape de HTML),
`redirecionar()` (com `exit`), `definir_mensagem()` e `mostrar_mensagem()`
(mensagem flash na sessão).
**Verificar:** `php -l includes/funcoes.php`; teste rápido mostrando uma
mensagem e conferindo que ela aparece uma vez e some no recarregamento.

#### [ ] T0.6 — Sessão, login e CSRF
**Fazer:** em `includes/sessao.php`, iniciar sessão com cookie `HttpOnly` e
`SameSite=Lax`; criar `usuario_logado()`, `exigir_login()` e funções de token
CSRF (`csrf_token()` e `validar_csrf()`).
**Verificar:** `php -l`; abrir uma página interna sem sessão redireciona para
`login.php`; um POST sem token é recusado.

#### [ ] T0.7 — Layout e CSS base
**Fazer:** `includes/topo.php`, `includes/rodape.php` e `assets/estilo.css`
com menu (mostra "Entrar/Cadastrar" ou "Disciplinas/Horários/Grade/Sair"),
mensagens flash e layout legível em celular.
**Verificar:** uma página temporária abre com menu, CSS aplicado e sem erro no
console do navegador.

#### [ ] T0.8 — Página de entrada
**Fazer:** `index.php` apenas redireciona: com sessão → `grade.php`; sem sessão
→ `login.php`.
**Verificar:** acessar `http://localhost/controle/` nos dois estados e conferir
o destino.

**Ao final da Fase 0:** `php -l` em todos os arquivos PHP e commit
`F0: estrutura inicial`.

---

### Fase 1 — F1: cadastro, login e logout

#### [ ] T1.1 — Cadastro de aluno
**Fazer:** `cadastro.php` com nome, usuário e senha; validar campos vazios,
tamanho mínimo da senha e usuário único; gravar com `password_hash()`; tratar
duplicata sem erro fatal.
**Verificar:** criar um aluno; no banco, `senha_hash` começa com `$2y$` e não é
a senha digitada; repetir o mesmo usuário mostra mensagem e não cria duplicata.

#### [ ] T1.2 — Login
**Fazer:** `login.php` busca o usuário com prepared statement, usa
`password_verify()`, chama `session_regenerate_id(true)` e redireciona para
`grade.php`; senha errada mostra mensagem genérica.
**Verificar:** login correto entra; senha errada não entra e exibe erro; tentar
acessar `grade.php` sem sessão volta ao login.

#### [ ] T1.3 — Logout e proteção das páginas
**Fazer:** `logout.php` encerra a sessão e destrói o cookie; todas as páginas
internas começam com `exigir_login()`.
**Verificar:** sair e tentar abrir `grade.php`, `disciplinas.php` e
`horarios.php`; todas redirecionam para o login. Botão "voltar" não restaura
conteúdo protegido.

#### [ ] T1.4 — Aceite da F1
**Fazer:** executar o roteiro da F1 do `PRODUCT.md` com dois alunos (A e B).
**Verificar:** todos os critérios de F1 passam; nenhum dado de A aparece para B.

**Commit:** `F1: cadastro, login e logout`.

---

### Fase 2 — F2: CRUD de disciplinas

#### [ ] T2.1 — Listar disciplinas
**Fazer:** `disciplinas.php` lista apenas as disciplinas do usuário da sessão,
ordenadas por nome; estado vazio com aviso.
**Verificar:** A vê só as disciplinas de A; B vê só as de B; aluno sem
disciplinas vê aviso, não erro.

#### [ ] T2.2 — Criar disciplina
**Fazer:** formulário com nome; rejeitar vazio e duplicata do mesmo aluno;
inserir com `usuario_id` vindo da sessão; mensagem de sucesso.
**Verificar:** criar disciplina nova aparece na lista; nome vazio e duplicado
mostram erro; B consegue criar disciplina com o mesmo nome que A.

#### [ ] T2.3 — Editar disciplina
**Fazer:** editar nome via `WHERE id = ? AND usuario_id = ?`; revalidar vazio e
duplicata; manter os horários vinculados.
**Verificar:** renomear reflete na lista e nos horários; tentar editar ID de
outro aluno (pela URL) não altera nada.

#### [ ] T2.4 — Excluir disciplina
**Fazer:** confirmação antes de excluir; excluir com filtro de dono; avisar que
os horários serão removidos junto.
**Verificar:** excluir remove a disciplina e seus horários; tentar excluir ID de
outro aluno não remove nada; cancelar não altera nada.

#### [ ] T2.5 — Aceite da F2
**Fazer:** executar os critérios de F2 do `PRODUCT.md` com A e B.
**Verificar:** todos passam, incluindo isolamento por aluno.

**Commit:** `F2: CRUD de disciplinas`.

---

### Fase 3 — F3: CRUD de horários

#### [ ] T3.1 — Listar horários
**Fazer:** `horarios.php` lista horários do aluno com `JOIN` em disciplinas;
mostrar dia da semana, início, fim e nome da disciplina.
**Verificar:** só aparecem horários de disciplinas do aluno; aluno sem horários
vê aviso; ordem por dia e hora.

#### [ ] T3.2 — Criar horário
**Fazer:** formulário com disciplina (apenas as do aluno), dia (1–6), início e
fim; validar formato `HH:MM` e `fim > início`; validar no servidor que a
disciplina pertence ao aluno; inserir.
**Verificar:** horário válido aparece na lista; fim menor/igual ao início é
recusado sem gravar; enviar `disciplina_id` de outro aluno pela ferramenta de
desenvolvedor é recusado.

#### [ ] T3.3 — Editar horário
**Fazer:** editar com filtro de dono (via `JOIN` na disciplina); repetir todas
as validações; mensagem de sucesso.
**Verificar:** alterações aparecem na lista; ID de outro aluno não é editável;
horário inválido não é salvo.

#### [ ] T3.4 — Excluir horário
**Fazer:** confirmação e exclusão com filtro de dono.
**Verificar:** excluir remove da lista e do banco; ID de outro aluno não remove.

#### [ ] T3.5 — Aceite da F3
**Fazer:** executar os critérios de F3 do `PRODUCT.md`.
**Verificar:** todos passam; nenhuma operação aceita dados de outro aluno.

**Commit:** `F3: CRUD de horarios`.

---

### Fase 4 — F4: bloqueio de choque

#### [ ] T4.1 — Função pura de choque
**Fazer:** em `includes/regras.php`, criar `ha_choque($inicio_a, $fim_a,
$inicio_b, $fim_b)` usando a fórmula da seção 2. A função não acessa banco.
**Verificar:** `php tests/run.php` cobre: sobreposição → `true`; adjacentes
(08:00–09:00 e 09:00–10:00) → `false`; separados → `false`; um dentro do outro
→ `true`.

#### [ ] T4.2 — Consulta de conflito no banco
**Fazer:** função que, para um aluno/dia/intervalo, busca horário conflitante
via `JOIN` nas disciplinas do aluno. Usar prepared statement. Permitir ignorar
um `horario_id` (para edição).
**Verificar:** com dois horários sobrepostos já gravados manualmente no banco de
teste, a consulta encontra o conflito; horário de outro aluno no mesmo horário
não é encontrado.

#### [ ] T4.3 — Aplicar no cadastro e na edição
**Fazer:** antes de inserir/atualizar, checar conflito dentro de uma transação;
se houver, não gravar e mostrar mensagem com o horário conflitante. Na edição,
ignorar o próprio registro.
**Verificar:** cadastro sobreposto é bloqueado e não aparece na lista nem no
banco; edição para um intervalo livre funciona; edição para conflito é
bloqueada; salvar o mesmo registro sem mudar o horário funciona.

#### [ ] T4.4 — Matriz de testes de choque
**Fazer:** testar manualmente, com o aluno A, os casos abaixo.

| Caso | Esperado |
|---|---|
| 08:00–09:00 e 08:30–09:30 | bloquear |
| 08:00–09:00 e 09:00–10:00 (adjacentes) | permitir |
| 08:00–09:00 e 10:00–11:00 | permitir |
| 08:00–09:00 e 08:30–08:45 (totalmente dentro) | bloquear |
| Mesmo horário em dias diferentes | permitir |
| Mesmo horário no aluno B | permitir (agendas separadas) |
| Editar horário para o próprio intervalo | permitir |

**Verificar:** todos os casos se comportam como esperado e o banco fica
consistente após cada bloqueio.

#### [ ] T4.5 — Aceite da F4
**Fazer:** executar os critérios de F4 do `PRODUCT.md`, simulando POST direto
(sem depender de JavaScript).
**Verificar:** todos passam; a checagem é comprovadamente no servidor.

**Commit:** `F4: bloqueio de choque de horario`.

---

### Fase 5 — F5: grade semanal e PDF

#### [ ] T5.1 — Grade na tela
**Fazer:** `grade.php` monta uma tabela de segunda a sábado com os horários do
aluno; mostrar disciplina e faixa de horário; estado vazio com aviso.
**Verificar:** horários aparecem no dia correto; recarregar reflete o banco;
aluno sem horários vê grade vazia sem erro.

#### [ ] T5.2 — Acabamento da grade
**Fazer:** destacar blocos, esconder dias vazios de forma simples, manter
legível no celular e imprimir sem menu.
**Verificar:** leitura confortável em tela pequena e na pré-visualização de
impressão; nenhum dado de outro aluno aparece.

#### [ ] T5.3 — Instalar o gerador de PDF
**Fazer:** criar `composer.json` e instalar **apenas** `dompdf/dompdf`
(`composer require dompdf/dompdf`); confirmar que `vendor/` está ignorado.
**Verificar:** `vendor/autoload.php` existe e é carregado sem erro; `git status`
não lista `vendor/`.

#### [ ] T5.4 — Exportar PDF
**Fazer:** `exportar_pdf.php` exige login, busca somente os horários do aluno e
gera o PDF com nome da disciplina, dia e horário; enviar como download. Não
gravar arquivo temporário no servidor.
**Verificar:** o PDF abre em leitor; conteúdo confere com a tela; aluno sem
horários gera PDF vazio com aviso; conta A não baixa dados de B, mesmo mudando
a URL.

#### [ ] T5.5 — Aceite da F5
**Fazer:** executar os critérios de F5 do `PRODUCT.md`.
**Verificar:** todos passam, incluindo PDF de aluno vazio e isolamento entre
contas.

**Commit:** `F5: grade semanal e exportacao em PDF`.

---

### Fase 6 — Qualidade, documentação e entrega

#### [ ] T6.1 — Testes automatizados da regra de choque
**Fazer:** finalizar `tests/regras_test.php` e o executor `tests/run.php`, com
saída clara de quantos testes passaram/falharam.
**Verificar:** `php tests/run.php` termina com código 0 e mensagem `OK` quando
tudo passa; ao quebrar a regra de propósito, o teste falha e aponta o caso.

#### [ ] T6.2 — Testes de isolamento entre alunos
**Fazer:** `tests/banco_test.php` cria dados em `controle_teste`, verifica que
A não lê/edita/exclui dados de B e limpa o que criou.
**Verificar:** `php tests/run.php` passa; o banco `controle` não é tocado;
rodar duas vezes seguidas não acumula lixo.

#### [ ] T6.3 — Roteiro manual completo
**Fazer:** `tests/roteiro_manual.md` com o fluxo do zero: cadastrar → login →
disciplina → horário → choque → grade → PDF → logout, usando A e B.
**Verificar:** uma pessoa que nunca viu o sistema consegue seguir o roteiro sem
ajuda e todos os passos dão o resultado esperado.

#### [ ] T6.4 — README de instalação e uso
**Fazer:** `README.md` com requisitos, como criar os bancos, importar schema,
configurar `config/banco.php`, iniciar Apache/MySQL e acessar a URL.
**Verificar:** seguir o README em uma instalação limpa (ou pasta copiada) deixa
o sistema funcionando.

#### [ ] T6.5 — Revisão de segurança
**Fazer:** revisar: senhas em hash, nenhum segredo versionado, todos os SQL com
prepared statements, todos os POST com CSRF, saída sempre com `e()`, todas as
consultas filtradas por aluno, `display_errors` desligado no uso normal.
**Verificar:** `git ls-files` não mostra `config/banco.php` nem `vendor/`;
tentativas de SQL injection e de trocar IDs na URL falham; nenhuma senha
aparece em tela, log ou banco.

#### [ ] T6.6 — Checklist final do MVP
**Fazer:** percorrer a seção 8 do `PRODUCT.md` e o fluxo completo do zero.
**Verificar:** os 5 itens da Definição de Pronto estão atendidos; commit final
`MVP: HorarioTEC entregue`.

---

## 4. Rastreabilidade com o PRODUCT.md

| Funcionalidade | Tarefas | Verificação final |
|---|---|---|
| F1 — Cadastro e login | T0.2, T0.6, T1.1–T1.3 | T1.4 |
| F2 — CRUD de disciplinas | T0.3, T2.1–T2.4 | T2.5 |
| F3 — CRUD de horários | T3.1–T3.4 | T3.5 |
| F4 — Choque de horário | T4.1–T4.4 | T4.5 |
| F5 — Grade e PDF | T5.1–T5.4 | T5.5 |
| Qualidade e entrega | T6.1–T6.6 | T6.6 |

## 5. Ordem de commits sugerida

Um commit por tarefa ou por fase concluída, sempre com o plano atualizado:

```text
F0: estrutura inicial
F1: cadastro, login e logout
F2: CRUD de disciplinas
F3: CRUD de horarios
F4: bloqueio de choque de horario
F5: grade semanal e exportacao em PDF
T6: testes, README e revisao final
```

Nunca commitar `config/banco.php`, `vendor/` nem qualquer token/senha.
