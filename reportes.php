<?php 
$page_title = "Reportes y Estadísticas";
require_once 'templates/header.php'; 
?>

<!-- Contenido Principal -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Reportes y Estadísticas</h1>

    <!-- Selector de Período -->
    <div class="btn-group mb-4" role="group" id="periodo-selector">
        <button type="button" class="btn btn-primary active" data-periodo="diario">Diario</button>
        <button type="button" class="btn btn-primary" data-periodo="semanal">Semanal</button>
        <button type="button" class="btn btn-primary" data-periodo="mensual">Mensual</button>
    </div>

    <!-- Indicadores Clave de Rendimiento (KPIs) -->
    <div class="row" id="kpi-container">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ingresos Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-ingresos">S/ --</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nuevas Ocupaciones</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-ocupaciones">--</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-bed fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Productos Vendidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-productos">--</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box-open fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Clientes Atendidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-clientes">--</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico y Top Habitaciones -->
    <div class="row">
        <!-- Columna del Gráfico -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Gráfico de Ingresos por Ocupaciones</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reporteGrafico"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Top Habitaciones -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Habitaciones Más Solicitadas</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="lista-top-habitaciones">
                        <li class="list-group-item text-center">Cargando...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Incluimos la librería Chart.js desde un CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Incluimos nuestro script específico para esta página -->
<script src="assets/reportes.js"></script>

<?php require_once 'templates/footer.php'; ?>