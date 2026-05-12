<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'cadastrar_entidade':
            $nome = $_POST['nome'] ?? '';
            $cnpj = $_POST['cnpj'] ?? '';
            $email = $_POST['email'] ?? '';
            
            echo "<script>alert('Sucesso: A entidade " . htmlspecialchars($nome) . " foi registrada.'); window.location.href = '../view/gerenciar_entidades.php';</script>";
            exit();
            break;

        case 'cadastrar_curso':
            $titulo = $_POST['titulo'] ?? '';
            $entidade_id = $_POST['entidade_id'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $carga = $_POST['carga_horaria'] ?? '';
            $imagem = $_POST['imagem_capa'] ?? '';
            
            $aulas_titulo = $_POST['aula_titulo'] ?? [];
            $aulas_duracao = $_POST['aula_duracao'] ?? [];
            $aulas_video = $_POST['aula_video'] ?? [];
            
            $quantidadeAulas = count($aulas_titulo);

            echo "<script>alert('Sucesso: O curso " . htmlspecialchars($titulo) . " foi publicado com " . $quantidadeAulas . " aulas cadastradas.'); window.location.href = '../view/gerenciar_cursos.php';</script>";
            exit();
            break;

        case 'deletar_item':
            $codigo = $_POST['codigo_auth'] ?? '';
            $tipo = $_POST['tipo_item'] ?? '';
            $id = $_POST['id_item'] ?? '';
            
            $codigoCorreto = 'DEL2026';

            if ($codigo === $codigoCorreto) {
                echo "<script>alert('Registro removido com sucesso.'); window.history.back();</script>";
            } else {
                echo "<script>alert('Código de autorização inválido.'); window.history.back();</script>";
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