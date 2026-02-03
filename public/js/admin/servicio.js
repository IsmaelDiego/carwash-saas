/**
 * servicio.js - Lógica para gestión de Servicios
 */
document.addEventListener("DOMContentLoaded", function() {

    // ==========================================================
    // 1. FUNCIONES AUXILIARES
    // ==========================================================

    function mostrarToast(mensaje, tipo = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = toastEl.querySelector('.toast-body');
        
        toastBody.textContent = mensaje;
        toastEl.className = 'toast align-items-center text-bg-' + tipo + ' border-0';
        
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    }

    function generarInputsPrecios(contenedorId, preciosActuales = []) {
        const contenedor = document.getElementById(contenedorId);
        if (!contenedor || !window.TIPOS_VEHICULO) return;

        let html = '';
        TIPOS_VEHICULO.forEach(tipo => {
            // Buscar precio actual si existe
            let precioActual = '';
            if (preciosActuales.length > 0) {
                const encontrado = preciosActuales.find(p => p.id_tipo_vehiculo == tipo.id_tipo_vehiculo);
                if (encontrado) precioActual = encontrado.precio;
            }

            html += `
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small text-muted">${tipo.nombre}</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">S/</span>
                        <input type="number" class="form-control" name="precios[${tipo.id_tipo_vehiculo}]" 
                               value="${precioActual}" min="0" step="0.50" placeholder="0.00">
                    </div>
                </div>
            `;
        });

        contenedor.innerHTML = html;
    }

    // ==========================================================
    // 2. FILTROS DE CARDS
    // ==========================================================

    // Buscador de texto
    $('#buscadorServicios').on('input', function() {
        const texto = $(this).val().toLowerCase();
        $('.card-servicio').each(function() {
            const nombre = $(this).data('nombre');
            $(this).toggle(nombre.includes(texto));
        });
        verificarSinResultados();
    });

    // Filtro por estado
    $('.btn-filtro-estado').on('click', function() {
        $('.btn-filtro-estado').removeClass('active btn-primary btn-success btn-secondary');
        $('.btn-filtro-estado').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('active');

        const estado = $(this).data('estado');
        
        if (estado === 'todos') {
            $(this).addClass('btn-primary');
            $('.card-servicio').show();
        } else {
            $(this).addClass(estado == 1 ? 'btn-success' : 'btn-secondary');
            $('.card-servicio').each(function() {
                $(this).toggle($(this).data('estado') == estado);
            });
        }
        verificarSinResultados();
    });

    function verificarSinResultados() {
        const visibles = $('.card-servicio:visible').length;
        if (visibles === 0) {
            if ($('#mensajeSinResultados').length === 0) {
                $('#contenedorServicios').append(`
                    <div class="col-12" id="mensajeSinResultados">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bx bx-search-alt fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron servicios</h5>
                            </div>
                        </div>
                    </div>
                `);
            }
        } else {
            $('#mensajeSinResultados').remove();
        }
    }

    // ==========================================================
    // 3. CRUD - REGISTRAR SERVICIO
    // ==========================================================

    // Al abrir modal de registro, generar inputs de precios
    $('#modalRegistrar').on('show.bs.modal', function() {
        generarInputsPrecios('contenedorPreciosRegistrar');
    });

    $('#formRegistrarServicio').on('submit', async function(e) {
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.html();
        btn.html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...').prop('disabled', true);

        const formData = new FormData(this);
        const data = {};
        
        // Procesar datos del formulario
        formData.forEach((value, key) => {
            if (key.startsWith('precios[')) {
                if (!data.precios) data.precios = {};
                const idTipo = key.match(/\[(\d+)\]/)[1];
                if (value && parseFloat(value) > 0) {
                    data.precios[idTipo] = parseFloat(value);
                }
            } else if (key === 'estado') {
                data[key] = this.querySelector('[name="estado"]').checked ? 1 : 0;
            } else {
                data[key] = value;
            }
        });
        
        // Si el checkbox no está marcado, asegurar que estado = 0
        if (!this.querySelector('[name="estado"]').checked) {
            data.estado = 0;
        }

        try {
            const response = await fetch(`${BASE_URL}/admin/servicio/registrar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarToast(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarToast(result.message, 'danger');
            }
        } catch (error) {
            mostrarToast('Error de conexión', 'danger');
        } finally {
            btn.html(btnText).prop('disabled', false);
        }
    });

    // ==========================================================
    // 4. CRUD - EDITAR SERVICIO
    // ==========================================================

    // Abrir modal de edición
    $(document).on('click', '.btn-editar', async function() {
        const id = $(this).data('id');
        
        try {
            const response = await fetch(`${BASE_URL}/admin/servicio/getone?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const servicio = result.data;
                
                $('#edit_id_servicio').val(servicio.id_servicio);
                $('#edit_nombre').val(servicio.nombre);
                $('#edit_descripcion').val(servicio.descripcion);
                $('#edit_duracion').val(servicio.duracion_minutos);
                $('#edit_estado').prop('checked', servicio.estado == 1);
                
                // Generar inputs de precios con valores actuales
                generarInputsPrecios('contenedorPreciosEditar', servicio.precios || []);
                
                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            } else {
                mostrarToast('No se pudo cargar el servicio', 'danger');
            }
        } catch (error) {
            mostrarToast('Error de conexión', 'danger');
        }
    });

    $('#formEditarServicio').on('submit', async function(e) {
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.html();
        btn.html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...').prop('disabled', true);

        const formData = new FormData(this);
        const data = {};
        
        formData.forEach((value, key) => {
            if (key.startsWith('precios[')) {
                if (!data.precios) data.precios = {};
                const idTipo = key.match(/\[(\d+)\]/)[1];
                if (value && parseFloat(value) > 0) {
                    data.precios[idTipo] = parseFloat(value);
                }
            } else if (key === 'estado') {
                data[key] = this.querySelector('[name="estado"]').checked ? 1 : 0;
            } else {
                data[key] = value;
            }
        });
        
        if (!this.querySelector('[name="estado"]').checked) {
            data.estado = 0;
        }

        try {
            const response = await fetch(`${BASE_URL}/admin/servicio/editar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarToast(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarToast(result.message, 'danger');
            }
        } catch (error) {
            mostrarToast('Error de conexión', 'danger');
        } finally {
            btn.html(btnText).prop('disabled', false);
        }
    });

    // ==========================================================
    // 5. CRUD - ELIMINAR SERVICIO
    // ==========================================================

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        $('#delete_id_servicio').val(id);
        $('#nombre_eliminar').text(nombre);
        
        new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    });

    $('#formEliminarServicio').on('submit', async function(e) {
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.html();
        btn.html('<i class="bx bx-loader-alt bx-spin"></i>').prop('disabled', true);

        const id_servicio = $('#delete_id_servicio').val();

        try {
            const response = await fetch(`${BASE_URL}/admin/servicio/eliminar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_servicio })
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarToast(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarToast(result.message, 'danger');
            }
        } catch (error) {
            mostrarToast('Error de conexión', 'danger');
        } finally {
            btn.html(btnText).prop('disabled', false);
        }
    });

    // ==========================================================
    // 6. LIMPIEZA DE MODALES
    // ==========================================================

    $('#modalRegistrar, #modalEditar, #modalEliminar').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', 'auto');
    });

}); // FIN DOMCONTENTLOADED