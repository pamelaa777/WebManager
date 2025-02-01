<?php
session_start();
require 'db.php';  // Conexão com o banco de dados

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $sobrenome = filter_input(INPUT_POST, 'sobrenome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = password_hash(filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS), PASSWORD_DEFAULT);
    $dataNasc = filter_input(INPUT_POST, 'dataNasc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tipoUsuario = filter_input(INPUT_POST, 'perfil', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Alunos
    $curso = filter_input(INPUT_POST, 'curso', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Alunos
    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Alunos
    $departamento = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Professores
    $areaAtuacao = filter_input(INPUT_POST, 'areaAtuacao', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Professores
    $setor = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Para Técnicos

    if (empty($nome) || empty($sobrenome) || empty($email) || empty($senha) || empty($dataNasc) || empty($tipoUsuario)) {
        echo "Por favor, preencha todos os campos.";
        exit;
    }

    try {
        // Verifica se o email já está cadastrado
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo "<p style='color: red;'>Email já cadastrado. Por favor, utilize outro.</p>";
            exit;
        }

        // Insere o usuário na tabela User
        $stmt = $pdo->prepare("INSERT INTO User (senha, tipoUsuario, status_login, email) VALUES (:senha, :tipoUsuario, 'ativo', :email)");
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipoUsuario', $tipoUsuario);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $IDUser = $pdo->lastInsertId(); // Captura o ID do novo usuário

        // Insere os dados específicos na tabela correspondente
        if ($tipoUsuario === 'aluno') {
            $stmt = $pdo->prepare("INSERT INTO Aluno (IDUser, nome, sobrenome, dataNascimento, CPF, curso, matricula) VALUES (:IDUser, :nome, :sobrenome, :dataNasc, :cpf, :curso, :matricula)");
            $stmt->bindParam(':IDUser', $IDUser);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':dataNasc', $dataNasc);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':curso', $curso);
            $stmt->bindParam(':matricula', $matricula);
        } elseif ($tipoUsuario === 'professor') {
            $stmt = $pdo->prepare("INSERT INTO Professor (IDUser, nome, sobrenome, dataNascimento, departamento, areaAtuacao) VALUES (:IDUser, :nome, :sobrenome, :dataNasc, :departamento, :areaAtuacao)");
            $stmt->bindParam(':IDUser', $IDUser);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':dataNasc', $dataNasc);
            $stmt->bindParam(':departamento', $departamento);
            $stmt->bindParam(':areaAtuacao', $areaAtuacao);
        } elseif ($tipoUsuario === 'tecnico') {
            $stmt = $pdo->prepare("INSERT INTO Tecnico (IDUser, nome, sobrenome, dataNascimento, setor) VALUES (:IDUser, :nome, :sobrenome, :dataNasc, :setor)");
            $stmt->bindParam(':IDUser', $IDUser);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':dataNasc', $dataNasc);
            $stmt->bindParam(':setor', $setor);
        }

        // Executa a inserção na tabela específica
        if (isset($stmt) && $stmt->execute()) {
            header('Location: sucesso.php');
            exit;
        } else {
            echo "<p style='color: red;'>Erro ao cadastrar usuário. Por favor, tente novamente.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erro no processo de cadastro: " . $e->getMessage() . "</p>";
    }
}
?>

<form method="POST" action="cadastro.php">
    <!-- Outros campos de cadastro -->

    <div class="textfield">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required>
    </div>

    <div class="textfield">
        <label for="sobrenome">Sobrenome:</label>
        <input type="text" name="sobrenome" required>
    </div>

    <div class="textfield">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
    </div>

    <div class="textfield">
        <label for="dataNasc">Data de Nascimento:</label>
        <input type="date" name="dataNasc" required>
    </div>

    <div class="textfield">
        <label for="perfil">Tipo de Usuário:</label>
        <select name="perfil" required>
            <option value="aluno">Aluno</option>
            <option value="professor">Professor</option>
            <option value="tecnico">Técnico</option>
        </select>
    </div>

    <!-- Campo para Professores -->
    <div class="textfield" id="campo-professor" style="display: none;">
        <label for="departamento">Departamento:</label>
        <input type="text" name="departamento">
        
        <label for="areaAtuacao">Área de Atuação:</label>
        <select name="areaAtuacao">
            <option value="portugues">Português</option>
            <option value="matematica">Matemática</option>
            <option value="historia">História</option>
            <option value="geografia">Geografia</option>
            <option value="ciencias">Ciências</option>
            <option value="ti">TI</option>
        </select>
    </div>

    <!-- Campo para Técnicos -->
    <div class="textfield" id="campo-tecnico" style="display: none;">
        <label for="setor">Setor:</label>
        <input type="text" name="setor">
    </div>

    <!-- Campo para Alunos -->
    <div class="textfield" id="campo-aluno" style="display: none;">
        <label for="curso">Curso:</label>
        <input type="text" name="curso">

        <label for="matricula">Matrícula:</label>
        <input type="text" name="matricula">
    </div>

    <button type="submit">Cadastrar</button>
</form>

<script>
// Função para mostrar/ocultar campos com base no tipo de usuário
document.querySelector('select[name="perfil"]').addEventListener('change', function() {
    var tipo = this.value;
    document.getElementById('campo-aluno').style.display = (tipo === 'aluno') ? 'block' : 'none';
    document.getElementById('campo-professor').style.display = (tipo === 'professor') ? 'block' : 'none';
    document.getElementById('campo-tecnico').style.display = (tipo === 'tecnico') ? 'block' : 'none';
});
</script>

