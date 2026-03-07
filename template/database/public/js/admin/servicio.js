const ServicioModule = {
    tabla: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
    },

    initDataTable: function() {
        if (!$('#tablaServicios').length) return;

        this.tabla = $('#tablaServicios').DataTable({
            "destroy": true, "processing": true, "responsive": true, "autoWidth": false, "ordering": true,
            "ajax": { "url": `${BASE_URL}/admin/servicio/getall`, "type": "GET" },
            
            // DOM LIMPIO: Solo Tabla (t), Paginación (p) e Información (i). Sin "Mostrar 10 registros" (l).
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            
            "pageLength": 10, // Defecto 10, ya no se puede cambiar por interfaz
            
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_",
                "zeroRecords": `<div class="text-center p-5"><h5 class="fw-bold text-primary">No se encontraron resultados</h5></div>`,
                "paginate": { "next": "Sig.", "previous": "Ant." }
            },
            "columns": [
                { "data": "id_servicio", "visible": false }, 
                { "data": "acumula_puntos", "visible": false },
                { "data": "permite_canje", "visible": false },
                // NOMBRE
                { 
                    "data": "nombre",
                    "render": function(data) {
                        return `<span class="fw-bold text-dark fs-6 text-uppercase">${data}</span>`;
                    }
                },
                // PRECIO
                { 
                    "data": "precio_base",
                    "render": function(data) {
                        return `<span class="badge bg-label-success fs-6">S/ ${parseFloat(data).toFixed(2)}</span>`;
                    }
                },
                // REGLAS
                { 
                    "data": null, "className": "text-center",
                    "render": function(data, type, row) {
                        let html = '';
                        if(row.acumula_puntos == 1) html += '<span class="badge bg-label-primary me-1" title="Acumula Puntos"><i class="bx bxs-star"></i></span>';
                        if(row.permite_canje == 1) html += '<span class="badge bg-label-warning" title="Permite Canje"><i class="bx bxs-gift"></i></span>';
                        return html || '<span class="text-muted">-</span>';
                    }
                },
                // ESTADO
                { 
                    "data": "estado", "className": "text-center",
                    "render": function(data, type, row) {
                        let checked = data == 1 ? 'checked' : '';
                        return `<div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input switch-estado" type="checkbox" data-id="${row.id_servicio}" ${checked}></div>`;
                    }
                },
                // ACCIONES
                { 
                    "data": null, "orderable": false, "className": "text-center",
                    "render": function() {
                        return `<div class="dropdown"><button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button><div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item btn-ver" href="javascript:void(0);"><i class="bx bx-show text-info me-2"></i> Ver Detalle</a><a class="dropdown-item btn-editar" href="javascript:void(0);"><i class="bx bx-edit text-warning me-2"></i> Editar</a><div class="dropdown-divider"></div><a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Eliminar</a></div></div>`;
                    }
                }
            ],
            // EXCEL SIN ID NI TÍTULO
            "buttons": [{
                extend: 'excelHtml5', className: 'd-none', filename: 'Reporte_Servicios', title: '',
                exportOptions: { columns: [3, 4, 1, 2], orthogonal: 'export' },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];
                    var fillIndex = $('fills fill', styles).length; $('fills', styles).append('<fill><patternFill patternType="solid"><fgColor rgb="FF00BFFF" /><bgColor indexed="64" /></patternFill></fill>');
                    var fontIndex = $('fonts font', styles).length; $('fonts', styles).append('<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>');
                    var styleIndex = $('cellXfs xf', styles).length; $('cellXfs', styles).append('<xf numFmtId="0" fontId="'+fontIndex+'" fillId="'+fillIndex+'" applyFont="1" applyFill="1" />');
                    $('row:first c', sheet).attr('s', styleIndex);
                }
            }]
        });
    },

    initEventosUI: function() {
        const self = this;

        // --- BUSCADOR GLOBAL DINÁMICO ---
        $('#buscadorGlobal').on('keyup', function() {
            self.tabla.search(this.value).draw();
        });

        // EXPORTAR
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // SWITCH ESTADO
        $('#tablaServicios tbody').on('change', '.switch-estado', async function() {
            const el = $(this); el.prop('disabled', true);
            try {
                const res = await fetch(`${BASE_URL}/admin/servicio/cambiarestado`, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({id_servicio: el.data('id'), estado: el.is(':checked')?1:0}) });
                const data = await res.json();
                if(data.success) { self.mostrarToast("Estado actualizado", "success"); self.tabla.ajax.reload(null, false); }
                else { el.prop('checked', !el.is(':checked')); self.mostrarToast("Error", "danger"); }
            } catch(e) { el.prop('checked', !el.is(':checked')); } finally { el.prop('disabled', false); }
        });

        function getData(el) { let tr = $(el).closest('tr'); if(tr.hasClass('child')) tr = tr.prev(); return self.tabla.row(tr).data(); }

        // VER DETALLE
        $('#tablaServicios tbody').on('click', '.btn-ver', function() {
            let data = getData(this);
            let estadoHtml = data.estado == 1 ? '<span class="badge bg-success">ACTIVO</span>' : '<span class="badge bg-secondary">INACTIVO</span>';
            let acumula = data.acumula_puntos == 1 ? '<span class="text-success fw-bold">SÍ</span>' : 'NO';
            let canje = data.permite_canje == 1 ? '<span class="text-primary fw-bold">SÍ</span>' : 'NO';

            let html = `
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <h2 class="fw-bold text-primary mb-0">${data.nombre}</h2>
                        <div class="mt-2">${estadoHtml}</div>
                    </div>
                    <div class="col-12"><div class="detalle-card p-3 text-center"><small class="detalle-label">Precio Base</small><div class="fw-bold fs-3 text-dark">S/ ${data.precio_base}</div></div></div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">Acumula Puntos</small><div>${acumula}</div></div></div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">Permite Canje</small><div>${canje}</div></div></div>
                </div>`;
            $('#contenidoDetalle').html(html);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        });

        // EDITAR
        $('#tablaServicios tbody').on('click', '.btn-editar', function() {
            let data = getData(this);
            $('#edit_id_servicio').val(data.id_servicio);
            $('#edit_nombre').val(data.nombre);
            $('#edit_precio_base').val(data.precio_base);
            $('#edit_acumula').prop('checked', data.acumula_puntos == 1);
            $('#edit_canje').prop('checked', data.permite_canje == 1);
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        // ELIMINAR
        $('#tablaServicios tbody').on('click', '.btn-eliminar', function() {
            let data = getData(this);
            $('#delete_id_servicio').val(data.id_servicio);
            $('#nombre_eliminar').text(data.nombre);
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        });
    },

    initFormularios: function() {
        const self = this;
        const handleForm = async (formId, url, btnText, modalId, reset=false) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Procesando...');
                try {
                    let formElement = document.getElementById(formId);
                    let formData = new FormData(formElement);
                    let object = {};
                    formData.forEach((value, key) => { object[key] = value; });

                    // Checkboxes Manual
                    let chkAcumula = $(`#${formId} input[name="acumula_puntos"]`);
                    let chkCanje = $(`#${formId} input[name="permite_canje"]`);
                    object['acumula_puntos'] = chkAcumula.is(':checked') ? 1 : 0;
                    object['permite_canje']  = chkCanje.is(':checked') ? 1 : 0;

                    let jsonData = JSON.stringify(object);
                    let res = await fetch(`${BASE_URL}${url}`, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: jsonData });
                    let data = await res.json();
                    if(data.success) {
                        bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                        self.initVisualFixes();
                        self.tabla.ajax.reload(null, false);
                        if(reset) this.reset();
                        self.mostrarToast(data.message, "success");
                    } else { self.mostrarToast(data.message, "danger"); }
                } catch(err) { self.mostrarToast("Error de conexión", "danger"); }
                finally { btn.prop('disabled', false).text(btnText); }
            });
        };
        handleForm('registrarServicio', '/admin/servicio/registrarservicio', 'GUARDAR', 'modalRegistrar', true);
        handleForm('formEditarServicio', '/admin/servicio/editarservicio', 'Actualizar', 'modalEditar');
        handleForm('formEliminarServicio', '/admin/servicio/eliminarservicio', 'SÍ, ELIMINAR', 'modalEliminar');
    },

    initVisualFixes: function() { $('.modal-backdrop, .offcanvas-backdrop').remove(); $('body').removeClass('modal-open offcanvas-open').css('overflow','').css('padding-right',''); },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = "11000";
        $('#toastMensaje').text(msg);
        new bootstrap.Toast(toastEl).show();
    }
};

document.addEventListener("DOMContentLoaded", () => { ServicioModule.init(); });