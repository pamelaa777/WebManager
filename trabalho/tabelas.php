<?php
session_start();
require 'db.php'; // Conexão com o banco de dados
require 'tabela.php';
// Verifica se o usuário é um técnico (ou outro papel com permissão)
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    echo "Acesso negado.";
    exit;
}

// Dados para inserção
$usuarios = [
    ['senha' => 'senha123', 'TipoUsuario' => 'aluno', 'nome' => 'Gabriel', 'sobrenome' => 'Bessa', 'email' => 'pamela@3.com', 'dataNasc' => '2000-01-01'],
    // Adicione mais usuários conforme necessário
];

$alunos = [
    ['IDUser' => 2, 'CPF' => '12345678901', 'curso' => 'Engenharia', 'matricula' => '20241001'],
    // Adicione mais alunos conforme necessário
];

$professores = [
    ['IDUser' => 3, 'departamento' => 'Matemática', 'areaAtuacao' => 'Álgebra'],
    // Adicione mais professores conforme necessário
];

$disciplinas = [
    ['NomeDisciplina' => 'Matemática I', 'IDProfessor' => 3],
    // Adicione mais disciplinas conforme necessário
];

$notas = [
    ['IDAluno' => 1, 'IDDisciplina' => 1, 'nota' => 8.5],
    // Adicione mais notas conforme necessário
];

try {
    // Inicia a transação
    $pdo->beginTransaction();

    // Inserindo usuários
    foreach ($usuarios as $usuario) {
        $stmt = $pdo->prepare("INSERT INTO User (senha, TipoUsuario, nome, sobrenome, email, dataNasc) VALUES (:senha, :tipo_usuario, :nome, :sobrenome, :email, :dataNasc)");
        $stmt->execute($usuario);
    }

    // Inserindo alunos
    foreach ($alunos as $aluno) {
        $stmt = $pdo->prepare("INSERT INTO Aluno (IDUser, CPF, curso, matricula) VALUES (:IDUser, :CPF, :curso, :matricula)");
        $stmt->execute($aluno);
    }

    // Inserindo professores
    foreach ($professores as $professor) {
        $stmt = $pdo->prepare("INSERT INTO Professor (IDUser, departamento, areaAtuacao) VALUES (:IDUser, :departamento, :areaAtuacao)");
        $stmt->execute($professor);
    }

    // Inserindo disciplinas
    foreach ($disciplinas as $disciplina) {
        $stmt = $pdo->prepare("INSERT INTO Disciplina (NomeDisciplina, IDProfessor) VALUES (:NomeDisciplina, :IDProfessor)");
        $stmt->execute($disciplina);
    }

    // Inserindo notas
    foreach ($notas as $nota) {
        $stmt = $pdo->prepare("INSERT INTO Notas (IDAluno, IDDisciplina, nota) VALUES (:IDAluno, :IDDisciplina, :nota)");
        $stmt->execute($nota);
    }

    // Commit da transação
    $pdo->commit();
    echo "Dados inseridos com sucesso!";
} catch (Exception $e) {
    // Rollback em caso de erro
    $pdo->rollBack();
    echo "Erro ao inserir dados: " . $e->getMessage();
}
?>

