<?php
session_start();

require_once 'conexao.php';

function obterDadosYoutube($url) {
    $videoId = '';
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
        $videoId = $match[1];
    }

    $titulo = "Aula do YouTube";
    $duracaoStr = "00:00";
    $segundosTotal = 0;

    if ($videoId) {
        $oembed_url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=" . $videoId . "&format=json";
        $oembed_data = @file_get_contents($oembed_url);
        if ($oembed_data) {
            $json = json_decode($oembed_data, true);
            if (isset($json['title'])) {
                $titulo = $json['title'];
            }
        }

        $html = @file_get_contents("https://www.youtube.com/watch?v=" . $videoId);
        if ($html && preg_match('/"approxDurationMs":"(\d+)"/', $html, $matches)) {
            $segundosTotal = round($matches[1] / 1000);
            if ($segundosTotal >= 3600) {
                $duracaoStr = gmdate("H:i:s", $segundosTotal);
            } else {
                $duracaoStr = gmdate("i:s", $segundosTotal);
            }
        }
    }

    return [
        'titulo' => $titulo,
        'duracao' => $duracaoStr,
        'segundos' => $segundosTotal,
        'video_url' => $url
    ];
}

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
            $imagem_capa = $_POST['imagem_capa'] ?? '';
            $aula_video = $_POST['aula_video'] ?? [];

            $dados_aulas = [];
            $total_segundos_curso = 0;

            foreach ($aula_video as $url) {
                if (!empty($url)) {
                    $info = obterDadosYoutube($url);
                    $dados_aulas[] = $info;
                    $total_segundos_curso += $info['segundos'];
                }
            }

            $horas = floor($total_segundos_curso / 3600);
            $minutos = floor(($total_segundos_curso % 3600) / 60);
            
            if ($horas > 0) {
                $carga_horaria = $horas . "h " . $minutos . "m";
            } else {
                $carga_horaria = $minutos . "m";
            }

            try {
                $pdo->beginTransaction();

                $sqlCurso = "INSERT INTO cursos (entidade_id, titulo, descricao, carga_horaria, imagem_capa) VALUES (?, ?, ?, ?, ?)";
                $stmtCurso = $pdo->prepare($sqlCurso);
                $stmtCurso->execute([$entidade_id, $titulo, $descricao, $carga_horaria, $imagem_capa]);
                
                $curso_id = $pdo->lastInsertId();

                if (!empty($dados_aulas)) {
                    $sqlAula = "INSERT INTO aulas (curso_id, titulo, duracao, video_url) VALUES (?, ?, ?, ?)";
                    $stmtAula = $pdo->prepare($sqlAula);

                    foreach ($dados_aulas as $aula) {
                        $stmtAula->execute([
                            $curso_id,
                            $aula['titulo'],
                            $aula['duracao'],
                            $aula['video_url']
                        ]);
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
            $codigo = trim($_POST['codigo_auth'] ?? '');
            $tipo_item = $_POST['tipo_item'] ?? '';
            $id_item = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);
            $codigoCorreto = $_SESSION['codigo_exclusao'] ?? '';

            if ($codigo === $codigoCorreto && !empty($codigoCorreto) && $id_item) {
                try {
                    if ($tipo_item === 'funcionario' || $tipo_item === 'aluno') {
                        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND tipo = ?");
                        $stmt->execute([$id_item, $tipo_item]);
                    } elseif ($tipo_item === 'entidade') {
                        $stmt = $pdo->prepare("DELETE FROM entidades WHERE id = ?");
                        $stmt->execute([$id_item]);
                    } elseif ($tipo_item === 'curso') {
                        $stmt = $pdo->prepare("DELETE FROM cursos WHERE id = ?");
                        $stmt->execute([$id_item]);
                    }
                    header("Location: ../view/gerenciar_" . $tipo_item . "s.php?sucesso=deletado");
                } catch (PDOException $e) {
                    header("Location: ../view/gerenciar_" . $tipo_item . "s.php?erro=falha_banco");
                }
            } else {
                $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../view/painel_funcionario.php';
                $separador = (strpos($redirect, '?') !== false) ? '&' : '?';
                header("Location: " . $redirect . $separador . "erro=codigo_invalido");
            }
            exit();
            break;

        case 'editar_aluno':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nome = trim($_POST['nome'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if ($id && $nome && $email) {
                try {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ? AND tipo = 'aluno'");
                    $stmt->execute([$nome, $email, $id]);
                    header("Location: ../view/gerenciar_alunos.php?sucesso=editado");
                } catch (PDOException $e) {
                    header("Location: ../view/gerenciar_alunos.php?erro=email_existente");
                }
            } else {
                header("Location: ../view/gerenciar_alunos.php?erro=dados_invalidos");
            }
            exit();
            break;

        case 'editar_funcionario':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nome = trim($_POST['nome'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if ($id && $nome && $email) {
                try {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ? AND tipo = 'funcionario'");
                    $stmt->execute([$nome, $email, $id]);
                    header("Location: ../view/gerenciar_funcionarios.php?sucesso=editado");
                } catch (PDOException $e) {
                    header("Location: ../view/gerenciar_funcionarios.php?erro=email_existente");
                }
            } else {
                header("Location: ../view/gerenciar_funcionarios.php?erro=dados_invalidos");
            }
            exit();
            break;

        case 'editar_entidade':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nome = trim($_POST['nome'] ?? '');
            $cnpj = trim($_POST['cnpj'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if ($id && $nome && $cnpj && $email) {
                try {
                    $stmt = $pdo->prepare("UPDATE entidades SET nome = ?, cnpj = ?, email_comercial = ? WHERE id = ?");
                    $stmt->execute([$nome, $cnpj, $email, $id]);
                    header("Location: ../view/gerenciar_entidades.php?sucesso=editado");
                } catch (PDOException $e) {
                    header("Location: ../view/gerenciar_entidades.php?erro=falha_banco");
                }
            } else {
                header("Location: ../view/gerenciar_entidades.php?erro=dados_invalidos");
            }
            exit();
            break;

        case 'editar_curso':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $titulo = trim($_POST['titulo'] ?? '');
            $entidade_id = filter_input(INPUT_POST, 'entidade_id', FILTER_VALIDATE_INT);
            $descricao = trim($_POST['descricao'] ?? '');
            $imagem_capa = filter_input(INPUT_POST, 'imagem_capa', FILTER_SANITIZE_URL);

            if ($id && $titulo && $entidade_id && $descricao && $imagem_capa) {
                try {
                    $stmt = $pdo->prepare("UPDATE cursos SET titulo = ?, entidade_id = ?, descricao = ?, imagem_capa = ? WHERE id = ?");
                    $stmt->execute([$titulo, $entidade_id, $descricao, $imagem_capa, $id]);
                    header("Location: ../view/gerenciar_cursos.php?sucesso=editado");
                } catch (PDOException $e) {
                    header("Location: ../view/gerenciar_cursos.php?erro=falha_banco");
                }
            } else {
                header("Location: ../view/gerenciar_cursos.php?erro=dados_invalidos");
            }
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