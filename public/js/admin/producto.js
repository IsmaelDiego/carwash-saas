/**
 * Módulo Productos — Admin
 * Estructura: Module Pattern + init() (igual que ClienteModule, EmpleadoModule, etc.)
 */
const ProductoModule = {
    tabla: null,

    init: function() {
        this.initTabla();
        this.cargarStats();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
    },

    // ════════════════════════════════════════════════════
    // 1. CARGAR DATATABLE
    // ════════════════════════════════════════════════════
    initTabla: function() {
        const self = this;
        this.tabla = $('#tablaProductos').DataTable({
            destroy: true,
            processing: true,
            responsive: true,
            autoWidth: false,
            ordering: true,
            ajax: `${BASE_URL}/admin/producto/getall`,
            dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            pageLength: 10,
            language: {
                lengthMenu: " _MENU_ ",
                info: "Mostrando _START_ a _END_ de _TOTAL_",
                infoEmpty: "0 productos",
                infoFiltered: "(filtrado)",
                paginate: { next: "Sig.", previous: "Ant." },
                "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de productos</h5></div>`, 
            },
            columns: [
                { data: 'fecha_raw', visible: false, defaultContent: '' },
                { data: 'id_producto', render: d => `<span class="fw-bold text-muted">#${d}</span>` },
                { data: 'nombre', render: d => `<div class="d-flex align-items-center gap-2"><span class="badge bg-label-primary p-2 rounded"><i class="bx bx-package"></i></span><span class="fw-bold">${self.escHtml(d)}</span></div>` },
                { data: null, className: 'text-center', render: function(d, t, row) {
                    const pc = parseFloat(row.precio_compra);
                    const pv = parseFloat(row.precio_venta);
                    let margenStr = '—';
                    if (pc > 0) {
                        const margen = (((pv - pc) / pc) * 100).toFixed(0);
                        const color = pv > pc ? 'text-success' : 'text-danger';
                        margenStr = `<span class="${color} small fw-bold">(${margen}%)</span>`;
                    }
                    return `<div class="d-flex flex-column" style="line-height:1.2;">
                                <span class="fw-bold text-primary">S/ ${pv.toFixed(2)}</span>
                                <small class="text-muted">Costo: S/ ${pc.toFixed(2)} ${margenStr}</small>
                            </div>`;
                }},
                { data: null, render: function(d, t, row) {
                    const actual = parseInt(row.stock_actual);
                    const min = parseInt(row.stock_minimo);
                    const pct = min > 0 ? Math.min((actual / (min * 3)) * 100, 100) : 100;
                    const barColor = actual === 0 ? 'bg-danger' : (actual <= min ? 'bg-warning' : 'bg-success');
                    return `<div class="d-flex flex-column align-items-center">
                                <span class="fw-bold mb-1">${actual} <small class="text-muted fw-normal">(Mín. ${min})</small></span>
                                <div class="progress" style="width:70px;height:4px">
                                    <div class="progress-bar ${barColor}" style="width:${pct}%"></div>
                                </div>
                            </div>`;
                }},
                { data: null, className: 'text-center', render: function(d, t, row) {
                    // Estado Stock
                    const actual = parseInt(row.stock_actual);
                    const min = parseInt(row.stock_minimo);
                    let stockBadge = '<span class="badge bg-label-success mb-1">Con stock</span>';
                    if (actual === 0) stockBadge = '<span class="badge bg-label-danger mb-1">Sin stock</span>';
                    else if (actual <= min) stockBadge = '<span class="badge bg-label-warning mb-1">Bajo stock</span>';

                    // Estado Caducidad
                    let cadBadge = '';
                    if (row.fecha_caducidad) {
                        let hoy = new Date();
                        hoy.setHours(0,0,0,0);
                        let limitDate = new Date();
                        limitDate.setDate(limitDate.getDate() + 30);
                        limitDate.setHours(0,0,0,0);
                        
                        let fcad = new Date(row.fecha_caducidad + 'T00:00:00');
                        
                        if (fcad < hoy) {
                            cadBadge = '<br><span class="badge bg-danger shadow-sm mt-1 animate__animated animate__pulse animate__infinite"><i class="bx bx-error-circle me-1"></i>Vencido</span>';
                        } else if (fcad <= limitDate) {
                             cadBadge = '<br><span class="badge bg-warning shadow-sm mt-1 text-dark"><i class="bx bx-timer me-1"></i>Por Vencer</span>';
                        } else {
                             cadBadge = `<br><small class="text-muted d-block mt-1"><i class="bx bx-calendar-check me-1"></i>Vence: ${fcad.toLocaleDateString('es-ES')}</small>`;
                        }
                    }

                    return `<div>${stockBadge}${cadBadge}</div>`;
                }},
                { data: null, className: 'text-center', orderable: false, render: function() {
                    return `
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-editar" href="javascript:void(0);">
                                <i class="bx bx-edit-alt me-1"></i> Editar
                            </a>
                            <a class="dropdown-item btn-stock" href="javascript:void(0);">
                                <i class="bx bx-transfer me-1"></i> Ajustar Stock
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);">
                                <i class="bx bx-trash me-1"></i> Eliminar
                            </a>
                        </div>
                    </div>`;
                }}
            ],
            buttons: [{
                extend: 'excelHtml5',
                className: 'd-none',
                filename: 'Reporte_Productos',
                title: '',
                exportOptions: { columns: [1, 2, 3, 4, 5] }, // Exclude action columns
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

    // ════════════════════════════════════════════════════
    // 2. CARGAR STATS
    // ════════════════════════════════════════════════════
    cargarStats: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/producto/getstats`);
            const s = await res.json();
            document.getElementById('stat_total').textContent = s.total || 0;
            document.getElementById('stat_con_stock').textContent = s.con_stock || 0;
            document.getElementById('stat_bajo_stock').textContent = s.bajo_stock || 0;
            document.getElementById('stat_sin_stock').textContent = s.sin_stock || 0;
            if(document.getElementById('stat_por_vencer')) document.getElementById('stat_por_vencer').textContent = s.por_vencer || 0;
            document.getElementById('stat_valor_inv').textContent = 'S/ ' + parseFloat(s.valor_inventario || 0).toFixed(0);
            document.getElementById('stat_valor_venta').textContent = 'S/ ' + parseFloat(s.valor_venta || 0).toFixed(0);
        } catch (e) { console.error('Error cargando stats:', e); }
    },

    // ════════════════════════════════════════════════════
    // 3. EVENTOS UI
    // ════════════════════════════════════════════════════
    initEventosUI: function() {
        const self = this;

        // FILTRO STOCK
        const filtroStock = document.getElementById('filtroStock');
        if (filtroStock) {
            filtroStock.addEventListener('change', () => self.tabla.draw());
        }

        // BUSCADOR GLOBAL
        const buscador = document.getElementById('buscadorGlobal');
        if (buscador) {
            buscador.addEventListener('keyup', function() {
                self.tabla.search(this.value).draw();
            });
        }

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, originalData) {
            if (settings.nTable.id !== 'tablaProductos') return true;
            
            // Filtro por Fechas
            let min = $("#filtroFechaInicio").val();
            let max = $("#filtroFechaFin").val();
            let fechaStr = data[0]; // fecha_raw está oculta en posición 0
            
            if (fechaStr) {
                let fecha = new Date(fechaStr);
                if (min && new Date(min) > fecha) return false;
                if (max) {
                    let maxDate = new Date(max);
                    maxDate.setHours(23, 59, 59, 999);
                    if (maxDate < fecha) return false;
                }
            }

            // Filtro por Stock
            let f = $('#filtroStock').val();
            if (!f) return true;
            
            let actual = parseInt(originalData.stock_actual);
            let minStock = parseInt(originalData.stock_minimo);
            
            if (f === 'con_stock') return (actual > minStock);
            if (f === 'bajo_stock') return (actual <= minStock && actual > 0);
            if (f === 'sin_stock') return (actual === 0);
            return true;
        });

        // FILTROS OFFCANVAS
        var offcanvasEl = document.getElementById("offcanvasFiltroProductos");
        if (offcanvasEl) {
            var filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl, { backdrop: true, scroll: true });
            
            $("#btnAbrirFiltro").on("click", e => { e.preventDefault(); filtroOffcanvas.show(); });
            
            $("#btnAplicarFiltros").on("click", () => {
                filtroOffcanvas.hide();
                self.tabla.draw();
            });

            $("#filtroFechaInicio, #filtroFechaFin").on("change", function() {
                self.tabla.draw();
            });

            $("#btnLimpiarFiltros").on("click", () => {
                $("#buscadorGlobal").val("");
                $("#filtroFechaInicio, #filtroFechaFin").val("");
                $('#filtroStock').val("");
                self.tabla.search("").draw();
            });
        }

        // EXPORTAR
        $("#btnExportar").on("click", () => self.tabla.button(".buttons-excel").trigger());

        // BOTÓN NUEVO
        $(document).on('click', '#btnNuevoProducto', () => self.abrirModalRegistro());

        // EDITAR (delegación de eventos en tablaProductos)
        $('#tablaProductos').on('click', '.btn-editar', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#edit_id').val(p.id_producto);
            $('#edit_nombre').val(p.nombre);
            $('#edit_precio_compra').val(p.precio_compra);
            $('#edit_precio_venta').val(p.precio_venta);
            $('#edit_stock').val(p.stock_actual);
            $('#edit_stock_min').val(p.stock_minimo);
            $('#edit_fecha_caducidad').val(p.fecha_caducidad);
            $('#modalEditar').modal('show');
        });

        // AJUSTAR STOCK
        $('#tablaProductos').on('click', '.btn-stock', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#stock_id').val(p.id_producto);
            $('#stock_nombre').text(p.nombre);
            $('#stock_actual_display').text(p.stock_actual);
            $('#stock_tipo').val('ENTRADA');
            $('#stock_cantidad').val(1);
            $('#modalStock').modal('show');
        });

        // ELIMINAR
        $('#tablaProductos').on('click', '.btn-eliminar', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#eliminar_id').val(p.id_producto);
            $('#eliminar_nombre').text(p.nombre);
            $('#modalEliminar').modal('show');
        });
    },

    // ════════════════════════════════════════════════════
    // 4. FORMULARIOS
    // ════════════════════════════════════════════════════
    initFormularios: function() {
        const self = this;

        const handleForm = async (formId, url, btnText, modalId, reset = false) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Procesando...');
                try {
                    let formData = new FormData(this);
                    let object = {};
                    formData.forEach((value, key) => { object[key] = value; });

                    let res = await fetch(`${BASE_URL}${url}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(object)
                    });
                    let data = await res.json();

                    if (data.success) {
                        $(`#${modalId}`).find('[data-bs-dismiss="modal"]').click();
                        self.initVisualFixes();
                        self.tabla.ajax.reload(null, false);
                        self.cargarStats();
                        if (reset) this.reset();
                        self.mostrarToast(data.message, 'success');
                        if (typeof window.updateGlobalNotifications === 'function') {
                            window.updateGlobalNotifications();
                        }
                    } else {
                        self.mostrarToast(data.message, 'danger');
                    }
                } catch (err) {
                    self.mostrarToast('Error de conexión', 'danger');
                } finally {
                    btn.prop('disabled', false).html(`<i class="bx bx-save me-1"></i>${btnText}`);
                }
            });
        };

        handleForm('formRegistro', '/admin/producto/registrar', 'GUARDAR', 'modalRegistro', true);
        handleForm('formEditar', '/admin/producto/editar', 'ACTUALIZAR', 'modalEditar');
        handleForm('formStock', '/admin/producto/ajustarstock', 'AJUSTAR', 'modalStock', true);
        handleForm('formEliminar', '/admin/producto/eliminar', 'CONTUNUAR', 'modalEliminar');
    },

    // ════════════════════════════════════════════════════
    // 5. MODALES Y UTILIDADES
    abrirModalRegistro: function() {
        const form = document.getElementById('formRegistro');
        if (form) form.reset();
        new bootstrap.Modal(document.getElementById('modalRegistro')).show();
    },

    // ════════════════════════════════════════════════════
    // 7. UTILIDADES
    // ════════════════════════════════════════════════════
    initVisualFixes: function() {
        $('.modal-backdrop, .offcanvas-backdrop').remove();
        $('body').removeClass('modal-open offcanvas-open').css('overflow', '').css('padding-right', '');

        // Resetear formulario al cerrar
        $("#modalRegistro").on("hidden.bs.modal", function () {
            const form = document.querySelector('#modalRegistro form');
            if (form) form.reset();
        });
    },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastProducto');
        if (!toastEl) {
            toastEl = document.getElementById('toastSistema');
        }
        if (!toastEl) return alert(msg);
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = '11000';
        const msgEl = document.getElementById('toastProductoMsg') || document.getElementById('toastMensaje');
        if (msgEl) msgEl.textContent = msg;
        new bootstrap.Toast(toastEl).show();
    },

    escHtml: function(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }
};

document.addEventListener("DOMContentLoaded", () => { ProductoModule.init(); });
