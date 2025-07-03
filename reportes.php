<?php
// Incluimos la conexión a la base de datos una sola vez.
require_once 'config/db_connect.php';

// ===================================================================
//  MANEJO DE LA PETICIÓN AJAX PARA OBTENER DATOS DEL REPORTE
// ===================================================================
// Este bloque solo se ejecuta si la página es llamada con los parámetros GET correctos.
if (isset($_GET['action']) && $_GET['action'] === 'get_report_data') {
    header('Content-Type: application/json');
    
    $periodo = $_GET['periodo'] ?? 'diario';
    $condition_ocupaciones = "";
    $condition_ventas = "";

    // Definimos los rangos de fechas de forma segura
    switch ($periodo) {
        case 'semanal':
            $condition_ocupaciones = "WHERE o.fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND o.fecha_inicio < CURDATE() + INTERVAL 1 DAY";
            $condition_ventas = "WHERE v.fecha_venta >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND v.fecha_venta < CURDATE() + INTERVAL 1 DAY";
            break;
        case 'mensual':
            $condition_ocupaciones = "WHERE o.fecha_inicio >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND o.fecha_inicio < CURDATE() + INTERVAL 1 DAY";
            $condition_ventas = "WHERE v.fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND v.fecha_venta < CURDATE() + INTERVAL 1 DAY";
            break;
        case 'diario':
        default:
            $condition_ocupaciones = "WHERE DATE(o.fecha_inicio) = CURDATE()";
            $condition_ventas = "WHERE DATE(v.fecha_venta) = CURDATE()";
            break;
    }

    try {
        $response = [];

        // --- KPIs ---
        $stmt_ing_ocu = $pdo->query("SELECT SUM(o.monto_total) FROM Ocupaciones o $condition_ocupaciones");
        $ingresos_ocupaciones = $stmt_ing_ocu->fetchColumn() ?: 0;
        $stmt_ing_ven = $pdo->query("SELECT SUM(v.monto_total) FROM Ventas v $condition_ventas");
        $ingresos_ventas = $stmt_ing_ven->fetchColumn() ?: 0;
        $response['kpis']['ingresos_totales'] = floatval($ingresos_ocupaciones) + floatval($ingresos_ventas);

        $stmt_ocu = $pdo->query("SELECT COUNT(o.id) FROM Ocupaciones o $condition_ocupaciones");
        $response['kpis']['nuevas_ocupaciones'] = $stmt_ocu->fetchColumn() ?: 0;

        $stmt_prod = $pdo->query("SELECT SUM(vd.cantidad_vendida) FROM Venta_Detalles vd JOIN Ventas v ON vd.venta_id = v.id $condition_ventas");
        $response['kpis']['productos_vendidos'] = $stmt_prod->fetchColumn() ?: 0;
        
        $stmt_clientes = $pdo->query("SELECT COUNT(DISTINCT o.cliente_id) FROM Ocupaciones o $condition_ocupaciones");
        $response['kpis']['clientes_atendidos'] = $stmt_clientes->fetchColumn() ?: 0;
        
        // --- Gráfico ---
        $chart_data = ['labels' => [], 'data' => []];
        $stmt_chart = $pdo->query("SELECT DATE_FORMAT(o.fecha_inicio, '%d/%m') as dia, SUM(o.monto_total) as total_dia FROM Ocupaciones o $condition_ocupaciones GROUP BY dia ORDER BY o.fecha_inicio ASC");
        while($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)){
            $chart_data['labels'][] = $row['dia'];
            $chart_data['data'][] = floatval($row['total_dia']);
        }
        $response['chart_data'] = $chart_data;
        
        // --- Listas ---
        $stmt_top_rooms = $pdo->query("SELECT o.habitacion_id, COUNT(o.id) as total_veces FROM Ocupaciones o $condition_ocupaciones GROUP BY o.habitacion_id ORDER BY total_veces DESC LIMIT 5");
        $response['top_habitaciones'] = $stmt_top_rooms->fetchAll(PDO::FETCH_ASSOC);

        $stmt_top_prods = $pdo->query("SELECT p.nombre, SUM(vd.cantidad_vendida) as total_vendido FROM Venta_Detalles vd JOIN Productos p ON vd.producto_id = p.id JOIN Ventas v ON vd.venta_id = v.id $condition_ventas GROUP BY p.nombre ORDER BY total_vendido DESC LIMIT 5");
        $response['top_productos'] = $stmt_top_prods->fetchAll(PDO::FETCH_ASSOC);
        
        // ========== LA CORRECCIÓN ESTÁ AQUÍ ==========
        // Unimos el filtro de período con el filtro de taxi usando AND
        $stmt_taxis = $pdo->query("
            SELECT o.taxi_info, COUNT(o.id) as total_viajes 
            FROM Ocupaciones o 
            $condition_ocupaciones AND o.taxi_info IS NOT NULL AND o.taxi_info != '' 
            GROUP BY o.taxi_info 
            ORDER BY total_viajes DESC
        ");
        $response['conteo_taxis'] = $stmt_taxis->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($response);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
    }
    // Detenemos la ejecución aquí para que solo devuelva el JSON.
    exit;
}
// ===================================================================


// --- Lógica para la carga normal de la página (HTML) ---
$page_title = "Reportes y Estadísticas"; 
require_once 'templates/header.php'; 
?>

<!-- Estilos CSS específicos para la impresión -->
<style>
@media print {
    body { background-color: #fff; }
    .sidebar, #periodo-selector, #reporte-header .btn { display: none !important; }
    .content { width: 100% !important; padding: 20px !important; margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; page-break-inside: avoid; }
    .chart-container { height: 400px !important; width: 100%; }
    canvas { max-width: 100%; }
}
</style>

<!-- Contenido Principal -->
<main id="content" class="content p-4">
    <div class="d-flex justify-content-between align-items-center mb-4" id="reporte-header">
        <h1>Reportes y Estadísticas</h1>
        <button class="btn btn-outline-secondary" id="btn-imprimir-reporte"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>
    </div>

    <!-- Selector de Período -->
    <div class="btn-group mb-4" role="group" id="periodo-selector">
        <button type="button" class="btn btn-primary active" data-periodo="diario">Diario</button>
        <button type="button" class="btn btn-primary" data-periodo="semanal">Semanal</button>
        <button type="button" class="btn btn-primary" data-periodo="mensual">Mensual</button>
    </div>

    <!-- Indicadores Clave de Rendimiento (KPIs) -->
    <div class="row" id="kpi-container">
        <!-- Generado por JS -->
    </div>

    <!-- Gráfico y Listas de Top -->
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Gráfico de Ingresos por Ocupaciones</h6></div>
                <div class="card-body chart-container"><canvas id="reporteGrafico"></canvas></div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Top 5 Habitaciones Más Solicitadas</h6></div>
                <div class="card-body"><ul class="list-group list-group-flush" id="lista-top-habitaciones"></ul></div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Top 5 Productos Más Vendidos</h6></div>
                <div class="card-body"><ul class="list-group list-group-flush" id="lista-top-productos"></ul></div>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Taxis -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Reporte de Taxis</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Identificador del Taxi</th>
                                    <th class="text-center" style="width: 20%;">Veces que trajo clientes</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-reporte-taxis"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Incluimos la librería Chart.js y el script embebido -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const periodoSelector = document.getElementById('periodo-selector');
    const kpiContainer = document.getElementById('kpi-container');
    const listaTopHabitaciones = document.getElementById('lista-top-habitaciones');
    const listaTopProductos = document.getElementById('lista-top-productos');
    const tablaReporteTaxis = document.getElementById('tabla-reporte-taxis');
    const btnImprimir = document.getElementById('btn-imprimir-reporte');
    const canvas = document.getElementById('reporteGrafico');
    const ctx = canvas.getContext('2d');
    let reporteGrafico;

    // --- MANEJO DE LA IMPRESIÓN ---
    btnImprimir.addEventListener('click', () => window.print());

    // --- INICIALIZACIÓN DEL GRÁFICO ---
    function inicializarGrafico() {
        if (reporteGrafico) reporteGrafico.destroy();
        reporteGrafico = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Ingresos (S/)', data: [], borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.3 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeInOutQuart' },
                scales: { y: { beginAtZero: true, ticks: { callback: value => `S/ ${value}` } } }
            }
        });
    }
    
    // --- FUNCIÓN PRINCIPAL PARA OBTENER Y MOSTRAR DATOS ---
    function cargarReporte(periodo = 'diario') {
        kpiContainer.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i></div>';
        listaTopHabitaciones.innerHTML = '<li class="list-group-item text-center">Cargando...</li>';
        listaTopProductos.innerHTML = '<li class="list-group-item text-center">Cargando...</li>';
        tablaReporteTaxis.innerHTML = '<tr><td colspan="2" class="text-center">Cargando...</td></tr>';
        
        // La llamada fetch ahora apunta a este mismo archivo
        fetch(`reportes.php?action=get_report_data&periodo=${periodo}`)
            .then(response => {
                if (!response.ok) throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);
                
                // Actualizar KPIs
                kpiContainer.innerHTML = `
                    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-start border-primary border-4 shadow-sm h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col"><div class="text-xs fw-bold text-primary text-uppercase mb-1">Ingresos Totales</div><div class="h5 mb-0 fw-bold text-gray-800">S/ ${parseFloat(data.kpis.ingresos_totales).toFixed(2)}</div></div><div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-body-tertiary"></i></div></div></div></div></div>
                    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-start border-success border-4 shadow-sm h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col"><div class="text-xs fw-bold text-success text-uppercase mb-1">Nuevas Ocupaciones</div><div class="h5 mb-0 fw-bold text-gray-800">${data.kpis.nuevas_ocupaciones}</div></div><div class="col-auto"><i class="fas fa-bed fa-2x text-body-tertiary"></i></div></div></div></div></div>
                    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-start border-info border-4 shadow-sm h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col"><div class="text-xs fw-bold text-info text-uppercase mb-1">Productos Vendidos</div><div class="h5 mb-0 fw-bold text-gray-800">${data.kpis.productos_vendidos || 0}</div></div><div class="col-auto"><i class="fas fa-box-open fa-2x text-body-tertiary"></i></div></div></div></div></div>
                    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-start border-warning border-4 shadow-sm h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col"><div class="text-xs fw-bold text-warning text-uppercase mb-1">Clientes Atendidos</div><div class="h5 mb-0 fw-bold text-gray-800">${data.kpis.clientes_atendidos}</div></div><div class="col-auto"><i class="fas fa-users fa-2x text-body-tertiary"></i></div></div></div></div></div>
                `;

                // Actualizar Gráfico
                reporteGrafico.data.labels = data.chart_data.labels;
                reporteGrafico.data.datasets[0].data = data.chart_data.data;
                reporteGrafico.update();

                // Actualizar listas
                actualizarLista(listaTopHabitaciones, data.top_habitaciones, item => `<span><i class="fas fa-door-open text-secondary me-2"></i> Hab. <strong>${item.habitacion_id}</strong></span><span class="badge bg-primary rounded-pill">${item.total_veces}</span>`);
                actualizarLista(listaTopProductos, data.top_productos, item => `<span><i class="fas fa-shopping-basket text-secondary me-2"></i> ${item.nombre}</span><span class="badge bg-primary rounded-pill">${item.total_vendido}</span>`);
                
                // Actualizar tabla de taxis
                tablaReporteTaxis.innerHTML = '';
                if(data.conteo_taxis && data.conteo_taxis.length > 0) {
                    data.conteo_taxis.forEach(taxi => {
                        tablaReporteTaxis.innerHTML += `<tr><td>${taxi.taxi_info}</td><td class="text-center">${taxi.total_viajes}</td></tr>`;
                    });
                } else {
                    tablaReporteTaxis.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No se registraron taxis en este período.</td></tr>';
                }
            })
            .catch(error => {
                console.error("Error al cargar el reporte:", error);
                kpiContainer.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error al cargar los datos. Verifique la consola (F12) para más detalles.</div></div>`;
            });
    }

    function actualizarLista(ulElement, data, formatter) {
        ulElement.innerHTML = '';
        if (data && data.length > 0) {
            data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = formatter(item);
                ulElement.appendChild(li);
            });
        } else {
            ulElement.innerHTML = '<li class="list-group-item text-muted">No hay datos para mostrar.</li>';
        }
    }

    // --- MANEJO DE EVENTOS ---
    periodoSelector.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON' && !e.target.classList.contains('active')) {
            const activeButton = periodoSelector.querySelector('.active');
            if (activeButton) activeButton.classList.remove('active');
            e.target.classList.add('active');
            cargarReporte(e.target.dataset.periodo);
        }
    });

    // --- CARGA INICIAL ---
    inicializarGrafico();
    cargarReporte('diario');
});
</script>

<?php require_once 'templates/footer.php'; ?>