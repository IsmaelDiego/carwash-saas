const PagoModule = {
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
            const res = await fetch(`${BASE_URL}/admin/pago/getstats`);
            const s = await res.json();
            if(document.getElementById('stat_total')) document.getElementById('stat_total').textContent = s.total || 0;
            if(document.getElementById('stat_pagados')) document.getElementById('stat_pagados').textContent = s.pagados || 0;
            if(document.getElementById('stat_pendientes')) document.getElementById('stat_pendientes').textContent = s.pendientes || 0;
            if(document.getElementById('stat_retrasados')) document.getElementById('stat_retrasados').textContent = s.retrasados || 0;
            if(document.getElementById('stat_monto_total')) document.getElementById('stat_monto_total').textContent = 'S/ ' + parseFloat(s.monto_total || 0).toFixed(2);
        } catch (e) { console.error('Error cargando stats:', e); }
    },

    initDataTable: function() {
        if (!$('#tbPagos').length) return;

        this.tabla = $('#tbPagos').DataTable({
            "destroy": true,
            "processing": true,
            "responsive": true,
            "order": [[4, 'desc']], // Order by Fecha Programada 
            "dom": '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            "ajax": { "url": `${BASE_URL}/admin/pago/getall`, "type": "GET" },
            "language": {
                "lengthMenu": " _MENU_ ",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ pagos",
                "infoEmpty": "0 pagos",
                "infoFiltered": "(filtrado)",
                "paginate": { "next": "Sig", "previous": "Ant" },
                 "zeroRecords": `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de pagos</h5></div>`, 
            },
            "columns": [
                { "data": "id_pago", "visible": false },
                { "data": "empleado", "className": "fw-bold" },
                { "data": "tipo" },
                { 
                    "data": "periodo",
                    "render": function(data) {
                        return data ? data : '-';
                    }
                },
                { 
                    "data": "fecha_programada",
                    "render": function(data) {
                        if(!data) return '';
                        let d = new Date(data + 'T00:00:00');
                        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    }
                },
                { 
                    "data": "estado",
                    "render": function(data) {
                        if(data === 'PENDIENTE') return '<span class="badge bg-label-warning">Pendiente</span>';
                        if(data === 'PAGADO') return '<span class="badge bg-label-success">Pagado</span>';
                        return '<span class="badge bg-label-danger">Retrasado</span>';
                    }
                },
                { 
                    "data": "monto",
                    "render": function(data) {
                        return 'S/ ' + parseFloat(data).toFixed(2);
                    }
                },
                {
                    "data": null, "className": "text-center", "orderable": false,
                    "render": function(data, type, row) {
                        if(row.estado !== 'PAGADO') {
                            return `
                                <button class="btn btn-sm btn-outline-success btn-pagar" data-id="${row.id_pago}" title="Marcar Pagado"><i class="bx bx-check"></i></button>
                            `;
                        } else {
                            return `<span class="text-muted"><i class="bx bx-check-double"></i></span>`;
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

        // NUEVO PAGO: Verificar primero, luego abrir modal
        $('#btnNuevoPago').on('click', async function(e) {
            e.preventDefault();
            const password = await window.confirmByPassword();
            if(password) {
                $('#modalRegistrarPago').modal('show');
            }
        });

        // Filtro Extendido para Month Input (sobre fecha_programada que es la columna de fecha fuerte)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
            if (settings.nTable.id !== 'tbPagos') return true;
            let m = $("#filtroMesAnio").val(); // Formato: "YYYY-MM"
            if (!m) return true; // Si está vacío, no filtra

            let fechaProgramada = rowData.fecha_programada; 
            if (!fechaProgramada) return true;

            return fechaProgramada.startsWith(m);
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
        let idPagoSeleccionado = null;

        $(document).on('click', '.btn-pagar', async function() {
            idPagoSeleccionado = $(this).data('id');
            
            const confirmed = await window.confirmByPassword();
            if(confirmed) {
                $('#modalConfirmarPago').modal('show');
            }
        });

        $('#btnConfirmarPago').on('click', function() {
            if(!idPagoSeleccionado) return;

            let btn = $(this);
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Procesando...');

            $.post(`${BASE_URL}/admin/pago/cambiarestado`, { id_pago: idPagoSeleccionado, estado: 'PAGADO' }, function(res) {
                btn.prop('disabled', false).text('Sí, pagar');
                $('#modalConfirmarPago').find('[data-bs-dismiss="modal"]').click();

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
                btn.prop('disabled', false).text('Sí, pagar');
                $('#modalConfirmarPago').find('[data-bs-dismiss="modal"]').click();
                self.mostrarToast('Error de red', 'danger');
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
        $('#formRegistrarPago').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...');

            $.ajax({
                url: `${BASE_URL}/admin/pago/registrar`,
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    btn.prop('disabled', false).text('Guardar');
                    if(res.success) {
                        self.tabla.ajax.reload(null, false);
                        self.cargarStats();
                        $('#formRegistrarPago')[0].reset();
                        $('#modalRegistrarPago').find('[data-bs-dismiss="modal"]').click();
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

document.addEventListener("DOMContentLoaded", () => { PagoModule.init(); });
