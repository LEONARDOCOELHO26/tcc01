<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tcc";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Receber dados do formulário
$condominio = $_POST['condominio'];
$sindico = $_POST['sindico'];
$cnpj = $_POST['cnpj'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// Verificar se as senhas coincidem
if ($senha !== $confirmar_senha) {
    echo "As senhas não coincidem.";
    exit();
}

// Hash da senha
$senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

// Inserir dados na tabela
$sql = "INSERT INTO Administradores (condominio, cnpj, telefone, email, senha)
        VALUES ('$condominio', '$cnpj', '$telefone', '$email', '$senha_hashed')";

if ($conn->query($sql) === TRUE) {
    echo "Cadastro realizado com sucesso!";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

// Fechar conexão
$conn->close();
?>
