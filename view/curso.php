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

$id_curso = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$curso = [
    'id' => $id_curso,
    'titulo' => 'Desenvolvimento Back-End com PHP',
    'entidade' => 'Fatec',
    'descricao' => 'Aprenda a criar aplicações robustas utilizando PHP, desde a lógica básica até a integração com banco de dados MySQL.',
    'aulas' => [
        ['id' => 101, 'titulo' => 'Introdução ao PHP e Ambiente', 'duracao' => '15:00', 'visto' => true],
        ['id' => 102, 'titulo' => 'Variáveis e Tipos de Dados', 'duracao' => '22:30', 'visto' => true],
        ['id' => 103, 'titulo' => 'Estruturas de Controle', 'duracao' => '18:45', 'visto' => false],
        ['id' => 104, 'titulo' => 'Funções e Reuso de Código', 'duracao' => '25:00', 'visto' => false],
        ['id' => 105, 'titulo' => 'Manipulando Bancos de Dados', 'duracao' => '40:20', 'visto' => false]
    ]
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $curso['titulo']; ?> - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/course.css">
</head>
<body class="course-viewer-body">
    <header class="course-header">
        <nav class="course-nav container-nav">
            <section class="nav-left">
                <a href="painel_aluno.php" class="back-link">← Voltar ao Painel</a>
                <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
            </section>
            <figure class="logo-area">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="sidebar-logo">
            </figure>
        </nav>
    </header>

    <main class="course-layout">
        <section class="player-section">
            <article class="video-container">
                <figure class="video-placeholder">
                    <span class="play-icon">▶</span>
                    <p>O player de vídeo será carregado aqui</p>
                </figure>
            </article>
            <article class="lesson-info">
                <header>
                    <span class="entity-tag"><?php echo htmlspecialchars($curso['entidade']); ?></span>
                    <h2>Aula atual: Introdução ao PHP e Ambiente</h2>
                </header>
                <p><?php echo htmlspecialchars($curso['descricao']); ?></p>
            </article>
        </section>

        <aside class="playlist-section">
            <header class="playlist-header">
                <h3>Conteúdo do Curso</h3>
                <span><?php echo count($curso['aulas']); ?> aulas</span>
            </header>
            <nav class="lesson-list">
                <?php foreach ($curso['aulas'] as $aula): ?>
                <button class="lesson-item <?php echo $aula['visto'] ? 'completed' : ''; ?>" data-id="<?php echo $aula['id']; ?>">
                    <section class="lesson-status">
                        <?php echo $aula['visto'] ? '✓' : '○'; ?>
                    </section>
                    <section class="lesson-details">
                        <span class="lesson-title"><?php echo htmlspecialchars($aula['titulo']); ?></span>
                        <span class="lesson-time"><?php echo $aula['duracao']; ?></span>
                    </section>
                </button>
                <?php endforeach; ?>
            </nav>
        </aside>
    </main>

    <script src="../assets/js/script.js"></script>
</body>
</html>