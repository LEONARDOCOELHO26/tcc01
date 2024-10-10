<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se a solicitação de logout foi feita
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    // Remove todas as variáveis de sessão
    session_unset();
    // Destrói a sessão
    session_destroy();
    // Redireciona para a página de login
    header("Location: index.php");
    exit(); // Certifique-se de sair após redirecionar
}

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

// Função para buscar todas as reservas do banco de dados
function getAllReservas($conn) {
    $sql = "SELECT r.id, a.nome as area_nome, r.data, r.horario_inicio, r.horario_fim, u.nome as usuario_nome 
            FROM reservas r 
            JOIN areas_comuns a ON r.area_comum_id = a.id 
            JOIN usuarios u ON r.usuario_id = u.id";
    $result = $conn->query($sql);
    $reservas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reservas[] = $row;
        }
    }
    return $reservas;
}

$reservas = getAllReservas($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://kit.fontawesome.com/45d9f78a17.js" crossorigin="anonymous"></script>
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
        <h1>Lista de Reservas</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Área Comum</th>
                    <th>Data</th>
                    <th>Horário de Início</th>
                    <th>Horário de Fim</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($reservas)) {
                    foreach ($reservas as $reserva) {
                        echo "<tr>";
                        echo "<td>{$reserva['id']}</td>";
                        echo "<td>{$reserva['area_nome']}</td>";
                        echo "<td>{$reserva['data']}</td>";
                        echo "<td>{$reserva['horario_inicio']}</td>";
                        echo "<td>{$reserva['horario_fim']}</td>";
                        echo "<td>{$reserva['usuario_nome']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhuma reserva encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
