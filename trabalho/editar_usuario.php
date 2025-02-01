<?php
session_start();
require 'db.php'; // Conexão com o banco de dados

// Verifica se um ID de usuário foi passado
if (!isset($_GET['id'])) {
    echo "Usuário não encontrado.";
    exit;
}

$idUsuario = $_GET['id'];

// Busca o tipo de usuário na tabela User
$stmt = $pdo->prepare("SELECT tipoUsuario, email FROM User WHERE IDUser = :idUsuario");
$stmt->bindParam(':idUsuario', $idUsuario);
$stmt->execute();
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

$tipoUsuario = $usuario['tipoUsuario'];
$tabela = '';
switch ($tipoUsuario) {
    case 'aluno':
        $tabela = 'Aluno';
        break;
    case 'professor':
        $tabela = 'Professor';
        break;
    case 'tecnico':
        $tabela = 'Tecnico';
        break;
}

// Busca o nome e sobrenome da tabela correspondente
$stmt = $pdo->prepare("SELECT nome, sobrenome FROM $tabela WHERE IDUser = :idUsuario");
$stmt->bindParam(':idUsuario', $idUsuario);
$stmt->execute();
$dadosUsuario = $stmt->fetch();

if (!$dadosUsuario) {
    echo "Dados do usuário não encontrados.";
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados pelo formulário
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$sobrenome = filter_input(INPUT_POST, 'sobrenome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $tipoUsuario = filter_input(INPUT_POST, 'tipo_usuario', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Atualiza os dados do usuário na tabela User
        $stmt = $pdo->prepare("UPDATE User SET email = :email, tipoUsuario = :tipo WHERE IDUser = :idUsuario");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':tipo', $tipoUsuario);
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->execute();

        // Atualiza os dados do nome e sobrenome na tabela correspondente
        $stmt = $pdo->prepare("UPDATE $tabela SET nome = :nome, sobrenome = :sobrenome WHERE IDUser = :idUsuario");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->execute();

        echo "<p>Dados do usuário atualizados com sucesso!</p>";
    } catch (Exception $e) {
        echo "<p>Erro ao atualizar dados: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="style.css">
    <title>Editar Usuário</title>
</head>
<body>
    <div class="container">
        <div class="main-login">
            <div class="left-login">
                <h1>Editar Usuário</h1>
                <img src="imagem.svg" class="left-login-image" alt="Edit User">
            </div>

            <div class="right-login">
                <div class="card-login">
                    <h1>Editar Dados de: <?php echo htmlspecialchars($dadosUsuario['nome']); ?></h1>
                    <form method="POST" action="editar_usuario.php?id=<?php echo $idUsuario; ?>">
                        <div class="textfield">
                            <label for="nome">Nome:</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>" required>
                        </div>

                        <div class="textfield">
                            <label for="sobrenome">Sobrenome:</label>
                            <input type="text" name="sobrenome" value="<?php echo htmlspecialchars($dadosUsuario['sobrenome']); ?>" required>
                        </div>

                        <div class="textfield">
                            <label for="email">Email:</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <div class="textfield">
                            <label for="tipo_usuario">Tipo de Usuário:</label>
                            <select name="tipo_usuario" required>
                                <option value="aluno" <?php echo ($tipoUsuario == 'aluno') ? 'selected' : ''; ?>>Aluno</option>
                                <option value="professor" <?php echo ($tipoUsuario == 'professor') ? 'selected' : ''; ?>>Professor</option>
                                <option value="tecnico" <?php echo ($tipoUsuario == 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-login">Atualizar</button>
                    </form>
                    <a href="acesso.php" class="btn-back">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
