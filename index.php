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
    header("Location: login.php");
    exit(); // Certifique-se de sair após redirecionar
}

// Verifica se o usuário já está logado
if (isset($_SESSION["Login"]) && $_SESSION["Login"] === true) {
    $access = $_SESSION["Access"];
    header("Location: {$access}_home.php");
    exit();
} else {
    include("login.php");
}
?>
