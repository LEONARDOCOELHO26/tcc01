<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se o usuário está logado, caso contrário redireciona para a página de login
if (!isset($_SESSION['Login']) || $_SESSION['Login'] !== true) {
    header("Location: index.php");
    exit();
}

// Pegando o ID e o nome do usuário logado
$usuario_id = $_SESSION['UserID'];
// Conexão com o banco de dados (substitua com suas credenciais)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tcc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se a solicitação de logout foi feita
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$error_message = '';
$success_message = '';

// Verifica se o formulário de reserva foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sala_id'])) {
    $sala_id = $_POST['sala_id'];
    $data = $_POST['data'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];

    // Verifica se o horário solicitado já está reservado
    $check_sql = "SELECT * FROM reservas WHERE area_comum_id = '$sala_id' AND data = '$data' AND (
                    (horario_inicio < '$horario_fim' AND horario_fim > '$horario_inicio')
                )";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $error_message = "Este horário já está reservado. Por favor, escolha outro horário.";
    } else {
        // Insere a reserva no banco de dados
        $insert_sql = "INSERT INTO reservas (area_comum_id, data, horario_inicio, horario_fim, usuario_id) VALUES ('$sala_id', '$data', '$horario_inicio', '$horario_fim', '$usuario_id')";

        if ($conn->query($insert_sql) === TRUE) {
            $success_message = "Reserva realizada com sucesso!";
        } else {
            $error_message = "Erro ao realizar a reserva: " . $conn->error;
        }
    }
}

// Verifica se a solicitação de cancelamento de reserva foi feita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelar_reserva_id'])) {
    $reserva_id = $_POST['cancelar_reserva_id'];

    // Deleta a reserva do banco de dados
    $delete_sql = "DELETE FROM reservas WHERE id = '$reserva_id' AND usuario_id = '$usuario_id'";

    if ($conn->query($delete_sql) === TRUE) {
        $success_message = "Reserva cancelada com sucesso!";
    } else {
        $error_message = "Erro ao cancelar a reserva: " . $conn->error;
    }
}

// Consulta para obter salas disponíveis
$sql = "SELECT * FROM areas_comuns WHERE status = 'Ativa'";
$result = $conn->query($sql);

// Consulta para obter as reservas do usuário logado
$reservas_sql = "SELECT r.*, a.nome AS sala_nome FROM reservas r INNER JOIN areas_comuns a ON r.area_comum_id = a.id WHERE r.usuario_id = '$usuario_id'";
$reservas_result = $conn->query($reservas_sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-"/>
    <title>Calendário</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
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
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
            display: flex;
            gap: 20px;
        }
        .salas, .reservas {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 48%;
        }
        .sala h2, .reservas h2 {
            margin-bottom: 10px;
        }
        .sala p, .reservas ul {
            margin-bottom: 10px;
        }
        .sala form {
            display: flex;
            flex-direction: column;
        }
        .sala form label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .sala form input[type="date"],
        .sala form input[type="time"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .sala form input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .sala form input[type="submit"]:hover {
            background-color: #218838;
        }
        .reservas ul {
            list-style: none;
            padding: 0;
        }
        .reservas ul li {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .reservas ul li p {
            margin: 0;
        }
        .reservas ul li form {
            display: inline;
        }
        .reservas ul li form button {
            background-color: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .reservas ul li form button:hover {
            background-color: #c82333;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logosidebar">
            <img src="img/EspaçoCon__1_-removebg-preview.png" alt="Logo">
        </div>
        <a href="#calendario" onclick="navigate('calendario')"><i class="far fa-calendar-alt"></i> Salas</a>
        <a href="?logout=true"><i class="fas fa-arrow-right-from-bracket"></i> Sair</a>
    </div>
    <div class="container">
        <div class="salas">
            <?php if (!empty($error_message)) : ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php elseif (!empty($success_message)) : ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='sala'>";
                    echo "<h2>Sala: " . $row["nome"] . "</h2>";
                    echo "<p>Capacidade: " . $row["capacidade"] . "</p>";
                    echo "<p>Horário de funcionamento: " . $row["horario_inicio"] . " às " . $row["horario_fim"] . "</p>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='sala_id' value='" . $row["id"] . "'>";
                    echo "<label>Data da reserva:</label>";
                    echo "<input type='date' name='data' required><br>";
                    echo "<label>Horário de início:</label>";
                    echo "<input type='time' name='horario_inicio' required><br>";
                    echo "<label>Horário de fim:</label>";
                    echo "<input type='time' name='horario_fim' required><br>";
                    echo "<input type='submit' value='Reservar'>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma sala disponível no momento.</p>";
            }
            ?>
        </div>
        <div class="reservas">
            <h2>Minhas Reservas</h2>
            <ul>
                <?php
                if ($reservas_result->num_rows > 0) {
                    while($reserva = $reservas_result->fetch_assoc()) {
                        echo "<li>";
                        echo "<p><strong>Sala:</strong> " . $reserva["sala_nome"] . "</p>";
                        echo "<p><strong>Data:</strong> " . $reserva["data"] . "</p>";
                        echo "<p><strong>Horário:</strong> " . $reserva["horario_inicio"] . " - " . $reserva["horario_fim"] . "</p>";
                        echo "<form method='post' onsubmit='return confirm(\"Tem certeza que deseja cancelar esta reserva?\");'>";
                        echo "<input type='hidden' name='cancelar_reserva_id' value='" . $reserva["id"] . "'>";
                        echo "<button type='submit'>Cancelar</button>";
                        echo "</form>";
                        echo "</li>";
                    }
                } else {
                    echo "<p>Você não tem reservas no momento.</p>";
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
