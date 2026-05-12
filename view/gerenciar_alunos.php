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

$alunos = [
    ['id' => 1, 'nome' => 'Ana Silva', 'email' => 'ana@email.com'],
    ['id' => 2, 'nome' => 'Carlos Souza', 'email' => 'carlos@email.com'],
    ['id' => 3, 'nome' => 'Beatriz Lima', 'email' => 'beatriz@email.com']
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Alunos - SIPN</title>
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
                <a href="cadastrar_entidade.php" class="sidebar-link">Cadastrar Entidade</a>
                <a href="gerenciar_alunos.php" class="sidebar-link active">Gerenciar Alunos</a>
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
                <h2>Gerenciar Alunos</h2>
                <p>Visualize, edite as informações ou remova alunos cadastrados na plataforma.</p>
            </header>

            <section class="course-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome Completo</th>
                                <th>E-mail</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td><?php echo $aluno['id']; ?></td>
                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                                <td>
                                    <button class="btn-edit btn-edit-trigger" data-type="aluno" data-id="<?php echo $aluno['id']; ?>" data-nome="<?php echo htmlspecialchars($aluno['nome']); ?>" data-email="<?php echo htmlspecialchars($aluno['email']); ?>">Editar</button>
                                    <button class="btn-danger btn-delete-trigger" data-type="aluno" data-id="<?php echo $aluno['id']; ?>">Deletar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </section>

    <section id="delete-modal" class="modal-hidden">
        <article class="login-card">
            <header class="card-header">
                <h2>Confirmação de Exclusão</h2>
                <button id="btn-close-delete-modal" class="close-btn-modal">×</button>
            </header>
            <form action="../controller/processa_admin.php" method="POST">
                <input type="hidden" name="acao" value="deletar_item">
                <input type="hidden" name="tipo_item" id="delete-tipo">
                <input type="hidden" name="id_item" id="delete-id">
                
                <p style="margin-bottom: 1.5rem; color: #4a5568;">Esta ação é irreversível. Por favor, insira seu código de autorização especial para prosseguir.</p>
                
                <fieldset class="input-group">
                    <label for="codigo-auth">Código de Autorização (DEL2026)</label>
                    <input type="password" id="codigo-auth" name="codigo_auth" required>
                </fieldset>
                
                <button type="submit" class="btn-solid w-100" style="background: #e53e3e;">Confirmar Exclusão</button>
            </form>
        </article>
    </section>

    <section id="edit-modal-aluno" class="modal-hidden">
        <article class="login-card">
            <header class="card-header">
                <h2>Editar Aluno</h2>
                <button id="btn-close-edit-aluno" class="close-btn-modal">×</button>
            </header>
            <form action="../controller/processa_admin.php" method="POST">
                <input type="hidden" name="acao" value="editar_aluno">
                <input type="hidden" name="id" id="edit-aluno-id">
                
                <fieldset class="input-group">
                    <label for="edit-aluno-nome">Nome Completo</label>
                    <input type="text" id="edit-aluno-nome" name="nome" required>
                </fieldset>

                <fieldset class="input-group">
                    <label for="edit-aluno-email">E-mail</label>
                    <input type="email" id="edit-aluno-email" name="email" required>
                </fieldset>
                
                <button type="submit" class="btn-solid w-100" style="background: #3182ce;">Salvar Alterações</button>
            </form>
        </article>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>