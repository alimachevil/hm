<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$periodo = $_GET['periodo'] ?? 'diario';
$date_condition = "";

switch ($periodo) {
    case 'semanal':
        // Desde el lunes de esta semana hasta hoy
        $date_condition = "AND o.fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())) DAY) AND o.fecha_inicio <= CURDATE()";
        $date_condition_ventas = "AND v.fecha_venta >= DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())) DAY) AND v.fecha_venta <= CURDATE()";
        break;
    case 'mensual':
        // Desde el primer día de este mes hasta hoy
        $date_condition = "AND o.fecha_inicio >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND o.fecha_inicio <= CURDATE()";
        $date_condition_ventas = "AND v.fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND v.fecha_venta <= CURDATE()";
        break;
    case 'diario':
    default:
        // Solo hoy
        $date_condition = "AND DATE(o.fecha_inicio) = CURDATE()";
        $date_condition_ventas = "AND DATE(v.fecha_venta) = CURDATE()";
        break;
}

try {
    $response = [];

    // 1. KPIs
    $kpis = [];
    $stmt = $pdo->query("SELECT SUM(monto_total) FROM Ocupaciones o WHERE 1=1 $date_condition");
    $kpis['ingresos'] = $stmt->fetchColumn() ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(id) FROM Ocupaciones o WHERE 1=1 $date_condition");
    $kpis['ocupaciones'] = $stmt->fetchColumn() ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(DISTINCT cliente_id) FROM Ocupaciones o WHERE 1=1 $date_condition");
    $kpis['clientes'] = $stmt->fetchColumn() ?? 0;

    $stmt = $pdo->query("SELECT SUM(vd.cantidad_vendida) FROM Venta_Detalles vd JOIN Ventas v ON vd.venta_id = v.id WHERE 1=1 $date_condition_ventas");
    $kpis['productos'] = $stmt->fetchColumn() ?? 0;

    $response['kpis'] = $kpis;

    // 2. Datos para el Gráfico (Ingresos por día)
    $chart_data = ['labels' => [], 'data' => []];
    $stmt_chart = $pdo->query("
        SELECT DATE(fecha_inicio) as dia, SUM(monto_total) as total_dia
        FROM Ocupaciones o
        WHERE 1=1 $date_condition
        GROUP BY dia
        ORDER BY dia ASC
    ");
    while($row = $stmt_chart->fetch()){
        $chart_data['labels'][] = date("d/m", strtotime($row['dia']));
        $chart_data['data'][] = $row['total_dia'];
    }
    $response['chart_data'] = $chart_data;

    // 3. Top 5 Habitaciones más solicitadas
    $top_rooms = [];
    $stmt_rooms = $pdo->query("
        SELECT habitacion_id, COUNT(id) as total_ocupaciones
        FROM Ocupaciones o
        WHERE 1=1 $date_condition
        GROUP BY habitacion_id
        ORDER BY total_ocupaciones DESC
        LIMIT 5
    ");
    $response['top_rooms'] = $stmt_rooms->fetchAll();

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>