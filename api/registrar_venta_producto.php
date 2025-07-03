<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['habitacion_id']) || empty($input['productos'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
    exit;
}

$pdo->beginTransaction();
try {
    // 1. Obtener la ocupación_id a partir de la habitacion_id
    $stmt_ocu = $pdo->prepare("SELECT id FROM Ocupaciones WHERE habitacion_id = ? AND activa = 1");
    $stmt_ocu->execute([$input['habitacion_id']]);
    $ocupacion_id = $stmt_ocu->fetchColumn();
    if (!$ocupacion_id) { throw new Exception('No se encontró una ocupación activa para esta habitación.'); }
    
    // 2. Crear el registro principal en la tabla 'Ventas'
    $pago_pendiente = $input['monto_pagado'] < $input['monto_total'];
    $stmt_venta = $pdo->prepare("INSERT INTO Ventas (ocupacion_id, monto_total, monto_pagado, pago_pendiente) VALUES (?, ?, ?, ?)");
    $stmt_venta->execute([$ocupacion_id, $input['monto_total'], $input['monto_pagado'], $pago_pendiente]);
    $venta_id = $pdo->lastInsertId(); // Obtenemos el ID de la venta que acabamos de crear

    // 3. Recorrer cada producto del carrito e insertarlo en 'Venta_Detalles'
    foreach ($input['productos'] as $prod) {
        // Insertar el detalle de la venta
        $stmt_detalle = $pdo->prepare("INSERT INTO Venta_Detalles (venta_id, producto_id, cantidad_vendida, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_detalle->execute([
            $venta_id,
            $prod['id'],
            $prod['cantidad'],
            $prod['precio'],
            $prod['cantidad'] * $prod['precio']
        ]);

        // Actualizar el stock del producto
        $stmt_stock = $pdo->prepare("UPDATE Productos SET stock = stock - ? WHERE id = ?");
        $stmt_stock->execute([$prod['cantidad'], $prod['id']]);

        // Registrar en el historial de stock
        $stmt_log = $pdo->prepare("INSERT INTO Historial_Stock (producto_id, stock_anterior, stock_nuevo, cambio, motivo) SELECT ?, stock + ?, stock, ?, ? FROM Productos WHERE id = ?");
        $motivo = "Venta a Hab. " . $input['habitacion_id'];
        $stmt_log->execute([$prod['id'], $prod['cantidad'], -$prod['cantidad'], $motivo, $prod['id']]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Venta registrada exitosamente.']);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>