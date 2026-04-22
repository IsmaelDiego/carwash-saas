const PermisoModule = {
    tabla: null,
    filtroOffcanvas: null,

    init: function() {
        this.initDataTable();
        this.cargarStats();
        this.initEventosUI();
        this.initFormularios();
    },

    cargarStats: async function() {
        try {
            const res = await fetch(`${BASE_URL}/admin/permiso/getstats`);
            const s = await res.json();
            if(document.getElementById('stat_total')) document.getElementById('stat_total').textContent = s.total || 0;
            if(document.getElementById('stat_aprobados')) document.getElementById('stat_aprobados').textContent = s.aprobados || 0;
            if(document.getElementById('stat_pendientes')) document.getElementById('stat_pendientes').textContent = s.pendientes || 0;
            if(document.getElementById('stat_rechazados')) document.getElementById('stat_rechazados').textContent = s.rechazados || 0;
        } catch (e) { console.error('Error cargando stats:', e); }
    },

    initDataTable: function() {
        if (!$('#tbPermisos').length) return;

        this.tabla = $('#tbPermisos').DataTable({
            "destroy": true,
            "processing": true,
            "responsive": true,
            "order": false, // Order by 'Desde' date mainly
            "dom": '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "ajax": { "url": `${BASE_URL}/admin/permiso/getall`, "type": "GET" },
            "language": {
                "lengthMenu": " _MENU_ ",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ permisos",
                "infoEmpty": "0 permisos",
                "infoFiltered": "(filtrado)",
                "paginate": { "next": "Sig", "previous": "Ant" },
                 "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de permisos</h5></div>`, 
            },
            "columns": [
                { "data": "id_permiso", "visible": false },
                { "data": "empleado", "className": "fw-bold" },
                { "data": "tipo" },
                { 
                    "data": "fecha_inicio",
                    "render": function(data) {
                        if(!data) return '';
                        let d = new Date(data + 'T00:00:00');
                        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    }
                },
                { 
                    "data": "fecha_fin",
                    "render": function(data) {
                        if(!data) return '';
                        let d = new Date(data + 'T00:00:00');
                        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    }
                },
                { "data": "motivo", "defaultContent": "" },
                { 
                    "data": "estado",
                    "render": function(data) {
                        if(data === 'PENDIENTE') return '<span class="badge bg-label-warning">Pendiente</span>';
                        if(data === 'APROBADO') return '<span class="badge bg-label-success">Aprobado</span>';
                        return '<span class="badge bg-label-danger">Rechazado</span>';
                    }
                },
                {
                    "data": null, "className": "text-center", "orderable": false,
                    "render": function(data, type, row) {
                        if(row.estado === 'PENDIENTE') {
                            return `
                                <button class="btn btn-sm btn-outline-success btn-estado" data-id="${row.id_permiso}" data-estado="APROBADO" title="Aprobar"><i class="bx bx-check"></i></button>
                                <button class="btn btn-sm btn-outline-danger btn-estado" data-id="${row.id_permiso}" data-estado="RECHAZADO" title="Rechazar"><i class="bx bx-x"></i></button>
                            `;
                        } else {
                            let estadoText = row.estado.charAt(0).toUpperCase() + row.estado.slice(1).toLowerCase();
                            return `<span class="text-muted"><i class="bx bx-check-double"></i> ${estadoText}</span>`;
                        }
                    }
                }
            ]
        });
    },

    initEventosUI: function() {
        const self = this;

        // Buscador
        $("#buscadorGlobal").on("keyup", function() { self.tabla.search(this.value).draw(); });

        // Filtros (Offcanvas)
        let offcanvasEl = document.getElementById('offcanvasFiltros');
        if(offcanvasEl) this.filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl);

        $("#btnAbrirFiltro").on('click', function() {
            if(self.filtroOffcanvas) self.filtroOffcanvas.show();
        });

        // NUEVO PERMISO: Verificar primero, luego abrir modal
        $('#btnNuevoPermiso').on('click', async function(e) {
            e.preventDefault();
            const password = await window.confirmByPassword();
            if(password) {
                $('#modalRegistrarPermiso').modal('show');
            }
        });

        // Filtro Extendido para Month Input
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
            if (settings.nTable.id !== 'tbPermisos') return true;
            let m = $("#filtroMesAnio").val(); // Formato: "YYYY-MM"
            if (!m) return true; // Si está vacío, no filtra

            let fechaInicio = rowData.fecha_inicio;
            if (!fechaInicio) return true;

            return fechaInicio.startsWith(m);
        });

        // Filtrado en vivo al elegir el mes
        $("#filtroMesAnio").on("change", function() {
            self.tabla.draw();
        });

        $("#btnAplicarFiltros").on('click', function() {
            self.tabla.draw();
            if(self.filtroOffcanvas) self.filtroOffcanvas.hide();
        });

        $("#btnLimpiarFiltros").on('click', function() {
            $("#filtroMesAnio").val('');
            self.tabla.draw();
        });

        // Acciones: Cambiar Estado
        let idSeleccionado = null;
        let estadoSeleccionado = null;
        
        $(document).on('click', '.btn-estado', async function() {
            const tempId = $(this).data('id');
            const tempEstado = $(this).data('estado');

            const confirmed = await window.confirmByPassword();
            if(!confirmed) return;

            idSeleccionado = tempId;
            estadoSeleccionado = tempEstado;
            let esAprobado = (estadoSeleccionado === 'APROBADO');
            let texto = esAprobado ? 'aprobar' : 'rechazar';
            let color = esAprobado ? 'success' : 'danger';
            let icon = esAprobado ? 'bx-check-circle' : 'bx-x-circle';

            document.getElementById('iconoEstadoPermiso').innerHTML = `<i class="bx ${icon} text-${color}" style="font-size: 4rem;"></i>`;
            document.getElementById('tituloConfirmarEstado').innerText = esAprobado ? '¿Aprobar Permiso?' : '¿Rechazar Permiso?';
            document.getElementById('textoConfirmarEstado').innerText = `¿Está seguro que desea ${texto} este registro?`;
            
            let btnC = document.getElementById('btnConfirmarEstado');
            btnC.className = `btn btn-${color}`;
            
            $('#modalConfirmarEstado').modal('show');
        });

        $('#btnConfirmarEstado').on('click', function() {
            if(!idSeleccionado) return;
            
            let btn = $(this);
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Procesando...');

            $.post(`${BASE_URL}/admin/permiso/cambiarestado`, { id_permiso: idSeleccionado, estado: estadoSeleccionado }, function(res) {
                btn.prop('disabled', false).text('Sí, confirmar');
                $('#modalConfirmarEstado').find('[data-bs-dismiss="modal"]').click();
                
                if(res.success) {
                    self.tabla.ajax.reload(null, false);
                    self.cargarStats();
                    self.mostrarToast(res.message, 'success');
                    if (typeof window.updateGlobalNotifications === 'function') {
                        window.updateGlobalNotifications();
                    }
                } else {
                    self.mostrarToast(res.message, 'danger');
                }
            }).fail(function() {
                btn.prop('disabled', false).text('Sí, confirmar');
                $('#modalConfirmarEstado').find('[data-bs-dismiss="modal"]').click();
                self.mostrarToast('Error de conexión', 'danger');
            });
        });
    },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = "11000";
        $('#toastMensaje').text(msg);
        new bootstrap.Toast(toastEl).show();
    },

    initFormularios: function() {
        const self = this;

        // Validar que Hasta no sea menor que Desde
        $('#perm_fecha_inicio').on('change', function() {
            let start = $(this).val();
            if(start) {
                $('#perm_fecha_fin').attr('min', start);
            }
        });

        $('#formRegistrarPermiso').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...');

            $.ajax({
                url: `${BASE_URL}/admin/permiso/registrar`,
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    btn.prop('disabled', false).text('Guardar');
                    if(res.success) {
                        self.tabla.ajax.reload(null, false);
                        self.cargarStats();
                        $('#formRegistrarPermiso')[0].reset();
                        $('#modalRegistrarPermiso').find('[data-bs-dismiss="modal"]').click();
                        self.mostrarToast(res.message, 'success');
                        if (typeof window.updateGlobalNotifications === 'function') {
                            window.updateGlobalNotifications();
                        }
                    } else {
                        self.mostrarToast(res.message, 'danger');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text('Guardar');
                    self.mostrarToast('Error de red', 'danger');
                }
            });
        });
    }
};

document.addEventListener("DOMContentLoaded", () => { PermisoModule.init(); });
