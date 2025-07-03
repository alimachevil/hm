<?php
// api/update_cliente.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id']) || empty($input['nombre'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (ID o Nombre).']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Clientes SET nombre = ?, telefono = ? WHERE id = ?");
    $stmt->execute([
        $input['nombre'],
        $input['telefono'],
        $input['id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente actualizado exitosamente.']);
    } else {
        echo json_encode(['status' => 'info', 'message' => 'No se realizaron cambios.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>