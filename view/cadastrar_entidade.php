<?php
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'funcionario') {
    if (!isset($_COOKIE['funcionario_logado']) || $_COOKIE['funcionario_logado'] !== 'true') {
        header("Location: login_funcionario.php");
        exit();
    } else {
        $_SESSION['usuario_tipo'] = 'funcionario';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Entidade - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-body">
    <section class="dashboard-layout">
        <aside class="sidebar">
            <header class="sidebar-header">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="sidebar-logo">
            </header>
            <nav class="sidebar-nav">
                <a href="painel_funcionario.php" class="sidebar-link">Início</a>
                <a href="cadastrar_curso.php" class="sidebar-link">Cadastrar Curso</a>
                <a href="cadastrar_entidade.php" class="sidebar-link active">Cadastrar Entidade</a>
                <a href="gerenciar_alunos.php" class="sidebar-link">Gerenciar Alunos</a>
                <a href="gerenciar_funcionarios.php" class="sidebar-link">Gerenciar Funcionários</a>
                <a href="gerenciar_cursos.php" class="sidebar-link">Gerenciar Cursos</a>
                <a href="gerenciar_entidades.php" class="sidebar-link">Gerenciar Entidades</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Cadastrar Nova Entidade</h2>
                <p>Adicione instituições de ensino parceiras ao sistema.</p>
            </header>

            <section class="course-section">
                <article class="admin-form-card" style="max-width: 600px; margin: 0 auto;">
                    <form action="../controller/processa_admin.php" method="POST">
                        <input type="hidden" name="acao" value="cadastrar_entidade">
                        
                        <fieldset class="input-group">
                            <label for="nome">Nome da Instituição</label>
                            <input type="text" id="nome" name="nome" required>
                        </fieldset>

                        <fieldset class="input-group">
                            <label for="cnpj">CNPJ</label>
                            <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" required>
                        </fieldset>

                        <fieldset class="input-group">
                            <label for="email">E-mail de Contato Comercial</label>
                            <input type="email" id="email" name="email" required>
                        </fieldset>

                        <menu style="margin-top: 2rem; padding: 0; display: flex; gap: 1rem;">
                            <button type="submit" class="btn-solid w-100">Finalizar Cadastro</button>
                            <a href="painel_funcionario.php" class="btn-outline w-100" style="text-align: center; display: inline-block;">Cancelar</a>
                        </menu>
                    </form>
                </article>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>