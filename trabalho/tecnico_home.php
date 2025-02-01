<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'tecnico') {
    header('Location: login.php');
    exit;
}

echo "<h1>Bem-vindo, Administrador " . $_SESSION['usuario'] . "</h1>";
echo "<a href='listar_usuarios.php'>Gerenciar Usuários</a>";

// Em vez de redirecionar para a página do tipo de usuário, redirecione para resultado_login.php
header('Location: resultado_login.php');

?>
<a href="loutin.php">Logout</a>
