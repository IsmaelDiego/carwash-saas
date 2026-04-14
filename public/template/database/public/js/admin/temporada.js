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
            
            // DOM minimalista
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "pageLength": 10,
            "language": { "zeroRecords": "No hay historial", "paginate": { "next": ">", "previous": "<" } },
            
            "columns": [
                { "data": "id_temporada", "visible": false },
                
                // 1. TEMPORADA
                { 
                    "data": "nombre",
                    "render": function(data) { return `<span class="fw-bold text-dark">${data}</span>`; }
                },
                // 2. PERIODO
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        let ini = new Date(row.fecha_inicio).toLocaleDateString();
                        let fin = row.fecha_fin ? new Date(row.fecha_fin).toLocaleDateString() : '<span class="text-success fw-bold">Vigente</span>';
                        return `<span class="small text-muted">${ini} — ${fin}</span>`;
                    }
                },
                // 3. PUNTOS GENERADOS
                { 
                    "data": "puntos_generados",
                    "render": function(data) { return `<span class="fw-bold text-primary">${parseFloat(data).toFixed(0)}</span>`; }
                },
                // 4. CANJES
                { 
                    "data": "puntos_redimidos",
                    "render": function(data) { return `<span class="fw-bold text-danger">${parseFloat(data).toFixed(0)}</span>`; }
                },
                // 5. ESTADO
                { 
                    "data": "estado", "className": "text-center",
                    "render": function(data) {
                        return data == 1 
                            ? '<span class="badge bg-label-success">ACTIVA</span>' 
                            : '<span class="badge bg-label-secondary">CERRADA</span>';
                    }
                },
                // 6. ACCIONES (Solo Detalle)
                { 
                    "data": null, "className": "text-center",
                    "render": function() {
                        return `<button class="btn btn-sm btn-icon btn-ver text-info"><i class="bx bx-show fs-4"></i></button>`;
                    }
                }
            ],
            // EXCEL
            "buttons": [{
                extend: 'excelHtml5', className: 'd-none', filename: 'Historial_Temporadas', title: '',
                exportOptions: { columns: [1, 2, 3, 4, 5], orthogonal: 'export' },
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
        $('#buscadorGlobal').on('keyup', function() { self.tabla.search(this.value).draw(); });
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // A. BOTONES DEL CARD SUPERIOR (EDITAR)
        $('.btn-editar-card').on('click', function() {
            // Llenamos el modal con los data-attributes del botón
            $('#edit_id_temporada').val($(this).data('id'));
            $('#edit_nombre').val($(this).data('nom'));
            $('#edit_inicio').val($(this).data('ini'));
            $('#edit_fin').val($(this).data('fin'));
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        // B. BOTÓN CERRAR TEMPORADA (AJAX DIRECTO)
        $('#btnCerrarTemporada').on('click', async function() {
            let id = $(this).data('id');
            if(!confirm('¿Seguro que deseas CERRAR esta temporada? Esto archivará el periodo.')) return;

            try {
                // Estado 0 = Cerrado
                const res = await fetch(`${BASE_URL}/admin/temporada/cambiarestado`, { 
                    method: 'POST', headers: {'Content-Type':'application/json'}, 
                    body: JSON.stringify({id_temporada: id, estado: 0}) 
                });
                const data = await res.json();
                if(data.success) {
                    window.location.reload(); // Recargamos para actualizar cards
                } else {
                    self.mostrarToast("Error al cerrar", "danger");
                }
            } catch(e) { self.mostrarToast("Error de conexión", "danger"); }
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
                        // Si editamos la actual, mejor recargar para ver cambios en Card
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
    },

    initVisualFixes: function() { $('.modal-backdrop').remove(); $('body').removeClass('modal-open').css('overflow','auto'); },

    mostrarToast: function(msg, tipo) {
        // Reutiliza tu toast global
        let toastEl = document.getElementById('toastSistema');
        if(toastEl){
             toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
             $('#toastMensaje').text(msg);
             new bootstrap.Toast(toastEl).show();
        } else { alert(msg); }
    }
};

document.addEventListener("DOMContentLoaded", () => { TemporadaModule.init(); });