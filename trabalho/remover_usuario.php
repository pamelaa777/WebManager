<?php
session_start();
require 'db.php'; // Conexão com o banco de dados


// Verifica se o usuário está logado e é um técnico
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    header('Location: login.php');
    exit;
}

// Remove um usuário
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM User WHERE IDUser = :idUsuario");
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->execute();

        echo "Usuário removido com sucesso!";
    } catch (Exception $e) {
        echo "Erro ao remover usuário: " . $e->getMessage();
    }
}
?>
<a href="acesso.php">Voltar</a>

