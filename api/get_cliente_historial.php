<?php
// api/get_cliente_historial.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de cliente inválido.']);
    exit;
}

$cliente_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.fecha_inicio,
            o.estadia_dias,
            o.monto_total,
            h.numero_habitacion,
            th.nombre AS tipo_habitacion,
            (SELECT SUM(p.monto_pagado) FROM Pagos p WHERE p.ocupacion_id = o.id) >= o.monto_total AS pagado_completo
        FROM Ocupaciones o
        JOIN Habitaciones h ON o.habitacion_id = h.numero_habitacion
        JOIN Tipos_Habitaciones th ON h.tipo_id = th.id
        WHERE o.cliente_id = ?
        ORDER BY o.fecha_inicio DESC
    ");
    $stmt->execute([$cliente_id]);
    $historial = $stmt->fetchAll();

    echo json_encode($historial);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>