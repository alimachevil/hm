<?php
// api/registrar_pago.php (ACTUALIZADO)
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['ocupacion_id']) || !isset($input['monto_pagado']) || $input['monto_pagado'] <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Datos de pago inválidos.']);
    exit;
}

$pdo->beginTransaction();
try {
    $stmt_get_ocu = $pdo->prepare("SELECT cliente_id, monto_total FROM Ocupaciones WHERE id = ?");
    $stmt_get_ocu->execute([$input['ocupacion_id']]);
    $ocupacion = $stmt_get_ocu->fetch();
    if (!$ocupacion) { throw new Exception('Ocupación no encontrada.'); }

    $stmt_pagado = $pdo->prepare("SELECT SUM(monto_pagado) FROM Pagos WHERE ocupacion_id = ?");
    $stmt_pagado->execute([$input['ocupacion_id']]);
    $total_pagado_anterior = $stmt_pagado->fetchColumn() ?? 0;

    $nuevo_total_pagado = $total_pagado_anterior + $input['monto_pagado'];
    
    // LÓGICA CONDICIONAL PARA COMPROBANTE
    $comprobante_tipo = null;
    $comprobante_numero = null;
    $deuda_saldada = false;

    if ($nuevo_total_pagado >= $ocupacion['monto_total']) {
        $comprobante_tipo = $input['comprobante'] ?? null;
        $comprobante_numero = $input['numero_comprobante'] ?? null;
        $deuda_saldada = true;
    }

    $stmt_pago = $pdo->prepare(
        "INSERT INTO Pagos (ocupacion_id, cliente_id, monto_pagado, metodo_pago, comprobante, numero_comprobante, fecha_pago)
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt_pago->execute([
        $input['ocupacion_id'],
        $ocupacion['cliente_id'],
        $input['monto_pagado'],
        $input['metodo_pago'],
        $comprobante_tipo,
        $comprobante_numero
    ]);
    
    $pdo->commit();
    echo json_encode([
        'status' => 'success', 
        'message' => 'Pago registrado.',
        'nuevo_estado' => $deuda_saldada ? 'pagada' : 'pago-pendiente'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>