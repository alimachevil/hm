<?php
// api/update_cliente.php (CORREGIDO PARA ACEPTAR 'origen')
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

// Mantenemos la validación original, ya que 'origen' puede ser opcional.
if (empty($input['id']) || empty($input['nombre'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (ID o Nombre).']);
    exit;
}

try {
    // MODIFICACIÓN CLAVE: Añadimos 'origen = ?' a la sentencia UPDATE.
    $stmt = $pdo->prepare("UPDATE Clientes SET nombre = ?, telefono = ?, origen = ? WHERE id = ?");
    
    // Y añadimos el valor correspondiente al array de execute().
    $stmt->execute([
        $input['nombre'],
        $input['telefono'],
        $input['origen'],   // <-- Aquí está el nuevo campo
        $input['id']
    ]);

    // La lógica de respuesta se mantiene igual.
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente actualizado exitosamente.']);
    } else {
        echo json_encode(['status' => 'info', 'message' => 'No se realizaron cambios en los datos.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>