/**
 * Módulo Productos — Admin
 * Estructura: Module Pattern + init() (igual que ClienteModule, EmpleadoModule, etc.)
 */
const ProductoModule = {
    productosData: [],

    init: function() {
        this.cargarProductos();
        this.cargarStats();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
    },

    // ════════════════════════════════════════════════════
    // 1. CARGAR PRODUCTOS
    // ════════════════════════════════════════════════════
    cargarProductos: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/producto/getall`);
            const json = await res.json();
            this.productosData = json.data || [];
            this.renderTabla();
        } catch (e) {
            console.error('Error cargando productos:', e);
        }
    },

    // ════════════════════════════════════════════════════
    // 2. RENDER TABLA
    // ════════════════════════════════════════════════════
    renderTabla: function() {
        const self = this;
        const tbody = document.getElementById('tbodyProductos');
        if (!tbody) return;
        const filtro = document.getElementById('filtroStock')?.value || '';

        let data = this.productosData;
        if (filtro === 'con_stock') data = data.filter(p => p.stock_actual > p.stock_minimo);
        else if (filtro === 'bajo_stock') data = data.filter(p => p.stock_actual <= p.stock_minimo && p.stock_actual > 0);
        else if (filtro === 'sin_stock') data = data.filter(p => p.stock_actual == 0);

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-muted">
                <i class="bx bx-package" style="font-size:2.5rem"></i>
                <p class="mt-2 mb-0">Sin productos ${filtro ? 'en esta categoría' : 'registrados'}</p>
                ${!filtro ? '<button class="btn btn-sm btn-primary mt-2" id="btnAgregarVacio"><i class="bx bx-plus me-1"></i>Agregar producto</button>' : ''}
            </td></tr>`;

            // Evento para botón vacío
            const btnVacio = document.getElementById('btnAgregarVacio');
            if (btnVacio) btnVacio.addEventListener('click', () => self.abrirModalRegistro());
            return;
        }

        tbody.innerHTML = data.map(p => {
            const margen = p.precio_compra > 0
                ? (((p.precio_venta - p.precio_compra) / p.precio_compra) * 100).toFixed(0) + '%'
                : '—';
            const margenColor = p.precio_venta > p.precio_compra ? 'text-success' : 'text-danger';

            let estadoBadge = '';
            if (p.stock_actual == 0) {
                estadoBadge = '<span class="badge bg-label-danger">Sin stock</span>';
            } else if (p.stock_actual <= p.stock_minimo) {
                estadoBadge = '<span class="badge bg-label-warning">Bajo stock</span>';
            } else {
                estadoBadge = '<span class="badge bg-label-success">Con stock</span>';
            }

            const pct = p.stock_minimo > 0 ? Math.min((p.stock_actual / (p.stock_minimo * 3)) * 100, 100) : 100;
            const barColor = p.stock_actual == 0 ? 'bg-danger' : (p.stock_actual <= p.stock_minimo ? 'bg-warning' : 'bg-success');
            const stockBar = `<div class="d-flex align-items-center gap-2">
                <span class="fw-bold">${p.stock_actual}</span>
                <div class="progress" style="width:60px;height:6px">
                    <div class="progress-bar ${barColor}" style="width:${pct}%"></div>
                </div>
            </div>`;

            return `<tr>
                <td class="fw-bold text-muted">#${p.id_producto}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-package"></i></span>
                        <span class="fw-bold">${self.escHtml(p.nombre)}</span>
                    </div>
                </td>
                <td class="text-muted text-center">S/ ${parseFloat(p.precio_compra).toFixed(2)}</td>
                <td class="fw-bold text-primary text-center">S/ ${parseFloat(p.precio_venta).toFixed(2)}</td>
                <td class="${margenColor} fw-bold text-center">${margen}</td>
                <td>${stockBar}</td>
                <td class="text-muted text-center">${p.stock_minimo}</td>
                <td class="text-center" >${estadoBadge}</td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-editar" href="javascript:void(0);" data-id="${p.id_producto}">
                                <i class="bx bx-edit-alt me-1"></i> Editar
                            </a>
                            <a class="dropdown-item btn-stock" href="javascript:void(0);" data-id="${p.id_producto}">
                                <i class="bx bx-transfer me-1"></i> Ajustar Stock
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);" data-id="${p.id_producto}">
                                <i class="bx bx-trash me-1"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </td>
            </tr>`;
        }).join('');
    },

    // ════════════════════════════════════════════════════
    // 3. CARGAR STATS
    // ════════════════════════════════════════════════════
    cargarStats: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/producto/getstats`);
            const s = await res.json();
            document.getElementById('stat_total').textContent = s.total || 0;
            document.getElementById('stat_con_stock').textContent = s.con_stock || 0;
            document.getElementById('stat_bajo_stock').textContent = s.bajo_stock || 0;
            document.getElementById('stat_sin_stock').textContent = s.sin_stock || 0;
            document.getElementById('stat_valor_inv').textContent = 'S/ ' + parseFloat(s.valor_inventario || 0).toFixed(0);
            document.getElementById('stat_valor_venta').textContent = 'S/ ' + parseFloat(s.valor_venta || 0).toFixed(0);
        } catch (e) { console.error('Error cargando stats:', e); }
    },

    // ════════════════════════════════════════════════════
    // 4. EVENTOS UI
    // ════════════════════════════════════════════════════
    initEventosUI: function() {
        const self = this;

        // Helper para obtener producto por ID
        function getProductoById(id) {
            return self.productosData.find(p => p.id_producto == id);
        }

        // FILTRO STOCK
        const filtroStock = document.getElementById('filtroStock');
        if (filtroStock) {
            filtroStock.addEventListener('change', () => self.renderTabla());
        }

        // BUSCADOR GLOBAL
        const buscador = document.getElementById('buscadorGlobal');
        if (buscador) {
            buscador.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tbodyProductos tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(val) ? '' : 'none';
                });
            });
        }

        // BOTÓN NUEVO
        $(document).on('click', '#btnNuevoProducto', () => self.abrirModalRegistro());

        // EDITAR (delegación de eventos en tbody)
        $('#tbodyProductos').on('click', '.btn-editar', function() {
            const p = getProductoById($(this).data('id'));
            if (!p) return;
            $('#edit_id').val(p.id_producto);
            $('#edit_nombre').val(p.nombre);
            $('#edit_precio_compra').val(p.precio_compra);
            $('#edit_precio_venta').val(p.precio_venta);
            $('#edit_stock').val(p.stock_actual);
            $('#edit_stock_min').val(p.stock_minimo);
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        // AJUSTAR STOCK
        $('#tbodyProductos').on('click', '.btn-stock', function() {
            const p = getProductoById($(this).data('id'));
            if (!p) return;
            $('#stock_id').val(p.id_producto);
            $('#stock_nombre').text(p.nombre);
            $('#stock_actual_display').text(p.stock_actual);
            $('#stock_tipo').val('ENTRADA');
            $('#stock_cantidad').val(1);
            new bootstrap.Modal(document.getElementById('modalStock')).show();
        });

        // ELIMINAR
        $('#tbodyProductos').on('click', '.btn-eliminar', function() {
            const p = getProductoById($(this).data('id'));
            if (!p) return;
            $('#eliminar_id').val(p.id_producto);
            $('#eliminar_nombre').text(p.nombre);
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        });
    },

    // ════════════════════════════════════════════════════
    // 5. FORMULARIOS
    // ════════════════════════════════════════════════════
    initFormularios: function() {
        const self = this;

        const handleForm = async (formId, url, btnText, modalId, reset = false) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Procesando...');
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
                        bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                        self.initVisualFixes();
                        self.cargarProductos();
                        self.cargarStats();
                        if (reset) this.reset();
                        self.mostrarToast(data.message, 'success');
                    } else {
                        self.mostrarToast(data.message, 'danger');
                    }
                } catch (err) {
                    self.mostrarToast('Error de conexión', 'danger');
                } finally {
                    btn.prop('disabled', false).text(btnText);
                }
            });
        };

        handleForm('formRegistro', '/admin/producto/registrar', 'GUARDAR', 'modalRegistro', true);
        handleForm('formEditar', '/admin/producto/editar', 'ACTUALIZAR', 'modalEditar');
        handleForm('formStock', '/admin/producto/ajustarstock', 'AJUSTAR', 'modalStock', true);
        handleForm('formEliminar', '/admin/producto/eliminar', 'SÍ, ELIMINAR', 'modalEliminar');
    },

    // ════════════════════════════════════════════════════
    // 6. MODALES
    // ════════════════════════════════════════════════════
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
