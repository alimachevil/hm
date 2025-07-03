<?php 
$page_title = "Gestión de Productos";
require_once 'templates/header.php';

// Obtener lista de productos para los selects y la tabla
$productos_stmt = $pdo->query("SELECT id, nombre, stock, precio FROM Productos ORDER BY nombre ASC");
$lista_productos = $productos_stmt->fetchAll();

// Obtener habitaciones con ocupación activa para el select de venta
$hab_stmt = $pdo->query("SELECT h.numero_habitacion FROM Habitaciones h JOIN Ocupaciones o ON h.numero_habitacion = o.habitacion_id WHERE o.activa = 1 ORDER BY h.numero_habitacion ASC");
$lista_habitaciones_ocupadas = $hab_stmt->fetchAll();

// Obtener la lista de ventas fiadas (CONSULTA CORREGIDA)
$fiados_stmt = $pdo->query("
    SELECT 
        v.id AS venta_id,          -- El ID de la venta
        v.monto_total,
        v.monto_pagado,
        v.fecha_venta,             -- Usamos el nombre original de la columna, SIN ALIAS
        o.habitacion_id,
        c.nombre AS cliente_nombre
    FROM 
        Ventas v
    JOIN 
        Ocupaciones o ON v.ocupacion_id = o.id
    JOIN 
        Clientes c ON o.cliente_id = c.id
    WHERE 
        v.pago_pendiente = 1
    ORDER BY 
        v.fecha_venta DESC
");
$lista_fiados = $fiados_stmt->fetchAll();
?>

<!-- Contenido Principal -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Productos y Ventas</h1>

    <div class="row">
        <!-- Columna Izquierda: Lista de Productos -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="fas fa-box-open"></i> Inventario de Productos</div>
                <div class="card-body">
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

        <!-- Columna Derecha: Formulario de Venta -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="fas fa-cash-register"></i> Registrar Venta</div>
                <div class="card-body">
                    <!-- Formulario para añadir productos al "carrito" -->
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
                            <button class="btn btn-primary w-100" id="btn-add-producto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <!-- "Carrito" de compras -->
                    <h6>Productos en la Venta</h6>
                    <ul id="lista-venta-productos" class="list-group mb-3">
                        <li class="list-group-item text-muted">Añada productos a la venta...</li>
                    </ul>

                    <!-- Detalles finales de la venta -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="venta-habitacion" class="form-label">Vender a Habitación:</label>
                            <select id="venta-habitacion" class="form-select">
                                <?php foreach ($lista_habitaciones_ocupadas as $h): ?>
                                    <option value="<?php echo $h['numero_habitacion']; ?>"><?php echo $h['numero_habitacion']; ?></option>
                                <?php endforeach; ?>
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

        <!-- Sección de Fiados (CORREGIDA Y VERIFICADA) -->
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
                        <!-- Usamos venta_id para el id de la fila -->
                        <tr id="fiado-row-<?php echo $fiado['venta_id']; ?>"> 
                            <!-- Usamos fecha_venta para la fecha -->
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
<!-- Modal para Ajustar Stock -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Stock de <span id="stock-nombre-producto" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Stock actual: <strong id="stock-actual"></strong></p>
                <input type="hidden" id="stock-producto-id">
                <div class="mb-3">
                    <label for="stock-nuevo" class="form-label">Nuevo Stock Total</label>
                    <input type="number" id="stock-nuevo" class="form-control" min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-stock">Guardar Nuevo Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Pagar Fiado -->
<div class="modal fade" id="modalPagarFiado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago de Deuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Deuda actual: <strong class="text-danger">S/ <span id="fiado-deuda-actual"></span></strong></p>
                <input type="hidden" id="fiado-registro-id">
                <div class="mb-3">
                    <label for="fiado-monto-pago" class="form-label">Monto a Pagar (S/)</label>
                    <input type="number" id="fiado-monto-pago" class="form-control" step="0.10" min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-confirmar-pago-fiado">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>


<!-- Script específico para esta página -->
<script src="assets/productos.js"></script>

<?php require_once 'templates/footer.php'; ?>