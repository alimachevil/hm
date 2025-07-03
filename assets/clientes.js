document.addEventListener('DOMContentLoaded', function () {
    const tablaClientes = document.getElementById('tabla-clientes');
    const modalEditarCliente = new bootstrap.Modal(document.getElementById('modalEditarCliente'));
    const btnGuardarCambios = document.getElementById('btn-guardar-cambios-cliente');
    const historialContainer = document.getElementById('historial-cliente-container');
    const historialContenido = document.getElementById('historial-contenido');

    // Usar delegación de eventos en la tabla
    tablaClientes.addEventListener('click', function (e) {
        const editButton = e.target.closest('.btn-edit');
        const tableRow = e.target.closest('tr');

        if (editButton) {
            e.stopPropagation(); // Evita que el clic se propague a la fila <tr>
            const id = editButton.dataset.id;
            const dni = editButton.dataset.dni;
            const nombre = editButton.dataset.nombre;
            const telefono = editButton.dataset.telefono;

            // Rellenar el modal de edición
            document.getElementById('edit-cliente-id').value = id;
            document.getElementById('edit-cliente-dni').value = dni;
            document.getElementById('edit-cliente-nombre').value = nombre;
            document.getElementById('edit-cliente-telefono').value = telefono;
            
            modalEditarCliente.show();
        } else if (tableRow) {
            const clienteId = tableRow.dataset.clienteId;
            const clienteNombre = tableRow.cells[1].textContent;
            mostrarHistorial(clienteId, clienteNombre);
        }
    });

    // Lógica para guardar los cambios del cliente
    btnGuardarCambios.addEventListener('click', function () {
        const id = document.getElementById('edit-cliente-id').value;
        const nombre = document.getElementById('edit-cliente-nombre').value;
        const telefono = document.getElementById('edit-cliente-telefono').value;

        fetch('api/update_cliente.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, nombre, telefono })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'info') {
                alert(data.message);
                modalEditarCliente.hide();
                // Actualizar la fila en la tabla sin recargar la página
                const rowToUpdate = tablaClientes.querySelector(`tr[data-cliente-id='${id}']`);
                if(rowToUpdate) {
                    rowToUpdate.cells[1].textContent = nombre;
                    rowToUpdate.cells[2].textContent = telefono || 'N/A';
                    // También actualizar los data-attributes del botón
                    const button = rowToUpdate.querySelector('.btn-edit');
                    button.dataset.nombre = nombre;
                    button.dataset.telefono = telefono;
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => alert('Error: ' + error.message));
    });

    // Función para mostrar el historial del cliente
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