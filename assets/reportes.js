document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const periodoSelector = document.getElementById('periodo-selector');
    const kpiContainer = document.getElementById('kpi-container');
    const listaTopHabitaciones = document.getElementById('lista-top-habitaciones');
    const listaTopProductos = document.getElementById('lista-top-productos');
    const tablaReporteTaxis = document.getElementById('tabla-reporte-taxis');
    const btnImprimir = document.getElementById('btn-imprimir-reporte');
    const ctx = document.getElementById('reporteGrafico').getContext('2d');
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
        
        fetch(`api/get_report_data.php?periodo=${periodo}`)
            .then(response => {
                if (!response.ok) throw new Error('Error de red al cargar el reporte.');
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
                actualizarLista(listaTopHabitaciones, data.top_habitaciones, item => `<span><i class="fas fa-door-open text-secondary me-2"></i> Hab. ${item.habitacion_id}</span><span class="badge bg-primary rounded-pill">${item.total_veces}</span>`);
                actualizarLista(listaTopProductos, data.top_productos, item => `<span><i class="fas fa-shopping-basket text-secondary me-2"></i> ${item.nombre}</span><span class="badge bg-primary rounded-pill">${item.total_vendido}</span>`);
                
                // Actualizar tabla de taxis
                tablaReporteTaxis.innerHTML = '';
                if(data.conteo_taxis.length > 0) {
                    data.conteo_taxis.forEach(taxi => {
                        tablaReporteTaxis.innerHTML += `<tr><td>${taxi.taxi_info}</td><td class="text-center">${taxi.total_viajes}</td></tr>`;
                    });
                } else {
                    tablaReporteTaxis.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No se registraron taxis en este período.</td></tr>';
                }
            })
            .catch(error => alert('Error al cargar reporte: ' + error.message));
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
            periodoSelector.querySelector('.active').classList.remove('active');
            e.target.classList.add('active');
            cargarReporte(e.target.dataset.periodo);
        }
    });

    // --- CARGA INICIAL ---
    inicializarGrafico();
    cargarReporte('diario');
});