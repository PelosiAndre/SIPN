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

$cursos = [
    ['id' => 1, 'titulo' => 'Desenvolvimento Back-End com PHP', 'entidade_id' => 1, 'entidade' => 'Fatec', 'descricao' => 'Aprenda a criar aplicações robustas utilizando PHP.', 'carga_horaria' => '60h', 'imagem_capa' => 'https://exemplo.com/php.png'],
    ['id' => 2, 'titulo' => 'Marketing Digital na Prática', 'entidade_id' => 2, 'entidade' => 'Sebrae', 'descricao' => 'Estratégias completas de marketing digital.', 'carga_horaria' => '40h', 'imagem_capa' => 'https://exemplo.com/mkt.png'],
    ['id' => 3, 'titulo' => 'Gestão Ágil de Projetos', 'entidade_id' => 3, 'entidade' => 'Instituto Tecnológico', 'descricao' => 'Implemente metodologias ágeis na sua equipe.', 'carga_horaria' => '20h', 'imagem_capa' => 'https://exemplo.com/agil.png']
];

$entidades = [
    ['id' => 1, 'nome' => 'Fatec'],
    ['id' => 2, 'nome' => 'Sebrae'],
    ['id' => 3, 'nome' => 'Instituto Tecnológico']
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos - SIPN</title>
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
                <a href="painel_funcionario.php" class="sidebar-link">Início</a>
                <a href="cadastrar_curso.php" class="sidebar-link">Cadastrar Curso</a>
                <a href="cadastrar_entidade.php" class="sidebar-link">Cadastrar Entidade</a>
                <a href="gerenciar_alunos.php" class="sidebar-link">Gerenciar Alunos</a>
                <a href="gerenciar_funcionarios.php" class="sidebar-link">Gerenciar Funcionários</a>
                <a href="gerenciar_cursos.php" class="sidebar-link active">Gerenciar Cursos</a>
                <a href="gerenciar_entidades.php" class="sidebar-link">Gerenciar Entidades</a>
            </nav>
            <footer class="sidebar-footer">
                <a href="../controller/logout.php" class="btn-logout">Sair da Conta</a>
            </footer>
        </aside>

        <main class="dashboard-content">
            <header class="content-header">
                <h2>Gerenciar Cursos</h2>
                <p>Edite detalhes, atualize descrições, modifique aulas ou remova cursos do catálogo.</p>
            </header>

            <section class="course-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título do Curso</th>
                                <th>Entidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td><?php echo $curso['id']; ?></td>
                                <td><?php echo htmlspecialchars($curso['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($curso['entidade']); ?></td>
                                <td>
                                    <button class="btn-edit btn-edit-trigger" data-type="curso" 
                                        data-id="<?php echo $curso['id']; ?>" 
                                        data-titulo="<?php echo htmlspecialchars($curso['titulo']); ?>" 
                                        data-entidade="<?php echo $curso['entidade_id']; ?>"
                                        data-descricao="<?php echo htmlspecialchars($curso['descricao']); ?>"
                                        data-carga="<?php echo htmlspecialchars($curso['carga_horaria']); ?>"
                                        data-imagem="<?php echo htmlspecialchars($curso['imagem_capa']); ?>">Editar</button>
                                    <button class="btn-danger btn-delete-trigger" data-type="curso" data-id="<?php echo $curso['id']; ?>">Deletar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </section>

    <section id="delete-modal" class="modal-hidden">
        <article class="login-card">
            <header class="card-header">
                <h2>Confirmação de Exclusão</h2>
                <button id="btn-close-delete-modal" class="close-btn-modal">×</button>
            </header>
            <form action="../controller/processa_admin.php" method="POST">
                <input type="hidden" name="acao" value="deletar_item">
                <input type="hidden" name="tipo_item" id="delete-tipo">
                <input type="hidden" name="id_item" id="delete-id">
                
                <p style="margin-bottom: 1.5rem; color: #4a5568;">Esta ação é irreversível. Por favor, insira seu código de autorização especial para prosseguir.</p>
                
                <fieldset class="input-group">
                    <label for="codigo-auth">Código de Autorização (DEL2026)</label>
                    <input type="password" id="codigo-auth" name="codigo_auth" required>
                </fieldset>
                
                <button type="submit" class="btn-solid w-100" style="background: #e53e3e;">Confirmar Exclusão</button>
            </form>
        </article>
    </section>

    <section id="edit-modal-curso" class="modal-hidden">
        <article class="login-card" style="max-width: 900px; width: 95%; max-height: 90vh; overflow-y: auto;">
            <header class="card-header">
                <h2>Editar Curso</h2>
                <button id="btn-close-edit-curso" class="close-btn-modal">×</button>
            </header>
            <form action="../controller/processa_admin.php" method="POST">
                <input type="hidden" name="acao" value="editar_curso">
                <input type="hidden" name="id" id="edit-curso-id">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <fieldset class="input-group">
                        <label for="edit-curso-titulo">Título do Curso</label>
                        <input type="text" id="edit-curso-titulo" name="titulo" required>
                    </fieldset>

                    <fieldset class="input-group">
                        <label for="edit-curso-entidade">Entidade Responsável</label>
                        <select id="edit-curso-entidade" name="entidade_id" class="custom-select" required>
                            <?php foreach ($entidades as $ent): ?>
                            <option value="<?php echo $ent['id']; ?>"><?php echo htmlspecialchars($ent['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                </div>

                <fieldset class="input-group">
                    <label for="edit-curso-descricao">Descrição Completa</label>
                    <textarea id="edit-curso-descricao" name="descricao" rows="4" required></textarea>
                </fieldset>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <fieldset class="input-group">
                        <label for="edit-curso-carga">Carga Horária</label>
                        <input type="text" id="edit-curso-carga" name="carga_horaria" required>
                    </fieldset>

                    <fieldset class="input-group">
                        <label for="edit-curso-imagem">URL Imagem Capa</label>
                        <input type="url" id="edit-curso-imagem" name="imagem_capa" required>
                    </fieldset>
                </div>
                
                <div class="section-divider">
                    <h3>Aulas do Curso</h3>
                </div>

                <div id="edit-lessons-wrapper">
                    <div class="lesson-row">
                        <fieldset class="input-group">
                            <label>Título da Aula</label>
                            <input type="text" name="aula_titulo[]" value="Introdução ao Curso" required>
                        </fieldset>
                        <fieldset class="input-group">
                            <label>Duração</label>
                            <input type="text" name="aula_duracao[]" value="10:00" required>
                        </fieldset>
                        <fieldset class="input-group">
                            <label>URL do Vídeo</label>
                            <input type="url" name="aula_video[]" value="https://exemplo.com/v1" required>
                        </fieldset>
                        <button type="button" class="btn-remove-lesson" disabled>X</button>
                    </div>
                </div>

                <button type="button" id="btn-add-edit-lesson" class="btn-outline-small" style="margin-bottom: 2rem;">+ Adicionar Nova Aula</button>

                <button type="submit" class="btn-solid w-100" style="background: #3182ce;">Salvar Alterações</button>
            </form>
        </article>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>