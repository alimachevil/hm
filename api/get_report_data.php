<?php
// api/get_report_data.php (VERSIÓN ROBUSTA)
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$periodo = $_GET['periodo'] ?? 'diario';
$condition_ocupaciones = "";
$condition_ventas = "";

// Definimos los rangos de fechas de forma segura
switch ($periodo) {
    case 'semanal':
        // BETWEEN es inclusivo, por lo que no necesitamos el "+ INTERVAL 1 DAY"
        $condition_ocupaciones = "WHERE o.fecha_inicio BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
        $condition_ventas = "WHERE v.fecha_venta BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
        break;
    case 'mensual':
        $condition_ocupaciones = "WHERE o.fecha_inicio >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND o.fecha_inicio <= CURDATE()";
        $condition_ventas = "WHERE v.fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND v.fecha_venta <= CURDATE()";
        break;
    case 'diario':
    default:
        $condition_ocupaciones = "WHERE DATE(o.fecha_inicio) = CURDATE()";
        $condition_ventas = "WHERE DATE(v.fecha_venta) = CURDATE()";
        break;
}

try {
    $response = [];

    // --- 1. KPIs ---
    $kpis = [];
    $stmt_ing_ocu = $pdo->query("SELECT SUM(o.monto_total) FROM Ocupaciones o $condition_ocupaciones");
    $ingresos_ocupaciones = $stmt_ing_ocu->fetchColumn() ?: 0;
    $stmt_ing_ven = $pdo->query("SELECT SUM(v.monto_total) FROM Ventas v $condition_ventas");
    $ingresos_ventas = $stmt_ing_ven->fetchColumn() ?: 0;
    $kpis['ingresos_totales'] = $ingresos_ocupaciones + $ingresos_ventas;

    $stmt_ocu = $pdo->query("SELECT COUNT(o.id) FROM Ocupaciones o $condition_ocupaciones");
    $kpis['nuevas_ocupaciones'] = $stmt_ocu->fetchColumn() ?: 0;

    $stmt_prod = $pdo->query("SELECT SUM(vd.cantidad_vendida) FROM Venta_Detalles vd JOIN Ventas v ON vd.venta_id = v.id $condition_ventas");
    $kpis['productos_vendidos'] = $stmt_prod->fetchColumn() ?: 0;
    
    $stmt_clientes = $pdo->query("SELECT COUNT(DISTreport_data.php` (Versión a Prueba de Errores)

Esta versión se asegura de que las consultas sean correctas y maneja los casos donde no hay datos sin fallar.

**Acción:** Reemplaza todo el contenido de `api/get_report_data.php` con este código.

```php
<?php
// api/get_report_data.php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

// Validar que la conexión a la BD exista
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos.']);
    exit;
}

$periodo = $_GET['periodo'] ?? 'diario';
$condition_ocupaciones = "";
$condition_ventas = "";

// Usamos marcadores de posición para más seguridad y claridad
$params_ocupaciones = [];
$params_ventas = [];

switch ($periodo) {
    case 'semanal':
        $condition_ocupaciones = "WHERE o.fecha_inicio >= ? AND o.fecha_inicio < ?";
        $condition_ventas = "WHERE v.fecha_venta >= ? AND v.fecha_venta < ?";
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d', strtotime('next sunday'));
        $params_ocupaciones = [$start_date, $end_date];
        $params_ventas = [$start_date, $end_date];
        break;
    case 'mensual':
        $condition_ocupaciones = "WHERE o.fecha_inicio >= ? AND o.fecha_inicio < ?";
        $condition_ventas = "WHERE v.fecha_venta >= ? AND v.fecha_venta < ?";
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d', strtotime('first day of next month'));
        $params_ocupaciones = [$start_date, $end_date];
        $params_ventas = [$start_date, $end_date];
        break;
    case 'diario':
    default:
        $condition_ocupaciones = "WHERE DATE(o.fecha_inicio) = CURDATE()";
        $condition_ventas = "WHERE DATE(v.fecha_venta) = CURDATE()";
        break;
}

try {
    $response = [];
    
    // --- 1. KPIs ---
    $kpis = [];
    $stmt_ing_ocu = $pdo->prepare("SELECT SUM(o.monto_total) FROM Ocupaciones o $condition_ocupaciones");
    $stmt_ing_ocu->execute($params_ocupaciones);
    $ingresos_ocupaciones = $stmt_ing_ocu->fetchColumn() ?? 0;
    
    $stmt_ing_ven = $pdo->prepare("SELECT SUM(v.monto_total) FROM Ventas v $condition_ventas");
    $stmt_ing_ven->execute($params_ventas);
    $ingresos_ventas = $stmt_ing_ven->fetchColumn() ?? 0;
    $kpis['ingresos_totales'] = floatval($ingresos_ocupaciones) + floatval($ingresos_ventas);

    $stmt_ocu = $pdo->prepare("SELECT COUNT(o.id) FROM Ocupaciones o $condition_ocupaciones");
    $stmt_ocu->execute($params_ocupaciones);
    $kpis['nuevas_ocupaciones'] = $stmt_ocu->INCT o.cliente_id) FROM Ocupaciones o $condition_ocupaciones");
    $kpis['clientes_atendidos'] = $stmt_clientes->fetchColumn() ?: 0;

    $response['kpis'] = $kpis;

    // --- 2. Datos para el Gráfico ---
    $chart_data = ['labels' => [], 'data' => []];
    $stmt_chart = $pdo->query("SELECT DATE_FORMAT(o.fecha_inicio, '%d/%m') as dia, SUM(o.monto_total) as total_dia FROM Ocupaciones o $condition_ocupaciones GROUP BY dia ORDER BY o.fecha_inicio ASC");
    while($row = $stmt_chart->fetch()){
        $chart_data['labels'][] = $row['dia'];
        $chart_data['data'][] = $row['total_dia'];
    }
    $response['chart_data'] = $chart_data;

    // --- 3. Reportes en listas ---
    $stmt_top_rooms = $pdo->query("SELECT o.habitacion_id, COUNT(o.id) as total_veces FROM Ocupaciones o $condition_ocupaciones GROUP BY o.habitacion_id ORDER BY total_veces DESC LIMIT 5");
    $response['top_habitaciones'] = $stmt_top_rooms->fetchAll(PDO::FETCH_ASSOC);

    $stmt_top_prods = $pdo->query("SELECT p.nombre, SUM(vd.cantidad_vendida) as total_vendido FROM Venta_Detalles vd JOIN Productos p ON vd.producto_id = p.id JOIN Ventas v ON vd.venta_id = v.id $condition_ventas GROUP BY p.nombre ORDER BY total_vendido DESC LIMIT 5");
    $response['top_productos'] = $stmt_top_prods->fetchAll(PDO::FETCH_ASSOC);

    $stmt_taxis = $pdo->query("SELECT o.taxi_info, COUNT(o.id) as total_viajes FROM Ocupaciones o $condition_ocupaciones WHERE o.taxi_info IS NOT NULL AND o.taxi_info != '' GROUP BY o.taxi_info ORDER BY total_viajes DESC");
    $response['conteo_taxis'] = $stmt_taxis->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    // Devolvemos el error en formato JSON para que el frontend pueda leerlo
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>