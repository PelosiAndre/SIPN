<?php
session_start();

require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'cadastrar_entidade':
            $nome = $_POST['nome'] ?? '';
            $cnpj = $_POST['cnpj'] ?? '';
            $email = $_POST['email'] ?? '';

            try {
                $sql = "INSERT INTO entidades (nome, cnpj, email_comercial) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $cnpj, $email]);
                header("Location: ../view/gerenciar_entidades.php");
            } catch (PDOException $e) {
                header("Location: ../view/cadastrar_entidade.php");
            }
            exit();
            break;

        case 'cadastrar_curso':
            $titulo = $_POST['titulo'] ?? '';
            $entidade_id = $_POST['entidade_id'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $carga_horaria = $_POST['carga_horaria'] ?? '';
            $imagem_capa = $_POST['imagem_capa'] ?? '';

            $aula_titulo = $_POST['aula_titulo'] ?? [];
            $aula_duracao = $_POST['aula_duracao'] ?? [];
            $aula_video = $_POST['aula_video'] ?? [];

            try {
                $pdo->beginTransaction();

                $sqlCurso = "INSERT INTO cursos (entidade_id, titulo, descricao, carga_horaria, imagem_capa) VALUES (?, ?, ?, ?, ?)";
                $stmtCurso = $pdo->prepare($sqlCurso);
                $stmtCurso->execute([$entidade_id, $titulo, $descricao, $carga_horaria, $imagem_capa]);
                
                $curso_id = $pdo->lastInsertId();

                if (!empty($aula_titulo)) {
                    $sqlAula = "INSERT INTO aulas (curso_id, titulo, duracao, video_url) VALUES (?, ?, ?, ?)";
                    $stmtAula = $pdo->prepare($sqlAula);

                    for ($i = 0; $i < count($aula_titulo); $i++) {
                        if (!empty($aula_titulo[$i]) && !empty($aula_duracao[$i]) && !empty($aula_video[$i])) {
                            $stmtAula->execute([
                                $curso_id,
                                $aula_titulo[$i],
                                $aula_duracao[$i],
                                $aula_video[$i]
                            ]);
                        }
                    }
                }

                $pdo->commit();
                header("Location: ../view/gerenciar_cursos.php");
            } catch (PDOException $e) {
                $pdo->rollBack();
                header("Location: ../view/cadastrar_curso.php");
            }
            exit();
            break;

        case 'deletar_item':
            $codigo = $_POST['codigo_auth'] ?? '';
            $codigoCorreto = 'DEL2026';

            if ($codigo === $codigoCorreto) {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                } else {
                    header("Location: ../view/painel_funcionario.php");
                }
            } else {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                } else {
                    header("Location: ../view/painel_funcionario.php");
                }
            }
            exit();
            break;

        case 'editar_aluno':
            header("Location: ../view/gerenciar_alunos.php");
            exit();
            break;

        case 'editar_funcionario':
            header("Location: ../view/gerenciar_funcionarios.php");
            exit();
            break;

        case 'editar_curso':
            header("Location: ../view/gerenciar_cursos.php");
            exit();
            break;

        case 'editar_entidade':
            header("Location: ../view/gerenciar_entidades.php");
            exit();
            break;

        default:
            header("Location: ../index.php");
            exit();
            break;
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>