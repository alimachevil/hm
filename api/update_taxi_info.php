<?php
// api/update_taxi_info.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['ocupacion_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Falta el ID de la ocupación.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Ocupaciones SET taxi_info = ?, taxi_comision = ? WHERE id = ?");
    $stmt->execute([
        $input['taxi_info'] ?? null,
        $input['taxi_comision'] ?? 0.00,
        $input['ocupacion_id']
    ]);
    echo json_encode(['status' => 'success', 'message' => 'Datos del taxi actualizados.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>