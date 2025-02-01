<?php
session_start();
require 'db.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado e é um técnico
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    header('Location: login.php');
    exit;
}

// Promove um usuário para o tipo de administrador
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    try {
        $stmt = $pdo->prepare("UPDATE User SET TipoUsuario = 'tecnico' WHERE IDUser = :idUsuario");
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->execute();

        echo "Usuário promovido com sucesso!";
    } catch (Exception $e) {
        echo "Erro ao promover usuário: " . $e->getMessage();
    }
}
?>
<a href="acesso.php">Voltar</a>

