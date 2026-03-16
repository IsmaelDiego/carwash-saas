<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter,
    .dataTables_length {
        display: none !important;
    }
    .dataTables_paginate {
        display: flex !important;
        justify-content: flex-start !important;
        margin-top: 1rem !important;
    }
    .dataTables_info {
        text-align: right !important;
        margin-top: 1rem !important;
        color: #b0b0b0 !important;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4" id="statsOrdenes">
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-list-ol text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Órdenes</small>
                            <div class="fw-bold text-primary" id="stat_ord_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-success shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-check-circle text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Finalizadas</small>
                            <div class="fw-bold text-success" id="stat_ord_finalizado" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-time text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">En Proceso / Cola</small>
                            <div class="fw-bold text-warning" id="stat_ord_proceso" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-info shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-money text-info"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Ingreso Total</small>
                            <div class="fw-bold text-info" id="stat_ord_ingreso" style="font-size:1.4rem">S/ 0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HEADER + ACCIONES -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-customize text-primary me-1"></i> REGISTRO DE ÓRDENES
                </h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Órdenes de Servicio</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar orden..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        </div>

                        <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
                            <i class="bx bx-filter-alt me-1"></i> Filtros

                        <button class="btn btn-outline-success" type="button" id="btnExportar">
                            <i class="bx bxs-file-export p-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA CARD -->
        <div class="card shadow-sm">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tbOrdenes">
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none">FechaRaw</th>
                            <th style="color: #f0f0f0;">N° Orden</th>
                            <th style="color: #f0f0f0;">Fecha</th>
                            <th style="color: #f0f0f0;">Cliente</th>
                            <th style="color: #f0f0f0;">Vehículo</th>
                            <th style="color: #f0f0f0;">Creado por</th>
                            <th class="text-center" style="color: #f0f0f0;">Estado</th>
                            <th class="text-end" style="color: #f0f0f0;">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- Extra Offcanvas Filtro -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltroOrdenes">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title text-white"><i class="bx bx-filter-alt me-2"></i> Filtros de Fechas</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-3">
        <label for="filtroFechaInicio" class="form-label">Desde:</label>
        <input type="date" id="filtroFechaInicio" class="form-control">
    </div>
    <div class="mb-4">
        <label for="filtroFechaFin" class="form-label">Hasta:</label>
        <input type="date" id="filtroFechaFin" class="form-control">
    </div>
    <div class="d-grid gap-2 mt-auto">
        <button type="button" class="btn btn-primary" id="btnAplicarFiltros">
            <i class="bx bx-check"></i> Aplicar / Cerrar
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
            <i class="bx bx-refresh"></i> Limpiar Todo
        </button>
    </div>
  </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
const BASE_URL = "<?= BASE_URL ?>";
let tablaOrdenes = null;

document.addEventListener('DOMContentLoaded', () => {
    initTabla();
    $('#tbOrdenes').on('xhr.dt', function() {
        setTimeout(() => computeStats(), 100);
    });
});

function initTabla() {
    tablaOrdenes = $('#tbOrdenes').DataTable({
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
            zeroRecords: `<div class="text-center p-5"><h5 class="fw-bold text-primary">No encontramos órdenes</h5></div>`
        },
        order: [[1, 'desc']]
    });

    initFiltrosUI();
}

function computeStats() {
    if (!tablaOrdenes) return;
    const data = tablaOrdenes.rows().data().toArray();
    
    const total = data.length;
    const finalizadas = data.filter(r => r.estado === 'FINALIZADO').length;
    const enProceso = data.filter(r => ['EN_PROCESO', 'EN_COLA'].includes(r.estado)).length;
    const ingreso = data.reduce((acc, r) => acc + (parseFloat(r.total_final) || 0), 0);

    const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
    el('stat_ord_total', total);
    el('stat_ord_finalizado', finalizadas);
    el('stat_ord_proceso', enProceso);
    el('stat_ord_ingreso', 'S/ ' + ingreso.toFixed(2));
}

function initFiltrosUI() {
    var offcanvasEl = document.getElementById("offcanvasFiltroOrdenes");
    var filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl, { backdrop: true, scroll: true });

    $("#btnAbrirFiltro").on("click", e => { e.preventDefault(); filtroOffcanvas.show(); });
    $("#btnExportar").on("click", () => tablaOrdenes.button(".buttons-excel").trigger());

    $("#btnAplicarFiltros").on("click", () => {
        filtroOffcanvas.hide();
        tablaOrdenes.draw();
    });

    $("#btnLimpiarFiltros").on("click", () => {
        $("#buscadorGlobal").val("");
        $("#filtroFechaInicio, #filtroFechaFin").val("");
        tablaOrdenes.search("").draw();
    });

    $("#buscadorGlobal").on("keyup", function() { tablaOrdenes.search(this.value).draw(); });

    // Custom Date Range filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'tbOrdenes') return true;
        let min = $("#filtroFechaInicio").val();
        let max = $("#filtroFechaFin").val();
        let fechaStr = data[0]; // La fecha oculta (RAW datetime string)
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
</script>
