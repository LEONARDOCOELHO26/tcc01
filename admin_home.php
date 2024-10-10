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
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para contar registros em uma tabela
function countRecords($conn, $table) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

$adminCount = countRecords($conn, 'administradores');
$areaCount = countRecords($conn, 'areas_comuns');
$historicoCount = countRecords($conn, 'historicos');
$reservaCount = countRecords($conn, 'reservas');
$usuarioCount = countRecords($conn, 'usuarios');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .box {
            border: 1px solid black;
            font-size: 1.5rem;
            padding: 5px;
            margin: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
        }
        .box span {
            margin-left: 20px;
        }
        .box .count {
            font-size: 2rem;
            margin-right: 20px;
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
        <h1>Dashboard</h1>
        
        <div class="box">
            <span>Áreas Comuns</span>
            <span class="count"><?php echo $areaCount; ?></span>
        </div>
        
        <div class="box">
            <span>Reservas</span>
            <span class="count"><?php echo $reservaCount; ?></span>
        </div>
        <div class="box">
            <span>Usuários</span>
            <span class="count"><?php echo $usuarioCount; ?></span>
        </div>
    </div>
</body>
</html>
