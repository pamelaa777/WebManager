<?php
$host = 'localhost';   // Substitua pelo seu servidor de banco de dados localhost
$dbname = 'a2023952489@teiacoltec.org';      // Nome do banco de dados
$username = 'a2023952489@teiacoltec.org';    // Nome de usuário do banco de dados
$password = '@Coltec2024';        // Senha do banco de dados

try {
    // Criação da conexão usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    exit;
}
?>


