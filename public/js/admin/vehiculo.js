const VehiculoModule = {
    tabla: null,
    filtroOffcanvas: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        this.initFormularios();
        this.initBuscadorPropietario();
        // Stats: computar después de cada carga AJAX
        const self = this;
        $('#tablaVehiculos').on('xhr.dt', function() {
            setTimeout(() => self.computeStats(), 100);
        });
    },

    // 1. DATA TABLE
    initDataTable: function() {
        if (!$('#tablaVehiculos').length) return;

        this.tabla = $('#tablaVehiculos').DataTable({
            "destroy": true,
            "processing": true,
            "responsive": true,
            "autoWidth": false,
            "ordering": true,
            "ajax": { "url": `${BASE_URL}/admin/vehiculo/getall`, "type": "GET" },
            
            // DOM: Paginación Izquierda, Info Derecha
            "dom": '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            
            "language": {
                "lengthMenu": " _MENU_ ",
                "info": "Mostrando _START_ a _END_ de _TOTAL_",
                "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de vehículos</h5></div>`, 
                "paginate": { "next": "Sig.", "previous": "Ant." }
            },

            "columns": [
                // --- COLUMNAS OCULTAS (IDs) ---
                // [0] ID Vehículo
                { "data": "id_vehiculo", "visible": false, "defaultContent": "" },
                // [1] ID Cliente
                { "data": "id_cliente", "visible": false, "defaultContent": "" },
                // [2] ID Categoria
                { "data": "id_categoria", "visible": false, "defaultContent": "" },
                
                // --- COLUMNAS VISIBLES (Y EXPORTABLES) ---
                
                // [3] PLACA
                { 
                    "data": "placa", 
                    "render": function(data) {
                        return `<span class="badge bg-warning text-dark border border-dark" style="font-family:monospace; letter-spacing:1px; font-size:0.9rem;">${data}</span>`;
                    }
                },
                
                // [4] CATEGORÍA (Nombre traído por JOIN)
                { 
                    "data": "nombre_categoria", "defaultContent": "General",
                    "render": function(data) {
                        return `<span class="badge bg-label-info">${data}</span>`;
                    }
                },

                // [5] COLOR
                { "data": "color", "defaultContent": "-" },

                // [6] PROPIETARIO (Nombre traído por JOIN)
                { 
                    "data": "nombre_propietario", "defaultContent": "Sin dueño",
                    "render": function(data, type, row) {
                        return `<div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">${data}</span>
                                    <small class="text-muted">${row.dni_propietario || ''}</small>
                                </div>`;
                    }
                },

                // [7] OBSERVACIONES (Oculta en tabla, visible en Excel)
                { "data": "observaciones", "visible": false, "defaultContent": "" },
                
                // [8] FECHA (Oculta en tabla, visible en Excel)
                { "data": "fecha_registro", "visible": false, "defaultContent": "" },

                // [9] ACCIONES (Detalle, Editar, Eliminar)
                { 
                    "data": null, "orderable": false, "className": "text-center",
                    "render": function() {
                        return `
                        <div class="dropdown">
                          <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-4"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item btn-ver" href="javascript:void(0);">
                                <i class="bx bx-show text-info me-2"></i> Ver Detalle
                            </a>
                            <a class="dropdown-item btn-editar" href="javascript:void(0);">
                                <i class="bx bx-edit text-warning me-2"></i> Editar
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);">
                                <i class="bx bx-trash me-2"></i> Eliminar
                            </a>
                          </div>
                        </div>`;
                    }
                }
            ],
            
            // --- CONFIGURACIÓN EXCEL (Celeste + Sin IDs + Sin Título) ---
            "buttons": [{
                extend: 'excelHtml5', 
                className: 'd-none', 
                filename: 'Reporte_Vehiculos',
                title: '', // <--- QUITA EL TÍTULO
                // Exportamos: Placa(3), Categoria(4), Color(5), Dueño(6), Obs(7), Fecha(8)
                exportOptions: { columns: [3, 4, 5, 6, 7, 8], orthogonal: 'export' },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];
                    
                    // Fondo Celeste (#00BFFF)
                    var fillIndex = $('fills fill', styles).length; 
                    $('fills', styles).append('<fill><patternFill patternType="solid"><fgColor rgb="FF00BFFF" /><bgColor indexed="64" /></patternFill></fill>');
                    
                    // Letra Blanca
                    var fontIndex = $('fonts font', styles).length; 
                    $('fonts', styles).append('<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>');
                    
                    // Estilo combinado
                    var styleIndex = $('cellXfs xf', styles).length; 
                    $('cellXfs', styles).append('<xf numFmtId="0" fontId="'+fontIndex+'" fillId="'+fillIndex+'" applyFont="1" applyFill="1" />');
                    
                    // Aplicar a fila 1
                    $('row:first c', sheet).attr('s', styleIndex);
                }
            }]
        });
    },

    // 2. EVENTOS DE INTERFAZ
    initEventosUI: function() {
        const self = this;
        
        // --- CONTROL FILTROS ---
        var offcanvasEl = document.getElementById('offcanvasFiltros');
        if(offcanvasEl) this.filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl, {backdrop:true, scroll:true});

        $('#btnAbrirFiltro').on('click', (e) => { e.preventDefault(); self.filtroOffcanvas.show(); });
        $('#btnAplicarFiltros').on('click', () => { 
            self.tabla.draw(); 
            self.filtroOffcanvas.hide(); 
        });
        $('#buscadorGlobal').on('keyup', function() { self.tabla.search(this.value).draw(); });
        $('#btnLimpiarFiltros').on('click', () => { 
            $('#buscadorGlobal').val(''); 
            $('#filtroFechaInicio, #filtroFechaFin').val(''); 
            self.tabla.search('').draw(); 
        });

        // B. FILTRO FECHAS (Plugin DataTables)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            // Solo aplicar si estamos en la tabla de vehículos
            if (settings.nTable.id !== 'tablaVehiculos') return true;

            let min = $("#filtroFechaInicio").val();
            let max = $("#filtroFechaFin").val();
            let fechaStr = data[8]; // Fecha (Columna oculta 8: fecha_registro)
            if (!fechaStr) return true;

            let fv = new Date(fechaStr).getTime();
            if (min && new Date(min).getTime() > fv) return false;
            if (max && new Date(max).getTime() < fv) return false;
            return true;
        });

        // Redibujar al cambiar fechas directamente
        $("#filtroFechaInicio, #filtroFechaFin").on("change", () => self.tabla.draw());
        
        // Exportar Excel
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // Helper Data
        function getData(el) { 
            let tr = $(el).closest('tr'); 
            if(tr.hasClass('child')) tr = tr.prev(); 
            return self.tabla.row(tr).data(); 
        }

        // --- ACCIONES ---
        
        // VER DETALLE
        $('#tablaVehiculos tbody').on('click', '.btn-ver', function() {
            let data = getData(this);
            let html = `
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-info" style="width: 80px; height: 80px;">
                        <span class="fs-1 fw-bold text-info"><i class="bx bx-car"></i></span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark text-uppercase">${data.placa}</h4>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        <span class="badge bg-warning text-dark px-3 py-2 shadow-sm rounded-pill"><i class="bx bx-shape-polygon me-1"></i>${data.nombre_categoria}</span>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-user text-primary"></i> Propietario</small>
                            <span class="fw-bold text-dark fs-6">${data.nombre_propietario}</span>
                            <span class="text-muted small"><i class="bx bx-id-card"></i> ${data.dni_propietario || 'No registrado'}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-palette text-info"></i> Color</small>
                            <span class="fw-semibold text-dark fs-6">${data.color || '-'}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-time text-secondary"></i> Registrado</small>
                            <span class="fw-semibold text-dark fs-6 text-truncate">${new Date(data.fecha_registro.replace(/-/g, "/")).toLocaleDateString()}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-notepad text-warning"></i> Observaciones</small>
                            <span class="text-muted small">${data.observaciones || '<i>Vehículo sin anotaciones previas o reportes de daños detallados en el momento de su inscripción.</i>'}</span>
                        </div>
                    </div>
                </div>`;
            $('#contenidoDetalle').html(html);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        });

        // EDITAR
        $('#tablaVehiculos tbody').on('click', '.btn-editar', function() {
            let data = getData(this);
            $('#edit_id_vehiculo').val(data.id_vehiculo);
            $('#edit_nombre_cliente').val(data.nombre_propietario);
            $('#edit_placa').val(data.placa);
            $('#edit_color').val(data.color);
            $('#edit_id_categoria').val(data.id_categoria); // Select de categoría
            $('#edit_observaciones').val(data.observaciones);
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        // ELIMINAR
        $('#tablaVehiculos tbody').on('click', '.btn-eliminar', function() {
            let data = getData(this);
            $('#delete_id_vehiculo').val(data.id_vehiculo);
            $('#placa_eliminar').text(data.placa);
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        });
    },

    // 3. FORMULARIOS (JSON)
    initFormularios: function() {
        const self = this;
        const handleForm = async (formId, url, btnText, modalId, reset=false) => {
            $(`#${formId}`).on('submit', async function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Procesando...');
                try {
                    let formData = new FormData(this);
                    let jsonData = JSON.stringify(Object.fromEntries(formData));

                    let res = await fetch(`${BASE_URL}${url}`, { 
                        method: 'POST', 
                        headers: {'Content-Type': 'application/json'}, 
                        body: jsonData 
                    });
                    
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
        handleForm('registrarVehiculo', '/admin/vehiculo/registrarvehiculo', 'GUARDAR', 'modalRegistrar', true);
        handleForm('formEditarVehiculo', '/admin/vehiculo/editarvehiculo', 'ACTUALIZAR', 'modalEditar');
        handleForm('formEliminarVehiculo', '/admin/vehiculo/eliminarvehiculo', 'SÍ, ELIMINAR', 'modalEliminar');
    },

    initVisualFixes: function() {
        const self = this;
        $('.modal-backdrop, .offcanvas-backdrop').remove();
        $('body').removeClass('modal-open offcanvas-open').css('overflow','').css('padding-right','');

        // Resetear formulario de registro al cerrar
        $("#modalRegistrar").on("hidden.bs.modal", function () {
            const form = document.getElementById("registrarVehiculo");
            if (form) {
                form.reset();
                self.resetBuscadorPropietario();
            }
        });
    },

    // --- LÓGICA BUSCADOR PROPIETARIO (SELECT LITE) ---
    clientesCache: [],
    initBuscadorPropietario: function() {
        const self = this;
        const trigger = $('#selectTriggerPropietario');
        const dropdown = $('#dropdownPropietarios');
        const inputSearch = $('#inputSearchInterno');
        const listaItems = $('#listaItemsPropietarios');
        const txtDisplay = $('#txtSeleccionComun');
        const hiddenId = $('#val_id_cliente');

        // 1. Cargar y Ordenar por Recientes
        const cargarClientes = async () => {
            try {
                const res = await fetch(`${BASE_URL}/admin/cliente/getall`);
                const data = await res.json();
                // Ordenar por ID descendente (más recientes primero)
                self.clientesCache = (data.data || []).sort((a, b) => b.id_cliente - a.id_cliente);
                self.renderItemsPropietarios(""); 
            } catch(e) { console.error("Error cargando clientes:", e); }
        };
        cargarClientes();

        // 2. Toggle Dropdown
        trigger.on('click', function(e) {
            e.stopPropagation();
            dropdown.toggleClass('show');
            if (dropdown.hasClass('show')) {
                inputSearch.val('').focus();
                self.renderItemsPropietarios(""); 
            }
        });

        // 3. Filtrado Interno
        inputSearch.on('input', function() {
            self.renderItemsPropietarios($(this).val());
        });

        // Evitar cierre al hacer click dentro del buscador
        dropdown.on('click', (e) => e.stopPropagation());

        // 4. Seleccionar Item
        listaItems.on('click', '.resultado-item', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');

            hiddenId.val(id);
            txtDisplay.html(`<i class="bx bxs-user-check me-2 text-primary"></i> ${nombre}`);
            dropdown.removeClass('show');
            trigger.addClass('border-primary shadow-sm');
        });

        // Cerrar al click afuera
        $(document).on('click', () => dropdown.removeClass('show'));
    },

    renderItemsPropietarios: function(query) {
        const self = this;
        const q = query.toLowerCase().trim();
        const listaItems = $('#listaItemsPropietarios');
        
        let filtrados = [];
        if (q === "") {
            // Mostrar los 10 más recientes
            filtrados = self.clientesCache.slice(0, 10);
        } else {
            // Filtrar y mostrar top 10 matches
            filtrados = self.clientesCache.filter(c => 
                (c.nombres + ' ' + c.apellidos).toLowerCase().includes(q) || 
                c.dni.includes(q)
            ).slice(0, 10);
        }

        if (filtrados.length > 0) {
            let html = '';
            filtrados.forEach(c => {
                html += `
                    <div class="resultado-item animate__animated animate__fadeIn animate__faster" data-id="${c.id_cliente}" data-nombre="${c.nombres} ${c.apellidos}">
                        <span class="nombre text-dark">${c.nombres} ${c.apellidos}</span>
                        <span class="dni text-muted"><i class="bx bx-id-card me-1"></i>${c.dni}</span>
                    </div>`;
            });
            listaItems.html(html);
        } else {
            listaItems.html('<div class="p-3 text-center text-muted small">No se encontraron clientes</div>');
        }
    },

    resetBuscadorPropietario: function() {
        $('#val_id_cliente').val('');
        $('#txtSeleccionComun').text('Seleccione al dueño...');
        $('#selectTriggerPropietario').removeClass('border-primary shadow-sm');
        $('#dropdownPropietarios').removeClass('show');
    },

    computeStats: function() {
        if (!this.tabla) return;
        const data = this.tabla.rows().data().toArray();
        const total = data.length;
        const categorias = new Set(data.map(r => r.nombre_categoria)).size;
        const propietarios = new Set(data.map(r => r.id_cliente)).size;
        const now = new Date();
        const mes = data.filter(r => {
            if (!r.fecha_registro) return false;
            const f = new Date(r.fecha_registro);
            return f.getMonth() === now.getMonth() && f.getFullYear() === now.getFullYear();
        }).length;

        const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
        el('stat_veh_total', total);
        el('stat_veh_categorias', categorias);
        el('stat_veh_propietarios', propietarios);
        el('stat_veh_mes', mes);
    },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = "11000";
        $('#toastMensaje').text(msg);
        new bootstrap.Toast(toastEl).show();
    }
};

document.addEventListener("DOMContentLoaded", () => { VehiculoModule.init(); });