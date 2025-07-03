<?php
// Incluimos la conexión a la base de datos una sola vez al principio.
require_once 'config/db_connect.php';

// ===================================================================
//  MANEJO DE PETICIONES (ROUTER INTERNO)
// ===================================================================

// --- PARTE 1: MANEJO DE ACTUALIZACIÓN DE CLIENTE (Formulario POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_cliente') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $origen = $_POST['origen'];

    if (!empty($id) && !empty($nombre)) {
        try {
            $stmt = $pdo->prepare("UPDATE Clientes SET nombre = ?, telefono = ?, origen = ? WHERE id = ?");
            $stmt->execute([$nombre, $telefono, $origen, $id]);
            // Redirigimos a la misma página para evitar reenvío de formulario y ver los cambios.
            header('Location: clientes.php?update=success');
            exit;
        } catch (Exception $e) {
            header('Location: clientes.php?update=error');
            exit;
        }
    }
}


// --- PARTE 2: MANEJO DE PETICIÓN DE HISTORIAL (Llamada AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'get_historial') {
    header('Content-Type: application/json');
    $cliente_id = $_GET['id'] ?? 0;

    try {
        $stmt = $pdo->prepare("
            SELECT 
                o.fecha_inicio, o.estadia_dias, o.monto_total,
                h.numero_habitacion, th.nombre AS tipo_habitacion,
                (SELECT p.comprobante FROM Pagos p WHERE p.ocupacion_id = o.id AND p.comprobante IS NOT NULL ORDER BY p.fecha_pago DESC LIMIT 1) AS comprobante_tipo,
                (SELECT p.numero_comprobante FROM Pagos p WHERE p.ocupacion_id = o.id AND p.numero_comprobante IS NOT NULL ORDER BY p.fecha_pago DESC LIMIT 1) AS comprobante_numero,
                (SELECT p.metodo_pago FROM Pagos p WHERE p.ocupacion_id = o.id ORDER BY p.fecha_pago DESC LIMIT 1) AS ultimo_metodo_pago,
                (SELECT SUM(p.monto_pagado) FROM Pagos p WHERE p.ocupacion_id = o.id) >= o.monto_total AS pagado_completo
            FROM Ocupaciones o
            JOIN Habitaciones h ON o.habitacion_id = h.numero_habitacion
            JOIN Tipos_Habitaciones th ON h.tipo_id = th.id
            WHERE o.cliente_id = ?
            ORDER BY o.fecha_inicio DESC
        ");
        $stmt->execute([$cliente_id]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($historial);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener el historial.']);
    }
    // Detenemos la ejecución para no renderizar el resto del HTML.
    exit;
}


// --- PARTE 3: LÓGICA PARA LA CARGA NORMAL DE LA PÁGINA (GET) ---
$page_title = "Gestión de Clientes"; 
require_once 'templates/header.php'; 

// Obtenemos la lista de clientes para mostrar en la tabla.
$stmt = $pdo->query("SELECT id, documento_identidad, nombre, telefono, origen FROM Clientes ORDER BY nombre ASC");
$clientes = $stmt->fetchAll();
?>

<!-- ==================================================== -->
<!--           CONTENIDO PRINCIPAL HTML                   -->
<!-- ==================================================== -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Gestión de Clientes</h1>

    <!-- Área de la Lista de Clientes -->
    <div class="card">
        <div class="card-header"><i class="fas fa-users"></i> Lista de Clientes Registrados</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre y Apellidos</th>
                            <th>Teléfono</th>
                            <th>Origen</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr style="cursor: pointer;" data-cliente-id="<?php echo $cliente['id']; ?>" title="Haz clic para ver el historial">
                                <td><?php echo htmlspecialchars($cliente['documento_identidad']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['origen'] ?? 'N/A'); ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary btn-edit" 
                                            data-id="<?php echo $cliente['id']; ?>"
                                            data-dni="<?php echo htmlspecialchars($cliente['documento_identidad']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>"
                                            data-origen="<?php echo htmlspecialchars($cliente['origen'] ?? ''); ?>"
                                            title="Editar Cliente">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Área para mostrar el Historial -->
    <div id="historial-cliente-container" class="mt-5" style="display: none;">
        <h2 class="mb-3">Historial de Visitas: <span id="historial-nombre-cliente" class="text-primary"></span></h2>
        <div id="historial-contenido" class="row"></div>
    </div>
</main>

<!-- Modal para Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Información del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- El formulario ahora envía los datos por POST a la misma página -->
            <form id="form-editar-cliente" method="POST" action="clientes.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_cliente">
                    <input type="hidden" id="edit-cliente-id" name="id">
                    <div class="mb-3">
                        <label for="edit-cliente-dni" class="form-label">Documento de Identidad</label>
                        <input type="text" class="form-control" id="edit-cliente-dni" name="dni" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-cliente-nombre" class="form-label">Nombre y Apellidos</label>
                        <input type="text" class="form-control" id="edit-cliente-nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-cliente-telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="edit-cliente-telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="edit-cliente-origen" class="form-label">País / Ciudad de Origen</label>
                        <input type="text" class="form-control" id="edit-cliente-origen" name="origen">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!-- El botón ahora es de tipo 'submit' -->
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================================== -->
<!--                  JAVASCRIPT                          -->
<!-- ==================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tablaClientes = document.getElementById('tabla-clientes');
    const modalEditarClienteElement = document.getElementById('modalEditarCliente');
    const modalEditarCliente = new bootstrap.Modal(modalEditarClienteElement);
    const historialContainer = document.getElementById('historial-cliente-container');
    const historialContenido = document.getElementById('historial-contenido');

    // Lógica para rellenar el modal y mostrar el historial
    tablaClientes.addEventListener('click', function (e) {
        const editButton = e.target.closest('.btn-edit');
        const tableRow = e.target.closest('tr');

        if (editButton) {
            e.stopPropagation(); 
            // Rellenar el modal
            modalEditarClienteElement.querySelector('#edit-cliente-id').value = editButton.dataset.id;
            modalEditarClienteElement.querySelector('#edit-cliente-dni').value = editButton.dataset.dni;
            modalEditarClienteElement.querySelector('#edit-cliente-nombre').value = editButton.dataset.nombre;
            modalEditarClienteElement.querySelector('#edit-cliente-telefono').value = editButton.dataset.telefono;
            modalEditarClienteElement.querySelector('#edit-cliente-origen').value = editButton.dataset.origen;
            modalEditarCliente.show();
        } else if (tableRow) {
            // Mostrar el historial
            const clienteId = tableRow.dataset.clienteId;
            const clienteNombre = tableRow.cells[1].textContent;
            mostrarHistorial(clienteId, clienteNombre);
        }
    });

    // Lógica para mostrar el historial con el nuevo diseño
    function mostrarHistorial(clienteId, clienteNombre) {
        document.getElementById('historial-nombre-cliente').textContent = clienteNombre;
        historialContenido.innerHTML = '<div class="col-12 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
        historialContainer.style.display = 'block';

        // Hacemos la llamada AJAX a este mismo archivo, pero con parámetros GET
        fetch(`clientes.php?action=get_historial&id=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                historialContenido.innerHTML = '';
                if (data.error) throw new Error(data.error);
                if (data.length === 0) {
                    historialContenido.innerHTML = '<div class="col-12"><div class="alert alert-info">Este cliente no tiene historial de visitas.</div></div>';
                    return;
                }
                
                data.forEach(estancia => {
                    const fechaInicio = new Date(estancia.fecha_inicio.replace(' ', 'T'));
                    const fechaFormateada = fechaInicio.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    
                    let tituloHtml = `<i class="fas fa-file-alt text-muted"></i> Ocupación #${estancia.id_ocupacion || ''}`;
                    if (estancia.comprobante_tipo && estancia.comprobante_numero) {
                        tituloHtml = `<i class="fas fa-receipt text-success"></i> <strong>${estancia.comprobante_tipo.toUpperCase()} N° ${estancia.comprobante_numero}</strong>`;
                    }

                    const estadoPagoHtml = estancia.pagado_completo
                        ? '<span class="badge bg-success">Pagado</span>'
                        : '<span class="badge bg-warning text-dark">Pendiente</span>';

                    const cardHtml = `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header fw-bold">${tituloHtml}</div>
                                <div class="card-body">
                                    <p class="card-text mb-1"><i class="fas fa-bed text-primary"></i> <strong>Hab.:</strong> ${estancia.numero_habitacion} (${estancia.tipo_habitacion})</p>
                                    <p class="card-text mb-1"><i class="fas fa-moon text-primary"></i> <strong>Estancia:</strong> ${estancia.estadia_dias} días</p>
                                    <p class="card-text mb-2"><i class="fas fa-dollar-sign text-primary"></i> <strong>Monto:</strong> S/ ${parseFloat(estancia.monto_total).toFixed(2)}</p>
                                    <p class="card-text mb-0"><i class="far fa-credit-card text-primary"></i> <strong>Pago:</strong> ${estancia.ultimo_metodo_pago || 'No reg.'}</p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small class="text-muted">${fechaFormateada}</small>
                                    ${estadoPagoHtml}
                                </div>
                            </div>
                        </div>
                    `;
                    historialContenido.innerHTML += cardHtml;
                });
            })
            .catch(error => {
                historialContenido.innerHTML = `<div class="col-12"><div class="alert alert-danger">No se pudo cargar el historial: ${error.message}</div></div>`;
            });
    }
});
</script>

<?php require_once 'templates/footer.php'; ?>