<?php
session_start();
if (isset($_COOKIE['funcionario_logado']) && $_COOKIE['funcionario_logado'] === 'true') {
    header("Location: painel_funcionario.php");
    exit();
}
if (!isset($_SESSION['codigo_funcionario'])) {
    $_SESSION['codigo_funcionario'] = 'SIPN' . rand(1000, 9999);
}
$showReset = isset($_GET['redefinir']) ? true : false;
$emailReset = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Funcionário - SIPN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-bg">
    <header class="auth-header">
        <nav class="auth-nav">
            <a href="../index.php" class="back-link">← Voltar para o início</a>
            <figure class="logo-area">
                <img src="../assets/img/logo.png" alt="Logo SIPN" class="logo-img">
            </figure>
        </nav>
    </header>

    <main class="auth-container">
        <section class="auth-card">
            <header class="auth-toggle <?php echo $showReset ? 'hidden' : ''; ?>" id="auth-toggle-header">
                <button id="btn-tab-login" class="toggle-btn active">Entrar</button>
                <button id="btn-tab-register" class="toggle-btn">Cadastrar</button>
            </header>

            <article id="section-login" class="auth-section <?php echo $showReset ? 'hidden' : ''; ?>">
                <header class="section-title">
                    <h1>Portal do Funcionário</h1>
                    <p>Acesso restrito para gestão da plataforma.</p>
                </header>
                <form action="../controller/processa_auth.php" method="POST" class="auth-form">
                    <fieldset class="input-group">
                        <label for="email-login">E-mail Corporativo</label>
                        <input type="email" id="email-login" name="email" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="senha-login">Senha</label>
                        <input type="password" id="senha-login" name="senha" required>
                    </fieldset>
                    <fieldset class="options-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="lembrar_me"> Lembrar-me
                        </label>
                        <button type="button" id="btn-show-forgot" class="link-btn">Esqueceu a senha?</button>
                    </fieldset>
                    <button type="submit" name="acao" value="login_funcionario" class="btn-solid w-100">Acessar Sistema</button>
                </form>
            </article>

            <article id="section-forgot-request" class="auth-section hidden">
                <header class="section-title">
                    <h1>Solicitar Código</h1>
                    <p>Uso exclusivo para colaboradores.</p>
                </header>
                <form action="../controller/processa_auth.php" method="POST" class="auth-form">
                    <input type="hidden" name="acao" value="solicitar_codigo">
                    <input type="hidden" name="tipo_usuario" value="funcionario">
                    <fieldset class="input-group">
                        <label for="email-recovery">E-mail corporativo</label>
                        <input type="email" id="email-recovery" name="email" required>
                    </fieldset>
                    <button type="submit" class="btn-solid w-100">Enviar Código</button>
                    <button type="button" class="btn-back-login link-btn w-100">Voltar para o login</button>
                </form>
            </article>

            <article id="section-forgot-reset" class="auth-section <?php echo $showReset ? '' : 'hidden'; ?>">
                <header class="section-title">
                    <h1>Redefinir Senha</h1>
                    <p>Insira o código recebido e a sua nova senha.</p>
                </header>
                <form action="../controller/processa_auth.php" method="POST" class="auth-form">
                    <input type="hidden" name="acao" value="redefinir_senha">
                    <input type="hidden" name="tipo_usuario" value="funcionario">
                    <fieldset class="input-group">
                        <label for="email-reset">E-mail</label>
                        <input type="email" id="email-reset" name="email" value="<?php echo $emailReset; ?>" required readonly>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="codigo-reset">Código Recebido</label>
                        <input type="text" id="codigo-reset" name="codigo" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="senha-reset">Nova Senha</label>
                        <input type="password" id="senha-reset" name="senha" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="confirma-senha-reset">Confirmar Nova Senha</label>
                        <input type="password" id="confirma-senha-reset" name="confirma_senha" required>
                    </fieldset>
                    <button type="submit" class="btn-solid w-100">Redefinir Senha</button>
                    <a href="login_funcionario.php" class="btn-back-login link-btn w-100" style="display: block; text-align: center; margin-top: 15px;">Voltar para o login</a>
                </form>
            </article>

            <article id="section-register" class="auth-section hidden">
                <header class="section-title">
                    <h1>Cadastro de Funcionário</h1>
                    <p>Solicite seu acesso administrativo.</p>
                </header>
                <form id="form-register-func" action="../controller/processa_auth.php" method="POST" class="auth-form" data-codigo="<?php echo $_SESSION['codigo_funcionario']; ?>">
                    <input type="hidden" name="acao" value="cadastrar_funcionario">
                    <fieldset class="input-group">
                        <label for="nome-cad">Nome Completo</label>
                        <input type="text" id="nome-cad" name="nome" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="cpf-cad">CPF</label>
                        <input type="text" id="cpf-cad" name="cpf" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="email-cad">E-mail Corporativo</label>
                        <input type="email" id="email-cad" name="email" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="codigo-cad">Código de Autorização</label>
                        <input type="text" id="codigo-cad" name="codigo_acesso" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="senha-cad">Senha</label>
                        <input type="password" id="senha-cad" name="senha" required>
                    </fieldset>
                    <fieldset class="input-group">
                        <label for="confirma-senha-cad">Confirmar Senha</label>
                        <input type="password" id="confirma-senha-cad" name="confirma_senha" required>
                    </fieldset>
                    <button type="submit" class="btn-solid w-100">Solicitar Cadastro</button>
                </form>
            </article>
        </section>
    </main>

    <script src="../assets/js/script.js"></script>

    <?php if (isset($_SESSION['flash_log'])): ?>
    <script>
        console.log("<?php echo $_SESSION['flash_log']; ?>");
    </script>
    <?php unset($_SESSION['flash_log']); endif; ?>
</body>
</html>