<?php
session_start();
require 'db.php'; // Conexão com o banco de dados

// Obtém o ID do usuário a partir da sessão (usuário logado)
$idUsuario = $_SESSION['id_usuario'] ?? null;

// Verifica se o usuário está logado
if (!$idUsuario) {
    echo "<p>Você precisa estar logado para alterar a senha.</p>";
    exit;
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    try {
        // Verifica se a senha atual está correta
        $stmt = $pdo->prepare("SELECT senha FROM User WHERE IDUser = :idUsuario");
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();

        if ($resultado && password_verify($senha_atual, $resultado['senha'])) {
            // Verifica se a nova senha e a confirmação são iguais
            if ($nova_senha === $confirmar_senha) {
                // Atualiza com a nova senha
                $hashedSenha = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE User SET senha = :senha WHERE IDUser = :idUsuario");
                $stmt->bindParam(':senha', $hashedSenha, PDO::PARAM_STR);
                $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();

                echo "<p>Senha alterada com sucesso!</p>";
                // Redirecionar para evitar reenvio do formulário
                header("Location: acesso.php");
                exit();
            } else {
                echo "<p>A nova senha e a confirmação não coincidem.</p>";
            }
        } else {
            echo "<p>A senha atual está incorreta.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erro ao alterar senha: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Alterar Senha</title>
</head>
<body>
<div class="main-login">
    <div class="card-login">
        <h1>Alterar Senha</h1>
        <form method="POST" action="">
            <div class="textfield">
                <label for="senha_atual">Senha Atual</label>
                <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" required>
            </div>

            <div class="textfield">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" required>
            </div>

            <div class="textfield">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua nova senha" required>
            </div>

            <input class="btn-login" type="submit" value="Alterar Senha">
        </form>
        <a class="cadastro-link" href="acesso.php">Voltar</a>
    </div>
</div>
</body>
</html>

