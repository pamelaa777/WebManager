<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'professor') {
    header('Location: login.php');
    exit;
}
require 'db.php';

// Listar alunos
$stmt = $pdo->prepare("SELECT * FROM Aluno");
$stmt->execute();
$alunos = $stmt->fetchAll();

echo "<h1>Bem-vindo, Professor " . $_SESSION['usuario'] . "</h1>";
echo "<h2>Lista de Alunos</h2>";

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nome</th><th>Curso</th><th>Matrícula</th></tr>";
foreach ($alunos as $aluno) {
    echo "<tr>";
    echo "<td>{$aluno['IDAluno']}</td>";
    echo "<td>{$aluno['nome']}</td>";
    echo "<td>{$aluno['curso']}</td>";
    echo "<td>{$aluno['matricula']}</td>";
    echo "</tr>";
}
echo "</table>";

// Em vez de redirecionar para a página do tipo de usuário, redirecione para resultado_login.php
header('Location: resultado_login.php');


?>
<a href="loutin.php">Loutin</a>
