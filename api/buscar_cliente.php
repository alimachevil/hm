<?php
// api/buscar_cliente.php
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
    // Preparamos una consulta segura para evitar inyección SQL
    $stmt = $pdo->prepare("SELECT nombre, telefono FROM Clientes WHERE documento_identidad = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        // Si se encontró el cliente, devolvemos sus datos
        echo json_encode([
            'found' => true,
            'nombre' => $cliente['nombre'],
            'telefono' => $cliente['telefono']
        ]);
    } else {
        // Si no se encontró, lo indicamos en la respuesta
        echo json_encode(['found' => false]);
    }

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['found' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}

?>