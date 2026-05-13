<?php
session_start();

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    if (!isset($_COOKIE['aluno_logado']) || $_COOKIE['aluno_logado'] !== 'true') {
        header("Location: login_aluno.php");
        exit();
    } else {
        $_SESSION['usuario_tipo'] = 'aluno';
    }
}

require_once '../controller/conexao.php';

$aluno_id = $_SESSION['usuario_id'] ?? 0;

$stmtNome = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmtNome->execute([$aluno_id]);
$usuarioInfo = $stmtNome->fetch(PDO::FETCH_ASSOC);

$primeiro_nome = "Estudante";
if ($usuarioInfo && !empty($usuarioInfo['nome'])) {
    $partes_nome = explode(' ', $usuarioInfo['nome']);
    $primeiro_nome = htmlspecialchars($partes_nome[0]);
}

$sqlCursos = "
    SELECT c.id, c.titulo, c.imagem_capa, e.nome AS entidade, m.id AS matricula_id, m.status
    FROM matriculas m
    JOIN cursos c ON m.curso_id = c.id
    JOIN entidades e ON c.entidade_id = e.id
    WHERE m.aluno_id = ?
";
$stmtCursos = $pdo->prepare($sqlCursos);
$stmtCursos->execute([$aluno_id]);
$cursos_usuario = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

$cursos_andamento = [];
$cursos_concluidos = [];

foreach ($cursos_usuario as $curso) {
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM aulas WHERE curso_id = ?");
    $stmtTotal->execute([$curso['id']]);
    $total_aulas = $stmtTotal->fetchColumn();

    $stmtConcluidas = $pdo->prepare("SELECT COUNT(*) FROM progresso_aulas WHERE matricula_id = ?");
    $stmtConcluidas->execute([$curso['matricula_id']]);
    $aulas_concluidas = $stmtConcluidas->fetchColumn();

    $progresso = 0;
    if ($total_aulas > 0) {
        $progresso = round(($aulas_concluidas / $total_aulas) * 100);
    }

    $curso['progresso'] = $progresso;

    if ($curso['status'] === 'concluido') {
        $cursos_concluidos[] = $curso;
    } else {
        $cursos_andamento[] = $curso;
    }
}

$sqlCatalogo = "
    SELECT c.id, c.titulo, c.imagem_capa, e.nome AS entidade
    FROM cursos c
    JOIN entidades e ON c.entidade_id = e.id
    WHERE c.id NOT IN (SELECT curso_id FROM matriculas WHERE aluno_id = ?)
";
$stmtCatalogo = $pdo->prepare($sqlCatalogo);
$stmtCatalogo->execute([$aluno_id]);
$catalogo_cursos = $stmtCatalogo->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno - SIPN</title>
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
                <a href="painel_aluno.php#meus-cursos" class="sidebar-link active">Meus Cursos</a>
                <a href="painel_aluno.php#catalogo" class="sidebar-link">Catálogo de Cursos</a>
                <a href="certificados.php" class="sidebar-link">Meus Certificados</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Olá, <?php echo $primeiro_nome; ?>!</h2>
                <p>Pronto para aprender algo novo hoje?</p>
            </header>

            <section id="meus-cursos" class="course-section">
                <header class="section-header">
                    <h2>Meus Cursos em Andamento</h2>
                </header>
                <article class="course-grid">
                    <?php foreach ($cursos_andamento as $curso): ?>
                    <article class="course-card">
                        <figure class="course-thumb placeholder-bg">
                            <?php if (!empty($curso['imagem_capa']) && $curso['imagem_capa'] !== 'default_curso.png'): ?>
                                <img src="<?php echo htmlspecialchars($curso['imagem_capa']); ?>" alt="Capa do curso" class="course-card-img">
                            <?php endif; ?>
                        </figure>
                        <section class="course-info">
                            <span class="course-entity"><?php echo htmlspecialchars($curso['entidade']); ?></span>
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <section class="progress-container">
                                <header class="progress-labels">
                                    <span>Progresso</span>
                                    <span><?php echo $curso['progresso']; ?>%</span>
                                </header>
                                <progress value="<?php echo $curso['progresso']; ?>" max="100"><?php echo $curso['progresso']; ?>%</progress>
                            </section>
                        </section>
                        <footer class="course-action">
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="btn-solid w-100 d-block text-center">Continuar Aula</a>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                    
                    <?php if (empty($cursos_andamento)): ?>
                        <article class="empty-state">
                            <p>Você não possui cursos em andamento no momento.</p>
                        </article>
                    <?php endif; ?>
                </article>
            </section>

            <?php if (!empty($cursos_concluidos)): ?>
            <section id="concluidos" class="course-section">
                <header class="section-header">
                    <h2>Cursos Concluídos</h2>
                </header>
                <article class="course-grid">
                    <?php foreach ($cursos_concluidos as $curso): ?>
                    <article class="course-card">
                        <figure class="course-thumb placeholder-bg">
                            <?php if (!empty($curso['imagem_capa']) && $curso['imagem_capa'] !== 'default_curso.png'): ?>
                                <img src="<?php echo htmlspecialchars($curso['imagem_capa']); ?>" alt="Capa do curso" class="course-card-img">
                            <?php endif; ?>
                        </figure>
                        <section class="course-info">
                            <header class="progress-labels">
                                <span class="course-entity"><?php echo htmlspecialchars($curso['entidade']); ?></span>
                                <span class="badge-concluido">Concluído</span>
                            </header>
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        </section>
                        <footer class="course-action">
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="btn-outline w-100 d-block text-center">Rever Aulas</a>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                </article>
            </section>
            <?php endif; ?>

            <section id="catalogo" class="course-section">
                <header class="section-header catalog-header">
                    <h2>Catálogo de Cursos</h2>
                    <section class="search-bar">
                        <input type="text" id="search-catalog" placeholder="Buscar cursos">
                        <button type="button" class="btn-search">🔍</button>
                    </section>
                </header>
                <article class="course-grid" id="catalog-grid">
                    <?php foreach ($catalogo_cursos as $curso): ?>
                    <article class="course-card catalog-item">
                        <figure class="course-thumb placeholder-bg">
                            <?php if (!empty($curso['imagem_capa']) && $curso['imagem_capa'] !== 'default_curso.png'): ?>
                                <img src="<?php echo htmlspecialchars($curso['imagem_capa']); ?>" alt="Capa do curso" class="course-card-img">
                            <?php endif; ?>
                        </figure>
                        <section class="course-info">
                            <span class="course-entity"><?php echo htmlspecialchars($curso['entidade']); ?></span>
                            <h3 class="course-title"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        </section>
                        <footer class="course-action">
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="btn-outline w-100 d-block text-center">Acessar Curso</a>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                </article>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>