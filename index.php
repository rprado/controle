<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eef3f6">
    <meta name="description" content="Organize sua semana de aulas com o HorárioTEC.">
    <title>Entrar | HorárioTEC</title>
    <link rel="stylesheet" href="assets/estilo.css">
</head>
<body>
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-10">
        <header class="mx-auto flex max-w-7xl items-center justify-between gap-4">
            <a class="inline-flex items-center gap-3 rounded-md text-decoration-none no-underline" href="index.php" aria-label="HorárioTEC, página inicial">
                <span class="grid size-11 place-items-center rounded-xl bg-[#173b57] text-lg font-bold text-white" aria-hidden="true">H</span>
                <span>
                    <span class="block text-lg font-bold tracking-tight text-[#173b57]">HorárioTEC</span>
                    <span class="block text-xs font-medium text-[#526977]">sua semana, em ordem</span>
                </span>
            </a>
            <p class="hidden text-sm text-[#526977] sm:block">Organização simples para sua rotina acadêmica</p>
        </header>

        <main class="mx-auto grid min-w-0 max-w-7xl grid-cols-[minmax(0,1fr)] items-center gap-10 py-10 md:py-14 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:gap-16 lg:py-16">
            <section class="mx-auto min-w-0 w-full max-w-md" aria-labelledby="titulo-login">
                <div class="mb-8">
                    <p class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#d9e1e8] bg-white px-3 py-1.5 text-xs font-semibold text-[#176b87]">
                        <span class="size-2 rounded-full bg-[#5d8c71]" aria-hidden="true"></span>
                        A semana começa com um plano
                    </p>
                    <h1 id="titulo-login" class="mb-3 text-3xl font-bold tracking-tight text-[#173b57] sm:text-4xl">Que bom ter você de volta.</h1>
                    <p class="m-0 leading-7 text-[#526977]">Entre para consultar sua grade e deixar cada aula no seu lugar.</p>
                </div>

                <div class="card border border-[#d9e1e8] bg-white shadow-sm">
                    <div class="card-body gap-5 p-6 sm:p-8">
                        <div>
                            <h2 class="mb-1 text-xl font-bold text-[#173b57]">Entrar na sua conta</h2>
                            <p class="m-0 text-sm text-[#526977]">Use seu nome de usuário para continuar.</p>
                        </div>

                        <form action="#" method="get" class="grid gap-5">
                            <div class="grid gap-2">
                                <label class="text-sm font-semibold text-[#173b57]" for="usuario">Usuário</label>
                                <input class="login-input" id="usuario" name="usuario" type="text" autocomplete="username" placeholder="Ex.: ana.silva" maxlength="50" required>
                            </div>
                            <div class="grid gap-2">
                                <label class="text-sm font-semibold text-[#173b57]" for="senha">Senha</label>
                                <input class="login-input" id="senha" name="senha" type="password" autocomplete="current-password" placeholder="Digite sua senha" required>
                            </div>
                            <button class="btn btn-primary min-h-12 w-full rounded-lg text-base font-semibold" type="submit">Entrar</button>
                        </form>

                        <p class="m-0 text-center text-sm text-[#526977]">Ainda não tem uma conta? <a class="font-semibold underline underline-offset-4" href="#cadastro">Cadastre-se</a></p>
                        <p class="m-0 rounded-lg bg-[#f4f7f8] px-3 py-2 text-xs leading-5 text-[#526977]" role="note">Protótipo visual com dados fictícios. O acesso será conectado nas próximas etapas.</p>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-[#526977]">Feito para acompanhar seu semestre, aula por aula.</p>
            </section>

            <section class="schedule-panel min-w-0" aria-labelledby="titulo-grade-preview">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="mb-1 text-sm font-semibold text-[#176b87]">UMA VISÃO DA SUA ROTINA</p>
                        <h2 id="titulo-grade-preview" class="m-0 text-2xl font-bold tracking-tight text-[#173b57]">Sua semana, de segunda a sábado</h2>
                    </div>
                    <span class="badge badge-soft badge-primary gap-1 rounded-full px-3 py-2 text-xs">Exemplo de grade</span>
                </div>

                <div class="card overflow-hidden border border-[#d9e1e8] bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-[#d9e1e8] px-5 py-4 sm:px-6">
                        <div>
                            <p class="m-0 text-sm font-semibold text-[#173b57]">Turma de exemplo</p>
                            <p class="m-0 mt-1 text-xs text-[#526977]">Técnico em Informática · 2º semestre</p>
                        </div>
                        <span class="hidden items-center gap-2 text-xs text-[#526977] sm:inline-flex"><span class="size-2 rounded-full bg-[#5d8c71]" aria-hidden="true"></span>Semana organizada</span>
                    </div>
                    <div class="schedule-scroll overflow-x-auto p-4 sm:p-5" tabindex="0" aria-label="Prévia da grade semanal. Role horizontalmente para consultar todos os dias.">
                        <div class="schedule-grid">
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Segunda</h3>
                                <div class="class-block mb-2"><p class="m-0 text-xs font-bold text-[#173b57]">Algoritmos</p><p class="m-0 mt-1 text-[11px] text-[#526977]">08:00–09:40</p></div>
                                <div class="class-block class-block-alt"><p class="m-0 text-xs font-bold text-[#173b57]">Matemática</p><p class="m-0 mt-1 text-[11px] text-[#526977]">10:00–11:40</p></div>
                            </div>
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Terça</h3>
                                <div class="class-block class-block-alt mb-2"><p class="m-0 text-xs font-bold text-[#173b57]">Física</p><p class="m-0 mt-1 text-[11px] text-[#526977]">08:00–09:40</p></div>
                                <div class="class-block"><p class="m-0 text-xs font-bold text-[#173b57]">Programação</p><p class="m-0 mt-1 text-[11px] text-[#526977]">13:30–15:10</p></div>
                            </div>
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Quarta</h3>
                                <div class="class-block mb-2"><p class="m-0 text-xs font-bold text-[#173b57]">Banco de Dados</p><p class="m-0 mt-1 text-[11px] text-[#526977]">09:00–10:40</p></div>
                                <div class="class-block class-block-alt"><p class="m-0 text-xs font-bold text-[#173b57]">Português</p><p class="m-0 mt-1 text-[11px] text-[#526977]">13:30–15:10</p></div>
                            </div>
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Quinta</h3>
                                <div class="class-block class-block-alt mb-2"><p class="m-0 text-xs font-bold text-[#173b57]">Redes</p><p class="m-0 mt-1 text-[11px] text-[#526977]">08:00–09:40</p></div>
                                <div class="class-block"><p class="m-0 text-xs font-bold text-[#173b57]">Algoritmos</p><p class="m-0 mt-1 text-[11px] text-[#526977]">10:00–11:40</p></div>
                            </div>
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Sexta</h3>
                                <div class="class-block mb-2"><p class="m-0 text-xs font-bold text-[#173b57]">Programação</p><p class="m-0 mt-1 text-[11px] text-[#526977]">08:00–09:40</p></div>
                                <div class="class-block class-block-alt"><p class="m-0 text-xs font-bold text-[#173b57]">Inglês</p><p class="m-0 mt-1 text-[11px] text-[#526977]">10:00–11:40</p></div>
                            </div>
                            <div class="schedule-day">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#526977]">Sábado</h3>
                                <p class="m-0 rounded-md border border-dashed border-[#cbd8e0] px-2 py-3 text-center text-[11px] leading-4 text-[#526977]">Um espaço livre para recarregar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 border-t border-[#d9e1e8] bg-[#f8fafb] px-5 py-3 text-xs text-[#526977] sm:px-6">
                        <span class="grid size-5 place-items-center rounded-full bg-[#eaf2f5] font-bold text-[#176b87]" aria-hidden="true">i</span>
                        A grade é apenas ilustrativa; seus horários ficam privados na sua conta.
                    </div>
                </div>
            </section>
        </main>

        <footer class="mx-auto flex max-w-7xl flex-col gap-2 border-t border-[#d9e1e8] pt-5 text-xs text-[#526977] sm:flex-row sm:items-center sm:justify-between">
            <span>HorárioTEC · Organização acadêmica sem complicação.</span>
            <span>Protótipo para estudantes do ensino técnico federal.</span>
        </footer>
    </div>
</body>
</html>
