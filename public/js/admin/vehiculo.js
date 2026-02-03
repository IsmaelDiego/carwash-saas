const VehiculoApp = {
    // CONFIGURACIÓN
    config: {
        urlBase: `${BASE_URL}/admin/vehiculo`,
        urlClientes: `${BASE_URL}/admin/cliente`,
        tablaID: '#tablaVehiculos',
        clientesCache: [],
        tablaInstancia: null
    },

    // INICIALIZACIÓN
    init() {
        this.initTable();
        this.cargarTiposVehiculo();
        this.initClientSearch();
        this.initListeners();
    },

    /**************************
     * 1. CARGAR SELECTS
     **************************/
    cargarTiposVehiculo: async function() {
        try {
            const res = await fetch(`${this.config.urlBase}/gettipos`);
            const data = await res.json();
            let options = '<option value="">Seleccione...</option>';
            if (data.data) {
                data.data.forEach(tipo => {
                    options += `<option value="${tipo.id_tipo_vehiculo}">${tipo.nombre}</option>`;
                });
            }
            document.querySelectorAll('#tipo_vehiculo_id, #edit_tipo_vehiculo_id')
                    .forEach(sel => sel.innerHTML = options);
        } catch (e) {
            console.error("Error cargando tipos:", e);
        }
    },

    /**************************
     * 2. BUSCADOR DE CLIENTES
     **************************/
    initClientSearch() {
        const els = {
            btn: document.getElementById('btnAbrirSelect'),
            menu: document.getElementById('menuSelect'),
            input: document.getElementById('inputBuscador'),
            lista: document.getElementById('listaClientes'),
            txt: document.getElementById('textoSelect'),
            x: document.getElementById('btnLimpiarSelect'),
            id: document.getElementById('id_cliente')
        };
        if (!els.btn) return;

        // Abrir / Cerrar menú
        els.btn.addEventListener('click', e => {
            if (e.target.id === 'btnLimpiarSelect') return;
            els.menu.classList.toggle('d-none');
            if (!els.menu.classList.contains('d-none')) {
                els.input.value = '';
                els.input.focus();
                this.renderClientes(this.config.clientesCache, els);
            }
        });

        // Filtrar clientes
        els.input.addEventListener('input', e => {
            const val = e.target.value.toLowerCase();
            const filtered = this.config.clientesCache.filter(c =>
                `${c.nombres} ${c.apellidos}`.toLowerCase().includes(val) ||
                (c.dni || '').includes(val)
            );
            this.renderClientes(filtered, els);
        });

        // Limpiar selección
        els.x.addEventListener('click', () => {
            els.txt.innerText = "Seleccione un cliente...";
            els.id.value = '';
            els.x.classList.add('d-none');
        });

        // Cargar clientes al abrir modal
        $('#modalRegistrar').on('show.bs.modal', async () => {
            els.x.click();
            try {
                const res = await fetch(`${this.config.urlClientes}/getall`);
                const data = await res.json();
                this.config.clientesCache = data.data || [];
            } catch (err) {
                console.error("Error cargando clientes:", err);
            }
        });

        // Cerrar menú si clic fuera
        document.addEventListener('click', e => {
            if (!els.btn.contains(e.target) && !els.menu.contains(e.target)) {
                els.menu.classList.add('d-none');
            }
        });
    },

    renderClientes(list, els) {
        els.lista.innerHTML = '';
        if (!list.length) {
            els.lista.innerHTML = '<li class="list-group-item text-center">No encontrado</li>';
            return;
        }
        list.slice(0, 50).forEach(c => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action cursor-pointer';
            li.innerHTML = `
                <div class="fw-bold small">${c.nombres} ${c.apellidos}</div>
                <div class="small text-muted">${c.dni || 'S/D'}</div>`;
            li.addEventListener('click', () => {
                els.txt.innerText = `${c.nombres} ${c.apellidos}`;
                els.id.value = c.id_cliente;
                els.menu.classList.add('d-none');
                els.x.classList.remove('d-none');
            });
            els.lista.appendChild(li);
        });
    },

    /**************************
     * 3. DATATABLE
     **************************/
    initTable() {
        if (!$(this.config.tablaID).length) return;

        this.config.tablaInstancia = $(this.config.tablaID).DataTable({
            destroy: true,
            responsive: true,
            ajax: {
                url: `${this.config.urlBase}/getall`,
                type: 'GET',
                dataSrc: json => json.data || []
            },
            columns: [
                { data: "placa", className: "fw-bold font-monospace" },
                { data: "propietario", visible: false },
                { data: "nombre_tipo", render: d => `<span class="badge bg-label-info">${d || '-'}</span>` },
                { data: "marca" },
                { data: "modelo" },
                { data: "color", render: d => d ? `<i class='bx bxs-circle' style='color:${d.toLowerCase()}'></i> ${d}` : '-' },
                { data: "estado", render: d => d == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' },
                { data: null, render: (d,t,r) => {
                    const json = encodeURIComponent(JSON.stringify(r));
                    return `
                        <div class="dropdown">
                            <button class="btn p-0" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item btn-editar" href="#" data-json="${json}">Editar</a>
                                <a class="dropdown-item btn-eliminar text-danger" href="#" data-json="${json}">Eliminar</a>
                            </div>
                        </div>`;
                }}
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            dom: '<"d-none"B><"px-4 pt-3"l>rt<"d-flex justify-content-between align-items-center px-4 pb-3"ip>',
            buttons: ['excelHtml5']
        });
    },

    /**************************
     * 4. LISTENERS (formularios y botones)
     **************************/
    initListeners() {
        // Formularios
        const handleForm = (id, endpoint, modalId) => {
            const form = document.getElementById(id);
            if (!form) return;
            form.addEventListener('submit', async e => {
                e.preventDefault();
                if (id === 'registrarvehiculo' && !document.getElementById('id_cliente').value) {
                    return alert("Seleccione cliente");
                }
                try {
                    const res = await fetch(`${this.config.urlBase}/${endpoint}`, {
                        method: 'POST',
                        body: JSON.stringify(Object.fromEntries(new FormData(form))),
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.config.tablaInstancia.ajax.reload(null, false);
                        bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                        form.reset();
                        this.mostrarToast(data.message, 'success');
                    } else {
                        this.mostrarToast(data.message, 'danger');
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        };

        handleForm('registrarvehiculo', 'registrarvehiculo', 'modalRegistrar');
        handleForm('formEditarVehiculo', 'editarvehiculo', 'modalEditar');
        handleForm('formEliminarVehiculo', 'eliminarvehiculo', 'modalEliminar');

        // Delegación de eventos Editar / Eliminar
        document.body.addEventListener('click', e => {
            if (e.target.closest('.btn-editar')) {
                const data = JSON.parse(decodeURIComponent(e.target.closest('.btn-editar').dataset.json));
                $('#edit_id_vehiculo').val(data.id_vehiculo);
                $('#edit_placa').val(data.placa);
                $('#edit_tipo_vehiculo_id').val(data.tipo_vehiculo_id);
                $('#edit_marca').val(data.marca);
                $('#edit_modelo').val(data.modelo);
                $('#edit_color').val(data.color);
                $('#edit_observaciones').val(data.observaciones);
                $('#edit_propietario').val(data.propietario);
                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            }

            if (e.target.closest('.btn-eliminar')) {
                const data = JSON.parse(decodeURIComponent(e.target.closest('.btn-eliminar').dataset.json));
                $('#delete_id_vehiculo').val(data.id_vehiculo);
                $('#placa_eliminar').text(data.placa);
                new bootstrap.Modal(document.getElementById('modalEliminar')).show();
            }
        });

        // Exportar Excel
        document.getElementById('btnExportar')?.addEventListener('click', () => {
            this.config.tablaInstancia.button('.buttons-excel').trigger();
        });
    },

    /**************************
     * 5. TOASTS
     **************************/
    mostrarToast(msg, type) {
        const el = document.getElementById('toastSistema');
        if (el) {
            document.getElementById('toastMensaje').innerText = msg;
            el.className = `bs-toast toast fade show bg-${type}`;
            setTimeout(() => el.classList.remove('show'), 3000);
        }
    }
};

// INICIALIZAR APP
document.addEventListener("DOMContentLoaded", () => VehiculoApp.init());
