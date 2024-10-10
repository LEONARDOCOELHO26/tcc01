<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Salas</title>
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
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Conexão com o banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tcc";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica conexão
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Verifica se o formulário de reserva foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sala_id'])) {
            $sala_id = $_POST['sala_id'];
            $data = $_POST['data'];
            $horario_inicio = $_POST['horario_inicio'];
            $horario_fim = $_POST['horario_fim'];
            $usuario_id = 3; // Substitua pelo ID do usuário logado ou obtenha dinamicamente

            // Verifica se o usuário com o usuário_id existe na tabela usuários
            $check_user_sql = "SELECT id FROM usuarios WHERE id = '$usuario_id'";
            $result_user = $conn->query($check_user_sql);

            if ($result_user->num_rows > 0) {
                // Insere a reserva no banco de dados
                $insert_sql = "INSERT INTO reservas (area_comum_id, data, horario_inicio, horario_fim, usuario_id) VALUES ('$sala_id', '$data', '$horario_inicio', '$horario_fim', '$usuario_id')";

                if ($conn->query($insert_sql) === TRUE) {
                    echo "<p>Reserva realizada com sucesso!</p>";
                } else {
                    echo "<p>Erro ao realizar a reserva: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Usuário não encontrado.</p>";
            }
        }

        // Consulta para obter salas disponíveis
        $sql = "SELECT * FROM areas_comuns WHERE status = 'Ativa'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Exibir as salas disponíveis
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

        $conn->close();
        ?>
    </div>
</body>
</html>
