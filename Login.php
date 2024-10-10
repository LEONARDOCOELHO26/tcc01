<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dados de conexão com o banco de dados
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "tcc";

// Criar conexão
$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$error = '';

// Verifica se o formulário de login foi enviado
if (isset($_POST["login"])) {
    $login = trim($_POST["email"]);
    $password = $_POST["password"];

    // Determina a tabela com base no tipo de login (email ou número)
    $stmt = is_numeric($login) ? 
        $conn->prepare("SELECT id, senha FROM usuarios WHERE cpf = ?") :
        $conn->prepare("SELECT id, senha FROM Administradores WHERE email = ?");

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['senha'])) {
            $_SESSION["Login"] = true;
            $_SESSION["UserID"] = $user['id'];
            $_SESSION["Access"] = is_numeric($login) ? "user" : "admin";
            header("Location: " . (is_numeric($login) ? "user_home.php" : "admin_home.php"));
            exit();
        } else {
            $error = "Senha inválida";
        }
    } else {
        $error = "Usuário não encontrado";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .loginbox {
            width: 90%;
            max-width: 400px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logintitle {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .logininput {
            width: 100%;
            margin: 1vh 0;
            padding: 1.5vh;
            display: block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .loginlabel {
            font-size: 15px;
            text-align: left;
            width: 100%;
            margin: 1vh 0 0.5vh;
            display: block;
            font-weight: bold;
        }

        .imgbox {
            background-color: beige;
            border: 1px solid #ccc;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            margin: 2vh auto;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .imgbox img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .loginbutton {
            width: 100%;
            height: 6vh;
            margin: 3vh 0;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .loginbutton:hover {
            background-color: #0056b3;
        }

        .loginlinks {
            margin: 2vh 0;
            font-size: 14px;
        }

        .loginlinks a {
            color: #007BFF;
            text-decoration: none;
            margin: 0 1vh;
        }

        .loginlinks a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form method="post" action="">
        <div class="loginbox">
            <div class="imgbox">
                <img src="img\EspaçoCon__1_-removebg-preview.png" alt="Imagem do perfil">
            </div>
            <span class="logintitle">Login</span>
            <label for="email" class="loginlabel">E-mail ou ID</label>
            <input name="email" id="email" type="text" class="logininput" placeholder="Digite seu e-mail ou ID" required>
            <label for="password" class="loginlabel">Senha</label>
            <input name="password" id="password" type="password" class="logininput" placeholder="Digite sua senha" required>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="loginlinks">  
                <a href="Cadastro.html">Ainda não tem cadastro?</a>
            </div>
            <button type="submit" name="login" class="loginbutton">Logar</button>
        </div>
    </form>
</body>
</html>
