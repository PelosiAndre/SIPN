<?php
require_once '../controller/conexao.php';

$codigo_pesquisa = filter_input(INPUT_GET, 'codigo', FILTER_SANITIZE_STRING);
$certificado = null;
$busca_realizada = false;
$erro = false;

if ($codigo_pesquisa) {
    $busca_realizada = true;
    $stmt = $pdo->prepare("
        SELECT c.codigo_autenticacao, 
               c.curso_titulo_snapshot AS curso_titulo, 
               c.entidade_nome_snapshot, 
               c.carga_horaria_snapshot, 
               DATE_FORMAT(c.data_emissao, '%d/%m/%Y') AS data_conclusao, 
               u.nome AS aluno_nome
        FROM certificados c
        JOIN usuarios u ON c.aluno_id = u.id
        WHERE c.codigo_autenticacao = ?
    ");
    $stmt->execute([trim($codigo_pesquisa)]);
    $certificado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$certificado) {
        $erro = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Certificado - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-body">
    <header id="main-header">
        <nav class="container-nav">
            <a href="../index.php" class="logo-area">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="logo-img">
            </a>
            <section class="nav-actions">
                <a href="../index.php" class="btn-outline">Voltar ao Início</a>
            </section>
        </nav>
    </header>

    <main class="verify-layout">
        <article class="login-card verify-card">
            <header class="verify-header">
                <span class="verify-shield">🛡️</span>
                <h2>Validação de Certificado</h2>
                <p>Insira o código de autenticação para verificar a validade de um certificado emitido pelo SIPN.</p>
            </header>

            <form action="verificar_certificados.php" method="GET" class="mb-2">
                <fieldset class="input-group">
                    <label for="codigo">Código do Certificado</label>
                    <input type="text" id="codigo" name="codigo" placeholder="Ex: CERT-2026-A1B2C3D4" value="<?php echo htmlspecialchars($codigo_pesquisa ?? ''); ?>" required autocomplete="off">
                </fieldset>
                
                <button type="submit" class="btn-solid w-100">Verificar Autenticidade</button>
            </form>

            <?php if ($busca_realizada): ?>
                <hr class="section-divider">

                <?php if ($erro): ?>
                    <article class="alert alert-error text-center">
                        <strong>Certificado Inválido ou Não Encontrado.</strong><br>
                        Verifique se o código foi digitado corretamente e tente novamente.
                    </article>
                <?php else: ?>
                    <article class="alert alert-success text-center mb-1">
                        <strong>✓ Certificado Autêntico</strong>
                    </article>

                    <section class="cert-data-box">
                        <h3 class="cert-data-title">Dados da Certificação</h3>
                        
                        <p class="cert-data-item">
                            <strong>Aluno(a):</strong> <?php echo htmlspecialchars($certificado['aluno_nome']); ?>
                        </p>
                        <p class="cert-data-item">
                            <strong>Curso:</strong> <?php echo htmlspecialchars($certificado['curso_titulo']); ?>
                        </p>
                        <p class="cert-data-item">
                            <strong>Instituição:</strong> <?php echo htmlspecialchars($certificado['entidade_nome_snapshot']); ?>
                        </p>
                        <p class="cert-data-item">
                            <strong>Carga Horária:</strong> <?php echo htmlspecialchars($certificado['carga_horaria_snapshot']); ?>
                        </p>
                        <p class="cert-data-item">
                            <strong>Data de Emissão:</strong> <?php echo htmlspecialchars($certificado['data_conclusao']); ?>
                        </p>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    </main>
</body>
</html>