<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'solicitar_codigo':
            $email = $_POST['email'] ?? '';
            $tipoUsuario = $_POST['tipo_usuario'] ?? 'aluno';
            
            if (!empty($email)) {
                $codigoRecuperacao = rand(100000, 999999);
                $_SESSION['codigo_recuperacao'] = $codigoRecuperacao;
                $_SESSION['flash_log'] = "[SIMULAÇÃO DE EMAIL] Para: " . $email . " | Assunto: SIPN - Recuperação de Senha | Seu código é: " . $codigoRecuperacao;
                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?redefinir=1&email=" . urlencode($email));
                exit();
            } else {
                echo "<script>alert('Por favor, insira um e-mail válido.'); window.history.back();</script>";
                exit();
            }
            break;

        case 'redefinir_senha':
            $email = $_POST['email'] ?? '';
            $codigo = $_POST['codigo'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';
            $tipoUsuario = $_POST['tipo_usuario'] ?? 'aluno';
            $codigoSalvo = $_SESSION['codigo_recuperacao'] ?? '';

            if ($senha !== $confirmaSenha) {
                echo "<script>alert('Erro: As senhas digitadas não coincidem.'); window.history.back();</script>";
                exit();
            }

            if ($codigo != $codigoSalvo || empty($codigoSalvo)) {
                echo "<script>alert('Erro: O código de recuperação é inválido ou expirou.'); window.history.back();</script>";
                exit();
            }

            unset($_SESSION['codigo_recuperacao']);
            echo "<script>alert('Senha redefinida com sucesso!'); window.location.href = '../view/login_" . htmlspecialchars($tipoUsuario) . ".php';</script>";
            exit();
            break;

        case 'cadastrar_funcionario':
            $codigoAcesso = $_POST['codigo_acesso'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';
            $codigoValido = $_SESSION['codigo_funcionario'] ?? '';
            
            if ($senha !== $confirmaSenha) {
                echo "<script>alert('Erro: As senhas digitadas não coincidem.'); window.history.back();</script>";
                exit();
            }

            if ($codigoAcesso !== $codigoValido || empty($codigoValido)) {
                echo "<script>alert('Erro: O código de autorização é inválido.'); window.history.back();</script>";
                exit();
            }

            unset($_SESSION['codigo_funcionario']);
            echo "<script>alert('Cadastro de funcionário realizado com sucesso!'); window.location.href = '../view/login_funcionario.php';</script>";
            exit();
            break;

        case 'login_aluno':
            $_SESSION['usuario_tipo'] = 'aluno';
            if (isset($_POST['lembrar_me'])) {
                setcookie('aluno_logado', 'true', time() + (86400 * 30), "/");
            }
            header("Location: ../view/login_aluno.php"); 
            exit();
            break;

        case 'login_funcionario':
            $_SESSION['usuario_tipo'] = 'funcionario';
            if (isset($_POST['lembrar_me'])) {
                setcookie('funcionario_logado', 'true', time() + (86400 * 30), "/");
            }
            header("Location: ../view/login_funcionario.php");
            exit();
            break;

        case 'cadastrar_aluno':
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';

            if ($senha !== $confirmaSenha) {
                echo "<script>alert('Erro: As senhas digitadas não coincidem.'); window.history.back();</script>";
                exit();
            }

            echo "<script>alert('Cadastro de aluno realizado com sucesso!'); window.location.href = '../view/login_aluno.php';</script>";
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