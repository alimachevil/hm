document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const modalStock = new bootstrap.Modal(document.getElementById('modalStock'));
    const modalPagarFiado = new bootstrap.Modal(document.getElementById('modalPagarFiado'));
    const tablaInventario = document.getElementById('tabla-inventario');
    const tablaFiados = document.getElementById('tabla-fiados');

    // Formulario de venta
    const btnAddProducto = document.getElementById('btn-add-producto');
    const listaVentaProductos = document.getElementById('lista-venta-productos');
    const ventaMontoTotalSpan = document.getElementById('venta-monto-total');
    const btnConfirmarVenta = document.getElementById('btn-confirmar-venta');

    let carrito = [];

    // --- LÓGICA DE AJUSTE DE STOCK ---
    tablaInventario.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-update-stock')) {
            const btn = e.target;
            document.getElementById('stock-producto-id').value = btn.dataset.id;
            document.getElementById('stock-nombre-producto').textContent = btn.dataset.nombre;
            document.getElementById('stock-actual').textContent = btn.dataset.stock;
            document.getElementById('stock-nuevo').value = btn.dataset.stock;
            modalStock.show();
        }
    });

    document.getElementById('btn-guardar-stock').addEventListener('click', function() {
        const id = document.getElementById('stock-producto-id').value;
        const stock_nuevo = document.getElementById('stock-nuevo').value;
        
        fetch('api/update_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, stock_nuevo })
        })
        .then(res => res.json()).then(data => {
            if (data.status === 'success') {
                const row = document.getElementById('producto-row-' + id);
                row.querySelector('.stock-val').textContent = data.nuevo_stock;
                row.querySelector('.btn-update-stock').dataset.stock = data.nuevo_stock;
                modalStock.hide();
            } else { throw new Error(data.message); }
        }).catch(err => alert('Error: ' + err.message));
    });

    // --- LÓGICA DEL FORMULARIO DE VENTA ---
    btnAddProducto.addEventListener('click', function() {
        const productoSelect = document.getElementById('venta-producto');
        const cantidadInput = document.getElementById('venta-cantidad');
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        
        const producto = {
            id: parseInt(productoSelect.value),
            nombre: selectedOption.text,
            cantidad: parseInt(cantidadInput.value),
            precio: parseFloat(selectedOption.dataset.precio)
        };

        if (producto.cantidad <= 0) return;

        // Si el producto ya está en el carrito, solo aumenta la cantidad
        const existingProduct = carrito.find(p => p.id === producto.id);
        if (existingProduct) {
            existingProduct.cantidad += producto.cantidad;
        } else {
            carrito.push(producto);
        }
        renderCarrito();
    });

    function renderCarrito() {
        listaVentaProductos.innerHTML = '';
        if (carrito.length === 0) {
            listaVentaProductos.innerHTML = '<li class="list-group-item text-muted">Añada productos a la venta...</li>';
            ventaMontoTotalSpan.textContent = '0.00';
            return;
        }
        let total = 0;
        carrito.forEach((prod, index) => {
            const subtotal = prod.cantidad * prod.precio;
            total += subtotal;
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span>${prod.cantidad} x ${prod.nombre}</span>
                <div>
                    <span class="badge bg-primary rounded-pill me-2">S/ ${subtotal.toFixed(2)}</span>
                    <button class="btn btn-sm btn-danger btn-remove-item" data-index="${index}">×</button>
                </div>
            `;
            listaVentaProductos.appendChild(li);
        });
        ventaMontoTotalSpan.textContent = total.toFixed(2);
    }
    
    listaVentaProductos.addEventListener('click', function(e){
        if(e.target.classList.contains('btn-remove-item')){
            const index = e.target.dataset.index;
            carrito.splice(index, 1);
            renderCarrito();
        }
    });

    btnConfirmarVenta.addEventListener('click', function(){
        if(carrito.length === 0){
            alert('Añada al menos un producto a la venta.');
            return;
        }
        const dataToSend = {
            habitacion_id: document.getElementById('venta-habitacion').value,
            productos: carrito,
            monto_total: parseFloat(ventaMontoTotalSpan.textContent),
            monto_pagado: parseFloat(document.getElementById('venta-monto-pagado').value)
        };

        fetch('api/registrar_venta_producto.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(dataToSend)
        })
        .then(res => res.json()).then(data => {
            if(data.status === 'success'){
                alert(data.message);
                window.location.reload();
            } else { throw new Error(data.message); }
        }).catch(err => alert('Error: ' + err.message));
    });

    // --- LÓGICA DE PAGOS FIADOS ---
    if(tablaFiados){
        tablaFiados.addEventListener('click', function(e){
            if(e.target.classList.contains('btn-pagar-fiado')){
                const btn = e.target;
                // 'data-registro-id' es el nombre que le dimos en el HTML, lo mantenemos por consistencia
                document.getElementById('fiado-registro-id').value = btn.dataset.registroId; 
                const deuda = parseFloat(btn.dataset.deuda).toFixed(2);
                document.getElementById('fiado-deuda-actual').textContent = deuda;
                document.getElementById('fiado-monto-pago').value = deuda;
                modalPagarFiado.show();
            }
        });
    }

    document.getElementById('btn-confirmar-pago-fiado').addEventListener('click', function(){
        const dataToSend = {
            // Cambiamos el nombre de la clave para que coincida con lo que espera el PHP
            venta_id: document.getElementById('fiado-registro-id').value,
            monto: document.getElementById('fiado-monto-pago').value
        };
    
        fetch('api/pagar_fiado.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(dataToSend)
        })
        .then(res => res.json()).then(data => {
            if(data.status === 'success'){
                modalPagarFiado.hide();
                if(data.deuda_saldada){
                    // Ahora el id de la fila es 'fiado-row-...'
                    document.getElementById('fiado-row-' + dataToSend.venta_id).remove();
                } else {
                    window.location.reload();
                }
            } else { throw new Error(data.message); }
        }).catch(err => alert('Error: ' + err.message));
    });

});