<?php 
// 1. Definimos el título de la página. Esta variable será usada en 'header.php'.
$page_title = "Habitaciones"; 

// 2. Incluimos el "pan de arriba": el doctype, head, CSS, y el sidebar.
require_once 'templates/header.php'; 

// 3. Lógica PHP específica para esta página: obtener y procesar las habitaciones.

// Primero, la auto-liberación de habitaciones que ya terminaron su estancia.
try {
    $sql_checkout = "
        UPDATE Ocupaciones 
        SET activa = 0 
        WHERE activa = 1 AND DATE_ADD(fecha_inicio, INTERVAL estadia_dias DAY) < CURDATE()
    ";
    $pdo->exec($sql_checkout);
} catch (PDOException $e) {
    error_log("Error en el checkout automático: " . $e->getMessage());
}

// Segundo, la consulta principal para mostrar el estado de todas las habitaciones.
$sql = "
    SELECT 
        h.numero_habitacion, 
        h.precio,
        th.nombre as tipo_nombre,
        o.id as ocupacion_id,
        o.monto_total,
        o.fecha_inicio,
        o.estadia_dias,
        (SELECT SUM(p.monto_pagado) FROM Pagos p WHERE p.ocupacion_id = o.id) as total_pagado
    FROM Habitaciones h
    JOIN Tipos_Habitaciones th ON h.tipo_id = th.id
    LEFT JOIN Ocupaciones o ON h.numero_habitacion = o.habitacion_id AND o.activa = 1
    ORDER BY h.numero_habitacion ASC
";
$stmt = $pdo->query($sql);
$habitaciones = [];
while ($row = $stmt->fetch()) {
    $estado = 'disponible';
    if ($row['ocupacion_id']) {
        if (isset($row['total_pagado']) && $row['total_pagado'] >= $row['monto_total']) {
            $estado = 'pagada';
        } else {
            $estado = 'pago-pendiente';
        }
    }
    $row['estado'] = $estado;
    $habitaciones[$row['numero_habitacion']] = $row;
}

// Función auxiliar para imprimir el HTML de una habitación.
function render_habitacion($num, $data) {
    if (!isset($data[$num])) return;
    $hab = $data[$num];
    echo "<div class='habitacion {$hab['estado']}' data-numero='{$hab['numero_habitacion']}'>{$hab['numero_habitacion']}</div>";
}
?>

<!-- ==================================================== -->
<!--           CONTENIDO PRINCIPAL (EL RELLENO)           -->
<!-- ==================================================== -->
<main id="content" class="content p-4">
    <h1 class="mb-4">Mapa de Habitaciones</h1>
    
    <div class="d-flex align-items-center gap-4 mb-4">
        <div class="d-flex align-items-center gap-2"><div class="leyenda disponible"></div> Disponible</div>
        <div class="d-flex align-items-center gap-2"><div class="leyenda pago-pendiente"></div> Ocupada (Pago Pendiente)</div>
        <div class="d-flex align-items-center gap-2"><div class="leyenda pagada"></div> Ocupada (Pagada)</div>
    </div>

    <div class="pisos-container">
        <!-- Columna 1 -->
        <div class="piso-columna">
            <div class="grupo-habitaciones"><?php foreach (['102', '103', '104', '105'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
        <!-- Columna 2 -->
        <div class="piso-columna">
            <div class="grupo-habitaciones"><?php foreach (['201', '202', '203', '204', '205', '206'] as $num) render_habitacion($num, $habitaciones); ?></div>
            <div class="grupo-habitaciones mt-3"><?php foreach (['207', '208', '209', '210', '211', '212', '213'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
        <!-- Columna 3 -->
        <div class="piso-columna">
            <div class="grupo-habitaciones"><?php foreach (['301', '302', '303', '304', '305', '306'] as $num) render_habitacion($num, $habitaciones); ?></div>
            <div class="grupo-habitaciones mt-3"><?php foreach (['307', '308', '309', '310', '311', '312', '313'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
        <!-- Columna 4 -->
        <div class="piso-columna">
            <div class="grupo-habitaciones"><?php foreach (['401', '402', '403', '404', '405', '406'] as $num) render_habitacion($num, $habitaciones); ?></div>
            <div class="grupo-habitaciones mt-3"><?php foreach (['407', '408', '409', '410', '411', '412', '413'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
        <!-- Columna 5 -->
        <div class="piso-columna">
             <div class="grupo-habitaciones"><?php foreach (['501', '502', '503', '504', '505', '506'] as $num) render_habitacion($num, $habitaciones); ?></div>
            <div class="grupo-habitaciones mt-3"><?php foreach (['507', '508', '509', '510', '511', '512', '513'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
        <!-- Columna 6 -->
        <div class="piso-columna">
            <div class="grupo-habitaciones"><?php foreach (['601', '602', '603', '604', '605'] as $num) render_habitacion($num, $habitaciones); ?></div>
        </div>
    </div>
</main>

<!-- ==================================================== -->
<!--     MODALES Y SCRIPT ESPECÍFICOS PARA ESTA PÁGINA    -->
<!-- ==================================================== -->

<!-- Modal 1: Registrar Ocupación (ACTUALIZADO CON NUEVOS CAMPOS) -->
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Ocupación - Habitación <span id="reg-numero-hab"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tipo:</strong> <span id="reg-tipo-hab"></span></p>
                <hr>

                <!-- === Sección Cliente (con campo de Origen) === -->
                <h6><i class="fas fa-user-circle"></i> Datos del Cliente</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cliente-dni" class="form-label">Documento de Identidad</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cliente-dni" placeholder="Buscar o ingresar DNI">
                            <button class="btn btn-outline-secondary" type="button" id="btn-buscar-cliente">Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="cliente-nombre" class="form-label">Nombre y Apellidos</label>
                        <input type="text" class="form-control" id="cliente-nombre">
                    </div>
                    <div class="col-md-6">
                        <label for="cliente-celular" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="cliente-celular">
                    </div>
                    <!-- NUEVO CAMPO: Origen del Cliente -->
                    <div class="col-md-6">
                        <label for="cliente-origen" class="form-label">País / Ciudad de Origen</label>
                        <input type="text" class="form-control" id="cliente-origen">
                    </div>
                </div>
                <hr>

                <!-- === Sección Registro (con campos de Hora y Taxi) === -->
                <h6><i class="fas fa-calendar-alt"></i> Datos del Registro</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="reg-estancia" class="form-label">Estancia (días)</label>
                        <input type="number" class="form-control" id="reg-estancia" value="1" min="1">
                    </div>
                    <div class="col-md-4">
                        <label for="reg-costo-dia" class="form-label">Costo por día (S/)</label>
                        <input type="number" class="form-control" id="reg-costo-dia" step="0.50">
                    </div>
                    <div class="col-md-4">
                        <label for="reg-adicional" class="form-label">Adicional / Descuento (S/)</label>
                        <input type="number" class="form-control" id="reg-adicional" value="0.00" step="0.50">
                    </div>

                    <!-- NUEVOS CAMPOS: Hora de Ingreso y Taxi -->
                    <div class="col-md-4">
                        <label for="reg-hora-ingreso" class="form-label">Hora de Ingreso</label>
                        <input type="time" class="form-control" id="reg-hora-ingreso">
                    </div>
                    <div class="col-md-4">
                        <label for="reg-taxi-info" class="form-label">Info Taxi (Placa/Nombre)</label>
                        <input type="text" class="form-control" id="reg-taxi-info">
                    </div>
                    <div class="col-md-4">
                        <label for="reg-taxi-comision" class="form-label">Comisión Taxi (S/)</label>
                        <input type="number" class="form-control" id="reg-taxi-comision" value="0.00" step="0.50">
                    </div>
                    
                    <div class="col-12 text-end mt-3">
                        <h4>Monto Total: S/ <span id="reg-monto-total">0.00</span></h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-registro"><i class="fas fa-save"></i> Registrar Ocupación</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Registrar Pago (Habitación Ocupada - Amarillo) -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Registrar Pago - Habitación <span id="pago-numero-hab"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1"><strong>Tipo:</strong> <span id="pago-tipo-hab"></span></p>
                <p><strong>Estancia:</strong> Día <span id="pago-dia-actual"></span> de <span id="pago-dias-totales"></span></p>
                <div class="alert alert-info" id="pago-info-cliente"><strong>Cliente:</strong> <span id="pago-cliente-nombre"></span><br><strong>DNI:</strong> <span id="pago-cliente-dni"></span></div>
                <div class="text-center mb-3">
                    <h5>Monto Total: S/ <span id="pago-monto-total"></span></h5>
                    <h6>Monto Pagado: S/ <span id="pago-monto-pagado"></span></h6>
                    <h5 class="text-danger">Saldo Pendiente: S/ <span id="pago-saldo-pendiente"></span></h5>
                </div>
                <hr>
                 <h6><i class="fas fa-cash-register"></i> Registrar Nuevo Pago</h6>
                 <div class="row g-3">
                    <div class="col-md-12"><label for="pago-monto" class="form-label">Monto a Registrar (S/)</label><input type="number" class="form-control" id="pago-monto" step="0.50" placeholder="0.00"></div>
                     <div class="col-md-6"><label for="pago-metodo" class="form-label">Método de Pago</label><select id="pago-metodo" class="form-select"><option selected>Efectivo</option><option>Yape/Plin</option><option>Tarjeta (OpenPay)</option></select></div>
                    <div class="col-md-6"><label for="pago-comprobante" class="form-label">Comprobante</label><select id="pago-comprobante" class="form-select"><option selected>Boleta</option><option>Factura</option></select></div>
                 </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-confirmar-pago"><i class="fas fa-check"></i> Registrar Pago</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Ver Información (VERIFICADO) -->
<div class="modal fade" id="modalInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Información de Ocupación - Habitación <span id="info-numero-hab"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1"><strong>Tipo:</strong> <span id="info-tipo-hab"></span></p>
                <!-- ESTA ES LA LÍNEA CRÍTICA -->
                <p class="mb-1"><strong>Fecha de Inicio:</strong> <span id="info-fecha-inicio"></span></p>
                <p><strong>Estancia:</strong> Día <span id="info-dia-actual"></span> de <span id="info-dias-totales"></span></p>
                <hr>
                <h6><i class="fas fa-user-check"></i> Datos del Cliente</h6>
                <p><strong>Nombre:</strong> <span id="info-cliente-nombre"></span><br><strong>DNI:</strong> <span id="info-cliente-dni"></span></p>
                <hr>
                <h6><i class="fas fa-file-invoice-dollar"></i> Resumen Financiero</h6>
                <p class="mb-0"><strong>Monto Total:</strong> S/ <span id="info-monto-total"></span></p>
                <p class="text-success fw-bold">COMPLETAMENTE PAGADO</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="btn-liberar-habitacion"><i class="fas fa-undo"></i> Liberar Habitación</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Incluimos el script JS específico para esta página -->
<script src="assets/script.js"></script>

<?php 
// 4. Incluimos el "pan de abajo": el cierre de las etiquetas principales y los scripts base.
require_once 'templates/footer.php'; 
?>