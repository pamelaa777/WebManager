<?php
session_start();
require 'db.php'; // Conexão com o banco de dados

// Verifica se o usuário é um professor
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    echo "Acesso negado.";
    exit;
}

// Obtém o ID do aluno a partir da URL
$idAluno = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idAluno <= 0) {
    die("Erro: O ID do aluno não existe.");
}

// Função para inserir nova nota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_nota'])) {
        $disciplina = filter_input(INPUT_POST, 'disciplina', FILTER_SANITIZE_STRING);
        $novaNota = filter_input(INPUT_POST, 'nova_nota', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        try {
            // Insere nova nota no banco de dados
            $stmt = $pdo->prepare("INSERT INTO Notas (IDAluno, Disciplina, Nota) VALUES (:id_aluno, :disciplina, :nova_nota)");
            $stmt->bindParam(':id_aluno', $idAluno);
            $stmt->bindParam(':disciplina', $disciplina);
            $stmt->bindParam(':nova_nota', $novaNota);
            $stmt->execute();

            echo "<p>Nota adicionada com sucesso!</p>";
        } catch (Exception $e) {
            echo "<p>Erro ao adicionar nota: " . $e->getMessage() . "</p>";
        }
    }

    // Verifica se o botão de deletar foi acionado
    if (isset($_POST['deletar_nota'])) {
        $idNota = filter_input(INPUT_POST, 'id_nota', FILTER_VALIDATE_INT);
        
        if ($idNota) {
            try {
                // Apaga a nota do banco de dados
                $stmt = $pdo->prepare("DELETE FROM Notas WHERE IDNotas = :id_nota");
                $stmt->bindParam(':id_nota', $idNota);
                $stmt->execute();

                echo "<p>Nota apagada com sucesso!</p>";
            } catch (Exception $e) {
                echo "<p>Erro ao apagar nota: " . $e->getMessage() . "</p>";
            }
        }
    }
}

// Busca as notas do aluno
try {
    $stmt = $pdo->prepare("SELECT Notas.IDNotas, Aluno.nome, Notas.Disciplina, Notas.Nota
                            FROM Notas
                            JOIN Aluno ON Notas.IDAluno = Aluno.IDAluno
                            WHERE Aluno.IDAluno = :id_aluno");
    $stmt->bindParam(':id_aluno', $idAluno);
    $stmt->execute();
    $notas = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao buscar notas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ver Notas</title>
    <style>
        .user-table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            color: #f0ffffde;
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            color: #f0ffffde;
        }
        .user-table th {
            background-color: #00ff88;
            color: #2b134b;
            font-weight: bold;
        }
        .user-table td {
            background-color: #514869;
        }
        .user-table tr:nth-child(even) {
            background-color: #2f2841;
        }
        .btn-alterar {
            background-color: #00ff88;
            color: #2b134b;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 8px;
        }
        .btn-apagar {
            background-color: #f44336; /* Cor vermelha */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 8px;
        }
        .btn-voltar {
            background-color: #00ff88;
            color: #2b134b;
            border: none;
            padding: 10px 20px;
            text-decoration: none; margin-top: 20px;
            display: inline-block;
            border-radius: 8px;
        }
        .btn-voltar:hover {
            background-color: #77ffc0;
        }
        h2 {
            color: #00ff88;
        }
    </style>
</head>
<body>
    <div class="main-login">
        <div class="card-login">
            <h2>Notas do Aluno</h2>

            <h3>Adicionar Nova Nota</h3>
            <form method='POST' action='ver_notas.php?id=<?php echo $idAluno; ?>'>
                <input type='hidden' name='id_aluno' value='<?php echo $idAluno; ?>'>
                <label for="disciplina">Disciplina:</label>
                <select name="disciplina" required>
                    <option value="portugues">Português</option>
                    <option value="matematica">Matemática</option>
                    <option value="historia">História</option>
                    <option value="geografia">Geografia</option>
                    <option value="ciencias">Ciências</option>
                    <option value="TI">TI</option>
                </select>
                <label for="nova_nota">Nota:</label>
                <input type='number' name='nova_nota' placeholder='Nota' step='0.1' required>
                <button type='submit' name='adicionar_nota' class='btn-alterar'>Adicionar Nota</button>
            </form>

            <table class='user-table'>
                <tr>
                    <th>ID</th>
                    <th>Nome do Aluno</th>
                    <th>Disciplina</th>
                    <th>Nota</th>
                    <th>Ações</th>
                </tr>
                <?php if (empty($notas)): ?>
                    <tr>
                        <td colspan="5">Nenhuma nota encontrada para este aluno.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($notas as $nota): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nota['IDNotas']); ?></td>
                            <td><?php echo htmlspecialchars($nota['nome']); ?></td>
                            <td><?php echo htmlspecialchars($nota['Disciplina']); ?></td>
                            <td><?php echo htmlspecialchars($nota['Nota']); ?></td>
                            <td>
                                <form method='POST' action='ver_notas.php?id=<?php echo $idAluno; ?>' style='display:inline;'>
                                    <input type='hidden' name='id_nota' value='<?php echo $nota['IDNotas']; ?>'>
                                    <button type='submit' name='deletar_nota' class='btn-apagar' onclick='return confirm("Tem certeza que deseja apagar esta nota?")'>Apagar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
            <a href="acesso.php" class="btn-voltar">Voltar</a>
        </div>
    </div>
</body>
</html>

