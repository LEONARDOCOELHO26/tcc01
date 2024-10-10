<?php
// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tcc";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Processamento do formulário de criação ou edição de área comum
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    if ($action == "save") {
        $id = $_POST["areaId"];
        $nome = $_POST["nome"];
        $capacidade = $_POST["capacidade"];
        $status = $_POST["status"];
        $horario_inicio = $_POST["horario_inicio"];
        $horario_fim = $_POST["horario_fim"];
        $regras = $_POST["regras"];
        $foto = '';

        // Verifica se os campos obrigatórios estão preenchidos
        if (!empty($nome) && !empty($capacidade) && !empty($status) && !empty($horario_inicio) && !empty($horario_fim) && !empty($regras)) {
            // Verifica se foi enviado um arquivo de imagem
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto_temp = $_FILES['foto']['tmp_name'];
                $foto_nome = $_FILES['foto']['name'];
                $foto_extensao = pathinfo($foto_nome, PATHINFO_EXTENSION);
                $foto = 'uploads/' . uniqid('area_') . '.' . $foto_extensao; // Nome único para a imagem
                
                // Move o arquivo para o diretório de uploads
                if (move_uploaded_file($foto_temp, $foto)) {
                    // Imagem foi carregada com sucesso
                } else {
                    echo "Erro ao carregar a imagem.";
                }
            }

            if (!empty($id)) {
                // Atualiza a área comum se o ID estiver presente
                $sql = "UPDATE areas_comuns SET nome=?, capacidade=?, status=?, horario_inicio=?, horario_fim=?, regras=?, foto=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisssssi", $nome, $capacidade, $status, $horario_inicio, $horario_fim, $regras, $foto, $id);
            } else {
                // Insere uma nova área comum
                $sql = "INSERT INTO areas_comuns (nome, capacidade, status, horario_inicio, horario_fim, regras, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisssss", $nome, $capacidade, $status, $horario_inicio, $horario_fim, $regras, $foto);
            }

            if ($stmt->execute()) {
                echo "Área comum salva com sucesso.";
            } else {
                echo "Erro ao salvar a área comum: " . $stmt->error;
            }
        } else {
            echo "Todos os campos são obrigatórios.";
        }
    } elseif ($action == "delete") {
        $id = $_POST["id"];
        $sql = "DELETE FROM areas_comuns WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Área comum excluída com sucesso.";
        } else {
            echo "Erro ao excluir a área comum: " . $stmt->error;
        }
    }
}

// Função para buscar todas as áreas comuns do banco de dados
function getAllAreasComuns($conn) {
    $sql = "SELECT id, nome, capacidade, status, horario_inicio, horario_fim, regras FROM areas_comuns";
    $result = $conn->query($sql);
    $areasComuns = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $areasComuns[] = $row;
        }
    }
    return $areasComuns;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Áreas Comuns</title>
    <script src="https://kit.fontawesome.com/45d9f78a17.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
        }

        .sidebar a:hover {
            background-color: #444;
        }

        .sidebar a.active {
            background-color: #007BFF;
        }

        .sidebar a {
            text-decoration: none;
        }

        .container {
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
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

        #createArea {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        #createArea:hover {
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

        .form-group input,
        .form-group select,
        .form-group textarea {
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
            <img src="img\EspaçoCon__1_-removebg-preview.png" alt="Logo">
        </div>
        <a href="admin_home.php" onclick="navigate('Painel')"><i class="fa-solid fa-chart-line"></i> Painel</a>
        <a href="user.php" onclick="navigate('Usuarios')"><i class="fa-solid fa-users"></i> Usuários</a>
        <a href="ajuda.php" onclick="navigate('Ajuda')"><i class="fa-solid fa-question"></i> Ajuda</a>
        <a href="relatorio.php" onclick="navigate('Reservas')"><i class="fa-solid fa-newspaper"></i> Relatórios</a>
        <a href="salas.php"><i class="fa-solid fa-gear"></i> Configuração Salas</a>
        <a href="?logout=true"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
    </div>
    <div class="container">
        <input type="text" id="search" placeholder="Pesquisar áreas comuns...">
        <button id="searchButton">Pesquisar</button>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Capacidade</th>
                    <th>Status</th>
                    <th>Horário de Funcionamento</th>
                    <th>Regras</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="areasComunsTable">
                <?php
                // Busca todas as áreas comuns do banco de dados
                $areasComuns = getAllAreasComuns($conn);
                foreach ($areasComuns as $area) {
                    echo "<tr>";
                    echo "<td>{$area['nome']}</td>";
                    echo "<td>{$area['capacidade']}</td>";
                    echo "<td>{$area['status']}</td>";
                    echo "<td>{$area['horario_inicio']} - {$area['horario_fim']}</td>";
                    echo "<td>{$area['regras']}</td>";
                    echo "<td>";
                    echo "<button class='edit' data-id='{$area['id']}'>Editar</button>";
                    echo "<button class='delete' data-id='{$area['id']}'>Excluir</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <button id="createArea">Criar Nova Área Comum</button>
    </div>

    <!-- Modal de Criação/Edição -->
    <div id="areaModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Criar Nova Área Comum</h2>
            <form id="areaForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="areaId" name="areaId">
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome" required>
                </div>
                <div class="form-group">
                    <label for="capacidade">Capacidade:</label>
                    <input type="number" id="capacidade" name="capacidade" placeholder="Digite a capacidade" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Ativa">Ativa</option>
                        <option value="Inativa">Inativa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="horario_inicio">Horário de Funcionamento - Início:</label>
                    <input type="time" id="horario_inicio" name="horario_inicio" required>
                </div>
                <div class="form-group">
                    <label for="horario_fim">Horário de Funcionamento - Fim:</label>
                    <input type="time" id="horario_fim" name="horario_fim" required>
                </div>
                <div class="form-group">
                    <label for="regras">Regras:</label>
                    <textarea id="regras" name="regras" placeholder="Digite as regras" required></textarea>
                </div>
                <button type="submit" id="saveArea" name="action" value="save">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        let currentEditRow = null;

        // Função de debounce para limitar a frequência das chamadas da função
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Função melhorada de busca que pesquisa em todas as colunas da tabela
        const searchAreasComuns = debounce(function() {
            const searchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#areasComunsTable tr');
            
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

        // Adiciona listener para o evento de input no campo de pesquisa
        document.getElementById('search').addEventListener('input', searchAreasComuns);

        // Função para exibir o modal para criação ou edição de área comum
        function openModal() {
            document.getElementById('areaModal').style.display = "block";
            document.getElementById('nome').value = '';
            document.getElementById('capacidade').value = '';
            document.getElementById('status').value = 'Ativa';
            document.getElementById('horario_inicio').value = '';
            document.getElementById('horario_fim').value = '';
            document.getElementById('regras').value = '';
            currentEditRow = null;
        }

        // Adiciona listener para o botão de criar nova área comum
        document.getElementById('createArea').addEventListener('click', function() {
            openModal();
        });

        // Adiciona listener para fechar o modal
        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            document.getElementById('areaModal').style.display = "none";
        });

        // Adiciona listener para fechar o modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target == document.getElementById('areaModal')) {
                document.getElementById('areaModal').style.display = "none";
            }
        };

        // Função para editar uma área comum
        function editArea() {
            const areaId = this.getAttribute('data-id');
            currentEditRow = this.parentElement.parentElement;
            document.getElementById('areaId').value = areaId;
            document.getElementById('nome').value = currentEditRow.cells[0].textContent.trim();
            document.getElementById('capacidade').value = currentEditRow.cells[1].textContent.trim();
            document.getElementById('status').value = currentEditRow.cells[2].textContent.trim();
            const horarios = currentEditRow.cells[3].textContent.trim().split(' - ');
            document.getElementById('horario_inicio').value = horarios[0];
            document.getElementById('horario_fim').value = horarios[1];
            document.getElementById('regras').value = currentEditRow.cells[4].textContent.trim();
            openModal();
        }

        // Adiciona listeners para os botões de editar
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', editArea);
        });

        // Adiciona listener para os botões de deletar
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                const areaId = this.getAttribute('data-id');
                if (confirm("Tem certeza que deseja excluir esta área comum?")) {
                    // Cria um formulário dinamicamente para enviar os dados via POST
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.style.display = 'none';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    input.value = areaId;
                    form.appendChild(input);
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
