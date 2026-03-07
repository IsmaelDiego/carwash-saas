const PromocionModule = {
    tabla: null,

    init: function() {
        this.initDataTable();
        this.initWhatsAppLogic();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
        this.actualizarVista();
    },

    // =======================================================
    // 1. TOAST CORREGIDO (TEXTO BLANCO SIEMPRE)
    // =======================================================
    mostrarToast: function(mensaje, tipo) {
        const toastEl = document.getElementById('toastSistema');
        if(!toastEl) return;

        // Elementos internos
        const icono = $('#toastIcono');
        const titulo = $('#toastTitulo');
        const cuerpo = $('#toastMensaje');
        const btnClose = toastEl.querySelector('.btn-close');

        // 1. RESETEO AGRESIVO: Quitamos estilos anteriores
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white', 'text-dark');
        cuerpo.removeClass('text-white text-dark'); 
        if(btnClose) btnClose.classList.remove('btn-close-white');

        // 2. APLICAR NUEVO ESTILO
        if (tipo === 'success') {
            toastEl.classList.add('bg-success', 'text-white');
            cuerpo.addClass('text-white');
            icono.attr('class', 'icon-base bx bx-check-circle me-2');
            titulo.text('Éxito');
        } 
        else if (tipo === 'warning') {
            // CAMBIO AQUÍ: Ahora usa text-white
            toastEl.classList.add('bg-warning', 'text-white'); 
            cuerpo.addClass('text-white');
            icono.attr('class', 'icon-base bx bx-error me-2');
            titulo.text('Atención');
        } 
        else { // Error
            toastEl.classList.add('bg-danger', 'text-white');
            cuerpo.addClass('text-white');
            icono.attr('class', 'icon-base bx bx-x-circle me-2');
            titulo.text('Error');
        }

        // En todos los casos (Success, Warning y Error) queremos la X blanca
        if(btnClose) btnClose.classList.add('btn-close-white');

        cuerpo.text(mensaje);
        new bootstrap.Toast(toastEl).show();
    },

    // =======================================================
    // 2. ACTUALIZACIÓN VISUAL
    // =======================================================
    actualizarVista: async function() {
        if(this.tabla) this.tabla.ajax.reload(null, false);

        try {
            const res = await fetch(`${BASE_URL}/admin/promocion/getdashboarddata`);
            const data = await res.json();
            
            if(data.recientes) {
                this.renderCards(data.recientes);
                this.renderSelectWhatsApp(data.activas);
            }
        } catch(e) { console.error("Error actualizando dashboard", e); }
    },

    renderCards: function(lista) {
        const contenedor = document.querySelector('.row-cols-md-2');
        if(!contenedor) return;
        
        if (lista.length === 0) {
            contenedor.innerHTML = `<div class="col-12"><div class="alert alert-secondary text-center p-4"><i class='bx bx-ghost fs-1 opacity-50'></i><p class="mt-2">No hay promociones recientes.</p></div></div>`;
            return;
        }

        let html = '';
        lista.forEach(promo => {
            const esPorcentaje = promo.tipo_descuento === 'PORCENTAJE';
            const valorShow = esPorcentaje ? Math.round(promo.valor) + '%' : 'S/' + parseFloat(promo.valor).toFixed(2);
            const bgIcon = esPorcentaje ? 'bg-label-info text-info' : 'bg-label-success text-success';
            const estadoClass = promo.estado == 1 ? 'success' : 'secondary';
            const estadoText = promo.estado == 1 ? 'ACTIVA' : 'INACTIVA';
            const f = new Date(promo.fecha_fin.replace(/-/g, '/')); 
            const finStr = !isNaN(f) ? f.toLocaleDateString() : promo.fecha_fin;
            const unavez = promo.solo_una_vez_por_cliente == 1 ? '<i class="bx bx-user-check text-warning"></i> 1 por cliente' : '<i class="bx bx-infinite text-primary"></i> Ilimitado';

            html += `
            <div class="col">
                <div class="card promo-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-${estadoClass}">${estadoText}</span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon p-0" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item btn-editar-card" href="javascript:void(0);" data-id="${promo.id_promocion}"><i class="bx bx-edit me-2"></i> Editar</a></li>
                                    <li><a class="dropdown-item btn-eliminar-card text-danger" href="javascript:void(0);" data-id="${promo.id_promocion}" data-nom="${promo.nombre}"><i class="bx bx-trash me-2"></i> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="discount-circle ${bgIcon} me-3 shadow-sm">${valorShow}</div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;" title="${promo.nombre}">${promo.nombre}</h5>
                                <small class="text-muted"><i class='bx bx-calendar'></i> Hasta: ${finStr}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <small class="text-muted fst-italic">${unavez}</small>
                            <button class="btn btn-sm btn-outline-primary btn-editar-card" data-id="${promo.id_promocion}">Ver detalles</button>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        contenedor.innerHTML = html;
    },

    renderSelectWhatsApp: function(activas) {
        const select = $('#selectPromoWS');
        select.empty();
        select.append('<option value="">-- Seleccionar --</option>');
        activas.forEach(act => {
            const valor = act.tipo_descuento === 'PORCENTAJE' ? Math.round(act.valor) + '%' : 'S/' + parseFloat(act.valor).toFixed(2);
            const f = new Date(act.fecha_fin.replace(/-/g, '/'));
            const finStr = !isNaN(f) ? f.toLocaleDateString() : act.fecha_fin;

            select.append($('<option>', {
                value: act.id_promocion, text: act.nombre,
                'data-nombre': act.nombre, 'data-valor': valor, 'data-fin': finStr
            }));
        });
    },

    initDataTable: function() {
        if (!$('#tablaPromociones').length) return;
        this.tabla = $('#tablaPromociones').DataTable({
            "destroy": true, "processing": true, "responsive": true, "autoWidth": false,
            "ajax": { "url": `${BASE_URL}/admin/promocion/getall`, "type": "GET" },
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "pageLength": 10,
            "columns": [
                { "data": "id_promocion", "visible": false },
                { "data": "tipo_descuento", "visible": false },
                { "data": "nombre", "render": d => `<span class="fw-bold text-dark text-uppercase">${d}</span>` },
                { "data": null, "render": (d,t,row) => row.tipo_descuento==='PORCENTAJE' ? `<span class="badge bg-label-info border border-info">${Math.round(row.valor)}% OFF</span>` : `<span class="badge bg-label-success border border-success">S/ ${parseFloat(row.valor).toFixed(2)} OFF</span>` },
                { "data": null, "render": (d,t,r) => {
                    let i = new Date(r.fecha_inicio.replace(/-/g, '/')).toLocaleDateString();
                    let f = new Date(r.fecha_fin.replace(/-/g, '/')).toLocaleDateString();
                    return `<small class="text-muted"><i class='bx bx-calendar'></i> ${i} - ${f}</small>`;
                }},
                { "data": "estado", "className": "text-center", "render": (d,t,r) => `<div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input switch-estado" type="checkbox" data-id="${r.id_promocion}" ${d==1?'checked':''}></div>` },
                { "data": null, "className": "text-center", "render": () => `<div class="dropdown"><button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button><div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item btn-editar" href="javascript:void(0);"><i class="bx bx-edit text-warning me-2"></i> Editar</a><div class="dropdown-divider"></div><a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Eliminar</a></div></div>` }
            ]
        });
    },

    initWhatsAppLogic: function() {
        const select = $('#selectPromoWS');
        const textarea = $('#textoMensaje');
        const preview = $('#previewMensaje');
        const templateBase = "Hola {{nombre}} 👋, Aprovecha nuestra promo *{{promocion}}*! Obtén *{{valor}}* de descuento en tu próximo lavado. Válido hasta: {{fechafin}}";

        $(document).on('change', '#selectPromoWS', function() {
            let selected = $(this).find(':selected');
            if(selected.val() === "") {
                textarea.val('');
                preview.html('Selecciona una promoción para ver el mensaje...');
                return;
            }
            let texto = templateBase.replace('{{promocion}}', selected.data('nombre')).replace('{{valor}}', selected.data('valor')).replace('{{fechafin}}', selected.data('fin'));
            textarea.val(texto);
            actualizarPreview(texto);
        });

        textarea.on('input', function() { actualizarPreview($(this).val()); });
        function actualizarPreview(txt) { preview.html(txt.replace(/\n/g, '<br>').replace(/{{nombre}}/g, '<strong>Juan</strong>').replace(/\*(.*?)\*/g, '<strong>$1</strong>')); }
    },

    initEventosUI: function() {
        const self = this;
        $('#buscadorGlobal').on('keyup', function() { self.tabla.search(this.value).draw(); });
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        $('#tablaPromociones tbody').on('change', '.switch-estado', async function() {
            const el = $(this); el.prop('disabled', true);
            try {
                const res = await fetch(`${BASE_URL}/admin/promocion/cambiarestado`, { 
                    method: 'POST', headers: {'Content-Type':'application/json'}, 
                    body: JSON.stringify({id_promocion: el.data('id'), estado: el.is(':checked')?1:0}) 
                });
                const data = await res.json();
                if(data.success) { 
                    self.mostrarToast("Estado actualizado correctamente", "success"); 
                    self.actualizarVista(); 
                } else { 
                    el.prop('checked', !el.is(':checked')); 
                    self.mostrarToast("Error al cambiar estado", "danger"); 
                }
            } catch(e) { el.prop('checked', !el.is(':checked')); } finally { el.prop('disabled', false); }
        });

        const abrirEditar = (id) => {
            let row = self.tabla.rows().data().toArray().find(r => r.id_promocion == id);
            if(row) {
                $('#edit_id_promocion').val(row.id_promocion);
                $('#edit_nombre').val(row.nombre);
                $('#edit_tipo').val(row.tipo_descuento);
                $('#edit_valor').val(row.valor);
                $('#edit_inicio').val(row.fecha_inicio);
                $('#edit_fin').val(row.fecha_fin);
                $('#edit_solo_una').prop('checked', row.solo_una_vez_por_cliente == 1);
                $('#edit_mensaje').val(row.mensaje_whatsapp);
                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            } else { self.mostrarToast("Registro no disponible en vista actual.", "danger"); }
        };

        const abrirEliminar = (id, nombre) => {
            $('#delete_id_promocion').val(id);
            $('#nombre_eliminar').text(nombre);
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        };

        $(document).on('click', '.btn-editar-card, .btn-editar', function() {
            let id = $(this).data('id') || self.tabla.row($(this).closest('tr')).data().id_promocion;
            abrirEditar(id);
        });

        $(document).on('click', '.btn-eliminar-card', function() { abrirEliminar($(this).data('id'), $(this).data('nom')); });
        
        $('#tablaPromociones tbody').on('click', '.btn-eliminar', function() {
             let data = self.tabla.row($(this).closest('tr')).data();
             abrirEliminar(data.id_promocion, data.nombre);
        });
    },

    initFormularios: function() {
        const self = this;
        
        const handleForm = async (formId, url, btnText, modalId) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                let originalText = btn.text();
                btn.prop('disabled', true).text('Procesando...');
                
                try {
                    let formData = new FormData(this);
                    if(formId.includes('registrar') || formId.includes('Editar')) {
                        let chk = $(`#${formId} input[name="solo_una_vez_por_cliente"]`);
                        formData.set('solo_una_vez_por_cliente', chk.is(':checked') ? 1 : 0);
                    }
                    let jsonData = JSON.stringify(Object.fromEntries(formData));
                    
                    let res = await fetch(`${BASE_URL}${url}`, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: jsonData });
                    let data = await res.json();
                    
                    if(data.success) {
                        if(modalId) bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                        self.mostrarToast(data.message, "success");
                        self.actualizarVista();
                        if(formId.includes('registrar')) this.reset();
                    } else { 
                        self.mostrarToast(data.message, "danger"); 
                    }
                } catch(err) { self.mostrarToast("Error de conexión", "danger"); } 
                finally { btn.prop('disabled', false).text(originalText); }
            });
        };

        handleForm('registrarPromocion', '/admin/promocion/registrarpromocion', 'GUARDAR', 'modalRegistrar');
        handleForm('formEditarPromocion', '/admin/promocion/editarpromocion', 'Actualizar', 'modalEditar');
        handleForm('formEliminarPromocion', '/admin/promocion/eliminarpromocion', 'SÍ, ELIMINAR', 'modalEliminar');

        // ENVIO WHATSAPP CON LIMPIEZA
        $('#formEnviarWhatsApp').on('submit', async function(e){
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            let originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> ENVIANDO...');
            
            try {
                let formData = new FormData(this);
                let jsonData = JSON.stringify(Object.fromEntries(formData));
                let res = await fetch(`${BASE_URL}/admin/promocion/enviarwhatsapp`, { 
                    method: 'POST', 
                    headers: {'Content-Type': 'application/json'}, 
                    body: jsonData 
                });
                let data = await res.json();
                
                if(data.success) {
                    let tipoToast = data.type ? data.type : 'success';
                    self.mostrarToast(data.message, tipoToast);
                    
                    // LIMPIEZA TOTAL
                    $('#formEnviarWhatsApp')[0].reset(); 
                    $('#selectPromoWS').val('').trigger('change'); 
                    $('#textoMensaje').val('');
                    $('#previewMensaje').html('Mensaje enviado. Selecciona otra promoción...');
                    
                } else { 
                    self.mostrarToast(data.message, "danger"); 
                }
            } catch(e) { 
                self.mostrarToast("Error de conexión", "danger"); 
            }
            finally { 
                btn.prop('disabled', false).html(originalHtml); 
            }
        });
    },

    initVisualFixes: function() { $('.modal-backdrop').remove(); $('body').removeClass('modal-open').css('overflow','auto'); }
};

document.addEventListener("DOMContentLoaded", () => { PromocionModule.init(); });