<?php
// api/registrar_ocupacion.php (VERSIÓN DEFINITIVA Y ROBUSTA)
header('Content-Type: application/json');
require_once '../config/db_connect.php';

// Leemos el "paquete" JSON que nos envió el JavaScript
$input = json_decode(file_get_contents('php://input'), true);

// Verificación inicial de datos indispensables
if (empty($input['numero_habitacion']) || empty($input['cliente_dni']) || empty($input['cliente_nombre'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (habitación, DNI o nombre).']);
    exit;
}

// Iniciamos una transacción. Si algo falla, se deshace todo.
$pdo->beginTransaction();

try {
    // 1. Recolectamos los datos opcionales de forma segura, asignando NULL si no vienen.
    $cliente_origen = $input['cliente_origen'] ?? null;
    $taxi_info = $input['taxi_info'] ?? null;
    $taxi_comision = !empty($input['taxi_comision']) ? $input['taxi_comision'] : 0.00;

    // 2. Comprobar que la habitación no haya sido ocupada mientras llenábamos el formulario.
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM Ocupaciones WHERE habitacion_id = :habitacion_id AND activa = 1");
    $stmt_check->execute([':habitacion_id' => $input['numero_habitacion']]);
    if ($stmt_check->fetchColumn() > 0) {
        throw new Exception('La habitación ya no está disponible. Por favor, recargue la página.');
    }

    // 3. Gestionar al Cliente: Lo buscamos y si no existe, lo creamos.
    $stmt_cliente = $pdo->prepare("SELECT id FROM Clientes WHERE documento_identidad = :dni");
    $stmt_cliente->execute([':dni' => $input['cliente_dni']]);
    $cliente_id = $stmt_cliente->fetchColumn();

    if (!$cliente_id) { // Si el cliente es nuevo
        $stmt_insert_cliente = $pdo->prepare(
            "INSERT INTO Clientes (documento_identidad, nombre, telefono, origen) VALUES (:dni, :nombre, :telefono, :origen)"
        );
        $stmt_insert_cliente->execute([
            ':dni' => $input['cliente_dni'],
            ':nombre' => $input['cliente_nombre'],
            ':telefono' => $input['cliente_celular'],
            ':origen' => $cliente_origen // Guardamos el nuevo campo
        ]);
        $cliente_id = $pdo->lastInsertId();
    } else { // Si el cliente ya existe, actualizamos su origen si se proporcionó uno.
        if (!empty($cliente_origen)) {
            $stmt_update_cliente = $pdo->prepare("UPDATE Clientes SET origen = :origen WHERE id = :id");
            $stmt_update_cliente->execute([':origen' => $cliente_origen, ':id' => $cliente_id]);
        }
    }

    // 4. Construir la fecha y hora de inicio usando el valor del formulario.
    $fecha_actual = date('Y-m-d');
    $hora_ingreso = !empty($input['hora_ingreso']) ? $input['hora_ingreso'] : date('H:i:s');
    $fecha_inicio_completa = $fecha_actual . ' ' . $hora_ingreso;

    // 5. Preparar la consulta para insertar la Ocupación usando marcadores con nombre.
    $sql_insert = "
        INSERT INTO Ocupaciones (
            cliente_id, habitacion_id, fecha_inicio, estadia_dias, monto_por_dia, 
            monto_adicional_descuento, monto_total, activa, taxi_info, taxi_comision
        ) VALUES (
            :cliente_id, :habitacion_id, :fecha_inicio, :estadia_dias, :monto_por_dia, 
            :adicional_descuento, :monto_total, 1, :taxi_info, :taxi_comision
        )
    ";
    $stmt_ocupacion = $pdo->prepare($sql_insert);
    
    // 6. Ejecutar la consulta pasando un array asociativo. El orden no importa, es a prueba de errores.
    $stmt_ocupacion->execute([
        ':cliente_id' => $cliente_id,
        ':habitacion_id' => $input['numero_habitacion'],
        ':fecha_inicio' => $fecha_inicio_completa, // Guarda la hora correcta
        ':estadia_dias' => $input['estadia_dias'],
        ':monto_por_dia' => $input['costo_dia'],
        ':adicional_descuento' => $input['adicional_descuento'],
        ':monto_total' => $input['monto_total'],
        ':taxi_info' => $taxi_info,             // Guarda la info del taxi
        ':taxi_comision' => $taxi_comision      // Guarda la comisión
    ]);

    // 7. Si todo salió bien, confirmamos los cambios en la base de datos.
    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Ocupación registrada exitosamente.']);

} catch (Exception $e) {
    // Si algo falló en cualquier punto, revertimos todos los cambios.
    $pdo->rollBack();
    http_response_code(500); // Internal Server Error
    // Devolvemos el mensaje de error exacto para facilitar la depuración
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>