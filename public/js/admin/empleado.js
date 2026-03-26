const EmpleadoModule = {
    tabla: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        this.initFormularios();
        this.initVisualFixes();
        this.cargarRoles();
        this.cargarStats();
        this.checkRecoveryParam();
    },

    // ════════════════════════════════════════════════════
    // 1. DATATABLE
    // ════════════════════════════════════════════════════
    initDataTable: function() {
        if (!$('#tablaEmpleados').length) return;

        this.tabla = $('#tablaEmpleados').DataTable({
            "destroy": true, "processing": true, "responsive": true, "autoWidth": false, "ordering": true,
            "ajax": { "url": `${BASE_URL}/admin/empleado/getall`, "type": "GET" },
            "dom": 't<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "pageLength": 10,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_",
                "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de empleados</h5></div>`, 
                "paginate": { "next": "Sig.", "previous": "Ant." }
            },
            "columns": [
                { "data": "id_usuario",    "visible": false },
                { "data": "id_rol",        "visible": false },
                { "data": "email",         "visible": false },
                { "data": "telefono",      "visible": false },
                { "data": "avatar_url",    "visible": false },
                { "data": "fecha_creacion","visible": false },

                // COL: EMPLEADO (Avatar + Nombre)
                {
                    "data": "nombres",
                    "render": function(data, type, row) {
                        const inicial = data.charAt(0).toUpperCase();
                        const colores = { '1': 'bg-label-primary', '2': 'bg-label-info', '3': 'bg-label-warning' };
                        const bgClass = colores[row.id_rol] || 'bg-label-secondary';
                        return `<div class="d-flex align-items-center gap-2">
                            <div class="avatar-empleado ${bgClass}">${inicial}</div>
                            <div>
                                <span class="fw-bold text-dark">${data}</span>
                                ${row.email ? `<br><small class="text-muted">${row.email}</small>` : ''}
                            </div>
                        </div>`;
                    }
                },
                // COL: DNI
                {
                    "data": "dni",
                    "render": function(data) {
                        return `<span class="fw-semibold font-monospace">${data}</span>`;
                    }
                },
                // COL: ROL
                {
                    "data": "rol_nombre", "className": "text-center",
                    "render": function(data, type, row) {
                        const iconos = { '1': 'bx-shield', '2': 'bx-calculator', '3': 'bx-wrench' };
                        const colores = { '1': 'primary', '2': 'info', '3': 'warning' };
                        const icono = iconos[row.id_rol] || 'bx-user';
                        const color = colores[row.id_rol] || 'secondary';
                        return `<span class="badge bg-label-${color} px-3 py-2">
                            <i class="bx ${icono} me-1"></i>${data}
                        </span>`;
                    }
                },
                // COL: CONTACTO
                {
                    "data": null, "className": "text-center",
                    "render": function(data, type, row) {
                        let html = '';
                        if (row.telefono) {
                            html += `<span class="text-success"><i class="bx bx-phone me-1"></i>${row.telefono}</span>`;
                        } else {
                            html += '<span class="text-muted">—</span>';
                        }
                        return html;
                    }
                },
                // COL: ESTADO (Switch)
                {
                    "data": "estado", "className": "text-center",
                    "render": function(data, type, row) {
                        let checked = data == 1 ? 'checked' : '';
                        let disabled = row.id_usuario == 1 ? 'disabled title="Super Admin"' : '';
                        return `<div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input switch-estado" type="checkbox" 
                                   data-id="${row.id_usuario}" ${checked} ${disabled}>
                        </div>`;
                    }
                },
                // COL: ACCIONES
                {
                    "data": null, "orderable": false, "className": "text-center",
                    "render": function(data, type, row) {
                        return `<div class="dropdown">
                            <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded fs-4"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-ver" href="javascript:void(0);">
                                    <i class="bx bx-id-card text-info me-2"></i>Ver Perfil
                                </a>
                                <a class="dropdown-item btn-editar" href="javascript:void(0);">
                                    <i class="bx bx-edit text-warning me-2"></i>Editar
                                </a>
                                <a class="dropdown-item btn-password" href="javascript:void(0);">
                                    <i class="bx bx-lock-alt text-primary me-2"></i>Cambiar Contraseña
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);">
                                    <i class="bx bx-trash me-2"></i>Eliminar
                                </a>
                            </div>
                        </div>`;
                    }
                }
            ],
            // EXPORT EXCEL
            "buttons": [{
                extend: 'excelHtml5', className: 'd-none', filename: 'Reporte_Personal', title: '',
                exportOptions: { columns: [6, 7, 8, 9], orthogonal: 'export' },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];
                    var fillIndex = $('fills fill', styles).length;
                    $('fills', styles).append('<fill><patternFill patternType="solid"><fgColor rgb="FF696CFF" /><bgColor indexed="64" /></patternFill></fill>');
                    var fontIndex = $('fonts font', styles).length;
                    $('fonts', styles).append('<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>');
                    var styleIndex = $('cellXfs xf', styles).length;
                    $('cellXfs', styles).append('<xf numFmtId="0" fontId="'+fontIndex+'" fillId="'+fillIndex+'" applyFont="1" applyFill="1" />');
                    $('row:first c', sheet).attr('s', styleIndex);
                }
            }]
        });
    },

    // ════════════════════════════════════════════════════
    // 2. CARGAR ROLES EN SELECTS
    // ════════════════════════════════════════════════════
    cargarRoles: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/empleado/getroles`);
            const json = await res.json();
            const roles = json.data || [];
            
            ['reg_rol', 'edit_rol'].forEach(selectId => {
                const sel = document.getElementById(selectId);
                if (!sel) return;
                // Mantener el primer option
                const firstOption = sel.options[0];
                sel.innerHTML = '';
                sel.appendChild(firstOption);
                
                roles.forEach(rol => {
                    const opt = document.createElement('option');
                    opt.value = rol.id_rol;
                    opt.textContent = rol.nombre;
                    sel.appendChild(opt);
                });
            });
        } catch(e) { console.error('Error cargando roles:', e); }
    },

    // ════════════════════════════════════════════════════
    // 3. CARGAR STATS
    // ════════════════════════════════════════════════════
    cargarStats: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/empleado/getstats`);
            const stats = await res.json();

            const total = (stats.activos || 0) + (stats.inactivos || 0);
            const porRol = stats.por_rol || [];

            const iconosRol = { 'Administrador': 'bx-shield', 'Cajero': 'bx-calculator', 'Operario': 'bx-wrench' };
            const coloresRol = { 'Administrador': 'primary', 'Cajero': 'info', 'Operario': 'warning' };

            let html = `
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="card stat-card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-label-primary"><i class="bx bx-group text-primary"></i></div>
                            <div>
                                <div class="stat-value text-primary">${total}</div>
                                <small class="text-muted fw-medium">Total Personal</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="card stat-card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-label-success"><i class="bx bx-check-circle text-success"></i></div>
                            <div>
                                <div class="stat-value text-success">${stats.activos || 0}</div>
                                <small class="text-muted fw-medium">Activos</small>
                            </div>
                        </div>
                    </div>
                </div>`;

            porRol.forEach(r => {
                const icono = iconosRol[r.nombre] || 'bx-user';
                const color = coloresRol[r.nombre] || 'secondary';
                html += `
                    <div class="col-sm-6 col-xl-3 mb-3">
                        <div class="card stat-card h-100 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-label-${color}"><i class="bx ${icono} text-${color}"></i></div>
                                <div>
                                    <div class="stat-value text-${color}">${r.cantidad}</div>
                                    <small class="text-muted fw-medium">${r.nombre}s</small>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });

            document.getElementById('statsContainer').innerHTML = html;
        } catch(e) { console.error('Error cargando stats:', e); }
    },

    // ════════════════════════════════════════════════════
    // 4. EVENTOS UI
    // ════════════════════════════════════════════════════
    initEventosUI: function() {
        const self = this;

        // EVENTOS RENIEC
        $('#btnBuscarDniEmpleado').on('click', () => self.consultarReniec());
        $('#reg_dni').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                self.consultarReniec();
            }
        });

        // BUSCADOR
        $('#buscadorGlobal').on('keyup', function() {
            self.tabla.search(this.value).draw();
        });

        // FILTRO POR ROL
        $('#filtroRol').on('change', function() {
            self.tabla.column(8).search(this.value).draw(); // Columna visible de Rol
        });

        // EXPORTAR
        $('#btnExportar').on('click', () => self.tabla.button('.buttons-excel').trigger());

        // NUEVO EMPLEADO: Verificar primero
        $('#btnNuevoEmpleado').on('click', async function(e) {
            e.preventDefault();
            const password = await window.confirmByPassword();
            if(password) {
                $('#modalRegistrar').modal('show');
            }
        });

        // TOGGLE PASSWORD
        $(document).on('click', '.btn-toggle-pass', function() {
            const targetId = $(this).data('target');
            const input = document.getElementById(targetId);
            const icon = $(this).find('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.removeClass('bx-hide').addClass('bx-show');
            } else {
                input.type = 'password';
                icon.removeClass('bx-show').addClass('bx-hide');
            }
        });

        // SWITCH ESTADO
        $('#tablaEmpleados tbody').on('change', '.switch-estado', async function() {
            const el = $(this); 
            const isChecked = el.is(':checked');
            el.prop('checked', !isChecked); // Revertir temporalmente
            
            const confirmed = await window.confirmByPassword();
            if(!confirmed) return;

            el.prop('checked', isChecked); // Restaurar si confirmó
            el.prop('disabled', true);
            try {
                const res = await fetch(`${BASE_URL}/admin/empleado/cambiarestado`, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ id_usuario: el.data('id'), estado: isChecked ? 1 : 0 })
                });
                const data = await res.json();
                if (data.success) {
                    self.mostrarToast("Estado actualizado", "success");
                    self.tabla.ajax.reload(null, false);
                    self.cargarStats();
                } else {
                    el.prop('checked', !el.is(':checked'));
                    self.mostrarToast(data.message || "Error", "danger");
                }
            } catch(e) {
                el.prop('checked', !el.is(':checked'));
            } finally { el.prop('disabled', false); }
        });

        // Helper para obtener data de la fila
        function getData(el) {
            let tr = $(el).closest('tr');
            if (tr.hasClass('child')) tr = tr.prev();
            return self.tabla.row(tr).data();
        }

        // ─── VER DETALLE ───
        $('#tablaEmpleados tbody').on('click', '.btn-ver', function() {
            const d = getData(this);
            const estadoHtml = d.estado == 1
                ? '<span class="badge bg-success px-3">ACTIVO</span>'
                : '<span class="badge bg-secondary px-3">INACTIVO</span>';
            
            const rolIconos = { '1': 'bx-shield', '2': 'bx-calculator', '3': 'bx-wrench' };
            const rolColores = { '1': 'primary', '2': 'info', '3': 'warning' };
            const icono = rolIconos[d.id_rol] || 'bx-user';
            const color = rolColores[d.id_rol] || 'secondary';

            const fecha = d.fecha_creacion ? new Date(d.fecha_creacion).toLocaleDateString('es-PE', { 
                year:'numeric', month:'long', day:'numeric' 
            }) : '—';

            const html = `
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-${color}" style="width: 80px; height: 80px;">
                        <span class="fs-1 fw-bold text-${color}">${d.nombres.charAt(0).toUpperCase()}</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">${d.nombres}</h4>
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                        <span class="badge bg-label-${color} px-3 py-2"><i class="bx ${icono} me-1"></i>${d.rol_nombre}</span>
                        ${estadoHtml}
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-id-card"></i> DNI</small>
                            <span class="fw-semibold font-monospace text-dark fs-6">${d.dni}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-phone"></i> Teléfono</small>
                            <span class="fw-semibold text-dark fs-6">${d.telefono || '—'}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-envelope"></i> Email</small>
                            <span class="fw-semibold text-primary fs-6">${d.email || '—'}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-calendar"></i> Registrado el</small>
                            <span class="fw-semibold text-dark fs-6">${fecha}</span>
                        </div>
                    </div>
                </div>`;
            $('#contenidoDetalle').html(html);
            $('#modalDetalle').modal('show');
        });

        // ─── EDITAR ───
        $('#tablaEmpleados tbody').on('click', '.btn-editar', async function() {
            const d = getData(this);

            const password = await window.confirmByPassword();
            if(!password) return;

            $('#edit_id_usuario').val(d.id_usuario);
            $('#edit_dni').val(d.dni);
            $('#edit_nombres').val(d.nombres);
            $('#edit_rol').val(d.id_rol);
            $('#edit_email').val(d.email || '');
            $('#edit_telefono').val(d.telefono || '');
            $('#modalEditar').modal('show');
        });

        // ─── CAMBIAR CONTRASEÑA ───
        $('#tablaEmpleados tbody').on('click', '.btn-password', async function() {
            const d = getData(this);

            const confirmed = await window.confirmByPassword();
            if(!confirmed) return;

            $('#pass_id_usuario').val(d.id_usuario);
            $('#pass_nombre_empleado').text(d.nombres);
            $('#nueva_password').val('');
            $('#modalPassword').modal('show');
        });

        // ─── ELIMINAR ───
        $('#tablaEmpleados tbody').on('click', '.btn-eliminar', async function() {
            const d = getData(this);

            const password = await window.confirmByPassword();
            if(!password) return;

            // Inyectar el password verificado para el backend
            $('#delete_password_admin').val(password);

            $('#delete_id_usuario').val(d.id_usuario);
            $('#nombre_eliminar').text(d.nombres);
            $('#modalEliminar').modal('show');
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
                    let formElement = document.getElementById(formId);
                    let formData = new FormData(formElement);
                    let object = {};
                    formData.forEach((value, key) => { object[key] = value; });

                    let jsonData = JSON.stringify(object);
                    let res = await fetch(`${BASE_URL}${url}`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: jsonData
                    });
                    let data = await res.json();

                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                        self.initVisualFixes();
                        self.tabla.ajax.reload(null, false);
                        self.cargarStats();
                        if (reset) formElement.reset();
                        self.mostrarToast(data.message, "success");
                    } else {
                        self.mostrarToast(data.message, "danger");
                    }
                } catch(err) {
                    self.mostrarToast("Error de conexión", "danger");
                } finally {
                    btn.prop('disabled', false).text(btnText);
                }
            });
        };

        handleForm('formRegistrarEmpleado', '/admin/empleado/registrarempleado', 'GUARDAR', 'modalRegistrar', true);
        handleForm('formEditarEmpleado', '/admin/empleado/editarempleado', 'Actualizar', 'modalEditar');
        handleForm('formCambiarPassword', '/admin/empleado/cambiarpassword', 'CAMBIAR CONTRASEÑA', 'modalPassword', true);
        handleForm('formEliminarEmpleado', '/admin/empleado/eliminarempleado', 'SÍ, ELIMINAR', 'modalEliminar');
    },

    // ════════════════════════════════════════════════════
    // 6. UTILIDADES
    // ════════════════════════════════════════════════════
    initVisualFixes: function() {
        $('.modal-backdrop, .offcanvas-backdrop').remove();
        $('body').removeClass('modal-open offcanvas-open').css('overflow', '').css('padding-right', '');

        // Resetear formularios al cerrar
        $("#modalRegistrar, #modalEliminar, #modalPassword").on("hidden.bs.modal", function () {
            const form = this.querySelector('form');
            if (form) form.reset();
            
            // Si es el modal de registro, reiniciar inputs y mensajes reniec
            if (this.id === 'modalRegistrar') {
                $("#dniFeedbackEmpleado").html('Introduce el DNI para autocompletar nombre.');
                $("#reg_nombres").val("");
                $("#btnBuscarDniEmpleado").prop("disabled", false).html('<i class="bx bx-search fs-5"></i>');
            }
        });
    },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = "11000";
        $('#toastMensaje').text(msg);
        new bootstrap.Toast(toastEl).show();
    },

    consultarReniec: async function () {
        let dni = $("#reg_dni").val().trim();
        let feedback = $("#dniFeedbackEmpleado");
        
        if (dni.length !== 8 && dni.length !== 11) {
            feedback.html('<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> Debe tener 8 (DNI) u 11 (RUC) dígitos.</span>');
            return;
        }

        $("#btnBuscarDniEmpleado").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop("disabled", true);
        feedback.html('<span class="text-primary fw-bold"><i class="bx bx-loader-circle bx-spin"></i> Consultando...</span>');

        try {
            let res = await fetch(`${BASE_URL}/api/dni`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ dni: dni }),
            });
            let obj = await res.json();
          
            if (obj.success) {
                let n = "";
                let a = "";
                
                if (dni.length === 8 && obj.data) {
                    n = obj.data.nombres || "";
                    a = `${obj.data.apellido_paterno || ""} ${obj.data.apellido_materno || ""}`.trim();
                } else if (dni.length === 11 && obj.data) {
                    n = obj.data.nombre_o_razon_social || "";
                    a = "RUC"; 
                }

                $("#reg_nombres").val(`${n} ${a}`.trim().replace(/\s+/g, ' '));
                feedback.html('<span class="text-success fw-bold"><i class="bx bx-check-circle"></i> ¡Datos encontrados!</span>');
            } else {
                feedback.html(`<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> ${obj.message || "No encontrado"}</span>`);
                $("#reg_nombres").val("");
            }
        } catch (e) {
            feedback.html('<span class="text-danger fw-bold"><i class="bx bx-wifi-off"></i> Error de conexión con el servidor.</span>');
            $("#reg_nombres").val("");
        } finally {
            $("#btnBuscarDniEmpleado").html('<i class="bx bx-search fs-5"></i>').prop("disabled", false);
        }
    },

    checkRecoveryParam: async function() {
        const urlParams = new URLSearchParams(window.location.search);
        const recoverId = urlParams.get('recover_id');
        if (recoverId) {
            // Removemos el parámetro pronto para evitar loops o re-activaciones al refrescar
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path:cleanUrl},'',cleanUrl);

            // 1. Pedir primero la clave de admin
            const confirmed = await window.confirmByPassword();
            if(!confirmed) return;

            try {
                // 2. Si confirmó, buscar datos y abrir modal
                const res = await fetch(`${BASE_URL}/admin/empleado/getone?id=${recoverId}`);
                const data = await res.json();
                
                if (data.success) {
                    const user = data.user;
                    $('#pass_id_usuario').val(user.id_usuario);
                    $('#pass_nombre_empleado').text(user.nombres);
                    $('#nueva_password').val('');
                    $('#modalPassword').modal('show');
                }
            } catch(e) { console.error('Error auto-recovery:', e); }
        }
    }
};

document.addEventListener("DOMContentLoaded", () => { EmpleadoModule.init(); });
