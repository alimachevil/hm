<?php
// api/buscar_cliente.php (ACTUALIZADO para incluir 'origen')
header('Content-Type: application/json');
require_once '../config/db_connect.php';

// Validamos que se haya enviado el parámetro 'dni' por GET
if (!isset($_GET['dni']) || empty($_GET['dni'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['found' => false, 'message' => 'No se proporcionó un DNI.']);
    exit;
}

$dni = $_GET['dni'];

try {
    // --- MODIFICACIÓN CLAVE AQUÍ ---
    // Añadimos la columna 'origen' a la lista de campos que seleccionamos.
    $stmt = $pdo->prepare("SELECT nombre, telefono, origen FROM Clientes WHERE documento_identidad = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        // Si se encontró el cliente, ahora devolvemos también su origen en la respuesta JSON.
        echo json_encode([
            'found' => true,
            'nombre' => $cliente['nombre'],
            'telefono' => $cliente['telefono'],
            'origen' => $cliente['origen'] // <-- Se añade el nuevo dato a la respuesta
        ]);
    } else {
        // Si no se encontró, la respuesta no cambia.
        echo json_encode(['found' => false]);
    }

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['found' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>