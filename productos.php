<?php
// Incluimos la conexión a la base de datos una sola vez.
require_once 'config/db_connect.php';

// ===================================================================
//  PROCESAMIENTO DEL FORMULARIO DE NUEVO PRODUCTO
// ===================================================================
// Este bloque solo se ejecuta si la página recibe una petición POST con la acción correcta.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_producto') {
    
    // Recolectamos y validamos los datos del formulario.
    $nombre = trim($_POST['nombre'] ?? '');
    $stock = $_POST['stock'] ?? 0;
    $precio = $_POST['precio'] ?? 0.00;

    // Verificamos que los datos esenciales no estén vacíos y sean del tipo correcto.
    if (!empty($nombre) && is_numeric($stock) && is_numeric($precio)) {
        $pdo->beginTransaction();
        try {
            // 1. Insertamos el nuevo producto.
            $stmt = $pdo->prepare("INSERT INTO Productos (nombre, stock, precio) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $stock, $precio]);
            $nuevo_id = $pdo->lastInsertId();

            // 2. Registramos la creación en el historial de stock.
            if ($stock > 0) {
                $stmt_log = $pdo->prepare("INSERT INTO Historial_Stock (producto_id, stock_anterior, stock_nuevo, cambio, motivo) VALUES (?, ?, ?, ?, ?)");
                $stmt_log->execute([$nuevo_id, 0, $stock, $stock, 'Creación de producto (Stock inicial)']);
            }
            
            $pdo->commit();
            // 3. Redirigimos a la misma página con un mensaje de éxito para evitar reenvío de formulario.
            header('Location: productos.php?status=add_success');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Redirigimos con un mensaje de error específico.
            $error_message = urlencode($e->getCode() == 23000 ? 'Error: Ya existe un producto con ese nombre.' : 'Error en el servidor al añadir el producto.');
            header('Location: productos.php?status=add_error&message=' . $error_message);
            exit;
        }
    } else {
        // Redirigimos si los datos enviados no son válidos.
        $error_message = urlencode('Los datos proporcionados para el nuevo producto son inválidos.');
        header('Location: productos.php?status=add_error&message=' . $error_message);
        exit;
    }
}
// ===================================================================


// --- Lógica para la carga normal de la página (GET) ---
$page_title = "Gestión de Productos";
require_once 'templates/header.php'; 

// Las consultas para mostrar la página no cambian.
$productos_stmt = $pdo->query("SELECT id, nombre, stock, precio FROM Productos ORDER BY nombre ASC");
$lista_productos = $productos_stmt->fetchAll();
$hab_stmt = $pdo->query("SELECT h.numero_habitacion FROM Habitaciones h JOIN Ocupaciones o ON h.numero_habitacion = o.habitacion_id WHERE o.activa = 1 ORDER BY h.numero_habitacion ASC");
$lista_habitaciones_ocupadas = $hab_stmt->fetchAll();
$fiados_stmt = $pdo->query("SELECT v.id AS venta_id, v.monto_total, v.monto_pagado, v.fecha_venta, o.habitacion_id, c.nombre AS cliente_nombre FROM Ventas v JOIN Ocupaciones o ON v.ocupacion_id = o.id JOIN Clientes c ON o.cliente_id = c.id WHERE v.pago_pendiente = 1 ORDER BY v.fecha_venta DESC");
$lista_fiados = $fiados_stmt->fetchAll();
?>

<!-- Contenido Principal -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Productos y Ventas</h1>

    <!-- Mostrar mensajes de éxito o error después de la redirección -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'add_success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Producto añadido exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'add_error'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Columna Izquierda: Inventario y Añadir Producto -->
        <div class="col-lg-7">
            
            <!-- Card de Inventario de Productos -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-box-open"></i> Inventario de Productos</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Stock Actual</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-inventario">
                                <?php foreach ($lista_productos as $p): ?>
                                    <tr id="producto-row-<?php echo $p['id']; ?>">
                                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                        <td class="text-center fw-bold stock-val"><?php echo $p['stock']; ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-secondary btn-update-stock" 
                                                    data-id="<?php echo $p['id']; ?>" 
                                                    data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>" 
                                                    data-stock="<?php echo $p['stock']; ?>">
                                                Ajustar Stock
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card para Añadir Nuevo Producto (Formulario modificado) -->
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle"></i> Añadir Nuevo Producto</div>
                <div class="card-body">
                    <!-- El formulario ahora envía los datos a esta misma página por POST -->
                    <form method="POST" action="productos.php">
                        <!-- Campo oculto para identificar la acción -->
                        <input type="hidden" name="action" value="add_producto">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="nuevo-producto-nombre" class="form-label">Nombre del Producto</label>
                                <input type="text" id="nuevo-producto-nombre" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="nuevo-producto-stock" class="form-label">Stock Inicial</label>
                                <input type="number" id="nuevo-producto-stock" name="stock" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label for="nuevo-producto-precio" class="form-label">Precio (S/)</label>
                                <input type="number" id="nuevo-producto-precio" name="precio" class="form-control" step="0.10" value="0.00" min="0" required>
                            </div>
                            <div class="col-12 mt-3">
                                <!-- El botón ahora es de tipo 'submit' -->
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Guardar Nuevo Producto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Columna Derecha: Formulario de Venta -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="fas fa-cash-register"></i> Registrar Venta</div>
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3 border-bottom pb-3">
                        <div class="col-sm-7">
                            <label for="venta-producto" class="form-label">Producto</label>
                            <select id="venta-producto" class="form-select">
                                <?php foreach ($lista_productos as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" data-precio="<?php echo $p['precio']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="venta-cantidad" class="form-label">Cantidad</label>
                            <input type="number" id="venta-cantidad" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary w-100" id="btn-add-producto" title="Añadir producto a la venta"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <h6>Productos en la Venta</h6>
                    <ul id="lista-venta-productos" class="list-group mb-3">
                        <li class="list-group-item text-muted">Añada productos a la venta...</li>
                    </ul>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="venta-habitacion" class="form-label">Vender a Habitación:</label>
                            <select id="venta-habitacion" class="form-select">
                                <?php if (empty($lista_habitaciones_ocupadas)): ?>
                                    <option value="">No hay habitaciones ocupadas</option>
                                <?php else: ?>
                                    <?php foreach ($lista_habitaciones_ocupadas as $h): ?>
                                        <option value="<?php echo $h['numero_habitacion']; ?>"><?php echo $h['numero_habitacion']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="venta-monto-pagado" class="form-label">Monto Pagado (S/)</label>
                            <input type="number" id="venta-monto-pagado" class="form-control" step="0.10" value="0.00">
                        </div>
                        <div class="col-6 text-end">
                            <label class="form-label">Monto Total</label>
                            <h3 class="fw-bold">S/ <span id="venta-monto-total">0.00</span></h3>
                        </div>
                    </div>
                    <button class="btn btn-success w-100 mt-3" id="btn-confirmar-venta">Confirmar Venta</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Fiados -->
    <?php if (!empty($lista_fiados)): ?>
    <div class="card mt-5" id="seccion-fiados">
        <div class="card-header bg-warning"><i class="fas fa-exclamation-triangle"></i> Pagos de Productos Pendientes (Fiados)</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Fecha Venta</th>
                            <th>Habitación</th>
                            <th>Cliente</th>
                            <th class="text-end">Deuda</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-fiados">
                        <?php foreach($lista_fiados as $fiado): ?>
                        <tr id="fiado-row-<?php echo $fiado['venta_id']; ?>"> 
                            <td><?php echo date("d/m/Y H:i", strtotime($fiado['fecha_venta'])); ?></td>
                            <td><?php echo htmlspecialchars($fiado['habitacion_id']); ?></td>
                            <td><?php echo htmlspecialchars($fiado['cliente_nombre']); ?></td>
                            <td class="text-end text-danger fw-bold">S/ <?php echo number_format($fiado['monto_total'] - $fiado['monto_pagado'], 2); ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-info btn-pagar-fiado"
                                        data-registro-id="<?php echo $fiado['venta_id']; ?>"
                                        data-deuda="<?php echo ($fiado['monto_total'] - $fiado['monto_pagado']); ?>">
                                    Registrar Pago
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<!-- ========= MODALES ========= -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Ajustar Stock de <span id="stock-nombre-producto" class="fw-bold"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Stock actual: <strong id="stock-actual"></strong></p><input type="hidden" id="stock-producto-id"><div class="mb-3"><label for="stock-nuevo" class="form-label">Nuevo Stock Total</label><input type="number" id="stock-nuevo" class="form-control" min="0"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btn-guardar-stock">Guardar Nuevo Stock</button></div></div></div>
</div>
<div class="modal fade" id="modalPagarFiado" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Registrar Pago de Deuda</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Deuda actual: <strong class="text-danger">S/ <span id="fiado-deuda-actual"></span></strong></p><input type="hidden" id="fiado-registro-id"><div class="mb-3"><label for="fiado-monto-pago" class="form-label">Monto a Pagar (S/)</label><input type="number" id="fiado-monto-pago" class="form-control" step="0.10" min="0"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="btn-confirmar-pago-fiado">Confirmar Pago</button></div></div></div>
</div>

<!-- Script específico para esta página -->
<script src="assets/productos.js"></script>

<?php require_once 'templates/footer.php'; ?>