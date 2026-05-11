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

$nome_aluno = "Estudante SIPN"; 

$certificados = [
    [
        'id_certificado' => 'CERT-2026-A001',
        'curso_titulo' => 'Lógica de Programação e Algoritmos',
        'entidade' => 'Fatec',
        'data_conclusao' => '15/04/2026',
        'carga_horaria' => '40h'
    ],
    [
        'id_certificado' => 'CERT-2026-B042',
        'curso_titulo' => 'Introdução ao Marketing Digital',
        'entidade' => 'Sebrae',
        'data_conclusao' => '02/05/2026',
        'carga_horaria' => '20h'
    ],
    [
        'id_certificado' => 'CERT-2026-C109',
        'curso_titulo' => 'Gestão Financeira para Iniciantes',
        'entidade' => 'Instituto Tecnológico',
        'data_conclusao' => '10/05/2026',
        'carga_horaria' => '15h'
    ]
];
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
                <article class="cert-card" id="card-<?php echo $cert['id_certificado']; ?>">
                    <header class="cert-header">
                        <span class="cert-icon">🏆</span>
                        <span class="cert-entity"><?php echo htmlspecialchars($cert['entidade']); ?></span>
                    </header>
                    <section class="cert-body">
                        <p class="cert-student">Certificamos que <br><strong><?php echo htmlspecialchars($nome_aluno); ?></strong></p>
                        <p class="cert-text">concluiu com êxito o curso completo de</p>
                        <h3 class="cert-course"><?php echo htmlspecialchars($cert['curso_titulo']); ?></h3>
                        
                        <div class="cert-details">
                            <div class="detail-item">
                                <span class="detail-label">Carga Horária</span>
                                <span class="detail-value"><?php echo $cert['carga_horaria']; ?></span>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-item">
                                <span class="detail-label">Data de Conclusão</span>
                                <span class="detail-value"><?php echo $cert['data_conclusao']; ?></span>
                            </div>
                        </div>
                        <p class="cert-code">Código de Autenticação: <?php echo htmlspecialchars($cert['id_certificado']); ?></p>
                    </section>
                    <footer class="cert-footer">
                        <button class="btn-solid w-100 btn-download-cert" data-target="card-<?php echo $cert['id_certificado']; ?>">Baixar PDF</button>
                    </footer>
                </article>
                <?php endforeach; ?>
            </section>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>