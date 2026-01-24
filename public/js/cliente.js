document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================================
    // 0. FUNCIÓN MAESTRA DEL TOAST (Usada por todo el sistema)
    // ==========================================================
    function mostrarToast(mensaje, tipo) {
        const toastEl = document.getElementById('toastSistema');
        const toastIcono = document.getElementById('toastIcono');
        const toastTitulo = document.getElementById('toastTitulo');
        const toastMensaje = document.getElementById('toastMensaje');

        toastEl.className = "bs-toast toast fade"; // Reset
        
        if (tipo === 'success') {
            toastEl.classList.add('bg-success');
            toastIcono.className = "icon-base bx bx-check-circle me-2";
            toastTitulo.innerText = "¡Éxito!";
        } else if (tipo === 'danger') {
            toastEl.classList.add('bg-danger');
            toastIcono.className = "icon-base bx bx-error-circle me-2";
            toastTitulo.innerText = "Error";
        } else if (tipo === 'warning') {
            toastEl.classList.add('bg-warning');
            toastIcono.className = "icon-base bx bx-info-circle me-2";
            toastTitulo.innerText = "Atención";
        }

        toastMensaje.innerText = mensaje;
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    }


    // ==========================================================
    // 1. REGISTRAR CLIENTE
    // ==========================================================
    const registerForm = document.getElementById("registrarcliente");
    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault(); 
            const submitBtn = registerForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Registrando...";

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries()); 

            try {
                const response = await fetch(`${BASE_URL}/admin/cliente/registrarcliente`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    mostrarToast("El cliente se ha guardado correctamente.", "success");
                    
                    if ($('#tablaClientes').length) {
                        $('#tablaClientes').DataTable().ajax.reload(function() { tabla.columns.adjust().draw(); }, false); 
                    }
                    
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('largeModal')).hide();
                        registerForm.reset(); 
                    }, 800); 

                } else {
                    mostrarToast(result.message || "No se pudo registrar.", "danger");
                }
            } catch (error) {
                mostrarToast("Error de conexión con el servidor.", "warning");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }

    // ==========================================================
    // 2. CONFIGURACIÓN DE TABLA DATATABLES
    // ==========================================================
    if ($('#tablaClientes').length) {
        
        var tabla = $('#tablaClientes').DataTable({
            "destroy": true,
            "scrollX": false,   
            "autoWidth": false, 
            "ajax": {
                "url": `${BASE_URL}/admin/cliente/getall`,
                "type": "GET"
            },
            
            "lengthChange": true,
            "lengthMenu": [[10, 25, 50], ["10 registros", "25 registros", "50 registros"]],
            "dom": '<"d-none"B><"px-4 pt-3"l>rt<"d-flex justify-content-between align-items-center px-4 pb-3"ip>', 
            
            // --- BOTÓN INVISIBLE DE EXCEL (DISEÑO PERSONALIZADO INFALIBLE) ---
            "buttons": [{
                extend: 'excelHtml5',
                title: '', 
                filename: 'Reporte_Clientes_' + new Date().toLocaleDateString().replace(/\//g, '-'),
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 
                    stripHtml: true,
                    orthogonal: 'export' 
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];

                    // 1. CREAMOS NUESTRA PROPIA FUENTE (Blanca y Negrita)
                    var fontCount = $('fonts font', styles).length;
                    $('fonts', styles).append('<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>');

                    // 2. CREAMOS NUESTRO PROPIO FONDO (Azul Oscuro Puro)
                    var fillCount = $('fills fill', styles).length;
                    $('fills', styles).append('<fill><patternFill patternType="solid"><fgColor rgb="5F61E6" /><bgColor indexed="64" /></patternFill></fill>');

                    // 3. CREAMOS EL ESTILO UNIENDO FUENTE Y FONDO
                    var styleCount = $('cellXfs xf', styles).length;
                    $('cellXfs', styles).append('<xf numFmtId="0" fontId="' + fontCount + '" fillId="' + fillCount + '" applyFont="1" applyFill="1" />');

                    // 4. APLICAMOS EL ESTILO NUEVO A TODA LA CABECERA
                    $('row:first c', sheet).attr('s', styleCount); 
                }
            }],

            "columns": [
                { 
                    "data": "nombres", 
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            let correo = row.email ? row.email : 'Sin correo';
                            return `<span class="fw-medium text-primary">${data} ${row.apellidos}</span><br>
                                    <small class="text-muted">${correo}</small>`;
                        }
                        return data; 
                    }
                },
                { "data": "apellidos", "visible": false },
                { "data": "dni_ruc", "render": function(data, type) { return type === 'display' ? `<span class="badge bg-label-dark">${data}</span>` : data; } },
                { "data": "sexo", "visible": false },
                { "data": "email", "visible": false },
                { "data": "telefono_principal" },
                { "data": "telefono_alternativo_w", "visible": false, "defaultContent": "-" },
                { 
                    "data": "created_at", 
                    "visible": true,
                    "render": function(data) {
                        if(!data) return '-';
                        let f = new Date(data);
                        return `${f.getDate().toString().padStart(2, '0')}/${(f.getMonth()+1).toString().padStart(2, '0')}/${f.getFullYear()}`;
                    }
                },
                { 
                    "data": "estado_whatsapp",
                    "render": function(data, type) {
                        if (type === 'display') return data == 1 ? `<span class="badge bg-label-success"><i class='bx bxl-whatsapp'></i> ON</span>` : `<span class="badge bg-label-secondary">OFF</span>`;
                        return data == 1 ? 'Sí' : 'No';
                    }
                },
                { "data": "puntos", "className": "text-center", "render": function(data, type) { return type === 'display' ? `<span class="badge bg-warning text-dark">${data}</span>` : data; } },
                { "data": "observaciones", "visible": false, "defaultContent": "Sin observaciones" },
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        let rowData = encodeURIComponent(JSON.stringify(row));
                        return `
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item btn-ver" href="javascript:void(0);" data-json="${rowData}"><i class="bx bx-show-alt me-1 text-info"></i> Detalle</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item btn-editar" href="javascript:void(0);" data-json="${rowData}"><i class="bx bx-edit-alt me-1"></i> Editar</a>
                            <a class="dropdown-item text-danger btn-eliminar" href="javascript:void(0);" data-json="${rowData}"><i class="bx bx-trash me-1"></i> Eliminar</a>
                          </div>
                        </div>`;
                    }
                }
            ],
            
            "ordering": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "paginate": { "next": "Siguiente", "previous": "Anterior" },
                "zeroRecords": `
                <div class="text-center p-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="130" class="mb-3 opacity-75" alt="Sin resultados">
                    <h5 class="fw-bold text-primary mb-1">No encontramos ningún cliente</h5>
                    <span class="text-muted">Intenta con otro término de búsqueda o cambia las fechas del filtro.</span>
                </div>`
            }
        });


        // ==========================================================
        // 3. EVENTOS UI Y FILTROS (Offcanvas)
        // ==========================================================

        // A.1 CONFIGURACIÓN: CREAR EL FILTRO DE FECHAS INVISIBLE
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let minDate = $('#filtroFechaInicio').val();
            let maxDate = $('#filtroFechaFin').val();
            
            // La fecha está en la columna 7 (Oculta) en formato DD/MM/YYYY
            let fechaTabla = data[7]; 
            if (!fechaTabla || fechaTabla === '-') return true;

            // Convertir "DD/MM/YYYY" de la tabla a Objeto Fecha (YYYY-MM-DD)
            let partes = fechaTabla.split('/');
            let rowDate = new Date(partes[2], partes[1] - 1, partes[0]);

            // Validar si la fecha de la tabla es mayor o igual a "Fecha Inicio"
            if (minDate) {
                let min = new Date(minDate + 'T00:00:00');
                if (rowDate < min) return false;
            }
            // Validar si la fecha de la tabla es menor o igual a "Fecha Fin"
            if (maxDate) {
                let max = new Date(maxDate + 'T23:59:59');
                if (rowDate > max) return false;
            }
            return true; // Si cumple las condiciones, se muestra en la tabla
        });

        // A.2 EVENTO: DISPARAR FILTRO DE FECHAS AL CAMBIAR
        $('#filtroFechaInicio, #filtroFechaFin').on('change', function() {
            tabla.draw(); // Redibuja la tabla aplicando el filtro de arriba
        });

        // A.3 EVENTO: BUSCADOR GLOBAL DE TEXTO
        $('#buscadorGlobal').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        // A.4 EVENTO: BOTÓN LIMPIAR FILTROS
        $('#btnLimpiarFiltros').on('click', function() {
            // 1. Limpiamos los inputs del HTML
            $('#buscadorGlobal').val('');
            $('#filtroFechaInicio').val('');
            $('#filtroFechaFin').val('');
            
            // 2. Limpiamos la búsqueda interna de DataTables y redibujamos
            tabla.search('').draw();
        });

        $('#btnExportar').on('click', function() {
            tabla.button('.buttons-excel').trigger(); 
            mostrarToast("El reporte Excel se está descargando.", "success");
        });

        // C. VER DETALLE (Código Corregido con todos los campos)
        $('#tablaClientes tbody').on('click', '.btn-ver', function () {
            let cliente = JSON.parse(decodeURIComponent($(this).attr('data-json')));
            let fecha = cliente.created_at ? new Date(cliente.created_at).toLocaleDateString() : '-';
            
            // Etiqueta bonita para el WhatsApp
            let badgeWhatsapp = cliente.estado_whatsapp == 1 
                ? `<span class="badge bg-success"><i class='bx bxl-whatsapp'></i> Activo</span>` 
                : `<span class="badge bg-secondary">Inactivo</span>`;

            let htmlDetalle = `
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Nombres:</strong> ${cliente.nombres}</li>
                    <li class="list-group-item"><strong>Apellidos:</strong> ${cliente.apellidos}</li>
                    <li class="list-group-item"><strong>DNI / RUC:</strong> ${cliente.dni_ruc}</li>
                    <li class="list-group-item"><strong>Sexo:</strong> ${cliente.sexo}</li>
                    <li class="list-group-item"><strong>Email:</strong> ${cliente.email || 'No registrado'}</li>
                    <li class="list-group-item"><strong>Tel. Principal:</strong> ${cliente.telefono_principal}</li>
                    <li class="list-group-item"><strong>Tel. Alternativo:</strong> ${cliente.telefono_alternativo_w || '-'}</li>
                    <li class="list-group-item"><strong>WhatsApp:</strong> ${badgeWhatsapp}</li>
                    <li class="list-group-item"><strong>Fecha de Registro:</strong> ${fecha}</li>
                    <li class="list-group-item"><strong>Puntos acumulados:</strong> <span class="badge bg-warning text-dark">${cliente.puntos} pts</span></li>
                    <li class="list-group-item"><strong>Observaciones:</strong> <br> ${cliente.observaciones || 'Sin observaciones'}</li>
                </ul>`;
            
            $('#contenidoDetalle').html(htmlDetalle);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        });

        $('#tablaClientes tbody').on('click', '.btn-editar', function () {
            let cliente = JSON.parse(decodeURIComponent($(this).attr('data-json')));
            $('#edit_id_cliente').val(cliente.id_cliente);
            $('#edit_dni').val(cliente.dni_ruc);
            $('#edit_nombres').val(cliente.nombres);
            $('#edit_apellidos').val(cliente.apellidos);
            $('#edit_sexo').val(cliente.sexo);
            $('#edit_puntos').val(cliente.puntos);
            $('#edit_email').val(cliente.email);
            $('#edit_tel1').val(cliente.telefono_principal);
            $('#edit_tel2').val(cliente.telefono_alternativo_w);
            $('#edit_observaciones').val(cliente.observaciones);
            $('#edit_whatsapp').prop('checked', cliente.estado_whatsapp == 1);
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        });

        $('#tablaClientes tbody').on('click', '.btn-eliminar', function () {
            let cliente = JSON.parse(decodeURIComponent($(this).attr('data-json')));
            $('#delete_id_cliente').val(cliente.id_cliente);
            $('#nombre_eliminar').text(cliente.nombres + ' ' + cliente.apellidos);
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        });

        $('#modalDetalle, #modalEditar, #modalEliminar').on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', 'auto');
        });


        // ==========================================================
        // 4. AJAX EDITAR Y ELIMINAR
        // ==========================================================
        const formEditar = document.getElementById("formEditarCliente");
        if (formEditar) {
            formEditar.addEventListener("submit", async (e) => {
                e.preventDefault();
                const btnSubmit = formEditar.querySelector("button[type='submit']");
                btnSubmit.disabled = true;
                btnSubmit.innerText = "Guardando...";

                try {
                    const response = await fetch(`${BASE_URL}/admin/cliente/editarcliente`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(Object.fromEntries(new FormData(formEditar).entries()))
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        tabla.ajax.reload(function() { tabla.columns.adjust().draw(); }, false); 
                        bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
                        mostrarToast(result.message, "success");
                    } else {
                        mostrarToast(result.message, "danger");
                    }
                } catch (error) { mostrarToast("Error de conexión.", "warning"); } 
                finally { btnSubmit.disabled = false; btnSubmit.innerText = "GUARDAR CAMBIOS"; }
            });
        }

        const formEliminar = document.getElementById("formEliminarCliente");
        if (formEliminar) {
            formEliminar.addEventListener("submit", async (e) => {
                e.preventDefault();
                const btnSubmit = formEliminar.querySelector("button[type='submit']");
                btnSubmit.disabled = true;
                btnSubmit.innerText = "Eliminando...";

                try {
                    const response = await fetch(`${BASE_URL}/admin/cliente/eliminarcliente`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(Object.fromEntries(new FormData(formEliminar).entries()))
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        tabla.ajax.reload(function() { tabla.columns.adjust().draw(); }, false); 
                        bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                        mostrarToast(result.message, "success");
                    } else {
                        mostrarToast(result.message, "danger");
                    }
                } catch (error) { mostrarToast("Error de conexión.", "warning"); } 
                finally { btnSubmit.disabled = false; btnSubmit.innerText = "Sí, eliminar"; }
            });
        }

    } // FIN DE IF TABLA CLIENTES
}); // FIN DOMCONTENTLOADED