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
    // 1.5. CLON SELECT2: BUSCADOR DE CLIENTES SIN LIBRERÍAS
    // ==========================================================
    const btnSelect = document.getElementById('btnAbrirSelect');
    const textoSelect = document.getElementById('textoSelect');
    const btnLimpiarSelect = document.getElementById('btnLimpiarSelect');
    const menuSelect = document.getElementById('menuSelect');
    const inputBuscador = document.getElementById('inputBuscador');
    const listaClientes = document.getElementById('listaClientes');
    const idOculto = document.getElementById('id_cliente');

    let clientesData = []; // Guardará los clientes temporalmente

    // A. Cargar datos al abrir el Modal
    $('#modalRegistrar').on('show.bs.modal', async function () {
        limpiarSelect();
        listaClientes.innerHTML = '<li class="list-group-item text-center text-muted border-0"><i class="bx bx-loader-alt bx-spin"></i> Cargando clientes...</li>';
        
        try {
            const response = await fetch(`${BASE_URL}/admin/cliente/getall`);
            const data = await response.json();
            clientesData = data.data; // Guardamos en memoria
            renderizarLista(clientesData);
        } catch (error) { 
            console.error("Error al cargar clientes", error); 
        }
    });

    // B. Abrir / Cerrar el menú al hacer clic
    btnSelect.addEventListener('click', function(e) {
        if(e.target.id === 'btnLimpiarSelect') return; // Evita abrir si hacen clic en la "X"
        
        menuSelect.classList.toggle('d-none');
        
        if (!menuSelect.classList.contains('d-none')) {
            inputBuscador.value = ''; // Limpiar búsqueda anterior
            renderizarLista(clientesData); // Mostrar todos
            inputBuscador.focus(); // Autoseleccionar buscador
        }
    });

    // C. Buscador Interno (Filtrar mientras escribes)
    inputBuscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase();
        const filtrados = clientesData.filter(c => 
            (c.nombres + ' ' + c.apellidos).toLowerCase().includes(texto) || 
            (c.dni && c.dni.includes(texto))
        );
        renderizarLista(filtrados);
    });

    // D. Función para pintar la lista (HTML)
    function renderizarLista(arrayClientes) {
        listaClientes.innerHTML = '';
        if (arrayClientes.length === 0) {
            listaClientes.innerHTML = '<li class="list-group-item text-danger text-center border-0"><small>No hay coincidencias</small></li>';
            return;
        }

        arrayClientes.slice(0, 50).forEach(cliente => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action cursor-pointer py-2 border-bottom';
            li.style.cursor = 'pointer';
            li.innerHTML = `
                <div class="fw-bold text-dark" style="font-size: 0.9rem;">${cliente.nombres} ${cliente.apellidos}</div>
                <div class="text-muted" style="font-size: 0.75rem;"><i class='bx bx-id-card'></i> DNI/RUC: ${cliente.dni_ruc || '---'}</div>
            `;
            
            // Acción al seleccionar un cliente
            li.addEventListener('click', function() {
                textoSelect.innerHTML = `<span class="fw-bold text-primary">${cliente.nombres} ${cliente.apellidos}</span>`;
                idOculto.value = cliente.id_cliente;
                
                menuSelect.classList.add('d-none'); // Cerrar menú
                btnLimpiarSelect.classList.remove('d-none'); // Mostrar "X"
            });
            listaClientes.appendChild(li);
        });
    }

    // E. Botón Limpiar (La "X")
    btnLimpiarSelect.addEventListener('click', limpiarSelect);

    function limpiarSelect() {
        textoSelect.innerHTML = `<span class="text-muted"><i class='bx bx-user me-1'></i> Seleccione un cliente...</span>`;
        idOculto.value = '';
        btnLimpiarSelect.classList.add('d-none');
    }

    // F. Cerrar si hacen clic afuera del select
    document.addEventListener('click', function(e) {
        if (!btnSelect.contains(e.target) && !menuSelect.contains(e.target)) {
            menuSelect.classList.add('d-none');
        }
    });
// ==========================================================
    // SELECCIÓN DE TIPO DE VEHÍCULO (Dropdown)
    // ==========================================================
    const itemsTipo = document.querySelectorAll('.item-tipo');
    const inputOcultoTipo = document.getElementById('tipo');
    const textoBotonTipo = document.getElementById('textoTipoVehiculo');

    itemsTipo.forEach(item => {
        item.addEventListener('click', function() {
            // 1. Obtenemos el valor oculto (Ej: "Auto")
            let valorSeleccionado = this.getAttribute('data-val');
            inputOcultoTipo.value = valorSeleccionado;

            // 2. Copiamos el HTML bonito (con el emoji) al botón principal
            textoBotonTipo.innerHTML = `<span >${this.innerHTML}</span>`;
            
            // 3. Removemos la clase 'active' de todos y se la ponemos al actual (para que quede marcado)
            itemsTipo.forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });
    // ==========================================================
    // 2. CONFIGURACIÓN DE TABLA DATATABLES
    // ==========================================================
    if ($('#tablaVehiculos').length) {
        
        var tabla = $('#tablaVehiculos').DataTable({
            "destroy": true,
            "scrollX": false,   
            "autoWidth": false, 
            "ajax": {
                "url": `${BASE_URL}/admin/vehiculo/getall`,
                "type": "GET"
            },
            
            "lengthChange": true,
            "lengthMenu": [[10, 25, 50], ["10 registros", "25 registros", "50 registros"]],
            "dom": '<"d-none"B><"px-4 pt-3"l>rt<"d-flex justify-content-between align-items-center px-4 pb-3"ip>', 
            
            // --- BOTÓN INVISIBLE DE EXCEL (DISEÑO PERSONALIZADO INFALIBLE) ---
            "buttons": [{
                extend: 'excelHtml5',
                title: '', 
                filename: 'Reporte_Vehiculos_' + new Date().toLocaleDateString().replace(/\//g, '-'),
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5, 6], 
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
                // 0. PLACA Y PROPIETARIO (Estilo "Placa Real" con fuente monoespaciada)
                { 
                    "data": "placa", 
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            let placaStr = data.toUpperCase();
                            let tipoStr = row.tipo ? row.tipo.toLowerCase() : '';

                            // --- 1. CONFIGURACIÓN POR DEFECTO (PARTICULAR) ---
                            let bgColor = '#FFFFFF'; // Fondo Blanco
                            let txtColor = '#000000'; // Letra Negra
                            let brdColor = '#000000'; // Borde Negro
                            let etiqueta = 'PARTICULAR';

                            // --- 2. IDENTIFICACIÓN POR TIPO DE SERVICIO ---
                            // Taxis y Carga pesada (Amarillo)
                            if (tipoStr.includes('taxi') || tipoStr.includes('carga') || tipoStr.includes('camion') || tipoStr.includes('furgon')) {
                                bgColor = '#FFD700'; // Amarillo Oficial
                                etiqueta = tipoStr.includes('taxi') ? 'TAXI' : 'CARGA';
                            } 
                            // Transporte Público / Combis (Verde)
                            else if (tipoStr.includes('bus') || tipoStr.includes('combi') || tipoStr.includes('custer') || tipoStr.includes('publico')) {
                                bgColor = '#28a745'; // Verde
                                txtColor = '#FFFFFF'; // Letra Blanca
                                brdColor = '#28a745';
                                etiqueta = 'URBANO';
                            }
                            // Transporte Interprovincial (Naranja)
                            else if (tipoStr.includes('interprovincial')) {
                                bgColor = '#fd7e14'; // Naranja
                                txtColor = '#FFFFFF';
                                brdColor = '#fd7e14';
                                etiqueta = 'INTERPROV.';
                            }
                            // Transporte Turístico (Morado)
                            else if (tipoStr.includes('turismo')) {
                                bgColor = '#6f42c1'; // Morado
                                txtColor = '#FFFFFF';
                                brdColor = '#6f42c1';
                                etiqueta = 'TURISMO';
                            }

                            // --- 3. IDENTIFICACIÓN POR LETRA (Excepciones Especiales) ---
                            // Estado: Empieza con E (Ej: EGD-123)
                            if (placaStr.startsWith('E') && isNaN(placaStr.charAt(1))) {
                                bgColor = '#FFFFFF';
                                txtColor = '#dc3545'; // Letra Roja
                                brdColor = '#dc3545';
                                etiqueta = 'ESTADO';
                            }
                            // Policial: Empieza con PL o EP
                            else if (placaStr.startsWith('PL') || placaStr.startsWith('EP')) {
                                bgColor = '#FFFFFF';
                                txtColor = '#198754'; // Letra Verde
                                brdColor = '#198754';
                                etiqueta = 'POLICÍA';
                            }

                            // --- 4. RENDERIZADO DEL DISEÑO DE LA PLACA ---
                            return `
                                <div class="d-flex flex-column align-items-start">
                                    <div style="background-color: ${bgColor}; color: ${txtColor}; border: 2px solid ${brdColor}; border-radius: 4px; text-align: center; min-width: 95px; box-shadow: 1px 1px 3px rgba(0,0,0,0.2); overflow: hidden;">
                                        <div style="font-size: 0.45rem; background-color: ${txtColor === '#FFFFFF' ? 'rgba(0,0,0,0.15)' : 'rgba(0,0,0,0.05)'}; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; padding: 1px 0;">
                                            PERÚ - ${etiqueta}
                                        </div>
                                        <div class="font-monospace fw-bold py-1" style="font-size: 1.05rem; letter-spacing: 1.5px;">
                                            ${placaStr}
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 text-truncate" style="max-width: 150px;" title="${row.propietario}">
                                        <i class='bx bx-user me-1'></i>${row.propietario}
                                    </small>
                                </div>`;
                        }
                        return data; // Modo exportación (Excel)
                    }
                },
                // 1. PROPIETARIO (Oculto en pantalla)
                { "data": "propietario", "visible": false },
                // 2. TIPO DE VEHÍCULO (Con Íconos y Colores Dinámicos)
                { 
                    "data": "tipo",
                    "render": function(data, type) {
                        if (type === 'display') {
                            let text = data.toLowerCase();
                            let icon = 'bx-car';
                            let color = 'primary';

                            if (text.includes('moto')) { icon = 'bx-cycling'; color = 'warning'; }
                            else if (text.includes('camioneta') || text.includes('suv') || text.includes('4x4')) { icon = 'bxs-truck'; color = 'info'; }
                            else if (text.includes('van') || text.includes('bus')) { icon = 'bx-bus'; color = 'danger'; }

                            return `<span class="badge bg-label-${color}"><i class='bx ${icon} me-1'></i> ${data}</span>`;
                        }
                        return data;
                    }
                },
                // 3. MARCA (En negrita para resaltar)
                { 
                    "data": "marca",
                    "render": function(data, type) {
                        return type === 'display' ? `<span class="fw-bold text-dark text-uppercase">${data}</span>` : data;
                    }
                },
                // 4. MODELO
                { 
                    "data": "modelo",
                    "render": function(data, type) {
                        return type === 'display' ? `<span class="text-capitalize">${data}</span>` : data;
                    }
                },
                // 5. COLOR (Con un círculo visual simulando pintura)
                { 
                    "data": "color",
                    "render": function(data, type) {
                        if (type === 'display') {
                            // Usamos el nombre del color en inglés para que CSS lo pinte, o gris por defecto si es raro.
                            let colorIngles = data.toLowerCase()
                                .replace('rojo','red').replace('azul','blue').replace('verde','green')
                                .replace('negro','black').replace('blanco','white').replace('gris','gray')
                                .replace('plomo','gray').replace('amarillo','yellow').replace('plateado','silver');
                            
                            return `
                            <div class="d-flex align-items-center">
                                <i class='bx bxs-circle me-2 fs-6' style='color: ${colorIngles}; text-shadow: 0 0 1px #000;'></i>
                                <span class="text-capitalize">${data}</span>
                            </div>`;
                        }
                        return data;
                    }
                },
                { 
                    "data": "observaciones",
                    "render": function(data, type) {
                        return type === 'display' ? `<span class="">${data}</span>` : data;
                    }
                },
                // 6. ACCIONES
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
        $('#tablaVehiculos tbody').on('click', '.btn-ver', function () {
            let vehiculo = JSON.parse(decodeURIComponent($(this).attr('data-json')));
            
            
            let htmlDetalle = `
                <ul class="list-group list-group-flush">
                    <li class="list-group-item" ><strong>Placa:</strong> ${vehiculo.placa}</li>
                    <li class="list-group-item"><strong>Propietario:</strong> ${vehiculo.propietario}</li>
                    <li class="list-group-item"><strong>Tipo:</strong> ${vehiculo.tipo}</li>
                    <li class="list-group-item"><strong>Marca:</strong> ${vehiculo.marca}</li>
                    <li class="list-group-item"><strong>Modelo:</strong> ${vehiculo.modelo || 'No registrado'}</li>
                    <li class="list-group-item"><strong>Color:</strong> ${vehiculo.color}</li>
                    <li class="list-group-item"><strong>Observaciones:</strong> ${vehiculo.observaciones}</li>
                </ul>`;
            
            $('#contenidoDetalle').html(htmlDetalle);
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        });

        $('#tablaVehiculos tbody').on('click', '.btn-editar', function () {
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

        $('#tablaVehiculos tbody').on('click', '.btn-eliminar', function () {
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