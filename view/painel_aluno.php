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

$meus_cursos = [
    [
        'id' => 1,
        'titulo' => 'Desenvolvimento Back-End com PHP',
        'entidade' => 'Fatec',
        'progresso' => 65
    ],
    [
        'id' => 2,
        'titulo' => 'Design de Interfaces (UI/UX)',
        'entidade' => 'SIPN Design',
        'progresso' => 20
    ]
];

$catalogo_cursos = [
    [
        'id' => 3,
        'titulo' => 'Banco de Dados Avançado',
        'entidade' => 'Fatec'
    ],
    [
        'id' => 4,
        'titulo' => 'Marketing Digital na Prática',
        'entidade' => 'Sebrae'
    ],
    [
        'id' => 5,
        'titulo' => 'Gestão Ágil de Projetos',
        'entidade' => 'Instituto Tecnológico'
    ],
    [
        'id' => 6,
        'titulo' => 'Introdução à Inteligência Artificial',
        'entidade' => 'Tech Academy'
    ]
];
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
                <a href="#meus-cursos" class="sidebar-link active">Meus Cursos</a>
                <a href="#catalogo" class="sidebar-link">Catálogo de Cursos</a>
                <a href="#certificados" class="sidebar-link">Meus Certificados</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Olá, Estudante!</h2>
                <p>Pronto para aprender algo novo hoje?</p>
            </header>

            <section id="meus-cursos" class="course-section">
                <header class="section-header">
                    <h2>Meus Cursos em Andamento</h2>
                </header>
                <article class="course-grid">
                    <?php foreach ($meus_cursos as $curso): ?>
                    <article class="course-card">
                        <figure class="course-thumb placeholder-bg"></figure>
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
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="btn-solid w-100">Continuar Aula</a>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                </article>
            </section>

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
                        <figure class="course-thumb placeholder-bg"></figure>
                        <section class="course-info">
                            <span class="course-entity"><?php echo htmlspecialchars($curso['entidade']); ?></span>
                            <h3 class="course-title"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        </section>
                        <footer class="course-action">
                            <a href="matricula.php?id=<?php echo $curso['id']; ?>" class="btn-outline w-100" style="text-align: center;">Ver Detalhes</a>
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