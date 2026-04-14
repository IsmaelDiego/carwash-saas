const CategoriaModule = {
    tabla: null,
    modalForm: null,

    init: function() {
        this.initDataTable();
        this.initEventosUI();
        
        const myModalEl = document.getElementById('modalFormCategoria');
        if (myModalEl) {
            this.modalForm = new bootstrap.Modal(myModalEl);
        }
    },

    initDataTable: function() {
        if (!$("#tbCategorias").length) return;

        this.tabla = $("#tbCategorias").DataTable({
            destroy: true,
            processing: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax: { url: `${BASE_URL}/admin/vehiculo/getallcategorias`, type: "GET" },
            dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            language: {
                lengthMenu: " _MENU_ ",
                info: "Mostrando _START_ a _END_ de _TOTAL_ categorías",
                infoEmpty: "0 categorías",
                infoFiltered: "(filtrado)",
                paginate: { next: "Siguiente", previous: "Anterior" },
                zeroRecords: `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay categorías registradas</h5></div>`
            },
            columns: [
                { data: "id_categoria", visible: false, defaultContent: "" },
                { data: "nombre", render: d => `<span class="fw-bold text-primary text-uppercase">${d}</span>` },
                { data: "factor_precio", className: "text-center", render: d => `<span class="badge bg-label-success">x${parseFloat(d).toFixed(2)}</span>` },
                { data: "factor_tiempo", className: "text-center", render: d => `<span class="badge bg-label-info">x${parseFloat(d).toFixed(2)}</span>` },
                {
                    data: null,
                    className: "text-center",
                    render: function(data, type, row) {
                        return `
                            <div class="d-inline-flex">
                                <button class="btn btn-sm btn-icon btn-label-warning me-2" onclick='CategoriaModule.abrirModalEdicion(${JSON.stringify(row)})' title="Editar"><i class="bx bx-edit"></i></button>
                                <button class="btn btn-sm btn-icon btn-label-danger" onclick="CategoriaModule.eliminarCategoria(${row.id_categoria})" title="Eliminar"><i class="bx bx-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ]
        });
    },

    initEventosUI: function() {
        $("#buscadorGlobal").on("keyup", (e) => {
            if (this.tabla) this.tabla.search(e.target.value).draw();
        });

        $("#formCategoria").on("submit", (e) => {
            e.preventDefault();
            this.guardarCategoria();
        });
    },

    abrirModalRegistro: function() {
        document.getElementById("formCategoria").reset();
        document.getElementById("cat_id").value = "";
        document.getElementById("modalTitleCategoria").textContent = "Nueva Categoría";
        this.modalForm.show();
    },

    abrirModalEdicion: function(cat) {
        document.getElementById("formCategoria").reset();
        document.getElementById("cat_id").value = cat.id_categoria;
        document.getElementById("cat_nombre").value = cat.nombre;
        document.getElementById("cat_factor_precio").value = cat.factor_precio;
        document.getElementById("cat_factor_tiempo").value = cat.factor_tiempo;
        document.getElementById("modalTitleCategoria").textContent = "Editar Categoría: " + cat.nombre;
        this.modalForm.show();
    },

    guardarCategoria: async function() {
        const id = document.getElementById("cat_id").value;
        const url = id ? `${BASE_URL}/admin/vehiculo/editarcategoria` : `${BASE_URL}/admin/vehiculo/registrarcategoria`;
        
        const payload = {
            id_categoria: id,
            nombre: document.getElementById("cat_nombre").value,
            factor_precio: document.getElementById("cat_factor_precio").value,
            factor_tiempo: document.getElementById("cat_factor_tiempo").value
        };

        const btn = document.getElementById("btnGuardarCategoria");
        const initText = btn.textContent;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Guardando...`;

        try {
            const res = await fetch(url, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                this.mostrarToast(data.message, 'success');
                this.modalForm.hide();
                this.tabla.ajax.reload(null, false);
            } else {
                this.mostrarToast(data.message, 'warning');
            }
        } catch (error) {
            this.mostrarToast('No se pudo conectar con el servidor.', 'danger');
        } finally {
            btn.disabled = false;
            btn.textContent = initText;
        }
    },

    eliminarCategoria: function(id) {
        // En vez de usar confirm() o Swal, disparamos el Modal de Bootstrap
        const modalEl = document.getElementById('modalEliminarCategoria');
        const modalInst = new bootstrap.Modal(modalEl);
        
        // Quitar eventos previos para no duplicar llamadas si se abre varias veces
        const btnEliminar = document.getElementById('btnConfirmarEliminacion');
        
        btnEliminar.onclick = async () => {
            const initText = btnEliminar.textContent;
            btnEliminar.disabled = true;
            btnEliminar.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...`;

            try {
                const res = await fetch(`${BASE_URL}/admin/vehiculo/eliminarcategoria`, {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({ id_categoria: id })
                });
                const data = await res.json();
                
                if (data.success) {
                    this.mostrarToast(data.message, 'success');
                    this.tabla.ajax.reload(null, false);
                    modalInst.hide();
                } else {
                    this.mostrarToast(data.message, 'warning');
                    modalInst.hide();
                }
            } catch (error) {
                this.mostrarToast('Fallo de conexión.', 'danger');
                modalInst.hide();
            } finally {
                btnEliminar.disabled = false;
                btnEliminar.textContent = initText;
            }
        };

        modalInst.show();
    },

    mostrarToast: function(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        if (!toastEl) return;
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        toastEl.style.zIndex = "11000";
        $('#toastMensaje').text(msg);
        new bootstrap.Toast(toastEl).show();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    CategoriaModule.init();
});
