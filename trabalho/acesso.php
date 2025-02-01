<?php
session_start();
require 'db.php'; // Conexão com o banco de dados

// Obtém o tipo de usuário e o ID
$tipo_usuario = $_SESSION['tipo_usuario'];
$id_usuario = $_SESSION['id_usuario'];

// Função para listar as notas do aluno
function listarNotas($pdo, $id_usuario) {
    $stmt = $pdo->prepare("
        SELECT Notas.Disciplina, Notas.Nota
        FROM Notas
        JOIN Aluno ON Notas.IDAluno = Aluno.IDAluno
        WHERE Aluno.IDUser = :id_usuario
    ");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Verifica se o técnico está alterando o status ou removendo um usuário
if ($tipo_usuario == 'tecnico') {
    if (isset($_POST['alterar_status'])) {
        // Alterar status (ativar/desativar)
        $idUsuarioAlterar = $_POST['id_usuario'];

        // Verificar o status atual do usuário
        $stmt = $pdo->prepare("SELECT status_login FROM User WHERE IDUser = :id");
        $stmt->bindParam(':id', $idUsuarioAlterar, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Alternar entre ativar e desativar
            $novoStatus = $usuario['status_login'] == 'ativo' ? 'inativo' : 'ativo';

            // Atualizar status no banco de dados
            $stmt = $pdo->prepare("UPDATE User SET status_login = :status WHERE IDUser = :id");
            $stmt->bindParam(':status', $novoStatus, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idUsuarioAlterar, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Redirecionar para evitar reenvio do formulário
        header("Location: acesso.php");
        exit();
    }

    if (isset($_POST['remover_usuario'])) {
        // Remover usuário
        $idUsuarioRemover = $_POST['id_usuario'];

        // Deletar o usuário do banco de dados
        $stmt = $pdo->prepare("DELETE FROM User WHERE IDUser = :id");
        $stmt->bindParam(':id', $idUsuarioRemover, PDO::PARAM_INT);
        $stmt->execute();

        // Redirecionar para evitar reenvio do formulário
        header("Location: acesso.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Acesso ao Sistema</title>
    <style>
        /* Estilos da tabela */
        .user-table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            color: #333;
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .user-table th {
            background-color: #5d5c61; /* Cabeçalho em cinza escuro */
            color: white; /* Texto em branco */
            font-weight: bold;
        }
        .user-table td {
            background-color: #f4f4f9; /* Fundo suave */
            color: black; /* Cor preta para os dados */
        }
        .user-table tr:nth-child(even) {
            background-color: #e8e8f0; /* Tom leve de cinza */
        }

        /* Botões */
        .btn-status {
            background-color: #4CAF50; /* Verde para ativar/desativar */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Estilo para o botão de Alterar Senha */
        .btn-alterar-senha {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-alterar-senha:hover {
            background-color: #45a049;
        }

        /* Estilo para o botão de Voltar */
        .btn-voltar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f44336; /* Vermelho */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-voltar:hover {
            background-color: #d32f2f; /* Vermelho mais escuro */
        }
    </style>
</head>
<body>
    <div class="main-login">
        <div class="left-login">
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
            <img src="imagem.svg" alt="Imagem de login" class="left-login-image">
        </div>
        <div class="right-login">
            <div class="card-login">
                <?php if ($tipo_usuario == 'tecnico'): ?>
                    <h2>Gerenciar Usuários</h2>
                    <div class="user-table-container">
                        <?php
                        // Listar todos os usuários com seus nomes e tipos
                        $stmt = $pdo->prepare("
                            SELECT u.IDUser,
                                   u.tipoUsuario,
                                   u.status_login,
                                   COALESCE(a.nome, p.nome, t.nome) AS nome,
                                   COALESCE(a.sobrenome, p.sobrenome, t.sobrenome) AS sobrenome
                            FROM User u
                            LEFT JOIN Aluno a ON u.IDUser = a.IDUser
                            LEFT JOIN Professor p ON u.IDUser = p.IDUser
                            LEFT JOIN Tecnico t ON u.IDUser = t.IDUser
                        ");
                        $stmt->execute();
                        $usuarios = $stmt->fetchAll();

                        echo "<table class='user-table'>";
                        echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Status</th><th>Ações</th></tr>";
                        foreach ($usuarios as $usuario) {
                            // Verificar o status atual
                            $statusTexto = $usuario['status_login'] == 'ativo' ? 'Ativado' : 'Desativado';
                            $acaoTexto = $usuario['status_login'] == 'ativo' ? 'Desativar' : 'Ativar';

                            echo "<tr>";
                            echo "<td class='id-column'>{$usuario['IDUser']}</td>";
                            echo "<td class='nome-column'>{$usuario['nome']} {$usuario['sobrenome']}</td>";
                            echo "<td class='tipo-column'>{$usuario['tipoUsuario']}</td>";
                            echo "<td>{$statusTexto}</td>";
                            echo "<td>
                                    <form method='POST' action='acesso.php' style='display:inline;'>
                                        <input type='hidden' name='id_usuario' value='{$usuario['IDUser']}'>
                                        <button type='submit' name='alterar_status' class='btn-status'>{$acaoTexto}</button>
                                    </form>
                                    |
                                    <form method='POST' action='acesso.php' style='display:inline;'>
                                        <input type='hidden' name='id_usuario' value='{$usuario['IDUser']}'>
                                        <button type='submit' name='remover_usuario' class='btn-remover'>Remover</button>
                                    </form> | <a href='editar_usuario.php?id={$usuario['IDUser']}'>Editar</a>
                                  </td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        ?>
                    </div>
                    <a href='alterar_senha.php?id=<?php echo $id_usuario; ?>' class='btn-alterar-senha'>Alterar Minha Senha</a>
                    <a href='index.html' class='btn-voltar'>Voltar</a> <!-- Botão de voltar aqui -->

                <?php elseif ($tipo_usuario == 'professor'): ?>
                    <h2>Gerenciar Alunos</h2>
                    <a href='alterar_senha.php?id=<?php echo $id_usuario; ?>' class='btn-alterar-senha'>Alterar Minha Senha</a>
                    <a href='index.html' class='btn-voltar'>Voltar</a> <!-- Botão de voltar aqui -->

                    <?php
                    // Listar apenas os alunos
                    $stmt = $pdo->prepare("
                        SELECT a.IDAluno,
                               a.nome,
                               a.sobrenome
                        FROM User u
                        JOIN Aluno a ON u.IDUser = a.IDUser
                        WHERE u.tipoUsuario = 'aluno'
                    ");
                    $stmt->execute();
                    $alunos = $stmt->fetchAll();

                    echo "<table class='user-table'>";
                    echo "<tr><th>ID</th><th>Nome</th><th>Ações</th></tr>";
                    foreach ($alunos as $aluno) {
                        echo "<tr>";
                        echo "<td class='id-column'>{$aluno['IDAluno']}</td>";
                        echo "<td class='nome-column'>{$aluno['nome']} {$aluno['sobrenome']}</td>";
                        echo "<td>
                                <a href='ver_notas.php?id={$aluno['IDAluno']}'>Ver Notas</a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    ?>

                <?php elseif ($tipo_usuario == 'aluno'): ?>
                    <h2>Suas Notas</h2>
                    <div class="user-table-container">
                        <?php
                        $notas = listarNotas($pdo, $id_usuario);
                        if (count($notas) > 0) {
                            echo "<table class='user-table'>";
                            echo "<tr><th>Disciplina</th><th>Nota</th></tr>";
                            foreach ($notas as $nota) {
                                echo "<tr>";
                                echo "<td>{$nota['Disciplina']}</td>";
                                echo "<td>{$nota['Nota']}</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "<p>Você ainda não tem notas registradas.</p>";
                        }
                        ?>
                    </div>
                    <a href='alterar_senha.php?id=<?php echo $id_usuario; ?>' class='btn-alterar-senha'>Alterar Minha Senha</a>
                    <a href='index.html' class='btn-voltar'>Voltar</a> <!-- Botão de voltar aqui -->

                <?php else: ?>
                    <p>Tipo de usuário desconhecido.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

