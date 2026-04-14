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
            ordering: false,
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
                { data: 'total_final', className: 'text-end fw-bold', render: d => `S/ ${parseFloat(d).toFixed(2)}` },
                { data: null, className: 'text-center', orderable: false, render: function(data) {
                    return `
                    <button class="btn btn-sm btn-icon btn-label-primary shadow-sm rounded-circle" onclick="ordenes.verDetalle(${data.id_orden})" title="Ver Detalles">
                        <i class="bx bx-show"></i>
                    </button>
                    `;
                }}
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
    },

    verDetalle: async function(id) {
        try {
            const res = await fetch(`${BASE_URL}/admin/orden/getdetalle?id=${id}`);
            const data = await res.json();
            
            if (!data.success) {
                alert("Error: " + data.message);
                return;
            }

            const ord = data.orden;
            const det = data.detalles;

            // Datos Base
            document.getElementById('mo_title').textContent = `ORDEN #${ord.id_orden}`;
            
            // Set Header color based on status
            const header = document.getElementById('mo_header_bg');
            const icon = document.getElementById('mo_icon');
            let colorHex = '#696cff'; // primary default
            let statusIcon = 'bx-receipt';
            
            if (ord.estado === 'FINALIZADO') { colorHex = '#71dd37'; statusIcon = 'bx-check-double'; }
            else if (ord.estado === 'POR_COBRAR') { colorHex = '#ffab00'; statusIcon = 'bx-dollar-circle'; }
            else if (ord.estado === 'EN_PROCESO') { colorHex = '#03c3ec'; statusIcon = 'bx-cog bx-spin'; }
            else if (ord.estado === 'ANULADO') { colorHex = '#ff3e1d'; statusIcon = 'bx-x-circle'; }
            
            header.style.backgroundColor = colorHex;
            icon.className = `bx ${statusIcon} fs-2`;
            icon.style.color = colorHex;

            const dateParsed = new Date(ord.fecha_creacion.replace(/-/g, "/")).toLocaleString('es-PE');
            document.getElementById('mo_fecha_estado').innerHTML = `<i class="bx bx-calendar me-1"></i> ${dateParsed}  —  <strong class="text-white text-uppercase" style="letter-spacing: 0.5px;">${ord.estado}</strong>`;

            // Cards info
            document.getElementById('mo_cliente_nombre').textContent = ord.cliente_nombres ? `${ord.cliente_nombres} ${ord.cliente_apellidos}` : 'Consumidor Final';
            document.getElementById('mo_cliente_dni').textContent = ord.cliente_dni || 'Sin Documento';

            document.getElementById('mo_placa').textContent = ord.placa || 'NINGUNA';
            document.getElementById('mo_color').textContent = ord.vehiculo_color || '---';
            document.getElementById('mo_categoria').textContent = ord.categoria_vehiculo || 'Sin Vehículo';

            document.getElementById('mo_creador').textContent = ord.creador_nombre || 'Desconocido';

            // Tabla de detalles (Productos y Servicios)
            const tbody = document.getElementById('mo_body_detalles');
            tbody.innerHTML = '';
            
            if (!det || det.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No hay servicios ni productos registrados en esta orden.</td></tr>`;
            } else {
                det.forEach(d => {
                    let nombreItem = d.servicio_nombre || d.producto_nombre || 'Item desconocido';
                    let isServicio = d.servicio_nombre ? true : false;
                    let iconItem = isServicio ? '<i class="bx bx-wrench text-info me-2"></i>' : '<i class="bx bx-box text-success me-2"></i>';
                    
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-dark fw-semibold">${iconItem} ${nombreItem}</td>
                        <td class="text-center">${d.cantidad}</td>
                        <td class="text-end">S/ ${parseFloat(d.precio_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            document.getElementById('mo_total_final').textContent = `S/ ${parseFloat(ord.total_final).toFixed(2)}`;

            // Logs adicionales
            let logs = [];
            if(ord.fecha_cierre) logs.push(`Cierre: ${new Date(ord.fecha_cierre.replace(/-/g, "/")).toLocaleString('es-PE')}`);
            if(ord.motivo_anulacion) logs.push(`Motivo Anulación: <b>${ord.motivo_anulacion}</b>`);
            document.getElementById('mo_logs_estado').innerHTML = logs.length > 0 ? logs.join(' &nbsp; | &nbsp; ') : 'No hay historial de cierres.';

            // Mostrar
            new bootstrap.Modal(document.getElementById('modalDetalleOrden')).show();

        } catch (e) {
            console.error(e);
            alert("Ocurrió un error al cargar la orden.");
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ordenes.init();
});
