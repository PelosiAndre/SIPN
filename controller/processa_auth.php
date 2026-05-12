<?php
session_start();
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'solicitar_codigo':
            $email = trim($_POST['email'] ?? '');
            $tipoUsuario = $_POST['tipo_usuario'] ?? 'aluno';
            
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND tipo = ?");
                $stmt->execute([$email, $tipoUsuario]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    $codigoRecuperacao = rand(100000, 999999);
                    $_SESSION['codigo_recuperacao'] = $codigoRecuperacao;
                    $_SESSION['email_recuperacao'] = $email;
                    $_SESSION['recuperacao_simulacao'] = "[SIMULAÇÃO DE EMAIL] Para: " . $email . " | Assunto: SIPN - Recuperação de Senha | Seu código é: " . $codigoRecuperacao;
                    
                    header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?redefinir=1&email=" . urlencode($email));
                    exit();
                } else {
                    header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?erro=email_nao_encontrado");
                    exit();
                }
            } else {
                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?erro=email_invalido");
                exit();
            }
            break;

        case 'redefinir_senha':
            $email = trim($_POST['email'] ?? '');
            $codigo = $_POST['codigo'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';
            $tipoUsuario = $_POST['tipo_usuario'] ?? 'aluno';
            
            $codigoSalvo = $_SESSION['codigo_recuperacao'] ?? '';
            $emailSalvo = $_SESSION['email_recuperacao'] ?? '';

            if ($senha !== $confirmaSenha) {
                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?redefinir=1&email=" . urlencode($email) . "&erro=senhas_diferentes");
                exit();
            }

            if ($codigo != $codigoSalvo || empty($codigoSalvo) || $email !== $emailSalvo) {
                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?redefinir=1&email=" . urlencode($email) . "&erro=codigo_invalido");
                exit();
            }

            try {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ? AND tipo = ?");
                $stmt->execute([$senhaHash, $email, $tipoUsuario]);

                unset($_SESSION['codigo_recuperacao']);
                unset($_SESSION['email_recuperacao']);
                unset($_SESSION['recuperacao_simulacao']);

                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?sucesso=senha_redefinida");
            } catch (PDOException $e) {
                header("Location: ../view/login_" . htmlspecialchars($tipoUsuario) . ".php?redefinir=1&email=" . urlencode($email) . "&erro=falha_banco");
            }
            exit();
            break;

        case 'cadastrar_funcionario':
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $codigoAcesso = $_POST['codigo_acesso'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';
            
            $codigoValido = $_SESSION['codigo_funcionario'] ?? '';

            if ($senha !== $confirmaSenha) {
                 header("Location: ../view/login_funcionario.php?erro=senhas_diferentes&aba=cadastro");
                 exit();
            }

            if ($codigoAcesso !== $codigoValido || empty($codigoValido)) { 
                 header("Location: ../view/login_funcionario.php?erro=codigo_invalido&aba=cadastro");
                 exit();
            }

            if ($nome && $email && $senha) {
                try {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'funcionario')");
                    $stmt->execute([$nome, $email, $senhaHash]);
                    unset($_SESSION['codigo_funcionario']);
                    header("Location: ../view/login_funcionario.php?sucesso=cadastro_realizado");
                } catch (PDOException $e) {
                    header("Location: ../view/login_funcionario.php?erro=email_existente&aba=cadastro");
                }
            } else {
                 header("Location: ../view/login_funcionario.php?erro=dados_invalidos&aba=cadastro");
            }
            exit();
            break;

        case 'cadastrar_aluno':
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';

            if (empty($nome) || empty($email) || empty($senha)) {
                header("Location: ../view/login_aluno.php?erro=dados_invalidos&aba=cadastro");
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../view/login_aluno.php?erro=email_invalido&aba=cadastro");
                exit();
            }

            if ($senha !== $confirmaSenha) {
                header("Location: ../view/login_aluno.php?erro=senhas_diferentes&aba=cadastro");
                exit();
            }

            try {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'aluno')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $senhaHash]);
                header("Location: ../view/login_aluno.php?sucesso=cadastro_realizado");
            } catch (PDOException $e) {
                header("Location: ../view/login_aluno.php?erro=email_existente&aba=cadastro");
            }
            exit();
            break;

        case 'login_aluno':
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (!empty($email) && !empty($senha)) {
                $stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = ? AND tipo = 'aluno'");
                $stmt->execute([$email]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_tipo'] = 'aluno';
                    $_SESSION['usuario_id'] = $usuario['id'];
                    
                    if (isset($_POST['lembrar_me'])) {
                        setcookie('aluno_logado', 'true', time() + (86400 * 30), "/");
                    }
                    header("Location: ../view/painel_aluno.php"); 
                } else {
                    header("Location: ../view/login_aluno.php?erro=credenciais_invalidas");
                }
            } else {
                header("Location: ../view/login_aluno.php?erro=dados_invalidos");
            }
            exit();
            break;

        case 'login_funcionario':
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (!empty($email) && !empty($senha)) {
                $stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = ? AND tipo = 'funcionario'");
                $stmt->execute([$email]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_tipo'] = 'funcionario';
                    $_SESSION['usuario_id'] = $usuario['id'];
                    
                    if (isset($_POST['lembrar_me'])) {
                        setcookie('funcionario_logado', 'true', time() + (86400 * 30), "/");
                    }
                    header("Location: ../view/painel_funcionario.php");
                } else {
                    header("Location: ../view/login_funcionario.php?erro=credenciais_invalidas");
                }
            } else {
                header("Location: ../view/login_funcionario.php?erro=dados_invalidos");
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