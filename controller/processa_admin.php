<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'cadastrar_entidade':
            $nome = $_POST['nome'] ?? '';
            echo "<script>alert('Sucesso: A entidade " . htmlspecialchars($nome) . " foi registrada.'); window.location.href = '../view/gerenciar_entidades.php';</script>";
            exit();
            break;

        case 'cadastrar_curso':
            $titulo = $_POST['titulo'] ?? '';
            echo "<script>alert('Sucesso: O curso " . htmlspecialchars($titulo) . " foi publicado no catálogo.'); window.location.href = '../view/gerenciar_cursos.php';</script>";
            exit();
            break;

        case 'deletar_item':
            $codigo = $_POST['codigo_auth'] ?? '';
            $codigoCorreto = 'DEL2026';

            if ($codigo === $codigoCorreto) {
                echo "<script>alert('Registro removido com sucesso.'); window.history.back();</script>";
            } else {
                echo "<script>alert('Código de autorização inválido.'); window.history.back();</script>";
            }
            exit();
            break;

        case 'editar_aluno':
            echo "<script>alert('Dados do aluno atualizados com sucesso!'); window.location.href = '../view/gerenciar_alunos.php';</script>";
            exit();
            break;

        case 'editar_funcionario':
            echo "<script>alert('Dados do funcionário atualizados com sucesso!'); window.location.href = '../view/gerenciar_funcionarios.php';</script>";
            exit();
            break;

        case 'editar_curso':
            echo "<script>alert('Informações do curso atualizadas com sucesso!'); window.location.href = '../view/gerenciar_cursos.php';</script>";
            exit();
            break;

        case 'editar_entidade':
            echo "<script>alert('Dados da entidade atualizados com sucesso!'); window.location.href = '../view/gerenciar_entidades.php';</script>";
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