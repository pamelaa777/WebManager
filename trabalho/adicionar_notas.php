<?php
session_start();
require 'db.php'; // Conexão com o banco de dados


// Adiciona notas para um aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $disciplina = $_POST['disciplina'];
    $nota = $_POST['nota'];

    try {
        // Primeiro, busca o ID do aluno baseado no Nome e Sobrenome
        $stmt = $pdo->prepare("SELECT IDAluno FROM Alunos WHERE nome = :nome AND sobrenome = :sobrenome");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($aluno) {
            // Se o aluno for encontrado, insere a nota
            $id_aluno = $aluno['IDAluno'];
            $stmt = $pdo->prepare("INSERT INTO Notas (IDAluno, disciplina, nota) VALUES (:idAluno, :disciplina, :nota)");
            $stmt->bindParam(':idAluno', $id_aluno);
            $stmt->bindParam(':disciplina', $disciplina);
            $stmt->bindParam(':nota', $nota);
            $stmt->execute();

            echo "Nota adicionada com sucesso!";
        } else {
            echo "Aluno não encontrado!";
        }
    } catch (Exception $e) {
        echo "Erro ao adicionar nota: " . $e->getMessage();
    }
}
?>
<a href="acesso.php">Voltar</a>

