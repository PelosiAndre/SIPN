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

$stmt = $pdo->query("SELECT id, nome FROM entidades ORDER BY nome ASC");
$entidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Curso - SIPN</title>
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
                <a href="cadastrar_curso.php" class="sidebar-link active">Cadastrar Curso</a>
                <a href="cadastrar_entidade.php" class="sidebar-link">Cadastrar Entidade</a>
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
                <h2>Cadastrar Novo Curso</h2>
                <p>Insira as informações gerais e os links do YouTube. O sistema calculará a carga horária e os títulos automaticamente.</p>
            </header>

            <section class="course-section">
                <article class="admin-form-card form-large">
                    <form action="../controller/processa_admin.php" method="POST">
                        <input type="hidden" name="acao" value="cadastrar_curso">
                        
                        <fieldset class="form-row">
                            <fieldset class="input-group">
                                <label for="titulo">Título do Curso</label>
                                <input type="text" id="titulo" name="titulo" required>
                            </fieldset>

                            <fieldset class="input-group">
                                <label for="entidade_id">Entidade Responsável</label>
                                <select id="entidade_id" name="entidade_id" class="custom-select" required>
                                    <option value="">Selecione uma instituição</option>
                                    <?php foreach ($entidades as $ent): ?>
                                    <option value="<?php echo $ent['id']; ?>"><?php echo htmlspecialchars($ent['nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                        </fieldset>

                        <fieldset class="input-group">
                            <label for="descricao">Descrição Completa</label>
                            <textarea id="descricao" name="descricao" rows="4" required></textarea>
                        </fieldset>

                        <fieldset class="input-group">
                            <label for="imagem_capa">URL da Imagem de Capa</label>
                            <input type="url" id="imagem_capa" name="imagem_capa" placeholder="https://exemplo.com/imagem.png" required>
                        </fieldset>

                        <header class="section-divider mt-1">
                            <h3>Aulas do Curso (Links do YouTube)</h3>
                        </header>

                        <section id="lessons-wrapper">
                            <article class="lesson-row-dynamic">
                                <fieldset class="input-group input-group-flex">
                                    <label>URL do Vídeo</label>
                                    <input type="url" name="aula_video[]" placeholder="https://www.youtube.com/watch?v=..." required>
                                </fieldset>
                                <button type="button" class="btn-remove-lesson btn-outline-small" disabled>X</button>
                            </article>
                        </section>

                        <button type="button" id="btn-add-lesson" class="btn-outline-small mb-2">+ Adicionar Nova Aula</button>

                        <menu class="form-actions mt-1">
                            <button type="submit" class="btn-solid w-100">Processar e Publicar Curso</button>
                            <a href="painel_funcionario.php" class="btn-outline w-100">Descartar</a>
                        </menu>
                    </form>
                </article>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>