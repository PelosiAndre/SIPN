<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPN - Educação Gratuita de Qualidade</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <header id="main-header">
        <nav class="container-nav">
            <figure class="logo-area">
                <img src="assets/img/logo.png" alt="Logo SIPN" class="logo-img">
            </figure>
            <section class="nav-actions">
                <a href="view/verificar_certificados.php" class="btn-outline">Verificar Certificado</a>
                <button id="btn-login-trigger" class="btn-outline">Acessar</button>
            </section>
        </nav>
    </header>

    <section id="login-modal" class="modal-hidden">
        <article class="login-card">
            <header class="card-header">
                <h2>Entrar na Plataforma</h2>
                <button id="btn-close-modal">×</button>
            </header>
            <nav class="login-options">
                <a href="view/login_aluno.php" class="btn-option">Portal do Aluno</a>
                <a href="view/login_funcionario.php" class="btn-option">Portal do Funcionário</a>
            </nav>
        </article>
    </section>

    <main>
        <section class="hero-section">
            <article class="hero-content">
                <h1>O conhecimento que transforma o seu futuro.</h1>
                <p>Aprenda novas habilidades em diversas áreas com cursos práticos e gratuitos, desenhados para o mercado real.</p>
                <button id="btn-hero-login" class="btn-solid">Começar Agora</button>
            </article>
        </section>

        <section class="features-split">
            <figure class="feature-image">
                <img src="assets/img/learning-path.png" alt="Pessoas de diversas áreas estudando">
            </figure>
            <article class="feature-text">
                <header>
                    <h2>Sua trilha, do seu jeito</h2>
                </header>
                <p>Nossa plataforma oferece flexibilidade total para você transitar entre diferentes áreas do conhecimento. Seja em Negócios, Saúde, Tecnologia ou Artes, você dita o ritmo do seu aprendizado.</p>
                <ul class="feature-list">
                    <li>Conteúdo multidisciplinar atualizado</li>
                    <li>Projetos práticos para seu portfólio</li>
                    <li>Sem taxas ou assinaturas escondidas</li>
                </ul>
                <footer class="feature-footer">
                    <button id="btn-feature-login" class="btn-solid">Explorar Áreas</button>
                </footer>
            </article>
        </section>

        <section class="final-cta">
            <article class="cta-content">
                <h2>Pronto para evoluir?</h2>
                <p>Junte-se a milhares de alunos que já estão construindo novas carreiras hoje mesmo.</p>
                <button id="btn-footer-login" class="btn-solid-white">Criar Conta Gratuita</button>
            </article>
        </section>
    </main>

    <footer class="site-footer">
        <section class="footer-container">
            <article class="footer-info">
                <img src="assets/img/logo.png" alt="Logo SIPN" class="footer-logo">
                <p>Sistema Integrado de Profissionalização e Negócios.</p>
            </article>
            <nav class="footer-nav">
                <a href="#">Diretrizes</a>
                <a href="#">Privacidade</a>
                <a href="view/verificar_certificados.php">Verificar Certificado</a>
                <a href="#">Contato</a>
            </nav>
        </section>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>