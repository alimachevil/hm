<?php
// api/get_habitacion_info.php

// Establecer la cabecera para indicar que la respuesta será en formato JSON
header('Content-Type: application/json');

// Incluir nuestro archivo de conexión a la base de datos
require_once '../config/db_connect.php';

// 1. Validar la entrada: Asegurarnos de que nos han pasado un número de habitación
if (!isset($_GET['numero']) || empty($_GET['numero'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Número de habitación no proporcionado.']);
    exit;
}

$numero_habitacion = $_GET['numero'];
$response = [];

try {
    // 2. Obtener los datos básicos de la habitación desde la tabla 'Habitaciones'
    $stmt_hab = $pdo->prepare("
        SELECT h.*, th.nombre as tipo_nombre 
        FROM Habitaciones h 
        JOIN Tipos_Habitaciones th ON h.tipo_id = th.id 
        WHERE h.numero_habitacion = ?
    ");
    $stmt_hab->execute([$numero_habitacion]);
    $habitacion = $stmt_hab->fetch();

    // Si la habitación no existe, devolvemos un error
    if (!$habitacion) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'La habitación no fue encontrada en la base de datos.']);
        exit;
    }
    $response['habitacion'] = $habitacion;

    // 3. Buscar si existe una ocupación ACTIVA para esa habitación
    $stmt_ocu = $pdo->prepare("
        SELECT o.*, c.nombre as cliente_nombre, c.documento_identidad as cliente_dni 
        FROM Ocupaciones o 
        JOIN Clientes c ON o.cliente_id = c.id 
        WHERE o.habitacion_id = ? AND o.activa = 1
    ");
    $stmt_ocu->execute([$numero_habitacion]);
    $ocupacion = $stmt_ocu->fetch();

    // 4. Determinar el estado final basado en si hay ocupación y si está pagada
    if ($ocupacion) {
        // Si hay una ocupación, la añadimos a la respuesta
        $response['ocupacion'] = $ocupacion;

        // Ahora, calculamos el total pagado para esa ocupación
        $stmt_pago = $pdo->prepare("SELECT SUM(monto_pagado) as total_pagado FROM Pagos WHERE ocupacion_id = ?");
        $stmt_pago->execute([$ocupacion['id']]);
        $pago = $stmt_pago->fetch();
        
        // El total pagado puede ser NULL si no hay pagos, así que lo convertimos a 0.00
        $response['pago'] = ['total_pagado' => $pago['total_pagado'] ?? 0.00];

        // Decidimos el estado final
        if ($response['pago']['total_pagado'] >= $ocupacion['monto_total']) {
            $response['estado'] = 'pagada';
        } else {
            $response['estado'] = 'pago-pendiente';
        }
    } else {
        // Si no se encontró una ocupación activa, el estado es 'disponible'
        $response['estado'] = 'disponible';
    }

    // 5. Devolver toda la información recopilada en formato JSON
    echo json_encode($response);

} catch (PDOException $e) {
    // Si ocurre un error en la base de datos durante la consulta
    http_response_code(500); // Internal Server Error
    // En producción, es mejor registrar este error en un log en lugar de mostrarlo
    echo json_encode(['error' => 'Error en el servidor al consultar la base de datos.', 'details' => $e->getMessage()]);
}
?>