document.addEventListener('DOMContentLoaded', function () {

    // =================================================================
    //  SELECCIÓN DE ELEMENTOS DEL DOM (Específicos para index.php)
    // =================================================================
    const pisosContainer = document.querySelector('.pisos-container');
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    
    // Modales y sus elementos
    const modalRegistroElement = document.getElementById('modalRegistro');
    const modalPagoElement = document.getElementById('modalPago');
    const modalInfoElement = document.getElementById('modalInfo');
    const modalRegistro = new bootstrap.Modal(modalRegistroElement);
    const modalPago = new bootstrap.Modal(modalPagoElement);
    const modalInfo = new bootstrap.Modal(modalInfoElement);

    // Botones de acción
    const btnConfirmarRegistro = document.getElementById('btn-confirmar-registro');
    const btnConfirmarPago = document.getElementById('btn-confirmar-pago');
    const btnBuscarCliente = document.getElementById('btn-buscar-cliente');
    const btnGuardarTaxi = document.getElementById('btn-guardar-taxi');
    const btnLiberarHabitacion = document.getElementById('btn-liberar-habitacion');

    // =================================================================
    //  LÓGICA DEL SIDEBAR
    // =================================================================
    sidebarCollapse.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });

    // =================================================================
    //  LÓGICA PARA BUSCAR CLIENTE POR DNI
    // =================================================================
    btnBuscarCliente.addEventListener('click', function() {
        const dniInput = document.getElementById('cliente-dni');
        const dni = dniInput.value.trim();

        if (dni === '') {
            alert('Por favor, ingrese un documento de identidad para buscar.');
            return;
        }

        btnBuscarCliente.disabled = true;
        btnBuscarCliente.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`api/buscar_cliente.php?dni=${dni}`)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    document.getElementById('cliente-nombre').value = data.nombre;
                    document.getElementById('cliente-celular').value = data.telefono || '';
                } else {
                    alert('Cliente no encontrado. Puede registrarlo como nuevo.');
                    document.getElementById('cliente-nombre').value = '';
                    document.getElementById('cliente-celular').value = '';
                }
            })
            .catch(error => {
                console.error('Error en la búsqueda:', error);
                alert('Ocurrió un error al buscar el cliente.');
            })
            .finally(() => {
                btnBuscarCliente.disabled = false;
                btnBuscarCliente.innerHTML = 'Buscar';
            });
    });

    // =================================================================
    //  LÓGICA PARA LIBERAR HABITACIÓN MANUALMENTE
    // =================================================================
    btnLiberarHabitacion.addEventListener('click', function() {
        const ocupacionId = modalInfoElement.dataset.ocupacionId;
        const numeroHab = modalInfoElement.querySelector('#info-numero-hab').textContent;

        if (!ocupacionId) {
            alert('Error: No se pudo identificar la ocupación.');
            return;
        }

        if (!confirm(`¿Está seguro de que desea liberar la habitación ${numeroHab}? Esta acción la marcará como disponible.`)) {
            return;
        }

        fetch('api/liberar_habitacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ocupacion_id: ocupacionId })
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert(result.message);
                modalInfo.hide();
                window.location.reload(); // Recargar la página para ver el cambio de forma segura
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => alert('Error al liberar la habitación: ' + error.message));
    });

    // =================================================================
    //  LÓGICA PRINCIPAL DE INTERACCIÓN CON HABITACIONES
    // =================================================================
    pisosContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('habitacion')) {
            fetchRoomData(e.target.dataset.numero);
        }
    });

    function fetchRoomData(numeroHab) {
        fetch(`api/get_habitacion_info.php?numero=${numeroHab}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                
                switch (data.estado) {
                    case 'disponible': abrirModalRegistro(data.habitacion); break;
                    case 'pago-pendiente': abrirModalPago(data); break;
                    case 'pagada': abrirModalInfo(data); break;
                }
            })
            .catch(error => alert('Error al obtener datos: ' + error.message));
    }
    
    // =================================================================
    //  FUNCIONES PARA ABRIR Y POBLAR LOS MODALES
    // =================================================================
    function abrirModalRegistro(habitacion) {
        modalRegistroElement.querySelector('#reg-numero-hab').textContent = habitacion.numero_habitacion;
        modalRegistroElement.querySelector('#reg-tipo-hab').textContent = habitacion.tipo_nombre;
        modalRegistroElement.querySelector('#reg-costo-dia').value = parseFloat(habitacion.precio).toFixed(2);
        modalRegistroElement.querySelector('#cliente-dni').value = '';
        modalRegistroElement.querySelector('#cliente-nombre').value = '';
        modalRegistroElement.querySelector('#cliente-celular').value = '';
        modalRegistroElement.querySelector('#cliente-origen').value = ''; // Limpiar campo nuevo
        modalRegistroElement.querySelector('#reg-estancia').value = 1;
        modalRegistroElement.querySelector('#reg-adicional').value = '0.00';
        modalRegistroElement.querySelector('#reg-taxi-info').value = ''; // Limpiar campo nuevo
        modalRegistroElement.querySelector('#reg-taxi-comision').value = '0.00'; // Limpiar campo nuevo
        
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        modalRegistroElement.querySelector('#reg-hora-ingreso').value = `${hours}:${minutes}`; // Poner hora actual
        
        calcularMontoTotal();
        modalRegistro.show();
    }
    
    function abrirModalPago(data) {
        // Guardamos el ID de la ocupación en el modal para usarlo en otras funciones
        modalPagoElement.dataset.ocupacionId = data.ocupacion.id;

        const { habitacion, ocupacion, pago } = data;
        const montoPagado = parseFloat(pago.total_pagado) || 0;
        const montoTotal = parseFloat(ocupacion.monto_total);
        const saldo = montoTotal - montoPagado;

        // --- CORRECCIÓN DEL BUG "Día NaN" ---
        // La fecha de la BD viene como "YYYY-MM-DD HH:MM:SS".
        // Reemplazar el espacio por una 'T' lo hace compatible con el formato ISO 8601 que JavaScript entiende bien.
        const fechaInicio = new Date(ocupacion.fecha_inicio.replace(' ', 'T'));
        
        let diaActual = 1; // Valor por defecto si la fecha es inválida
        // Comprobamos que la fecha se haya interpretado correctamente antes de hacer cálculos
        if (!isNaN(fechaInicio.getTime())) { 
            const hoy = new Date();
            diaActual = Math.floor((hoy - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;
            if (diaActual > ocupacion.estadia_dias) diaActual = ocupacion.estadia_dias;
            if (diaActual < 1) diaActual = 1;
        }

        // --- Llenado de los campos que ya tenías ---
        modalPagoElement.querySelector('#pago-numero-hab').textContent = habitacion.numero_habitacion;
        modalPagoElement.querySelector('#pago-tipo-hab').textContent = habitacion.tipo_nombre;
        modalPagoElement.querySelector('#pago-dia-actual').textContent = diaActual; // Usa el valor corregido
        modalPagoElement.querySelector('#pago-dias-totales').textContent = ocupacion.estadia_dias;
        modalPagoElement.querySelector('#pago-cliente-nombre').textContent = ocupacion.cliente_nombre;
        modalPagoElement.querySelector('#pago-cliente-dni').textContent = ocupacion.cliente_dni;
        modalPagoElement.querySelector('#pago-monto-total').textContent = montoTotal.toFixed(2);
        modalPagoElement.querySelector('#pago-monto-pagado').textContent = montoPagado.toFixed(2);
        modalPagoElement.querySelector('#pago-saldo-pendiente').textContent = saldo.toFixed(2);

        // --- NUEVO: Llenado de los nuevos campos de Taxi y limpieza de campos de pago ---
        // Rellenamos los campos del acordeón de "Editar Datos de Taxi"
        modalPagoElement.querySelector('#pago-taxi-info').value = ocupacion.taxi_info || '';
        modalPagoElement.querySelector('#pago-taxi-comision').value = ocupacion.taxi_comision || '0.00';
        
        // Limpiamos los campos del formulario de "Registrar Nuevo Pago"
        modalPagoElement.querySelector('#pago-monto').value = '';
        modalPagoElement.querySelector('#pago-numero-comprobante').value = '';
        modalPagoElement.querySelector('#pago-comprobante').value = ''; // Reseteamos el select de comprobante
        
        // --- Mostrar el modal ---
        modalPago.show();
    }
    
    function abrirModalInfo(data) {
        modalInfoElement.dataset.ocupacionId = data.ocupacion.id;
        const { habitacion, ocupacion } = data;
        const montoTotal = parseFloat(ocupacion.monto_total);
        
        let fechaFormateada = "No disponible";
        if (ocupacion.fecha_inicio) {
            const fechaParts = ocupacion.fecha_inicio.split('-');
            const fechaInicio = new Date(fechaParts[0], fechaParts[1] - 1, fechaParts[2]);
            if (!isNaN(fechaInicio.getTime())) {
                fechaFormateada = fechaInicio.toLocaleDateString('es-ES', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
            }
        }
        
        let diaActual = 1;
        const fechaInicioObj = new Date(ocupacion.fecha_inicio + 'T00:00:00');
        if (!isNaN(fechaInicioObj.getTime())) {
            const hoy = new Date();
            diaActual = Math.floor((hoy - fechaInicioObj) / (1000 * 60 * 60 * 24)) + 1;
            if (diaActual > ocupacion.estadia_dias) diaActual = ocupacion.estadia_dias;
            if (diaActual < 1) diaActual = 1;
        }

        const findAndFill = (selector, value) => {
            const el = modalInfoElement.querySelector(selector);
            if (el) el.textContent = value;
        };
        
        findAndFill('#info-numero-hab', habitacion.numero_habitacion);
        findAndFill('#info-tipo-hab', habitacion.tipo_nombre);
        findAndFill('#info-fecha-inicio', fechaFormateada);
        findAndFill('#info-dia-actual', diaActual);
        findAndFill('#info-dias-totales', ocupacion.estadia_dias);
        findAndFill('#info-cliente-nombre', ocupacion.cliente_nombre);
        findAndFill('#info-cliente-dni', ocupacion.cliente_dni);
        findAndFill('#info-monto-total', montoTotal.toFixed(2));
        
        modalInfo.show();
    }

    // =================================================================
    //  LÓGICA PARA ENVIAR FORMULARIOS
    // =================================================================
    btnConfirmarRegistro.addEventListener('click', function() {
    
        // 1. Creamos el objeto 'dataToSend' con claves claras y consistentes.
        //    Cada clave corresponde a un campo que el PHP esperará.
        const dataToSend = {
            // Datos de la Ocupación
            numero_habitacion: document.getElementById('reg-numero-hab').textContent,
            
            // --- ESTA ES LA LÍNEA CORREGIDA ---
            estadia_dias: document.getElementById('reg-estancia').value, // La clave ahora es 'estadia_dias'
            
            costo_dia: document.getElementById('reg-costo-dia').value,
            adicional_descuento: document.getElementById('reg-adicional').value,
            monto_total: document.getElementById('reg-monto-total').textContent,
            
            // Datos del Cliente
            cliente_dni: document.getElementById('cliente-dni').value.trim(),
            cliente_nombre: document.getElementById('cliente-nombre').value.trim(),
            cliente_celular: document.getElementById('cliente-celular').value.trim(),
            cliente_origen: document.getElementById('cliente-origen').value.trim(),
        
            // Nuevos datos de Registro
            hora_ingreso: document.getElementById('reg-hora-ingreso').value,
            taxi_info: document.getElementById('reg-taxi-info').value.trim(),
            taxi_comision: document.getElementById('reg-taxi-comision').value
        };
    
        // 2. Validación simple
        if (!dataToSend.cliente_dni || !dataToSend.cliente_nombre) {
            alert('El DNI y el Nombre del cliente son obligatorios.');
            return;
        }
    
        // 3. Petición Fetch para enviar los datos al backend
        fetch('api/registrar_ocupacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dataToSend)
        })
        .then(response => {
            // Mejor manejo de errores para ver qué devuelve el PHP
            if (!response.ok) {
                return response.text().then(text => { throw new Error("Error del servidor: " + text) });
            }
            return response.json();
        })
        .then(result => {
            if (result.status === 'success') {
                alert(result.message);
                window.location.reload();
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => {
            alert('Error al registrar la ocupación. Revisa la consola para más detalles.');
            console.error('Error detallado:', error);
        });
    });

    btnConfirmarPago.addEventListener('click', function() {
        const dataToSend = {
            ocupacion_id: modalPagoElement.dataset.ocupacionId,
            monto_pagado: document.getElementById('pago-monto').value,
            metodo_pago: document.getElementById('pago-metodo').value,
            comprobante: document.getElementById('pago-comprobante').value,
            numero_comprobante: document.getElementById('pago-numero-comprobante').value,
        };

        if (!dataToSend.monto_pagado || dataToSend.monto_pagado <= 0) {
            alert('Debe ingresar un monto de pago válido y mayor a cero.');
            return;
        }

        fetch('api/registrar_pago.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert(result.message);
                window.location.reload(); // Recargar la página
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => alert('Error al registrar pago: ' + error.message));
    });

    btnGuardarTaxi.addEventListener('click', function() {
        const ocupacionId = modalPagoElement.dataset.ocupacionId;
        const data = {
            ocupacion_id: ocupacionId,
            taxi_info: document.getElementById('pago-taxi-info').value,
            taxi_comision: document.getElementById('pago-taxi-comision').value,
        };
        fetch('api/update_taxi_info.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                alert(result.message);
            } else { throw new Error(result.message); }
        })
        .catch(error => alert('Error: ' + error.message));
    });

    // =================================================================
    //  CÁLCULO DINÁMICO EN MODAL DE REGISTRO
    // =================================================================
    const estanciaInput = document.getElementById('reg-estancia');
    const costoDiaInput = document.getElementById('reg-costo-dia');
    const adicionalInput = document.getElementById('reg-adicional');
    
    function calcularMontoTotal() {
        const estancia = parseInt(estanciaInput.value) || 0;
        const costoDia = parseFloat(costoDiaInput.value) || 0;
        const adicional = parseFloat(adicionalInput.value) || 0;
        const total = (estancia * costoDia) + adicional;
        document.getElementById('reg-monto-total').textContent = total.toFixed(2);
    }
    
    if (estanciaInput && costoDiaInput && adicionalInput) {
        estanciaInput.addEventListener('input', calcularMontoTotal);
        costoDiaInput.addEventListener('input', calcularMontoTotal);
        adicionalInput.addEventListener('input', calcularMontoTotal);
    }
});