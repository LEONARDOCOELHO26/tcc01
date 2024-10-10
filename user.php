<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se a solicitação de logout foi feita
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tcc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processamento do formulário de criação ou edição de usuário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
        $nome = $_POST["nome"];
        $cpf = $_POST["cpf"];
        $numero = $_POST["numero"];
        $apartamento = $_POST["apartamento"];
        $senha = isset($_POST["senha"]) ? $_POST["senha"] : '';

        if (!empty($nome) && !empty($cpf) && !empty($numero) && !empty($apartamento) && !empty($senha)) {
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

            if ($action == "save") {
                $id = $_POST["userId"];
                if (!empty($id)) {
                    $sql = "UPDATE usuarios SET nome=?, cpf=?, numero=?, apartamento=?, senha=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $nome, $cpf, $numero, $apartamento, $senhaCriptografada, $id);
                } else {
                    $sql = "INSERT INTO usuarios (nome, cpf, numero, apartamento, senha) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $nome, $cpf, $numero, $apartamento, $senhaCriptografada);
                }
                if ($stmt->execute()) {
                    echo "<script>alert('Usuário salvo com sucesso.');</script>";
                } else {
                    echo "<script>alert('Erro ao salvar usuário: " . $stmt->error . "');</script>";
                }
            } elseif ($action == "delete") {
                $id = $_POST["id"];
                $sql = "DELETE FROM usuarios WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo "<script>alert('Usuário excluído com sucesso.');</script>";
                } else {
                    echo "<script>alert('Erro ao excluir usuário: " . $stmt->error . "');</script>";
                }
            }
        } else {
            echo "<script>alert('Todos os campos são obrigatórios.');</script>";
        }
    }
}

// Função para buscar todos os usuários do banco de dados
function getAllUsers($conn) {
    $sql = "SELECT id, nome, cpf, numero, apartamento FROM usuarios";
    $result = $conn->query($sql);
    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <script src="https://kit.fontawesome.com/45d9f78a17.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
        }

        .sala {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        form {
            margin-top: 20px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
        }

        form input[type="date"],
        form input[type="time"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        form input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #218838;
        }
        .sidebar {
            height: 100vh;
            width: 200px;
            background-color: #333;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }
        .logosidebar {
            text-align: center;
            margin-bottom: 20px;
        }
        .logosidebar img {
            width: 80px;
            height: 80px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #444;
        }
        .sidebar a.active {
            background-color: #007BFF;
        }
        .sidebar span i {
            margin-right: 10px;
        }
        .container {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        #search {
            width: calc(100% - 110px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #searchButton {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        #searchButton:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        thead {
            background-color: #f8f8f8;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .edit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .edit:hover {
            background-color: #218838;
        }
        .delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .delete:hover {
            background-color: #c82333;
        }
        #createUser {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        #createUser:hover {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <div class="logosidebar">
            <img src="img/EspaçoCon__1_-removebg-preview.png" alt="Logo">
        </div>
        <a href="admin_home.php" onclick="navigate('Painel')"><i class="fa-solid fa-chart-line"></i> Painel</a>
        <a href="user.php" onclick="navigate('Usuarios')"><i class="fa-solid fa-users"></i> Usuários</a>
        <a href="ajuda.php" onclick="navigate('Ajuda')"><i class="fa-solid fa-question"></i> Ajuda</a>
        <a href="relatorio.php" onclick="navigate('Reservas')"><i class="fa-solid fa-newspaper"></i> Relatórios</a>
        <a href="salas.php"><i class="fa-solid fa-gear"></i> Configuração Salas</a>
        <a href="?logout=true"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
    </div>
    <div class="container">
        <input type="text" id="search" placeholder="Pesquisar usuários...">
        <button id="searchButton">Pesquisar</button>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Apartamento</th>
                    <th>CPF</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php
                $users = getAllUsers($conn);
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>{$user['nome']}</td>";
                    echo "<td>{$user['apartamento']}</td>";
                    echo "<td>{$user['cpf']}</td>";
                    echo "<td>";
                    echo "<button class='edit' data-id='{$user['id']}'>Editar</button>";
                    echo "<button class='delete' data-id='{$user['id']}'>Excluir</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <button id="createUser">Criar Novo Usuário</button>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Criar Novo Usuário</h2>
            <form id="userForm" method="POST">
                <input type="hidden" id="userId" name="userId">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome" required>
                </div>
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" placeholder="Digite o CPF" required>
                </div>
                <div class="form-group">
                    <label for="numero">Número de Telefone:</label>
                    <input type="text" id="numero" name="numero" placeholder="Digite o número de telefone" required>
                </div>
                <div class="form-group">
                    <label for="apartamento">Apartamento:</label>
                    <input type="text" id="apartamento" name="apartamento" placeholder="Digite o apartamento" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite a senha" required>
                </div>
                <button type="submit" id="saveUser" name="action" value="save">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        let currentEditRow = null;

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const searchUsers = debounce(function() {
            const searchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        match = true;
                    }
                });
                row.style.display = match ? '' : 'none';
            });
        }, 300);

        document.getElementById('search').addEventListener('input', searchUsers);

        function openModal() {
            document.getElementById('userModal').style.display = "block";
            document.getElementById('nome').value = '';
            document.getElementById('cpf').value = '';
            document.getElementById('numero').value = '';
            document.getElementById('apartamento').value = '';
            document.getElementById('senha').value = '';
            document.getElementById('userId').value = '';
            currentEditRow = null;
        }

        document.getElementById('createUser').addEventListener('click', function() {
            openModal();
        });

        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            document.getElementById('userModal').style.display = "none";
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById('userModal')) {
                document.getElementById('userModal').style.display = "none";
            }
        };

        function editUser() {
            const userId = this.getAttribute('data-id');
            currentEditRow = this.parentElement.parentElement;
            document.getElementById('userId').value = userId;
            document.getElementById('nome').value = currentEditRow.cells[0].textContent.trim();
            document.getElementById('apartamento').value = currentEditRow.cells[1].textContent.trim();
            document.getElementById('cpf').value = currentEditRow.cells[2].textContent.trim();
            document.getElementById('senha').value = '';
            document.getElementById('numero').value = ''; 
            openModal();
        }

        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', editUser);
        });

        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                if (confirm("Tem certeza que deseja excluir este usuário?")) {
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.style.display = 'none';
                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'id';
                    inputId.value = userId;
                    form.appendChild(inputId);
                    const action = document.createElement('input');
                    action.type = 'hidden';
                    action.name = 'action';
                    action.value = 'delete';
                    form.appendChild(action);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
