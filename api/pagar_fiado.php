<?php
// api/pagar_fiado.php (CORREGIDO)
header('Content-Type: application/json');
require_once '../config/db_connect.php';
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['venta_id']) || empty($input['monto'])) { exit; }

try {
    $stmt_get = $pdo->prepare("SELECT monto_total, monto_pagado FROM Ventas WHERE id = ?");
    $stmt_get->execute([$input['venta_id']]);
    $venta = $stmt_get->fetch();

    $nuevo_monto_pagado = $venta['monto_pagado'] + $input['monto'];
    $pago_pendiente = $nuevo_monto_pagado < $venta['monto_total'];

    $stmt_update = $pdo->prepare("UPDATE Ventas SET monto_pagado = ?, pago_pendiente = ? WHERE id = ?");
    $stmt_update->execute([$nuevo_monto_pagado, $pago_pendiente, $input['venta_id']]);

    echo json_encode(['status' => 'success', 'deuda_saldada' => !$pago_pendiente]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>