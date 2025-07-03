<?php
// api/registrar_pago.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

// Validaciones
if (empty($input['ocupacion_id']) || !isset($input['monto_pagado']) || $input['monto_pagado'] <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Datos de pago inválidos.']);
    exit;
}

$pdo->beginTransaction();

try {
    // Paso 1: Obtener el cliente_id de la ocupación para registrarlo en el pago
    $stmt_get_ocupacion = $pdo->prepare("SELECT cliente_id, monto_total FROM Ocupaciones WHERE id = ?");
    $stmt_get_ocupacion->execute([$input['ocupacion_id']]);
    $ocupacion_data = $stmt_get_ocupacion->fetch();
    
    if (!$ocupacion_data) {
        throw new Exception('La ocupación no existe.');
    }

    // Paso 2: Insertar el nuevo pago
    $stmt_pago = $pdo->prepare("
        INSERT INTO Pagos (ocupacion_id, cliente_id, monto_pagado, metodo_pago, comprobante, fecha_pago)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt_pago->execute([
        $input['ocupacion_id'],
        $ocupacion_data['cliente_id'],
        $input['monto_pagado'],
        $input['metodo_pago'],
        $input['comprobante']
    ]);
    
    // Paso 3: Comprobar el nuevo saldo
    $stmt_saldo = $pdo->prepare("SELECT SUM(monto_pagado) as total_pagado FROM Pagos WHERE ocupacion_id = ?");
    $stmt_saldo->execute([$input['ocupacion_id']]);
    $total_pagado = $stmt_saldo->fetchColumn();

    $nuevo_estado = 'pago-pendiente';
    if ($total_pagado >= $ocupacion_data['monto_total']) {
        $nuevo_estado = 'pagada';
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Pago registrado.', 'nuevo_estado' => $nuevo_estado]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>