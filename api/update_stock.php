<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id']) || !isset($input['stock_nuevo'])) { exit; }

$pdo->beginTransaction();
try {
    $stmt_old = $pdo->prepare("SELECT stock FROM Productos WHERE id = ?");
    $stmt_old->execute([$input['id']]);
    $stock_anterior = $stmt_old->fetchColumn();

    $stmt_update = $pdo->prepare("UPDATE Productos SET stock = ? WHERE id = ?");
    $stmt_update->execute([$input['stock_nuevo'], $input['id']]);

    $stmt_log = $pdo->prepare("INSERT INTO Historial_Stock (producto_id, stock_anterior, stock_nuevo, cambio, motivo) VALUES (?, ?, ?, ?, 'Ajuste manual')");
    $stmt_log->execute([$input['id'], $stock_anterior, $input['stock_nuevo'], $input['stock_nuevo'] - $stock_anterior]);

    $pdo->commit();
    echo json_encode(['status' => 'success', 'nuevo_stock' => $input['stock_nuevo']]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>