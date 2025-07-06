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

function render_habitacion($num, $data, $tipos) {
    // 1. Verificamos que la habitación exista en los datos.
    if (!isset($data[$num])) {
        return;
    }
    
    // 2. Asignamos los datos a una variable más corta.
    $hab = $data[$num];
    
    // 3. Obtenemos la inicial del tipo desde nuestro array estático.
    $tipo_inicial = $tipos[$num] ?? '?';
    
    // 4. Limpiamos el número de habitación.
    $numero_limpio = htmlspecialchars(trim($hab['numero_habitacion']));
    
    // 5. Generamos el HTML:
    //    - El contenedor principal SIGUE SIENDO el <div class='habitacion'>.
    //    - El 'data-numero' se mantiene para que tu JavaScript lo encuentre.
    //    - Dentro, añadimos los nuevos elementos visuales.
    echo "
        <div class='habitacion {$hab['estado']}' data-numero='{$numero_limpio}'>
            <div class='tipo-indicador'>{$tipo_inicial}</div>
            <span class='numero-habitacion'>{$numero_limpio}</span>
            <div class='status-bar'></div>
        </div>
    ";
}

$tipos_habitacion = [
    '102'=>'M', '103'=>'S', '104'=>'C', '105'=>'T',
    '201'=>'M', '202'=>'M', '203'=>'M', '204'=>'S', '205'=>'C', '206'=>'T', '207'=>'M', '208'=>'M', '209'=>'M', '210'=>'M', '211'=>'D', '212'=>'T', '213'=>'M',
    '301'=>'M', '302'=>'M', '303'=>'M', '304'=>'S', '305'=>'C', '306'=>'T', '307'=>'M', '308'=>'M', '309'=>'M', '310'=>'M', '311'=>'D', '312'=>'T', '313'=>'M',
    '401'=>'M', '402'=>'M', '403'=>'M', '404'=>'S', '405'=>'C', '406'=>'T', '407'=>'M', '408'=>'M', '409'=>'M', '410'=>'M', '411'=>'D', '412'=>'T', '413'=>'M',
    '501'=>'T', '502'=>'M', '503'=>'M', '504'=>'S', '505'=>'C', '506'=>'T', '507'=>'M', '508'=>'M', '509'=>'M', '510'=>'M', '511'=>'D', '512'=>'T', '513'=>'M',
    '601'=>'C', '602'=>'C', '603'=>'T', '604'=>'C', '605'=>'T'
];
?>

<style>
:root {
    --primary-color: #4A55A2;
    --background-color: #F0F2F5;
    --success-color: #22c55e;
    --warning-color: #f59e0b;
    --text-color: #334155;
    --font-heading: 'Poppins', sans-serif;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.piso-columna {
    display: flex; flex-direction: column; gap: 1rem;
    opacity: 0; animation: fadeInUp 0.6s ease-out forwards;
    animation-delay: var(--delay);
}
.pisos-container {
    display: flex; flex-wrap: wrap; gap: 2.5rem;
    justify-content: center; padding: 1.5rem 0;
}
.habitacion-link { text-decoration: none; }
.habitacion {
    width: 110px; height: 140px; border-radius: 12px; position: relative;
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    background-color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0; transition: all 0.3s ease; overflow: hidden;
}
.habitacion-link:hover .habitacion {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 10px 20px rgba(0,0,0,0.07);
}
.numero-habitacion {
    font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700;
    color: var(--text-color); line-height: 1;
}
.tipo-indicador {
    position: absolute; top: 8px; right: 8px; width: 28px; height: 28px;
    background-color: rgba(0, 0, 0, 0.05); border-radius: 50%;
    display: flex; justify-content: center; align-items: center;
    font-weight: 600; font-size: 0.9rem; color: var(--text-color);
}
.status-bar {
    position: absolute; bottom: 0; left: 0; width: 100%; height: 8px;
    transition: height 0.3s ease;
}
.habitacion-link:hover .status-bar { height: 12px; }
.habitacion.disponible .status-bar { background-color: var(--primary-color); }
.habitacion.pago-pendiente .status-bar { background-color: var(--warning-color); }
.habitacion.pagada .status-bar { background-color: var(--success-color); }
.leyenda { width: 15px; height: 15px; border-radius: 4px; }
.leyenda.disponible { background-color: var(--primary-color); }
.leyenda.pago-pendiente { background-color: var(--warning-color); }
.leyenda.pagada { background-color: var(--success-color); }
</style>

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
    <!-- Se añade el array $tipos_habitacion como tercer parámetro en cada llamada -->
    <div class="piso-columna" style="--delay: 0s;"><?php foreach (['102', '103', '104', '105'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div>
    <div class="piso-columna" style="--delay: 0.1s;"><?php foreach (['201', '202', '203', '204', '205', '206'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?><div class="grupo-habitaciones mt-3"><?php foreach (['207', '208', '209', '210', '211', '212', '213'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div></div>
    <div class="piso-columna" style="--delay: 0.2s;"><?php foreach (['301', '302', '303', '304', '305', '306'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?><div class="grupo-habitaciones mt-3"><?php foreach (['307', '308', '309', '310', '311', '312', '313'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div></div>
    <div class="piso-columna" style="--delay: 0.3s;"><?php foreach (['401', '402', '403', '404', '405', '406'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?><div class="grupo-habitaciones mt-3"><?php foreach (['407', '408', '409', '410', '411', '412', '413'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div></div>
    <div class="piso-columna" style="--delay: 0.4s;"><?php foreach (['501', '502', '503', '504', '505', '506'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?><div class="grupo-habitaciones mt-3"><?php foreach (['507', '508', '509', '510', '511', '512', '513'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div></div>
    <div class="piso-columna" style="--delay: 0.5s;"><?php foreach (['601', '602', '603', '604', '605'] as $num) render_habitacion($num, $habitaciones, $tipos_habitacion); ?></div>
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

<!-- Modal 2: Registrar Pago (ACTUALIZADO CON EDICIÓN DE TAXI Y MÁS) -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Gestionar Ocupación - Habitación <span id="pago-numero-hab"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Info de la Ocupación -->
                <p class="mb-1"><strong>Tipo:</strong> <span id="pago-tipo-hab"></span></p>
                <p><strong>Estancia:</strong> Día <span id="pago-dia-actual"></span> de <span id="pago-dias-totales"></span></p>
                <div class="alert alert-info">
                    <strong>Cliente:</strong> <span id="pago-cliente-nombre"></span><br>
                    <strong>DNI:</strong> <span id="pago-cliente-dni"></span>
                </div>

                <!-- Acordeón para Editar Datos Adicionales -->
                <div class="accordion" id="accordionAdicionales">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdicionales" aria-expanded="false" aria-controls="collapseAdicionales">
                                Editar Datos de Taxi
                            </button>
                        </h2>
                        <div id="collapseAdicionales" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionAdicionales">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    <div class="col-md-7">
                                        <label for="pago-taxi-info" class="form-label">Info Taxi</label>
                                        <input type="text" id="pago-taxi-info" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="pago-taxi-comision" class="form-label">Comisión (S/)</label>
                                        <input type="number" id="pago-taxi-comision" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary mt-2" id="btn-guardar-taxi">Guardar Cambios de Taxi</button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Resumen Financiero -->
                <div class="text-center mb-3">
                    <h5>Monto Total: S/ <span id="pago-monto-total"></span></h5>
                    <h6>Monto Pagado: S/ <span id="pago-monto-pagado"></span></h6>
                    <h5 class="text-danger">Saldo Pendiente: S/ <span id="pago-saldo-pendiente"></span></h5>
                </div>
                <hr>

                <!-- Formulario de Nuevo Pago -->
                <h6><i class="fas fa-cash-register"></i> Registrar Nuevo Pago</h6>
                 <div class="row g-3">
                    <div class="col-md-12">
                         <label for="pago-monto" class="form-label">Monto a Registrar (S/)</label>
                         <input type="number" class="form-control" id="pago-monto" step="0.10" placeholder="0.00">
                    </div>
                    <!-- NUEVO: Campo para número de comprobante -->
                    <div class="col-md-12">
                        <label for="pago-numero-comprobante" class="form-label">N° de Boleta/Factura</label>
                        <input type="text" class="form-control" id="pago-numero-comprobante">
                    </div>
                     <div class="col-md-6">
                        <label for="pago-metodo" class="form-label">Método de Pago</label>
                        <select id="pago-metodo" class="form-select">
                            <option selected>Efectivo</option>
                            <option>Yape</option>
                            <option>Plin</option>
                            <option>Tarjeta (OpenPay)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                         <label for="pago-comprobante" class="form-label">Comprobante</label>
                        <select id="pago-comprobante" class="form-select">
                            <option value="">(Solo al saldar deuda)</option>
                            <option value="Boleta">Boleta</option>
                            <option value="Factura">Factura</option>
                        </select>
                    </div>
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