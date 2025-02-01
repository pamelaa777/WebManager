<?php
// Exibe todos os erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica se o nome do usuário foi passado via query string
if (isset($_GET['usuario'])) {
    $usuario = htmlspecialchars($_GET['usuario']);
} else {
    // Caso não tenha o nome do usuário, redireciona ou define um valor padrão
    $usuario = "usuário";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sucesso ao Cadastrar</title>
</head>
<body>
    <div class="main-login">
        <div class="left-login">
            <!-- Exibe a mensagem de sucesso com o nome do usuário -->
            <h1>Sucesso ao Cadastrar, <?php echo $usuario; ?>!</h1>
            <img src="imagem.svg" alt="Imagem de sucesso">
        </div>
        <div class="right-login">
            <div class="card-login">
                <!-- Botão de Sair que redireciona para a página inicial -->
                <a href="index.html" class="btn-login">Sair</a>
            </div>
        </div>
    </div>
</body>
</html>

