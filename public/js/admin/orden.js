const ordenes = {
    tabla: null,

    init: function() {
        this.initTabla();
        this.initFiltrosUI();

        $('#tbOrdenes').on('xhr.dt', () => {
            setTimeout(() => this.computeStats(), 100);
        });
    },

    initTabla: function() {
        const self = this;
        this.tabla = $('#tbOrdenes').DataTable({
            destroy: true,
            processing: true,
            responsive: true,
            autoWidth: false,
            ordering: true,
            ajax: `${BASE_URL}/admin/orden/getlista`,
            dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            columns: [
                { data: 'fecha_raw', visible: false, defaultContent: ''},
                { data: 'id_orden', render: data => `<div class="d-flex flex-column"><span class="fw-bold text-primary">#${data}</span></div>` },
                { data: 'fecha' },
                { data: 'cliente' },
                { data: 'vehiculo' },
                { data: 'creador' },
                { data: 'estado', className: 'text-center', render: function(d) {
                    let color = 'secondary';
                    if(d === 'FINALIZADO') color = 'success';
                    else if(d === 'POR_COBRAR') color = 'warning';
                    else if(d === 'EN_COLA') color = 'primary';
                    else if(d === 'EN_PROCESO') color = 'info';
                    else if(d === 'ANULADO') color = 'danger';
                    return `<span class="badge bg-label-${color}">${d}</span>`;
                }},
                { data: 'total_final', className: 'text-end fw-bold', render: d => `S/ ${parseFloat(d).toFixed(2)}` }
            ],
            buttons: [{
                extend: 'excelHtml5',
                className: 'd-none',
                filename: 'Reporte_Ordenes',
                title: '',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] },
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
            }],
            language: {
                lengthMenu: " _MENU_ ",
                info: "Mostrando _START_ a _END_ de _TOTAL_ órdenes",
                infoEmpty: "0 órdenes",
                infoFiltered: "(filtrado)",
                paginate: { next: "Siguiente", previous: "Anterior" },
                zeroRecords: `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de ordenes</h5></div>`
            },
            order: [[1, 'desc']]
        });
    },

    computeStats: function() {
        if (!this.tabla) return;
        const data = this.tabla.rows().data().toArray();
        
        const total = data.length;
        const finalizadas = data.filter(r => r.estado === 'FINALIZADO').length;
        const enProceso = data.filter(r => ['EN_PROCESO', 'EN_COLA'].includes(r.estado)).length;
        const ingreso = data.reduce((acc, r) => acc + (parseFloat(r.total_final) || 0), 0);

        const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
        el('stat_ord_total', total);
        el('stat_ord_finalizado', finalizadas);
        el('stat_ord_proceso', enProceso);
        el('stat_ord_ingreso', 'S/ ' + ingreso.toFixed(2));
    },

    initFiltrosUI: function() {
        const self = this;
        var offcanvasEl = document.getElementById("offcanvasFiltroOrdenes");
        var filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl, { backdrop: true, scroll: true });

        $("#btnAbrirFiltro").on("click", e => { e.preventDefault(); filtroOffcanvas.show(); });
        $("#btnExportar").on("click", () => self.tabla.button(".buttons-excel").trigger());

        $("#btnAplicarFiltros").on("click", () => {
            filtroOffcanvas.hide();
            self.tabla.draw();
        });

        // Búsqueda automática al cambiar la fecha
        $("#filtroFechaInicio, #filtroFechaFin").on("change", function() {
            self.tabla.draw();
        });

        $("#btnLimpiarFiltros").on("click", () => {
            $("#buscadorGlobal").val("");
            $("#filtroFechaInicio, #filtroFechaFin").val("");
            self.tabla.search("").draw();
        });

        $("#buscadorGlobal").on("keyup", function() { self.tabla.search(this.value).draw(); });

        // Custom Date Range filter
        $.fn.dataTable.ext.search.push(function(settings, rowData, dataIndex) {
            if (settings.nTable.id !== 'tbOrdenes') return true;
            let min = $("#filtroFechaInicio").val();
            let max = $("#filtroFechaFin").val();
            let fechaStr = rowData[0]; // La fecha oculta (RAW datetime string)
            if (!fechaStr) return true;

            let fecha = new Date(fechaStr);
            if (min && new Date(min) > fecha) return false;
            
            if (max) {
                 let maxDate = new Date(max);
                 maxDate.setHours(23, 59, 59, 999);
                 if (maxDate < fecha) return false;
            }
            return true;
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ordenes.init();
});
