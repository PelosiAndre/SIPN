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
$curso_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$curso_id || $aluno_id === 0) {
    header("Location: painel_aluno.php");
    exit();
}

$stmtCurso = $pdo->prepare("
    SELECT c.titulo, c.descricao, c.carga_horaria, e.nome AS entidade 
    FROM cursos c 
    JOIN entidades e ON c.entidade_id = e.id 
    WHERE c.id = ?
");
$stmtCurso->execute([$curso_id]);
$curso_db = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$curso_db) {
    header("Location: painel_aluno.php");
    exit();
}

$stmtMatricula = $pdo->prepare("SELECT id, status FROM matriculas WHERE aluno_id = ? AND curso_id = ?");
$stmtMatricula->execute([$aluno_id, $curso_id]);
$matricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);

if (!$matricula) {
    $stmtNovaMatricula = $pdo->prepare("INSERT INTO matriculas (aluno_id, curso_id) VALUES (?, ?)");
    $stmtNovaMatricula->execute([$aluno_id, $curso_id]);
    $matricula_id = $pdo->lastInsertId();
    $status_matricula = 'em_andamento';
} else {
    $matricula_id = $matricula['id'];
    $status_matricula = $matricula['status'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aula_id'])) {
    $aula_concluir = filter_input(INPUT_POST, 'aula_id', FILTER_VALIDATE_INT);
    
    if ($aula_concluir) {
        $stmtVerificaProgresso = $pdo->prepare("SELECT id FROM progresso_aulas WHERE matricula_id = ? AND aula_id = ?");
        $stmtVerificaProgresso->execute([$matricula_id, $aula_concluir]);
        
        if (!$stmtVerificaProgresso->fetch()) {
            $stmtConcluir = $pdo->prepare("INSERT INTO progresso_aulas (matricula_id, aula_id) VALUES (?, ?)");
            $stmtConcluir->execute([$matricula_id, $aula_concluir]);

            $stmtTotalAulas = $pdo->prepare("SELECT COUNT(*) FROM aulas WHERE curso_id = ?");
            $stmtTotalAulas->execute([$curso_id]);
            $total_aulas = $stmtTotalAulas->fetchColumn();

            $stmtAulasConcluidas = $pdo->prepare("SELECT COUNT(*) FROM progresso_aulas WHERE matricula_id = ?");
            $stmtAulasConcluidas->execute([$matricula_id]);
            $aulas_concluidas = $stmtAulasConcluidas->fetchColumn();

            if ($aulas_concluidas >= $total_aulas && $status_matricula !== 'concluido') {
                $stmtAtualizaMatricula = $pdo->prepare("UPDATE matriculas SET status = 'concluido' WHERE id = ?");
                $stmtAtualizaMatricula->execute([$matricula_id]);

                $codigo_cert = 'CERT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
                
                $stmtCertificado = $pdo->prepare("
                    INSERT INTO certificados (codigo_autenticacao, aluno_id, curso_id, curso_titulo_snapshot, entidade_nome_snapshot, carga_horaria_snapshot) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmtCertificado->execute([
                    $codigo_cert, 
                    $aluno_id, 
                    $curso_id, 
                    $curso_db['titulo'], 
                    $curso_db['entidade'], 
                    $curso_db['carga_horaria']
                ]);
            }
        }
    }
    
    header("Location: curso.php?id=" . $curso_id . "&aula=" . $aula_concluir);
    exit();
}

$stmtAulas = $pdo->prepare("SELECT id, titulo, duracao, video_url FROM aulas WHERE curso_id = ? ORDER BY id ASC");
$stmtAulas->execute([$curso_id]);
$aulas = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);

$stmtProgressoList = $pdo->prepare("SELECT aula_id FROM progresso_aulas WHERE matricula_id = ?");
$stmtProgressoList->execute([$matricula_id]);
$aulas_vistas = $stmtProgressoList->fetchAll(PDO::FETCH_COLUMN);

$aula_atual = null;
$aula_solicitada = filter_input(INPUT_GET, 'aula', FILTER_VALIDATE_INT);

foreach ($aulas as $aula) {
    if ($aula_solicitada && $aula['id'] == $aula_solicitada) {
        $aula_atual = $aula;
        break;
    }
    if (!$aula_atual && !in_array($aula['id'], $aulas_vistas)) {
        $aula_atual = $aula;
    }
}

if (!$aula_atual && count($aulas) > 0) {
    $aula_atual = $aulas[0];
}

$status_matricula_atual = 'em_andamento';
$stmtStatusFinal = $pdo->prepare("SELECT status FROM matriculas WHERE id = ?");
$stmtStatusFinal->execute([$matricula_id]);
$statusFinalData = $stmtStatusFinal->fetch(PDO::FETCH_ASSOC);
if ($statusFinalData) {
    $status_matricula_atual = $statusFinalData['status'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso_db['titulo']); ?> - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/course.css">
    <script src="https://www.youtube.com/iframe_api"></script>
</head>
<body class="course-viewer-body">
    <header class="course-header">
        <nav class="course-nav container-nav">
            <section class="nav-left">
                <a href="painel_aluno.php" class="back-link">← Voltar ao Painel</a>
                <h1><?php echo htmlspecialchars($curso_db['titulo']); ?></h1>
            </section>
            <figure class="logo-area">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="sidebar-logo">
            </figure>
        </nav>
    </header>

    <main class="course-layout">
        <section class="player-section">
            <?php if ($status_matricula_atual === 'concluido'): ?>
                <article class="alert alert-success">
                    Parabéns! Você concluiu este curso. <a href="certificados.php" class="cert-link-success">Acesse seu certificado aqui</a>.
                </article>
            <?php endif; ?>

            <?php if ($aula_atual): ?>
                <?php
                $url_video = $aula_atual['video_url'];
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url_video, $match)) {
                    $url_video = 'https://www.youtube.com/embed/' . $match[1] . '?enablejsapi=1';
                }
                ?>
                <article class="video-container">
                    <iframe 
                        id="lesson-player"
                        src="<?php echo htmlspecialchars($url_video); ?>" 
                        class="video-iframe" 
                        allowfullscreen>
                    </iframe>
                </article>
                <article class="lesson-info">
                    <header>
                        <span class="entity-tag"><?php echo htmlspecialchars($curso_db['entidade']); ?></span>
                        <h2>Aula atual: <?php echo htmlspecialchars($aula_atual['titulo']); ?></h2>
                    </header>
                    <p><?php echo htmlspecialchars($curso_db['descricao']); ?></p>

                    <?php if (!in_array($aula_atual['id'], $aulas_vistas)): ?>
                        <form action="curso.php?id=<?php echo $curso_id; ?>" method="POST" class="lesson-action-form">
                            <input type="hidden" name="aula_id" value="<?php echo $aula_atual['id']; ?>">
                        </form>
                    <?php else: ?>
                        <p class="lesson-completed-msg">✓ Você já concluiu esta aula.</p>
                    <?php endif; ?>
                </article>
            <?php else: ?>
                <article class="lesson-info">
                    <header>
                        <h2>Este curso ainda não possui aulas cadastradas.</h2>
                    </header>
                </article>
            <?php endif; ?>
        </section>

        <aside class="playlist-section">
            <header class="playlist-header">
                <h3>Conteúdo do Curso</h3>
                <span><?php echo count($aulas); ?> aulas</span>
            </header>
            <nav class="lesson-list">
                <?php foreach ($aulas as $aula): ?>
                <?php 
                    $visto = in_array($aula['id'], $aulas_vistas);
                    $classe_ativa = ($aula_atual && $aula_atual['id'] == $aula['id']) ? 'active' : '';
                    $classe_concluida = $visto ? 'completed' : '';
                ?>
                <a href="curso.php?id=<?php echo $curso_id; ?>&aula=<?php echo $aula['id']; ?>" class="lesson-item <?php echo $classe_concluida; ?> <?php echo $classe_ativa; ?>">
                    <section class="lesson-status">
                        <?php echo $visto ? '✓' : '○'; ?>
                    </section>
                    <section class="lesson-details">
                        <span class="lesson-title"><?php echo htmlspecialchars($aula['titulo']); ?></span>
                        <span class="lesson-time"><?php echo htmlspecialchars($aula['duracao']); ?></span>
                    </section>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>
    </main>

    <script src="../assets/js/script.js"></script>
</body>
</html>