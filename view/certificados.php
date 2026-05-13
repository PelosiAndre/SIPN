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

$usuario_id = $_SESSION['usuario_id'] ?? 0;

$sql = "SELECT c.codigo_autenticacao AS id_certificado,
               c.curso_titulo_snapshot AS curso_titulo,
               c.entidade_nome_snapshot AS entidade,
               c.carga_horaria_snapshot AS carga_horaria,
               DATE_FORMAT(c.data_emissao, '%d/%m/%Y') AS data_conclusao,
               u.nome AS nome_aluno
        FROM certificados c
        JOIN usuarios u ON c.aluno_id = u.id
        WHERE c.aluno_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$certificados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nome_aluno = "Estudante SIPN";
if (count($certificados) > 0) {
    $nome_aluno = $certificados[0]['nome_aluno'];
} elseif ($usuario_id > 0) {
    $stmt_user = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt_user->execute([$usuario_id]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
    if ($user_data) {
        $nome_aluno = $user_data['nome'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Certificados - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/certificados.css">
</head>
<body class="dashboard-body">
    <section class="dashboard-layout">
        <aside class="sidebar">
            <header class="sidebar-header">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="sidebar-logo">
            </header>
            <nav class="sidebar-nav">
                <a href="painel_aluno.php#meus-cursos" class="sidebar-link">Meus Cursos</a>
                <a href="painel_aluno.php#catalogo" class="sidebar-link">Catálogo de Cursos</a>
                <a href="certificados.php" class="sidebar-link active">Meus Certificados</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Meus Certificados</h2>
                <p>Suas conquistas e comprovações de aprendizado prontas para o mercado.</p>
            </header>

            <section class="cert-grid">
                <?php foreach ($certificados as $cert): ?>
                <article class="cert-card" id="card-<?php echo htmlspecialchars($cert['id_certificado']); ?>">
                    <header class="cert-header">
                        <span class="cert-icon">🏆</span>
                        <span class="cert-entity"><?php echo htmlspecialchars($cert['entidade']); ?></span>
                    </header>
                    <section class="cert-body">
                        <p class="cert-student">Certificamos que <br><strong><?php echo htmlspecialchars($nome_aluno); ?></strong></p>
                        <p class="cert-text">concluiu com êxito o curso completo de</p>
                        <h3 class="cert-course"><?php echo htmlspecialchars($cert['curso_titulo']); ?></h3>
                        
                        <section class="cert-details">
                            <article class="detail-item">
                                <span class="detail-label">Carga Horária</span>
                                <span class="detail-value"><?php echo htmlspecialchars($cert['carga_horaria']); ?></span>
                            </article>
                            <hr class="detail-divider">
                            <article class="detail-item">
                                <span class="detail-label">Data de Conclusão</span>
                                <span class="detail-value"><?php echo htmlspecialchars($cert['data_conclusao']); ?></span>
                            </article>
                        </section>
                        <p class="cert-code">Código de Autenticação: <?php echo htmlspecialchars($cert['id_certificado']); ?></p>
                    </section>
                    <footer class="cert-footer">
                        <button class="btn-solid w-100 btn-download-cert" data-target="card-<?php echo htmlspecialchars($cert['id_certificado']); ?>">Baixar PDF</button>
                    </footer>
                </article>
                <?php endforeach; ?>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>