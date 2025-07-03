<?php
// api/add_producto.php (VERSIÓN DEFINITIVA Y ROBUSTA)
header('Content-Type: application/json');
require_once '../config/db_connect.php';

// Leemos el "paquete" JSON que nos envió el JavaScript
$input = json_decode(file_get_contents('php://input'), true);

// --- Validación más robusta ---
// Verificamos que las claves existan y que el nombre no esté vacío después de quitar espacios.
$nombre = trim($input['nombre'] ?? '');
if (empty($nombre) || !isset($input['stock']) || !isset($input['precio'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'El nombre, stock y precio son requeridos.']);
    exit;
}
// Verificamos que stock y precio sean numéricos.
if (!is_numeric($input['stock']) || !is_numeric($input['precio'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'El stock y el precio deben ser valores numéricos.']);
    exit;
}


// Iniciamos una transacción. Si algo falla, se deshace todo.
$pdo->beginTransaction();

try {
    // 1. Insertar el nuevo producto en la tabla 'Productos'
    $stmt = $pdo->prepare("INSERT INTO Productos (nombre, stock, precio) VALUES (?, ?, ?)");
    $stmt->execute([
        $nombre,
        $input['stock'],
        $input['precio']
    ]);
    
    $nuevo_id = $pdo->lastInsertId();

    // 2. NUEVA LÓGICA: Registrar la creación en el historial de stock
    // Si se añade un producto con stock inicial, es un evento de inventario.
    if ($input['stock'] > 0) {
        $stmt_log = $pdo->prepare(
            "INSERT INTO Historial_Stock (producto_id, stock_anterior, stock_nuevo, cambio, motivo) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt_log->execute([
            $nuevo_id,
            0, // El stock anterior es 0 porque el producto no existía
            $input['stock'], // El nuevo stock es el stock inicial
            $input['stock'], // El cambio es la cantidad de stock inicial
            'Creación de producto (Stock inicial)' // Motivo claro
        ]);
    }
    
    // 3. Si todo fue bien, confirmamos la transacción
    $pdo->commit();

    // 4. Devolvemos una respuesta de éxito con los datos del nuevo producto
    echo json_encode([
        'status' => 'success', 
        'message' => 'Producto añadido exitosamente.',
        'nuevo_producto' => [
            'id' => $nuevo_id,
            'nombre' => $nombre,
            'stock' => $input['stock'],
            'precio' => $input['precio']
        ]
    ]);

} catch (Exception $e) {
    // Si algo falló, revertimos todos los cambios
    $pdo->rollBack();
    http_response_code(500); // Internal Server Error
    
    // Capturar error específico de nombre de producto duplicado
    if ($e->getCode() == 23000) {
        echo json_encode(['status' => 'error', 'message' => 'Error: Ya existe un producto con ese nombre.']);
    } else {
        // Para cualquier otro error, devolvemos el mensaje del sistema para depurar
        echo json_encode(['status' => 'error', 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
}
?>