<?php
// api/liberar_habitacion.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['ocupacion_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID de ocupación no proporcionado.']);
    exit;
}

try {
    // Preparamos la consulta para desactivar la ocupación (activa = 0)
    $stmt = $pdo->prepare("UPDATE Ocupaciones SET activa = 0 WHERE id = ? AND activa = 1");
    $stmt->execute([$input['ocupacion_id']]);

    // rowCount() nos dice cuántas filas fueron afectadas. Si es > 0, funcionó.
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Habitación liberada exitosamente.']);
    } else {
        // Si no se afectaron filas, es porque la ocupación no existía o ya estaba inactiva.
        echo json_encode(['status' => 'warning', 'message' => 'La habitación ya se encontraba libre.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>