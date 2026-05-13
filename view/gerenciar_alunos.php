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

require_once '../controller/conexao.php';

if (!isset($_SESSION['codigo_exclusao'])) {
    $_SESSION['codigo_exclusao'] = 'DEL-' . rand(1000, 9999);
}

$stmt = $pdo->query("SELECT id, nome, email FROM usuarios WHERE tipo = 'aluno' ORDER BY id ASC");
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            <section class="auth-messages form-large">
                <?php if(isset($_GET['erro'])): ?>
                    <article class="alert alert-error">
                        <?php 
                            if($_GET['erro'] == 'codigo_invalido') echo "O código de exclusão informado é inválido.";
                            elseif($_GET['erro'] == 'email_existente') echo "Já existe outro usuário com este e-mail.";
                            elseif($_GET['erro'] == 'dados_invalidos') echo "Preencha todos os campos corretamente.";
                            else echo "Ocorreu um erro na operação.";
                        ?>
                    </article>
                <?php endif; ?>

                <?php if(isset($_GET['sucesso'])): ?>
                    <article class="alert alert-success">
                        <?php 
                            if($_GET['sucesso'] == 'deletado') echo "Aluno excluído permanentemente do sistema.";
                            elseif($_GET['sucesso'] == 'editado') echo "Informações do aluno atualizadas com sucesso.";
                        ?>
                    </article>
                <?php endif; ?>
            </section>

            <section class="course-section">
                <article class="table-responsive">
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
                </article>
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
                
                <p class="text-warning-muted">Esta ação é irreversível. Por favor, insira o código de exclusão para prosseguir.</p>
                
                <fieldset class="input-group">
                    <label for="codigo-auth">Código de Autorização</label>
                    <input type="text" id="codigo-auth" name="codigo_auth" required autocomplete="off">
                </fieldset>
                
                <button type="submit" class="btn-solid-danger w-100 btn-solid">Confirmar Exclusão</button>
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
                
                <button type="submit" class="btn-solid-edit w-100 btn-solid">Salvar Alterações</button>
            </form>
        </article>
    </section>

    <script src="../assets/js/script.js"></script>

    <?php if (isset($_SESSION['codigo_exclusao'])): ?>
    <script>
        console.log("[SISTEMA - ALERTA] CÓDIGO DE AUTORIZAÇÃO PARA EXCLUSÃO: <?php echo $_SESSION['codigo_exclusao']; ?>");
    </script>
    <?php endif; ?>
</body>
</html>