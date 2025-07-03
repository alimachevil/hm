document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const periodoSelector = document.getElementById('periodo-selector');
    const kpiIngresos = document.getElementById('kpi-ingresos');
    const kpiOcupaciones = document.getElementById('kpi-ocupaciones');
    const kpiProductos = document.getElementById('kpi-productos');
    const kpiClientes = document.getElementById('kpi-clientes');
    const listaTopHabitaciones = document.getElementById('lista-top-habitaciones');
    const ctx = document.getElementById('reporteGrafico').getContext('2d');
    
    let reporteGrafico;

    // --- INICIALIZACIÓN DEL GRÁFICO ---
    function inicializarGrafico() {
        if (reporteGrafico) {
            reporteGrafico.destroy();
        }
        reporteGrafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ingresos (S/)',
                    data: [],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4 // Para que la línea sea curva y suave
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000, // Duración de la animación en ms
                    easing: 'easeInOutQuart' // Tipo de animación
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'S/ ' + value; }
                        }
                    }
                }
            }
        });
    }
    
    // --- FUNCIÓN PRINCIPAL PARA OBTENER Y MOSTRAR DATOS ---
    function cargarReporte(periodo = 'diario') {
        // Mostrar estado de carga (visual)
        kpiIngresos.textContent = 'Cargando...';
        kpiOcupaciones.textContent = '...';
        kpiProductos.textContent = '...';
        kpiClientes.textContent = '...';
        listaTopHabitaciones.innerHTML = '<li class="list-group-item text-center">Cargando...</li>';
        
        fetch(`api/get_report_data.php?periodo=${periodo}`)
            .then(response => response.json())
            .then(data => {
                // Actualizar KPIs
                kpiIngresos.textContent = `S/ ${parseFloat(data.kpis.ingresos).toFixed(2)}`;
                kpiOcupaciones.textContent = data.kpis.ocupaciones;
                kpiProductos.textContent = data.kpis.productos;
                kpiClientes.textContent = data.kpis.clientes;

                // Actualizar Gráfico
                reporteGrafico.data.labels = data.chart_data.labels;
                reporteGrafico.data.datasets[0].data = data.chart_data.data;
                reporteGrafico.update();

                // Actualizar Top Habitaciones
                listaTopHabitaciones.innerHTML = '';
                if (data.top_rooms.length > 0) {
                    data.top_rooms.forEach(room => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            <span><i class="fas fa-door-open text-secondary"></i> Hab. ${room.habitacion_id}</span>
                            <span class="badge bg-primary rounded-pill">${room.total_ocupaciones}</span>
                        `;
                        listaTopHabitaciones.appendChild(li);
                    });
                } else {
                    listaTopHabitaciones.innerHTML = '<li class="list-group-item">No hay datos para este período.</li>';
                }
            })
            .catch(error => {
                console.error('Error al cargar el reporte:', error);
                alert('No se pudieron cargar los datos del reporte.');
            });
    }

    // --- MANEJO DE EVENTOS ---
    periodoSelector.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON') {
            // Manejar la clase 'active' para el botón presionado
            periodoSelector.querySelector('.active').classList.remove('active');
            e.target.classList.add('active');
            
            const periodo = e.target.dataset.periodo;
            cargarReporte(periodo);
        }
    });

    // --- CARGA INICIAL ---
    inicializarGrafico();
    cargarReporte('diario'); // Cargar el reporte diario por defecto al entrar a la página
});