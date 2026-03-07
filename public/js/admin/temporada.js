const TemporadaModule = {
    tabla: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
    },

    initDataTable: function() {
        if (!$('#tablaTemporadas').length) return;

        this.tabla = $('#tablaTemporadas').DataTable({
            "destroy": true, "processing": true, "responsive": true, "autoWidth": false, "ordering": true,
            "ajax": { "url": `${BASE_URL}/admin/temporada/getall`, "type": "GET" },
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "pageLength": 10,
            "language": { "zeroRecords": "No hay historial", "paginate": { "next": ">", "previous": "<" } },
            "columns": [
                { "data": "id_temporada", "visible": false },
                { "data": "nombre", "render": function(data) { return `<span class="fw-bold text-dark">${data}</span>`; } },
                { "data": null, "render": function(data, type, row) {
                        let ini = new Date(row.fecha_inicio).toLocaleDateString();
                        let fin = row.fecha_fin ? new Date(row.fecha_fin).toLocaleDateString() : '<span class="text-success fw-bold">Vigente</span>';
                        return `<span class="small text-muted">${ini} — ${fin}</span>`;
                    }
                },
                { "data": "puntos_generados", "render": function(data) { return `<span class="fw-bold text-primary">${parseFloat(data).toFixed(0)}</span>`; } },
                { "data": "puntos_redimidos", "render": function(data) { return `<span class="fw-bold text-danger">${parseFloat(data).toFixed(0)}</span>`; } },
                { "data": "estado", "className": "text-center", "render": function(data) {
                        return data == 1 ? '<span class="badge bg-label-success">ACTIVA</span>' : '<span class="badge bg-label-secondary">CERRADA</span>';
                    }
                },
                { "data": null, "className": "text-center", "render": function() {
                        return `<button class="btn btn-sm btn-icon btn-ver text-info"><i class="bx bx-show fs-4"></i></button>`;
                    }
                }
            ]
        });
    },

    initEventosUI: function() {
        const self = this;
        $('#buscadorGlobal').on('keyup', function() { self.tabla.search(this.value).draw(); });
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // A. BOTONES DEL CARD SUPERIOR (EDITAR)
        $('.btn-editar-card').on('click', function() {
            $('#edit_id_temporada').val($(this).data('id'));
            $('#edit_nombre').val($(this).data('nom'));
            $('#edit_inicio').val($(this).data('ini'));
            $('#edit_fin').val($(this).data('fin'));
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        // B. ABRIR MODAL CERRAR TEMPORADA (NUEVO)
        // Este evento escucha el click en el botón rojo "CERRAR TEMPORADA" de la tarjeta
        $(document).on('click', '#btnAbrirModalCerrar', function() {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');
            
            // Llenamos el modal con los datos
            $('#id_cerrar').val(id);
            $('#nombre_cerrar').text(nombre);
            
            // Abrimos el modal
            new bootstrap.Modal(document.getElementById('modalCerrar')).show();
        });

        // C. VER DETALLE (TABLA INFERIOR)
        $('#tablaTemporadas tbody').on('click', '.btn-ver', function() {
            let tr = $(this).closest('tr'); if(tr.hasClass('child')) tr = tr.prev();
            let data = self.tabla.row(tr).data();
            
            let html = `
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary">${data.nombre}</h4>
                    <span class="badge bg-label-${data.estado==1?'success':'secondary'}">${data.estado==1?'ACTIVA':'CERRADA'}</span>
                </div>
                <div class="row g-3">
                    <div class="col-6"><div class="p-3 border rounded bg-light text-center"><small>Puntos Generados</small><h4 class="mb-0 text-primary">${data.puntos_generados}</h4></div></div>
                    <div class="col-6"><div class="p-3 border rounded bg-light text-center"><small>Canjes Realizados</small><h4 class="mb-0 text-danger">${data.puntos_redimidos}</h4></div></div>
                    <div class="col-6"><div class="p-2 border-bottom"><small>Inicio:</small> <span class="fw-bold">${data.fecha_inicio}</span></div></div>
                    <div class="col-6"><div class="p-2 border-bottom"><small>Fin:</small> <span class="fw-bold">${data.fecha_fin||'-'}</span></div></div>
                </div>`;
            $('#contenidoDetalle').html(html);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        });
    },

    initFormularios: function() {
        const self = this;
        
        // Helper para formularios estándar
        const handleForm = async (formId, url, btnText, modalId) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Procesando...');
                try {
                    let formData = new FormData(this);
                    let jsonData = JSON.stringify(Object.fromEntries(formData));
                    let res = await fetch(`${BASE_URL}${url}`, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: jsonData });
                    let data = await res.json();
                    if(data.success) {
                        // Recargar página para actualizar cards y estado
                        if(formId === 'formEditarTemporada' || formId === 'registrarTemporada') {
                            window.location.reload(); 
                        } else {
                            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                            self.initVisualFixes();
                            self.tabla.ajax.reload(null, false);
                            self.mostrarToast(data.message, "success");
                        }
                    } else { self.mostrarToast(data.message, "danger"); }
                } catch(err) { self.mostrarToast("Error", "danger"); }
                finally { btn.prop('disabled', false).text(btnText); }
            });
        };

        handleForm('registrarTemporada', '/admin/temporada/registrartemporada', 'GUARDAR', 'modalRegistrar');
        handleForm('formEditarTemporada', '/admin/temporada/editartemporada', 'Actualizar', 'modalEditar');

        // FORMULARIO ESPECÍFICO PARA CERRAR TEMPORADA (NUEVO)
        $('#formCerrarTemporada').on('submit', async function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Finalizando...');
            
            try {
                let formData = new FormData(this);
                // Forzamos estado = 0 (Cerrado)
                formData.append('estado', 0); 
                let jsonData = JSON.stringify(Object.fromEntries(formData));
                
                let res = await fetch(`${BASE_URL}/admin/temporada/cambiarestado`, { 
                    method: 'POST', 
                    headers: {'Content-Type': 'application/json'}, 
                    body: jsonData 
                });
                let data = await res.json();
                
                if(data.success) {
                    // Al cerrar temporada, recargamos para que la UI cambie totalmente (Card activa desaparece)
                    window.location.reload();
                } else {
                    self.mostrarToast(data.message, "danger");
                    btn.prop('disabled', false).text('SÍ, FINALIZAR AHORA');
                }
            } catch(e) { 
                self.mostrarToast("Error de conexión", "danger"); 
                btn.prop('disabled', false).text('SÍ, FINALIZAR AHORA');
            }
        });
    },

    initVisualFixes: function() { $('.modal-backdrop').remove(); $('body').removeClass('modal-open').css('overflow','auto'); },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        if(toastEl){
             // Asegúrate de limpiar clases previas si usas Bootstrap 5
             toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
             toastEl.classList.add(`bg-${tipo}`);
             
             // Bootstrap 5 Toast requiere inicialización
             // Si usas una versión anterior o template específico, mantén tu className anterior
             // toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
             
             $('#toastMensaje').text(msg);
             new bootstrap.Toast(toastEl).show();
        } else { alert(msg); }
    }
};

document.addEventListener("DOMContentLoaded", () => { TemporadaModule.init(); });