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

$stmtCursos = $pdo->query("
    SELECT c.id, c.titulo, c.descricao, c.carga_horaria, c.imagem_capa, c.entidade_id, e.nome AS entidade 
    FROM cursos c 
    JOIN entidades e ON c.entidade_id = e.id 
    ORDER BY c.id ASC
");
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

$stmtEntidades = $pdo->query("SELECT id, nome FROM entidades ORDER BY nome ASC");
$entidades = $stmtEntidades->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos - SIPN</title>
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
                <a href="gerenciar_alunos.php" class="sidebar-link">Gerenciar Alunos</a>
                <a href="gerenciar_funcionarios.php" class="sidebar-link">Gerenciar Funcionários</a>
                <a href="gerenciar_cursos.php" class="sidebar-link active">Gerenciar Cursos</a>
                <a href="gerenciar_entidades.php" class="sidebar-link">Gerenciar Entidades</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Gerenciar Cursos</h2>
                <p>Edite detalhes, atualize descrições ou remova cursos do catálogo.</p>
            </header>

            <section class="auth-messages" style="max-width: 1000px;">
                <?php if(isset($_GET['erro'])): ?>
                    <article class="alert alert-error">
                        <?php 
                            if($_GET['erro'] == 'codigo_invalido') echo "O código de exclusão informado é inválido.";
                            elseif($_GET['erro'] == 'dados_invalidos') echo "Preencha todos os campos corretamente.";
                            elseif($_GET['erro'] == 'falha_banco') echo "Erro: Este curso possui alunos matriculados e não pode ser excluído.";
                            else echo "Ocorreu um erro na operação.";
                        ?>
                    </article>
                <?php endif; ?>

                <?php if(isset($_GET['sucesso'])): ?>
                    <article class="alert alert-success">
                        <?php 
                            if($_GET['sucesso'] == 'deletado') echo "Curso excluído permanentemente do catálogo.";
                            elseif($_GET['sucesso'] == 'editado') echo "Informações do curso atualizadas com sucesso.";
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
                                <th>Título do Curso</th>
                                <th>Entidade</th>
                                <th>Carga Horária</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td><?php echo $curso['id']; ?></td>
                                <td><?php echo htmlspecialchars($curso['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($curso['entidade']); ?></td>
                                <td><?php echo htmlspecialchars($curso['carga_horaria']); ?></td>
                                <td>
                                    <button class="btn-edit btn-edit-trigger" data-type="curso" 
                                        data-id="<?php echo $curso['id']; ?>" 
                                        data-titulo="<?php echo htmlspecialchars($curso['titulo']); ?>" 
                                        data-entidade="<?php echo $curso['entidade_id']; ?>"
                                        data-descricao="<?php echo htmlspecialchars($curso['descricao']); ?>"
                                        data-imagem="<?php echo htmlspecialchars($curso['imagem_capa']); ?>">Editar</button>
                                    <button class="btn-danger btn-delete-trigger" data-type="curso" data-id="<?php echo $curso['id']; ?>">Deletar</button>
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

    <section id="edit-modal-curso" class="modal-hidden">
        <article class="login-card modal-large">
            <header class="card-header">
                <h2>Editar Curso</h2>
                <button id="btn-close-edit-curso" class="close-btn-modal">×</button>
            </header>
            <form action="../controller/processa_admin.php" method="POST">
                <input type="hidden" name="acao" value="editar_curso">
                <input type="hidden" name="id" id="edit-curso-id">
                
                <fieldset class="form-row">
                    <fieldset class="input-group">
                        <label for="edit-curso-titulo">Título do Curso</label>
                        <input type="text" id="edit-curso-titulo" name="titulo" required>
                    </fieldset>

                    <fieldset class="input-group">
                        <label for="edit-curso-entidade">Entidade Responsável</label>
                        <select id="edit-curso-entidade" name="entidade_id" class="custom-select" required>
                            <?php foreach ($entidades as $ent): ?>
                            <option value="<?php echo $ent['id']; ?>"><?php echo htmlspecialchars($ent['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                </fieldset>

                <fieldset class="input-group">
                    <label for="edit-curso-descricao">Descrição Completa</label>
                    <textarea id="edit-curso-descricao" name="descricao" rows="4" required></textarea>
                </fieldset>

                <fieldset class="input-group">
                    <label for="edit-curso-imagem">URL da Imagem de Capa</label>
                    <input type="url" id="edit-curso-imagem" name="imagem_capa" required>
                </fieldset>

                <button type="submit" class="btn-solid-edit w-100 btn-solid mt-1">Salvar Alterações</button>
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