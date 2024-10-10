<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sala_id']) && isset($_POST['data']) && isset($_POST['hora_inicio']) && isset($_POST['hora_fim'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tcc";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO reservas (area_comum_id, data, horario_inicio, horario_fim, usuario_id) VALUES (:area_comum_id, :data, :hora_inicio, :hora_fim, :usuario_id)");
        
        $salaId = $_POST['sala_id'];
        $data = $_POST['data'];
        $horaInicio = $_POST['hora_inicio'];
        $horaFim = $_POST['hora_fim'];
        $usuarioId = 1; // Substitua pelo ID do usuário logado

        $stmt->execute(array(
            ':area_comum_id' => $salaId,
            ':data' => $data,
            ':hora_inicio' => $horaInicio,
            ':hora_fim' => $horaFim,
            ':usuario_id' => $usuarioId
        ));

        header('Content-Type: application/json');
        echo json_encode(array('success' => true));
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => $e->getMessage()));
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Dados inválidos.'));
}
?>
