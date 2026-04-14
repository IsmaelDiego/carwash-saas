/**
 * Módulo Productos — Admin (Fase 2: Lotes FIFO)
 * Estructura: Module Pattern + init()
 */
const ProductoModule = {
    tabla: null,

    init: function() {
        this.initTabla();
        this.cargarStats();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
        this.cargarAlertasVencimiento();
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
            dom: '<"row mx-2"<"col-md-12 my-2"l>><t><"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
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
                    const ps = parseFloat(row.precio_sugerido || pv);
                    
                    let margenStr = '—';
                    if (pc > 0) {
                        const margen = (((ps - pc) / pc) * 100).toFixed(0);
                        const color = ps > pc ? 'text-success' : 'text-danger';
                        margenStr = `<span class="${color} small fw-bold">(${margen}%)</span>`;
                    }
                    return `<div class="d-flex flex-column" style="line-height:1.2;">
                                <span class="fw-bold text-primary" title="Precio máximo sugerido por lotes">S/ ${ps.toFixed(2)}</span>
                                <small class="text-muted" style="font-size:0.75rem">Base: S/ ${pv.toFixed(2)}</small>
                                <small class="text-muted" style="font-size:0.65rem">Costo: S/ ${pc.toFixed(2)} ${margenStr}</small>
                            </div>`;
                }},
                { data: null, render: function(d, t, row) {
                    const actual = parseInt(row.stock_actual);
                    const min = parseInt(row.stock_minimo);
                    const lotes = parseInt(row.lotes_activos || 0);
                    const pct = min > 0 ? Math.min((actual / (min * 3)) * 100, 100) : 100;
                    const barColor = actual === 0 ? 'bg-danger' : (actual <= min ? 'bg-warning' : 'bg-success');
                    return `<div class="d-flex flex-column align-items-center">
                                <span class="fw-bold mb-1">${actual} <small class="text-muted fw-normal">(Mín. ${min})</small></span>
                                <div class="progress" style="width:70px;height:4px">
                                    <div class="progress-bar ${barColor}" style="width:${pct}%"></div>
                                </div>
                                <small class="text-muted mt-1" style="font-size:0.65rem"><i class="bx bx-layer"></i> ${lotes} lote${lotes !== 1 ? 's' : ''}</small>
                            </div>`;
                }},
                { data: null, className: 'text-center', render: function(d, t, row) {
                    const actual = parseInt(row.stock_actual);
                    const min = parseInt(row.stock_minimo);
                    let stockBadge = '<span class="badge bg-label-success mb-1">Con stock</span>';
                    if (actual === 0) stockBadge = '<span class="badge bg-label-danger mb-1">Sin stock</span>';
                    else if (actual <= min) stockBadge = '<span class="badge bg-label-warning mb-1">Bajo stock</span>';

                    // Vencimiento por lote más próximo
                    let cadBadge = '';
                    if (row.prox_vencimiento) {
                        let hoy = new Date();
                        hoy.setHours(0,0,0,0);
                        let limitDate = new Date();
                        limitDate.setDate(limitDate.getDate() + 30);
                        limitDate.setHours(0,0,0,0);
                        let fcad = new Date(row.prox_vencimiento + 'T00:00:00');
                        
                        if (fcad < hoy) {
                            cadBadge = '<br><span class="badge bg-danger shadow-sm mt-1 animate__animated animate__pulse animate__infinite"><i class="bx bx-error-circle me-1"></i>Lote Vencido</span>';
                        } else if (fcad <= limitDate) {
                            cadBadge = '<br><span class="badge bg-warning shadow-sm mt-1 text-dark"><i class="bx bx-timer me-1"></i>Lote Por Vencer</span>';
                        } else {
                            cadBadge = `<br><small class="text-muted d-block mt-1"><i class="bx bx-calendar-check me-1"></i>Vence: ${fcad.toLocaleDateString('es-ES')}</small>`;
                        }
                    } else if (row.fecha_caducidad) {
                        let hoy = new Date(); hoy.setHours(0,0,0,0);
                        let fcad = new Date(row.fecha_caducidad + 'T00:00:00');
                        if (fcad < hoy) {
                            cadBadge = '<br><span class="badge bg-danger shadow-sm mt-1"><i class="bx bx-error-circle me-1"></i>Vencido</span>';
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
                            <a class="dropdown-item btn-agregar-lote text-success" href="javascript:void(0);">
                                <i class="bx bx-archive-in me-1"></i> Agregar Lote
                            </a>
                            <a class="dropdown-item btn-ver-lotes" href="javascript:void(0);">
                                <i class="bx bx-layer me-1"></i> Ver Lotes / Dar de Baja
                            </a>
                            <a class="dropdown-item btn-kardex" href="javascript:void(0);">
                                <i class="bx bx-history me-1"></i> Kardex
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
                exportOptions: { columns: [1, 2, 3, 4, 5] },
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
            if(document.getElementById('stat_lotes_activos')) document.getElementById('stat_lotes_activos').textContent = s.total_lotes_activos || 0;
            document.getElementById('stat_valor_inv').textContent = 'S/ ' + parseFloat(s.valor_inventario || 0).toFixed(0);
            if(document.getElementById('stat_valor_venta')) document.getElementById('stat_valor_venta').textContent = 'S/ ' + parseFloat(s.valor_venta || 0).toFixed(0);
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
            
            let min = $("#filtroFechaInicio").val();
            let max = $("#filtroFechaFin").val();
            let fechaStr = data[0];
            
            if (fechaStr) {
                let fecha = new Date(fechaStr);
                if (min && new Date(min) > fecha) return false;
                if (max) {
                    let maxDate = new Date(max);
                    maxDate.setHours(23, 59, 59, 999);
                    if (maxDate < fecha) return false;
                }
            }

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

        // EDITAR
        $('#tablaProductos').on('click', '.btn-editar', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#edit_id').val(p.id_producto);
            $('#edit_nombre').val(p.nombre);
            $('#edit_precio_compra').val(p.precio_compra);
            $('#edit_precio_venta').val(p.precio_venta);
            $('#edit_stock_min').val(p.stock_minimo);
            $('#edit_fecha_caducidad').val(p.fecha_caducidad);
            $('#modalEditar').modal('show');
        });

        // AJUSTAR STOCK (Legacy)
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

        // ═══ AGREGAR LOTE ═══
        $('#tablaProductos').on('click', '.btn-agregar-lote', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#lote_id_producto').val(p.id_producto);
            $('#lote_producto_nombre').text(p.nombre);
            $('#lote_precio_compra').val(p.precio_compra);
            $('#lote_precio_venta').val(p.precio_venta);
            $('#lote_cantidad').val('');
            $('#lote_fecha_vencimiento').val(p.fecha_caducidad || '');
            $('#modalAgregarLote').modal('show');
        });

        // ═══ VER LOTES ═══
        $('#tablaProductos').on('click', '.btn-ver-lotes', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            self.cargarLotesProducto(p.id_producto, p.nombre);
        });

        // ═══ KARDEX ═══
        $('#tablaProductos').on('click', '.btn-kardex', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            self.cargarKardex(p.id_producto, p.nombre);
        });

        // ELIMINAR
        $('#tablaProductos').on('click', '.btn-eliminar', function() {
            const p = self.tabla.row($(this).closest('tr')).data();
            if (!p) return;
            $('#eliminar_id').val(p.id_producto);
            $('#eliminar_nombre').text(p.nombre);
            $('#modalEliminar').modal('show');
        });

        // ═══ BOTÓN ALERTAS VENCIMIENTO ═══
        $('#btnAlertasVencimiento').on('click', function() {
            self.cargarAlertasVencimiento();
            new bootstrap.Offcanvas(document.getElementById('offcanvasAlertas')).show();
        });

        // ═══ MERMA: Selector de motivo ═══
        $('#merma_motivo_sel').on('change', function() {
            const val = $(this).val();
            if (val && val !== 'otro') {
                $('#merma_motivo').val(val);
            } else if (val === 'otro') {
                $('#merma_motivo').val('').focus();
            }
        });

        // ═══ MERMA: Calcular gasto estimado ═══
        $('#merma_cantidad').on('input', function() {
            const cant = parseInt($(this).val()) || 0;
            const costo = parseFloat($('#merma_costo').data('raw')) || 0;
            $('#merma_gasto_estimado').text((cant * costo).toFixed(2));
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
                        self.cargarAlertasVencimiento();
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
        handleForm('formEliminar', '/admin/producto/eliminar', 'CONTINUAR', 'modalEliminar');
        handleForm('formAgregarLote', '/admin/producto/agregarlote', 'REGISTRAR LOTE', 'modalAgregarLote', true);
        handleForm('formMerma', '/admin/producto/registrarmerma', 'REGISTRAR MERMA', 'modalMerma', true);
    },

    // ════════════════════════════════════════════════════
    // 5. CARGAR LOTES DE UN PRODUCTO
    // ════════════════════════════════════════════════════
    cargarLotesProducto: async function(idProducto, nombre) {
        $('#verLotes_nombre').text(nombre);
        $('#tbodyLotes').html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="bx bx-loader-alt bx-spin"></i> Cargando...</td></tr>');
        new bootstrap.Modal(document.getElementById('modalVerLotes')).show();

        try {
            const res = await fetch(`${BASE_URL}/admin/producto/getlotes?id=${idProducto}`);
            const data = await res.json();
            const lotes = data.data || [];

            if ($.fn.DataTable.isDataTable('#tablaLotesModal')) {
                $('#tablaLotesModal').DataTable().destroy();
            }

            if (lotes.length === 0) {
                $('#tbodyLotes').html('<tr><td colspan="7" class="text-center text-muted py-4">No hay lotes activos para este producto.</td></tr>');
                return;
            }

            const self = this;
            let html = '';
            lotes.forEach(l => {
                const dias = l.dias_para_vencer;
                let vencBadge = '<span class="text-muted">—</span>';
                if (l.fecha_vencimiento) {
                    const f = new Date(l.fecha_vencimiento + 'T00:00:00');
                    if (dias !== null && dias <= 0) {
                        vencBadge = `<span class="badge bg-danger"><i class="bx bx-error-circle me-1"></i>VENCIDO (${Math.abs(dias)}d)</span>`;
                    } else if (dias !== null && dias <= 30) {
                        vencBadge = `<span class="badge bg-warning text-dark"><i class="bx bx-timer me-1"></i>${dias}d restantes</span>`;
                    } else {
                        vencBadge = `<span class="small">${f.toLocaleDateString('es-ES')}</span>`;
                    }
                }

                html += `<tr>
                    <td><span class="fw-bold text-muted">#${l.id_lote}</span></td>
                    <td class="text-center">
                        <span class="fw-bold">${l.cantidad_actual}</span>
                        <small class="text-muted">/ ${l.cantidad_inicial}</small>
                    </td>
                    <td class="text-center">S/ ${parseFloat(l.precio_compra).toFixed(2)}</td>
                    <td class="text-center fw-bold text-primary">S/ ${parseFloat(l.precio_venta).toFixed(2)}</td>
                    <td class="text-center">${vencBadge}</td>
                    <td class="text-center"><span class="badge bg-label-success">Activo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-danger btn-merma-lote" 
                            data-id="${l.id_lote}" data-nombre="${self.escHtml(nombre)}" 
                            data-cantidad="${l.cantidad_actual}" data-costo="${l.precio_compra}">
                            <i class="bx bx-trash bx-xs"></i>
                        </button>
                    </td>
                </tr>`;
            });

            if ($.fn.DataTable.isDataTable('#tablaLotesModal')) {
                $('#tablaLotesModal').DataTable().destroy();
            }
            $('#tbodyLotes').html(html);
            $('#tablaLotesModal').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                pageLength: 10,
                autoWidth: false,
                destroy: true
            });

            // Handler para botones de merma dentro de Ver Lotes
            $('#tablaLotesModal').off('click', '.btn-merma-lote').on('click', '.btn-merma-lote', function() {
                const btn = $(this);
                // Cerrar modal de lotes primero
                bootstrap.Modal.getInstance(document.getElementById('modalVerLotes'))?.hide();
                setTimeout(() => {
                    self.abrirMerma(btn.data('id'), btn.data('nombre'), btn.data('cantidad'), btn.data('costo'));
                }, 400);
            });
        } catch (e) {
            $('#tbodyLotes').html('<tr><td colspan="7" class="text-center text-danger py-4">Error cargando lotes.</td></tr>');
        }
    },

    // ════════════════════════════════════════════════════
    // 6. ALERTAS DE VENCIMIENTO
    // ════════════════════════════════════════════════════
    cargarAlertasVencimiento: async function() {
        const self = this;
        try {
            const res = await fetch(`${BASE_URL}/admin/producto/alertasvencimiento`);
            const data = await res.json();
            const alertas = data.alertas || [];

            // Badge en botón
            const badge = document.getElementById('badgeAlertasVenc');
            if (badge) {
                if (alertas.length > 0) {
                    badge.textContent = alertas.length;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Contenido del offcanvas
            const container = document.getElementById('listaAlertasVencimiento');
            if (!container) return;

            if (alertas.length === 0) {
                container.innerHTML = `<div class="text-center py-5 text-muted">
                    <i class="bx bx-check-shield fs-1 text-success"></i>
                    <p class="mt-2 fw-bold">¡Sin alertas!</p>
                    <small>No hay lotes vencidos ni próximos a vencer.</small>
                </div>`;
                return;
            }

            let html = '';
            alertas.forEach(a => {
                const esVencido = a.tipo === 'VENCIDO';
                const icon = esVencido ? 'bx-error-circle' : 'bx-timer';
                const bgColor = esVencido ? '#fff5f5' : '#fffbeb';
                const borderColor = esVencido ? '#fed7d7' : '#fef3c7';
                const textColor = esVencido ? 'text-danger' : 'text-warning';
                const label = esVencido ? 'VENCIDO' : `Vence en ${a.dias} día${a.dias !== 1 ? 's' : ''}`;

                html += `<div class="d-flex align-items-start gap-3 p-3 border-bottom position-relative" style="background:${bgColor}; border-left: 4px solid ${borderColor};">
                    <i class="bx ${icon} ${textColor} fs-4 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${a.producto}</div>
                        <small class="text-muted">Lote #${a.lote} · ${a.cantidad} unidades</small>
                        <div class="mt-1"><span class="badge ${esVencido ? 'bg-danger' : 'bg-warning text-dark'}">${label}</span></div>
                        <small class="text-muted d-block mt-1">${a.fecha}</small>
                        <div class="mt-2">
                            <button class="btn btn-xs btn-outline-danger btn-merma-alerta" 
                                data-id="${a.lote}" data-nombre="${self.escHtml(a.producto)}" 
                                data-cantidad="${a.cantidad}" data-costo="${a.costo || 0}">
                                <i class="bx bx-trash me-1"></i> Dar de Baja
                            </button>
                        </div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;

            // Mapear eventos al generar el html
            const selfObj = this;
            $(container).find('.btn-merma-alerta').on('click', function() {
                const btn = $(this);
                // Cerrar Offcanvas primero
                bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAlertas'))?.hide();
                setTimeout(() => {
                    selfObj.abrirMerma(btn.data('id'), btn.data('nombre'), btn.data('cantidad'), btn.data('costo'));
                }, 400);
            });
        } catch (e) {
            console.error('Error alertas:', e);
        }
    },

    // ════════════════════════════════════════════════
    // 7. KARDEX DE MOVIMIENTOS
    // ════════════════════════════════════════════════
    cargarKardex: async function(idProducto, nombre) {
        $('#kardex_nombre').text(nombre);
        $('#tbodyKardex').html('<tr><td colspan="6" class="text-center text-muted py-4"><i class="bx bx-loader-alt bx-spin"></i> Cargando...</td></tr>');
        new bootstrap.Modal(document.getElementById('modalKardex')).show();

        try {
            const res = await fetch(`${BASE_URL}/admin/producto/getkardex?id=${idProducto}`);
            const data = await res.json();
            const movs = data.data || [];

            if ($.fn.DataTable.isDataTable('#tablaKardexModal')) {
                $('#tablaKardexModal').DataTable().destroy();
            }

            if (movs.length === 0) {
                $('#tbodyKardex').html('<tr><td colspan="6" class="text-center text-muted py-4">Sin movimientos registrados.</td></tr>');
                return;
            }

            let html = '';
            movs.forEach(m => {
                const tipoMap = {
                    'ENTRADA': { icon: 'bx-archive-in', color: 'success', label: 'Entrada' },
                    'VENTA': { icon: 'bx-cart', color: 'primary', label: 'Venta' },
                    'MERMA': { icon: 'bx-trash', color: 'danger', label: 'Merma' },
                    'AJUSTE_SALIDA': { icon: 'bx-transfer', color: 'warning', label: 'Ajuste' }
                };
                const info = tipoMap[m.tipo] || { icon: 'bx-dots-horizontal', color: 'secondary', label: m.tipo };

                html += `<tr>
                    <td><small class="text-muted">${m.fecha_fmt || ''}</small></td>
                    <td><span class="badge bg-label-${info.color}"><i class="bx ${info.icon} me-1"></i>${info.label}</span></td>
                    <td class="text-center">${m.id_lote ? '#' + m.id_lote : '—'}</td>
                    <td class="text-center fw-bold">${m.tipo === 'ENTRADA' ? '+' : '-'}${m.cantidad}</td>
                    <td><small>${m.referencia || '—'}</small></td>
                    <td><small class="text-muted">${m.usuario_nombre || 'Sistema'}</small></td>
                </tr>`;
            });

            if ($.fn.DataTable.isDataTable('#tablaKardexModal')) {
                $('#tablaKardexModal').DataTable().destroy();
            }
            $('#tbodyKardex').html(html);
            $('#tablaKardexModal').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                pageLength: 25,
                autoWidth: false,
                destroy: true
            });
        } catch (e) {
            $('#tbodyKardex').html('<tr><td colspan="6" class="text-center text-danger py-4">Error cargando kardex.</td></tr>');
        }
    },

    // ════════════════════════════════════════════════
    // 8. ABRIR MODAL DE MERMA
    // ════════════════════════════════════════════════
    abrirMerma: function(idLote, nombreProducto, cantidadDisponible, costoUnitario) {
        $('#merma_id_lote').val(idLote);
        $('#merma_producto_nombre').text(nombreProducto);
        $('#merma_lote_id').text('#' + idLote);
        $('#merma_disponible').text(cantidadDisponible + ' u.');
        $('#merma_costo').text('S/ ' + parseFloat(costoUnitario).toFixed(2)).data('raw', costoUnitario);
        $('#merma_cantidad').val('').attr('max', cantidadDisponible);
        $('#merma_motivo_sel').val('');
        $('#merma_motivo').val('');
        $('#merma_gasto_estimado').text('0.00');
        new bootstrap.Modal(document.getElementById('modalMerma')).show();
    },

    // ════════════════════════════════════════════════════
    // MODALES Y UTILIDADES
    // ════════════════════════════════════════════════════
    abrirModalRegistro: function() {
        const form = document.getElementById('formRegistro');
        if (form) form.reset();
        new bootstrap.Modal(document.getElementById('modalRegistro')).show();
    },

    initVisualFixes: function() {
        setTimeout(() => {
            $('.modal-backdrop, .offcanvas-backdrop').remove();
            $('body').removeClass('modal-open offcanvas-open').css('overflow', '').css('padding-right', '');
        }, 400);

        $("#modalRegistro").off("hidden.bs.modal").on("hidden.bs.modal", function () {
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
