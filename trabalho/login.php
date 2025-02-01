<?php
session_start();
require 'db.php';  // Certifique-se de que esse arquivo conecta corretamente ao banco de dados

// Exibe todos os erros para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados enviados pelo formulário
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

    // Verifica se os campos foram preenchidos
    if (empty($email) || empty($senha)) {
        echo "Por favor, preencha todos os campos.";
        exit;
    }

    try {
        // Prepara a consulta para buscar o usuário pelo email
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário foi encontrado
        if ($user === false) {
            echo "<p style='color: red;'>Nenhum usuário encontrado com esse e-mail.</p>";
            exit;
        }

        // Verifica se a senha fornecida está correta
        if (password_verify($senha, $user['senha'])) {
            // Verifica o status da conta (se está ativa)
            if ($user['status_login'] !== 'ativo') {
                echo "<p style='color: red;'>Conta inativa. Por favor, contate o administrador.</p>";
                exit;
            }

            // Armazena as informações na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id_usuario'] = $user['IDUser'];  // Adiciona o ID do usuário na sessão
            $_SESSION['tipo_usuario'] = $user['tipoUsuario'] ?? null;  // Aqui é 'tipoUsuario'

            // Busca o nome do usuário na tabela correspondente
            $tipoUsuario = $_SESSION['tipo_usuario'];
            $stmt_nome = $pdo->prepare("SELECT nome FROM " . ucfirst($tipoUsuario) . " WHERE IDUser = :idUser");
            $stmt_nome->bindParam(':idUser', $_SESSION['id_usuario']);
            $stmt_nome->execute();

            $usuario_detalhes = $stmt_nome->fetch(PDO::FETCH_ASSOC);
            $_SESSION['usuario'] = $usuario_detalhes['nome'] ?? null;  // Nome do usuário

            // Redireciona o usuário de acordo com o tipo de acesso
            switch ($tipoUsuario) {
                case 'aluno':
                    header('Location: acesso.php?tipo=aluno');
                    exit;
                case 'professor':
                    header('Location: acesso.php?tipo=professor');
                    exit;
                case 'tecnico':
                    header('Location: acesso.php?tipo=tecnico');
                    exit;
                default:
                    echo "<p style='color: red;'>Tipo de usuário indefinido. Verifique seu cadastro.</p>";
                    exit;
            }
        } else {
            // Senha incorreta
            echo "<p style='color: red;'>Email ou senha incorretos.</p>";
        }
    } catch (PDOException $e) {
        // Exibe mensagem de erro detalhada para depuração
        echo "<p style='color: red;'>Erro no processo de login: " . $e->getMessage() . "</p>";
    }
}
?>

