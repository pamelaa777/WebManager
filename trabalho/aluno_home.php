<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'aluno') {
    header('Location: login.php');
    exit;
}

require 'db.php';


// Recuperar dados do aluno
$stmt = $pdo->prepare("SELECT * FROM User WHERE IDUser = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$aluno = $stmt->fetch();

// Exibir dados pessoais
echo "<h1>Bem-vindo, " . $aluno['nome'] . " " . $aluno['sobrenome'] . "</h1>";
echo "<p>Email: " . $aluno['email'] . "</p>";
echo "<p>Data de Nascimento: " . $aluno['dataNasc'] . "</p>";
echo "<a href='editar_perfil.php'>Editar Perfil</a>";
echo "<a href='trocar_senha.php'>Trocar Senha</a>";

// Em vez de redirecionar para a página do tipo de usuário, redirecione para resultado_login.php
header('Location: resultado_login.php');

?>
<a href="loutin.php">Loutin</a>
