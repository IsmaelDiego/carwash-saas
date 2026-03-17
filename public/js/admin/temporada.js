const TemporadaModule = {
    tabla: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
        this.initControlFechas(); 
    },

    initControlFechas: function() {
        const today = new Date().toISOString().split('T')[0];
        
        // Registro
        const inputInicio = $('input[name="fecha_inicio"]');
        inputInicio.val(today).attr('min', today);
        
        // Edición
        $('#edit_inicio').attr('min', today);
    },

    initDataTable: function() {
        if (!$('#tablaTemporadas').length) return;

        this.tabla = $('#tablaTemporadas').DataTable({
            "destroy": true, 
            "processing": true, 
            "responsive": true, 
            "autoWidth": false, 
            "ordering": true,
            "ajax": { "url": `${BASE_URL}/admin/temporada/getall`, "type": "GET" },
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "pageLength": 10,
            "language": { 
                "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de temporadas</h5></div>`, 
                "paginate": { "next": "Sig.", "previous": "Ant." } 
            },
            "columns": [
                { "data": "id_temporada", "visible": false },
                { "data": "nombre", "render": function(data) { return `<span class="fw-bold text-dark text-uppercase animate__animated animate__fadeIn">${data}</span>`; } },
                { "data": null, "render": function(data, type, row) {
                        const ini = new Date(row.fecha_inicio.replace(/-/g, '/')).toLocaleDateString();
                        const fin = row.fecha_fin ? new Date(row.fecha_fin.replace(/-/g, '/')).toLocaleDateString() : '<span class="badge bg-label-success">EN CURSO</span>';
                        return `<div class="d-flex align-items-center animate__animated animate__fadeIn"><i class="bx bx-calendar-event me-2 text-primary"></i><span class="small text-muted">${ini} — ${fin}</span></div>`;
                    }
                },
                { "data": "puntos_gen", "render": function(data) { return `<span class="fw-bold text-primary animate__animated animate__fadeIn">${parseFloat(data).toLocaleString()}</span>`; } },
                { "data": "puntos_red", "render": function(data) { return `<span class="fw-bold text-danger animate__animated animate__fadeIn">${parseFloat(data).toLocaleString()}</span>`; } },
                { "data": "estado", "className": "text-center", "render": function(data) {
                        return data == 1 ? '<span class="badge bg-label-success border-0 px-3 animate__animated animate__fadeIn">ACTIVA</span>' : '<span class="badge bg-label-secondary border-0 px-3 animate__animated animate__fadeIn">CERRADA</span>';
                    }
                },
                { "data": null, "className": "text-center", "render": function(data) {
                        return `<div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-icon btn-ver text-info" title="Ver Detalles">
                                        <i class="bx bx-show fs-4"></i>
                                    </button>
                                </div>`;
                    }
                }
            ],
            "buttons": [{
                extend: 'excelHtml5',
                className: 'd-none',
                filename: 'Reporte_Temporadas',
                title: '',
                exportOptions: { columns: [1, 2, 3, 4, 5], orthogonal: 'export' },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];
                    var fillIndex = $('fills fill', styles).length;
                    $('fills', styles).append('<fill><patternFill patternType="solid"><fgColor rgb="FF00BFFF" /><bgColor indexed="64" /></patternFill></fill>');
                    var fontIndex = $('fonts font', styles).length;
                    $('fonts', styles).append('<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>');
                    var styleIndex = $('cellXfs xf', styles).length;
                    $('cellXfs', styles).append('<xf numFmtId="0" fontId="' + fontIndex + '" fillId="' + fillIndex + '" applyFont="1" applyFill="1" />');
                    $('row:first c', sheet).attr('s', styleIndex);
                }
            }]
        });
    },

    initEventosUI: function() {
        const self = this;
        $('#buscadorGlobal').on('keyup', function() { self.tabla.search(this.value).draw(); });
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // A. BOTONES DEL CARD SUPERIOR (EDITAR)
        $('.btn-editar-card').on('click', function() {
            const id = $(this).data('id');
            const nom = $(this).data('nom');
            const ini = $(this).data('ini');
            
            $('#edit_id_temporada').val(id);
            $('#edit_nombre').val(nom);
            $('#edit_inicio').val(ini);

            // Lógica de Bloqueo de Fechas
            const today = new Date().toISOString().split('T')[0];
            if(ini <= today) {
                $('#edit_inicio').attr('readonly', true).css('background-color', '#f8f9fa');
            } else {
                $('#edit_inicio').attr('readonly', false).attr('min', today).css('background-color', '');
            }

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

        // C. VER DETALLE (TABLA INFERIOR Y BOTÓN OJO)
        $('#tablaTemporadas tbody').on('click', '.btn-ver', function() {
            let tr = $(this).closest('tr'); if(tr.hasClass('child')) tr = tr.prev();
            let data = self.tabla.row(tr).data();
            abrirDetalleGeneral(data);
        });

        // Evento para el botón de detalles en la card (si existiera o para uniformidad)
        $(document).on('click', '.btn-ver-dash', function() {
            const data = {
                nombre: $(this).data('nom'),
                fecha_inicio: $(this).data('ini'),
                fecha_fin: null, // Indicará periodo en curso
                puntos_gen: $(this).data('gen'),
                puntos_red: $(this).data('red'),
                estado: $(this).data('est')
            };
            abrirDetalleGeneral(data);
        });

        const abrirDetalleGeneral = (data) => {
            const ini = new Date(data.fecha_inicio.replace(/-/g, '/')).toLocaleDateString();
            const fin = data.fecha_fin ? new Date(data.fecha_fin.replace(/-/g, '/')).toLocaleDateString() : '<span class="badge bg-label-success">PERIODO EN CURSO</span>';
            
            let html = `
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-${data.estado==1?'success':'secondary'}" style="width: 80px; height: 80px;">
                        <span class="fs-1 fw-bold text-${data.estado==1?'success':'secondary'}"><i class="bx ${data.estado==1?'bx-check-double':'bx-stop-circle'}"></i></span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark text-uppercase">${data.nombre}</h4>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        <span class="badge bg-label-${data.estado==1?'success':'secondary'} px-3 py-2"><i class="bx ${data.estado==1?'bx-check':'bx-lock-alt'} me-1"></i>${data.estado==1?'ACTIVA':'CERRADA'}</span>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-calendar"></i> Apertura</small>
                            <span class="fw-semibold text-dark fs-6">${ini}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-calendar-check"></i> Cierre</small>
                            <span class="fw-semibold text-dark fs-6">${fin}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-star"></i> Pts. Generados</small>
                            <span class="fw-semibold text-primary fs-5">${parseFloat(data.puntos_gen).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-gift"></i> Tickets Canjeados</small>
                            <span class="fw-semibold text-danger fs-5">${parseFloat(data.puntos_red).toLocaleString()}</span>
                        </div>
                    </div>
                </div>`;
            $('#contenidoDetalle').html(html);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        };
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
                            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                            self.initVisualFixes();
                            self.mostrarToast(data.message, "success");
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                            self.initVisualFixes();
                            self.tabla.ajax.reload(null, false);
                            self.mostrarToast(data.message, "success");
                        }
                    } else { self.mostrarToast(data.message, "danger"); }
                } catch(err) { self.mostrarToast("Error", "danger"); }
                finally {
                    if (formId !== 'formEditarTemporada' && formId !== 'registrarTemporada' || !data?.success) {
                        btn.prop('disabled', false).text(btnText);
                    }
                }
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
                    bootstrap.Modal.getInstance(document.getElementById('modalCerrar')).hide();
                    self.initVisualFixes();
                    self.mostrarToast(data.message || "Se cerró la temporada correctamente", "success");
                    setTimeout(() => { window.location.reload(); }, 1500);
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

    initVisualFixes: function() { 
        $('.modal-backdrop, .offcanvas-backdrop').remove(); 
        $('body').removeClass('modal-open offcanvas-open').css({
            'overflow': '',
            'padding-right': ''
        }); 
    },

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