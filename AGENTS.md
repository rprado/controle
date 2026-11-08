# AGENTS.md — HorárioTEC

Regras para qualquer pessoa (ou agente de IA) que for alterar este repositório.
O objetivo é manter o MVP simples, seguro e fácil de entender por um iniciante.

## 1. Leia antes de começar

1. `PRODUCT.md` — o que o sistema deve fazer (escopo fechado).
2. `PLANO.md` — qual tarefa fazer agora e como verificá-la.
3. Este arquivo — como fazer (tecnologias, convenções, segurança, testes).

Se os três entrarem em conflito, **pare e pergunte**. Não invente escopo.

## 2. Regras de trabalho

- Faça **uma tarefa por vez**, exatamente a tarefa marcada como pendente no
  `PLANO.md`. Nada de adiantar fases.
- Não implemente itens das seções 4 e 7 do `PRODUCT.md` (funcionalidades
  futuras e fora de escopo).
- Não refatore partes que a tarefa atual não pede.
- Mantenha o código mais simples que resolva o problema; sem abstrações
  "para o futuro".
- Ao terminar, informe: o que mudou, como verificou e o resultado dos testes.
- Só marque `[x]` no `PLANO.md` depois que a verificação passar.
- **Não** faça `git push`, `git reset --hard`, `git rebase` ou alteração de
  histórico. Commits só quando o usuário pedir.
- Nunca apague ou reescreva `PRODUCT.md` para "ajustar" o código. O documento é
  a fonte da verdade.
- Se encontrar um segredo exposto, pare e avise (ver seção 5).

## 3. Tecnologias permitidas

| Permitido | Observações |
|---|---|
| PHP 8.1+ (ambiente atual: 8.2) | PHP puro, sem framework |
| MySQL/MariaDB do XAMPP | via **PDO** com prepared statements |
| HTML5, CSS3 | um único `assets/estilo.css` |
| JavaScript puro (vanilla) | opcional; só melhoria de tela, nunca regra de negócio |
| Composer | **somente** para instalar `dompdf/dompdf` (Fase 5) |
| Git | commits pequenos e descritivos |
| Apache do XAMPP | servir em `http://localhost/controle/` |

**Não permitido sem autorização explícita:**

- Frameworks PHP (Laravel, Symfony, CodeIgniter...), MVC ou sistema de rotas.
- ORMs ou query builders (Eloquent, Doctrine...).
- `mysqli` ou SQL concatenado com variáveis.
- jQuery, React, Vue, Bootstrap, Tailwind ou qualquer framework de front-end.
- npm, Node.js, bundlers, Sass/Less ou etapa de build.
- CDN externa (o sistema deve funcionar offline, exceto a instalação local).
- APIs externas, envio de e-mail, upload de arquivos, login social.
- Bibliotecas além do Dompdf. Precisa de outra? Pergunte antes e registre no
  `PLANO.md`.

## 4. Estrutura e convenções

### Arquivos e nomes

- Pastas e arquivos em minúsculas com `snake_case`: `exportar_pdf.php`,
  `banco_test.php`.
- Páginas têm o nome da funcionalidade: `disciplinas.php`, `horarios.php`.
- Funções e variáveis em `snake_case`, em português: `buscar_horarios()`,
  `$usuario_id`.
- Tabelas no plural (`usuarios`, `disciplinas`, `horarios`); colunas no
  singular (`nome`, `hora_inicio`).
- Um conceito por arquivo em `includes/`: sessão, funções, regras, layout.

### PHP

- Comece arquivos que só têm PHP **sem** a tag de fechamento `?>`.
- Indentação de **4 espaços**; arquivos em UTF-8 sem BOM.
- Inclua dependências com caminho absoluto:
  `require_once __DIR__ . '/includes/sessao.php';`
- Página típica: validação e gravação no topo, HTML depois.
- Todo formulário que grava usa **POST + redirect + GET** (evita reenvio).
- Toda página interna começa com `exigir_login()`.
- Erros de banco: `try/catch (PDOException $e)`, `error_log($e->getMessage())`
  e mensagem amigável ao usuário. Nunca mostrar stack trace na tela.
- Não use `extract()`, variáveis globais soltas nem `eval()`.
- Valide no servidor. JavaScript é só conveniência.

### Banco de dados

- Sempre **prepared statements** com `?` ou parâmetros nomeados.
- Toda consulta de dados do aluno filtra por `usuario_id` da **sessão**, nunca
  por um ID vindo do formulário ou da URL.
- O dono do horário vem da disciplina: `JOIN disciplinas` + `usuario_id`.
- Operações de gravação que dependem de checagem (ex.: conflito + insert) usam
  transação (`beginTransaction` / `commit` / `rollBack`).
- Não altere o schema fora da tarefa; mudanças de schema precisam ser
  registradas em `sql/schema.sql` e combinadas antes.
- Nunca rode comandos destrutivos (`DROP`, `TRUNCATE`, `DELETE` sem `WHERE`)
  no banco `controle`. Testes usam somente `controle_teste`.

### Regras de negócio

- Dias: `1` a `6` (segunda a sábado).
- Horário: `HH:MM` com zero à esquerda, 24h; no banco, `TIME`.
- `hora_fim` deve ser maior que `hora_inicio`.
- Choque: `inicio_novo < fim_existente` **e** `inicio_existente < fim_novo`, no
  mesmo dia. Horários adjacentes (um termina quando o outro começa) **não**
  conflitam.
- Choque só existe entre horários do **mesmo aluno**.
- Regra de choque fica em `includes/regras.php`, em função pura e testável.
- Exclusão de disciplina remove os horários junto (cascata), com aviso na tela.

### Interface

- Textos, mensagens de erro e títulos em **português**.
- `lang="pt-BR"`, `charset="utf-8"`.
- Formulários com `<label>` ligado ao campo, `required` e `maxlength`.
- Toda saída dinâmica passa por `e()` (escape HTML).
- Layout legível em celular, sem depender de JS para funcionar.
- Mensagens claras e específicas: "Já existe uma disciplina com esse nome."

## 5. Segredos e segurança

### Segredos

- **Nunca** versione `config/banco.php`, `.env`, tokens, senhas ou chaves.
- O Git só deve conter `config/banco.exemplo.php`, com valores fictícios.
- **Nunca** coloque token em URL de remote Git. Use o Git Credential Manager.
- O remote atual já teve token em texto puro; ele deve ser revogado/rotacionado
  antes de qualquer publicação. Se encontrar credencial em `.git/config`,
  arquivo, commit ou log: **pare e avise o usuário**.
- Não cole credenciais reais em chats, issues, prints, `README.md` ou testes.
- Se um segredo vazar: revogue/rotacione primeiro; apagar o arquivo ou o commit
  não desfaz o vazamento.
- Não registre senhas, tokens ou IDs de sessão em logs.

### Código seguro (obrigatório)

- Senhas: `password_hash($senha, PASSWORD_DEFAULT)` e `password_verify()`.
  Nunca MD5, SHA1 ou texto puro.
- Sessão: cookie `HttpOnly` e `SameSite=Lax`; `session_regenerate_id(true)`
  logo após o login; `session_destroy()` e remoção do cookie no logout.
- CSRF: todo POST valida `csrf_token()` / `validar_csrf()`. Token em campo
  oculto do formulário.
- XSS: escapar toda saída com `e()`.
- SQL injection: prepared statements, sem interpolação de variáveis.
- Autorização: validar no servidor que disciplina/horário pertence ao aluno
  logado, mesmo que o ID não apareça na tela.
- Não confiar em campos ocultos, `disabled` ou validação apenas no navegador.
- Em uso normal, `display_errors` desligado; erros vão para o log do PHP.

## 6. Procedimento de testes

### Antes de cada commit

1. Verificar sintaxe de todos os PHP alterados (ou de todos, é rápido):

   ```powershell
   Get-ChildItem -Recurse -Filter *.php |
     Where-Object { $_.FullName -notmatch '\\vendor\\' } |
     ForEach-Object { php -l $_.FullName }
   ```

   Todos devem retornar `No syntax errors detected`.

2. Rodar os testes automatizados (a partir da tarefa T6.1):

   ```powershell
   php tests\run.php
   ```

   Saída esperada: todos os testes `OK`, código de saída `0`.

3. Executar a verificação manual da tarefa no `PLANO.md`.

4. Só então marcar o checkbox e commitar (se solicitado).

### Banco de teste

- Use **sempre** o banco `controle_teste`, nunca `controle`, nos testes.
- Criar/recriar o banco quando necessário:

  ```powershell
  & C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS controle_teste CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  Get-Content -Raw sql\schema.sql | & C:\xampp\mysql\bin\mysql.exe -u root controle_teste
  ```

- Testes devem limpar os dados que criaram e poder rodar várias vezes seguidas.

### Teste manual mínimo (sempre que mexer em dados do aluno)

Use **dois alunos, A e B**, e confirme:

- A não vê, edita nem exclui disciplina/horário de B, inclusive trocando o ID
  na URL ou no formulário.
- Cadastro duplicado e senha errada são recusados.
- Página interna sem sessão redireciona para o login; logout bloqueia o acesso.
- Horário com fim ≤ início é recusado.
- Choque no mesmo dia é bloqueado; adjacente, outro dia e outro aluno passam.
- Texto digitado (ex.: `<script>alert(1)</script>`) aparece como texto, não
  executa.
- Grade e PDF mostram apenas os dados do aluno logado; aluno sem horários não
  gera erro.

### Teste específico de choque

| Início existente | Novo horário | Esperado |
|---|---|---|
| 08:00–09:00 | 08:30–09:30 | bloquear |
| 08:00–09:00 | 09:00–10:00 | permitir |
| 08:00–09:00 | 10:00–11:00 | permitir |
| 08:00–09:00 em outro dia | 08:00–09:00 | permitir |
| 08:00–09:00 no aluno B | 08:00–09:00 no aluno A | permitir |
| edição do próprio horário | mesmo intervalo | permitir |

Depois de cada bloqueio, confirme no banco que nada foi gravado.

### Teste do PDF

- Baixar o PDF logado como A e conferir que abre em leitor.
- Conferir disciplina, dia e horário contra a tela.
- Conferir que B não consegue obter o PDF de A alterando a URL.
- Aluno sem horários: PDF vazio com aviso, sem erro.

## 7. Comandos úteis (Windows/XAMPP)

```powershell
# Iniciar Apache e MySQL pelo painel do XAMPP, depois testar a URL:
# http://localhost/controle/

# Criar o banco de desenvolvimento e importar o schema
& C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS controle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Get-Content -Raw sql\schema.sql | & C:\xampp\mysql\bin\mysql.exe -u root controle

# Conferir tabelas
& C:\xampp\mysql\bin\mysql.exe -u root controle -e "SHOW TABLES;"

# Verificar sintaxe de um arquivo
php -l caminho\arquivo.php

# Rodar os testes (após T6.1)
php tests\run.php

# Instalar o Dompdf (somente na T5.3)
composer require dompdf/dompdf
```

## 8. Quando parar e perguntar

Pare antes de continuar se:

- a tarefa exigir tecnologia ou dependência fora da seção 3;
- houver dúvida de escopo ou conflito entre `PRODUCT.md` e `PLANO.md`;
- for necessário mudar o schema de forma não prevista;
- algum comando puder apagar dados fora de `controle_teste`;
- encontrar segredo, token ou senha no repositório;
- um teste falhar e a correção não for óbvia.

Ao pedir ajuda, informe: tarefa atual, o que tentou, comando/erro exato e o que
já foi verificado.
