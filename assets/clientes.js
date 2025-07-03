document.addEventListener('DOMContentLoaded', function () {
    const tablaClientes = document.getElementById('tabla-clientes');
    const modalEditarClienteElement = document.getElementById('modalEditarCliente');
    const modalEditarCliente = new bootstrap.Modal(modalEditarClienteElement);
    const btnGuardarCambios = document.getElementById('btn-guardar-cambios-cliente');
    const historialContainer = document.getElementById('historial-cliente-container');
    const historialContenido = document.getElementById('historial-contenido');

    // --- MANEJO DE EVENTOS EN LA TABLA ---
    tablaClientes.addEventListener('click', function (e) {
        const editButton = e.target.closest('.btn-edit');
        const tableRow = e.target.closest('tr');

        if (editButton) {
            e.stopPropagation(); 
            
            // Leemos todos los datos del botón, incluyendo 'data-origen'
            const id = editButton.dataset.id;
            const dni = editButton.dataset.dni;
            const nombre = editButton.dataset.nombre;
            const telefono = editButton.dataset.telefono;
            const origen = editButton.dataset.origen;

            // Rellenamos todos los campos del modal
            modalEditarClienteElement.querySelector('#edit-cliente-id').value = id;
            modalEditarClienteElement.querySelector('#edit-cliente-dni').value = dni;
            modalEditarClienteElement.querySelector('#edit-cliente-nombre').value = nombre;
            modalEditarClienteElement.querySelector('#edit-cliente-telefono').value = telefono;
            modalEditarClienteElement.querySelector('#edit-cliente-origen').value = origen;
            
            modalEditarCliente.show();
        } else if (tableRow) {
            const clienteId = tableRow.dataset.clienteId;
            const clienteNombre = tableRow.cells[1].textContent;
            mostrarHistorial(clienteId, clienteNombre);
        }
    });

    // --- LÓGICA PARA GUARDAR LOS CAMBIOS DEL CLIENTE (CORREGIDA) ---
    btnGuardarCambios.addEventListener('click', function () {
        // Recolectamos TODOS los datos del formulario, incluyendo el 'origen'
        const id = modalEditarClienteElement.querySelector('#edit-cliente-id').value;
        const nombre = modalEditarClienteElement.querySelector('#edit-cliente-nombre').value;
        const telefono = modalEditarClienteElement.querySelector('#edit-cliente-telefono').value;
        const origen = modalEditarClienteElement.querySelector('#edit-cliente-origen').value;

        // Enviamos el objeto completo al backend, incluyendo la clave 'origen'
        fetch('api/update_cliente.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, nombre, telefono, origen }) // <-- 'origen' se incluye aquí
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'info') {
                alert(data.message);
                modalEditarCliente.hide();
                // Actualizamos la fila en la tabla sin recargar la página
                const rowToUpdate = tablaClientes.querySelector(`tr[data-cliente-id='${id}']`);
                if(rowToUpdate) {
                    // Actualizamos el texto de TODAS las celdas relevantes
                    rowToUpdate.cells[1].textContent = nombre;
                    rowToUpdate.cells[2].textContent = telefono || 'N/A';
                    rowToUpdate.cells[3].textContent = origen || 'N/A'; // <-- Se actualiza la celda 'Origen'

                    // También actualizamos los data-attributes del botón para futuras ediciones
                    const button = rowToUpdate.querySelector('.btn-edit');
                    button.dataset.nombre = nombre;
                    button.dataset.telefono = telefono;
                    button.dataset.origen = origen; // <-- Se actualiza el 'data-origen' del botón
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => alert('Error: ' + error.message));
    });

    // --- FUNCIÓN PARA MOSTRAR EL HISTORIAL DEL CLIENTE ---
    // Esta función se mantiene exactamente como en tu versión original.
    function mostrarHistorial(clienteId, clienteNombre) {
        document.getElementById('historial-nombre-cliente').textContent = clienteNombre;
        historialContenido.innerHTML = '<div class="col-12 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
        historialContainer.style.display = 'block';

        fetch(`api/get_cliente_historial.php?id=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                historialContenido.innerHTML = '';
                if (data.length === 0) {
                    historialContenido.innerHTML = '<div class="col-12"><div class="alert alert-info">Este cliente no tiene historial de visitas.</div></div>';
                    return;
                }
                
                data.forEach(estancia => {
                    const fechaInicio = new Date(estancia.fecha_inicio + 'T00:00:00');
                    const fechaFormateada = fechaInicio.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                    const estadoPago = estancia.pagado_completo 
                        ? '<span class="badge bg-success">Pagado</span>'
                        : '<span class="badge bg-warning text-dark">Pendiente</span>';

                    const cardHtml = `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-alt text-primary"></i> ${fechaFormateada}</h5>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-bed"></i> <strong>Habitación:</strong> ${estancia.numero_habitacion} (${estancia.tipo_habitacion})
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-moon"></i> <strong>Estancia:</strong> ${estancia.estadia_dias} días
                                    </p>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-dollar-sign"></i> <strong>Monto:</strong> S/ ${parseFloat(estancia.monto_total).toFixed(2)}
                                    </p>
                                    ${estadoPago}
                                </div>
                            </div>
                        </div>
                    `;
                    historialContenido.innerHTML += cardHtml;
                });
            })
            .catch(error => {
                historialContenido.innerHTML = '<div class="col-12"><div class="alert alert-danger">No se pudo cargar el historial.</div></div>';
                console.error('Error fetching history:', error);
            });
    }
});