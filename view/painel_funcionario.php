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

$funcionario_id = $_SESSION['usuario_id'] ?? 0;

$stmtNome = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmtNome->execute([$funcionario_id]);
$usuarioInfo = $stmtNome->fetch(PDO::FETCH_ASSOC);

$primeiro_nome = "Administrador";
if ($usuarioInfo && !empty($usuarioInfo['nome'])) {
    $partes_nome = explode(' ', $usuarioInfo['nome']);
    $primeiro_nome = htmlspecialchars($partes_nome[0]);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Funcionário - SIPN</title>
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
                <a href="painel_funcionario.php" class="sidebar-link active">Início</a>
                <a href="cadastrar_curso.php" class="sidebar-link">Cadastrar Curso</a>
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
                <h2>Olá, <?php echo $primeiro_nome; ?>!</h2>
                <p>Selecione uma das opções abaixo para gerenciar a plataforma.</p>
            </header>

            <section class="course-section">
                <header class="section-header">
                    <h2>Novos Cadastros</h2>
                </header>
                <article class="action-grid">
                    <a href="cadastrar_curso.php" class="action-card">
                        <span class="action-icon">📚</span>
                        <h3>Cadastrar Curso</h3>
                        <p>Adicione novos cursos ao catálogo da plataforma.</p>
                    </a>
                    <a href="cadastrar_entidade.php" class="action-card">
                        <span class="action-icon">🏢</span>
                        <h3>Cadastrar Entidade</h3>
                        <p>Registre novas instituições parceiras de ensino.</p>
                    </a>
                </article>
            </section>

            <section class="course-section mt-4">
                <header class="section-header">
                    <h2>Gerenciamento de Dados</h2>
                </header>
                <article class="action-grid">
                    <a href="gerenciar_alunos.php" class="action-card">
                        <span class="action-icon">🎓</span>
                        <h3>Alunos</h3>
                        <p>Visualize, edite ou remova alunos do sistema.</p>
                    </a>
                    <a href="gerenciar_funcionarios.php" class="action-card">
                        <span class="action-icon">💼</span>
                        <h3>Funcionários</h3>
                        <p>Controle os acessos da equipe administrativa.</p>
                    </a>
                    <a href="gerenciar_cursos.php" class="action-card">
                        <span class="action-icon">📖</span>
                        <h3>Cursos</h3>
                        <p>Edite detalhes ou exclua cursos do catálogo.</p>
                    </a>
                    <a href="gerenciar_entidades.php" class="action-card">
                        <span class="action-icon">🏛️</span>
                        <h3>Entidades</h3>
                        <p>Gerencie as informações das instituições parceiras.</p>
                    </a>
                </article>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>