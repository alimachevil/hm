<?php 
// 1. Definimos el título de la página. Esta variable será usada en 'header.php'.
$page_title = "Gestión de Clientes"; 

// 2. Incluimos el "pan de arriba": el doctype, head, CSS, y el sidebar.
require_once 'templates/header.php'; 

// 3. Lógica PHP específica para esta página: obtener la lista de todos los clientes.
$stmt = $pdo->query("SELECT id, documento_identidad, nombre, telefono FROM Clientes ORDER BY nombre ASC");
$clientes = $stmt->fetchAll();
?>

<!-- ==================================================== -->
<!--           CONTENIDO PRINCIPAL (EL RELLENO)           -->
<!-- ==================================================== -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Gestión de Clientes</h1>

    <!-- Área de la Lista de Clientes -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users"></i> Lista de Clientes Registrados
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre y Apellidos</th>
                            <th>Teléfono</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr style="cursor: pointer;" data-cliente-id="<?php echo $cliente['id']; ?>" title="Haz clic para ver el historial">
                                <td><?php echo htmlspecialchars($cliente['documento_identidad']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono'] ?? 'N/A'); ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary btn-edit" 
                                            data-id="<?php echo $cliente['id']; ?>"
                                            data-dni="<?php echo htmlspecialchars($cliente['documento_identidad']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>"
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

    <!-- Área para mostrar el Historial (inicialmente oculta) -->
    <div id="historial-cliente-container" class="mt-5" style="display: none;">
        <h2 class="mb-3">Historial de Visitas: <span id="historial-nombre-cliente" class="text-primary"></span></h2>
        <div id="historial-contenido" class="row">
            <!-- El historial se generará aquí con JavaScript -->
        </div>
    </div>
</main>

<!-- ==================================================== -->
<!--     MODALES Y SCRIPT ESPECÍFICOS PARA ESTA PÁGINA    -->
<!-- ==================================================== -->

<!-- Modal para Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Información del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-cliente" onsubmit="return false;">
                    <input type="hidden" id="edit-cliente-id" name="id">
                    <div class="mb-3">
                        <label for="edit-cliente-dni" class="form-label">Documento de Identidad</label>
                        <input type="text" class="form-control" id="edit-cliente-dni" name="dni" readonly>
                        <small class="form-text text-muted">El documento de identidad no se puede modificar.</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit-cliente-nombre" class="form-label">Nombre y Apellidos</label>
                        <input type="text" class="form-control" id="edit-cliente-nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-cliente-telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="edit-cliente-telefono" name="telefono">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-cambios-cliente"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Incluimos el script JS específico para esta página de clientes -->
<script src="assets/clientes.js"></script>

<?php 
// 4. Incluimos el "pan de abajo": el cierre de las etiquetas principales y los scripts base.
require_once 'templates/footer.php'; 
?>