const ClienteModule = {
  tabla: null,
  filtroOffcanvas: null, // Variable para guardar la instancia

  init: function () {
    this.initDataTable();
    this.initEventosUI();
    this.initFormularios();
    // Solo fixes para MODALES, no para Offcanvas (Bootstrap maneja bien Offcanvas si no interferimos)
    this.initModalFixes();
  },

  // 1. CONFIGURACIÓN TABLA
  initDataTable: function () {
    if (!$("#tablaClientes").length) return;

    this.tabla = $("#tablaClientes").DataTable({
      destroy: true,
      processing: true,
      responsive: true,
      autoWidth: false,
      ordering: true,
      ajax: { url: `${BASE_URL}/admin/cliente/getall`, type: "GET" },
      // DOM: Paginación izquierda extrema, Info derecha
      dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
      language: {
        lengthMenu: " _MENU_ ",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "0 registros",
        infoFiltered: "(filtrado)",
        paginate: { next: "Siguiente", previous: "Anterior" },
        zeroRecords: `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="130" class="mb-3 opacity-75"><h5 class="fw-bold text-primary">No encontramos clientes</h5></div>`,
      },
      columns: [
        { data: "nombres", visible: false, defaultContent: "" },
        { data: "apellidos", visible: false, defaultContent: "" },
        { data: "dni", visible: false, defaultContent: "" },
        { data: "telefono", visible: false, defaultContent: "" },
        { data: "observaciones", visible: false, defaultContent: "" },
        { data: "fecha_registro", visible: false, defaultContent: "" },
        // VISIBLES
        {
          data: null,
          render: function (data, type, row) {
            let n = row.nombres || "";
            let a = row.apellidos || "";
            let d = row.dni || "S/DNI";
            return `<div class="d-flex flex-column"><span class="fw-bold text-primary text-uppercase">${n} ${a}</span><small class="text-muted"><i class="bx bx-id-card"></i> ${d}</small></div>`;
          },
        },
        {
          data: "sexo",
          className: "text-center",
          defaultContent: "-",
          render: function (data) {
            if (data === "M")
              return '<span class="badge bg-label-info p-2" title="Masculino"><i class="bx bx-male fs-5"></i></span>';
            if (data === "F")
              return '<span class="badge bg-label-danger p-2" title="Femenino"><i class="bx bx-female fs-5"></i></span>';
            return '<span class="badge bg-label-secondary">-</span>';
          },
        },
        {
          data: "estado_whatsapp",
          className: "text-center",
          defaultContent: 0,
          render: function (data, type, row) {
            let checked = data == 1 ? "checked" : "";
            return `<div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input switch-whatsapp" type="checkbox" data-id="${row.id_cliente}" ${checked} style="cursor: pointer; transform: scale(1.2);"></div>`;
          },
        },
        {
          data: "puntos_acumulados",
          className: "text-center",
          defaultContent: 0,
          render: function (data, type, row) {
            return `<span class="badge bg-warning">${data}</span>`;
          },
        },
        {
          data: "fecha_registro",
          className: "text-center",
          defaultContent: "-",
          render: function (data) {
            return data ? new Date(data).toLocaleDateString() : "-";
          },
        },
        {
          data: null,
          orderable: false,
          className: "text-center",
          render: function () {
            return `<div class="dropdown"><button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button><div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item btn-ver" href="javascript:void(0);"><i class="bx bx-show text-info me-2"></i> Ver Detalle</a><a class="dropdown-item btn-editar" href="javascript:void(0);"><i class="bx bx-edit text-warning me-2"></i> Editar</a><div class="dropdown-divider"></div><a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Eliminar</a></div></div>`;
          },
        },
      ],
      "buttons": [{
                extend: 'excelHtml5',
                className: 'd-none', // Oculto (se activa con tu botón personalizado)
                filename: 'Reporte_Clientes',
                title: '', // <--- ESTO ELIMINA EL TÍTULO GRANDE (El "Encabezado" del reporte)
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6], // Tus columnas ocultas
                    orthogonal: 'export'
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];

                    // 1. Definimos el FONDO CELESTE (DeepSkyBlue: #00BFFF)
                    // Nota: Excel usa formato ARGB, por eso agregamos 'FF' al inicio -> FF00BFFF
                    var fillIndex = $('fills fill', styles).length;
                    $('fills', styles).append(
                        '<fill><patternFill patternType="solid"><fgColor rgb="FF00BFFF" /><bgColor indexed="64" /></patternFill></fill>'
                    );

                    // 2. Definimos la LETRA BLANCA y Negrita
                    var fontIndex = $('fonts font', styles).length;
                    $('fonts', styles).append(
                        '<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>'
                    );

                    // 3. Creamos el ESTILO PERSONALIZADO (Uniendo Fondo + Letra)
                    var styleIndex = $('cellXfs xf', styles).length;
                    $('cellXfs', styles).append(
                        '<xf numFmtId="0" fontId="' + fontIndex + '" fillId="' + fillIndex + '" applyFont="1" applyFill="1" />'
                    );

                    // 4. Aplicamos el estilo a la PRIMERA FILA (Cabecera de columnas)
                    $('row:first c', sheet).attr('s', styleIndex);
                }
            }]
    });
  },

  // 2. EVENTOS UI
  // ==========================================================
  // 2. EVENTOS UI (CORREGIDO PARA EVITAR DOBLE CAPA)
  // ==========================================================
  initEventosUI: function () {
    const self = this;

    // A. CONTROL MANUAL DEL FILTRO (OFFCANVAS)
    // 1. Inicializamos la instancia UNA sola vez
    var offcanvasEl = document.getElementById("offcanvasDark");
    var filtroOffcanvas = new bootstrap.Offcanvas(offcanvasEl, {
      backdrop: true,
      scroll: true,
    });

    // 2. Botón Abrir (Ahora controlado 100% por JS, sin conflictos HTML)
    $("#btnAbrirFiltro").on("click", function (e) {
      e.preventDefault();
      filtroOffcanvas.show();
    });

    // 3. Botón Aplicar (Cierra el filtro)
    $("#btnAplicarFiltros").on("click", function () {
      let valor = $("#buscadorGlobal").val();
      self.tabla.search(valor).draw();
      filtroOffcanvas.hide();
    });

    // 4. Botón Limpiar (No cierra, solo limpia)
    $("#btnLimpiarFiltros").on("click", function () {
      $("#buscadorGlobal").val("");
      $("#filtroFechaInicio, #filtroFechaFin").val("");
      self.tabla.search("").draw();
    });

    // 5. Búsqueda Instantánea
    $("#buscadorGlobal").on("keyup", function (e) {
      self.tabla.search(this.value).draw();
    });

    // B. FILTRO FECHAS (Plugin DataTables)
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      let min = $("#filtroFechaInicio").val();
      let max = $("#filtroFechaFin").val();
      let fechaStr = data[6]; // Fecha (Columna oculta 6)
      if (!fechaStr) return true;

      let fecha = new Date(fechaStr);
      if (min && new Date(min) > fecha) return false;
      if (max && new Date(max) < fecha) return false;
      return true;
    });

    // Redibujar al cambiar fechas
    $("#filtroFechaInicio, #filtroFechaFin").on("change", () =>
      self.tabla.draw(),
    );

    // C. SWITCH WHATSAPP
    $("#tablaClientes tbody").on(
      "change",
      ".switch-whatsapp",
      async function () {
        const el = $(this);
        el.prop("disabled", true);
        try {
          const res = await fetch(
            `${BASE_URL}/admin/cliente/cambiarestadowhatsapp`,
            {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                id_cliente: el.data("id"),
                estado: el.is(":checked") ? 1 : 0,
              }),
            },
          );
          const data = await res.json();

          if (data.success) {
            self.mostrarToast("Estado actualizado", "success");
            // Recarga silenciosa
            self.tabla.ajax.reload(null, false);
          } else {
            el.prop("checked", !el.is(":checked"));
            self.mostrarToast("No se pudo actualizar.", "danger");
          }
        } catch (e) {
          el.prop("checked", !el.is(":checked"));
          self.mostrarToast("Error de conexión.", "danger");
        } finally {
          el.prop("disabled", false);
        }
      },
    );

    // D. BOTÓN EXPORTAR
    $("#btnExportar").on("click", () =>
      self.tabla.button(".buttons-excel").trigger(),
    );

    // Helper para obtener datos
    function getData(el) {
      let tr = $(el).closest("tr");
      if (tr.hasClass("child")) tr = tr.prev();
      return self.tabla.row(tr).data();
    }

    // E. VER DETALLE
    $("#tablaClientes tbody").on("click", ".btn-ver", function () {
      let data = getData(this);
      let estadoBadge =
        data.estado_whatsapp == 1
          ? '<span class="badge bg-success">ACTIVO</span>'
          : '<span class="badge bg-secondary">INACTIVO</span>';

      let nom = data.nombres || "";
      let ape = data.apellidos || "";
      let iniciales = (nom.charAt(0) + (ape.charAt(0) || "")).toUpperCase();
      let avatarClass =
        data.sexo === "F"
          ? "bg-label-danger text-danger"
          : "bg-label-info text-info";

      let html = `
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <div class="avatar avatar-xl mx-auto mb-2">
                            <span class="avatar-initial rounded-circle ${avatarClass} fs-2 fw-bold">${iniciales}</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">${nom} ${ape}</h4>
                        <p class="text-muted mb-0">${data.dni}</p>
                    </div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">Teléfono</small><div class="detalle-value">${data.telefono || "-"}</div></div></div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">Puntos</small><div class="detalle-value text-warning fw-bold">${data.puntos_acumulados || 0}</div></div></div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">WhatsApp</small><div class="mt-1">${estadoBadge}</div></div></div>
                    <div class="col-6"><div class="detalle-card p-3"><small class="detalle-label">Registro</small><div class="small">${data.fecha_registro ? new Date(data.fecha_registro).toLocaleDateString() : "-"}</div></div></div>
                    <div class="col-12"><label class="text-muted small fw-bold">OBSERVACIONES</label><div class="p-2 bg-light border rounded">${data.observaciones || "Sin observaciones"}</div></div>
                </div>`;

      $("#contenidoDetalle").html(html);
      new bootstrap.Modal(document.getElementById("modalDetalle")).show();
    });

    // F. EDITAR
    $("#tablaClientes tbody").on("click", ".btn-editar", function () {
      let data = getData(this);
      $("#edit_id_cliente").val(data.id_cliente);
      $("#edit_dni").val(data.dni);
      $("#edit_nombres").val(data.nombres);
      $("#edit_apellidos").val(data.apellidos);
      $("#edit_tel1").val(data.telefono);
      $("#edit_sexo").val(data.sexo);
      $("#edit_observaciones").val(data.observaciones);
      $("#edit_whatsapp").prop("checked", data.estado_whatsapp == 1);

      new bootstrap.Modal(document.getElementById("modalEditar")).show();
    });

    // G. ELIMINAR
    $("#tablaClientes tbody").on("click", ".btn-eliminar", function () {
      let data = getData(this);
      $("#delete_id_cliente").val(data.id_cliente);
      $("#nombre_eliminar").text(`${data.nombres} ${data.apellidos}`);
      new bootstrap.Modal(document.getElementById("modalEliminar")).show();
    });

    // H. RENIEC
    $("#btnBuscarDni").on("click", () => this.consultarReniec());
    $("#dni").on("keypress", (e) => {
      if (e.which === 13) {
        e.preventDefault();
        this.consultarReniec();
      }
    });
  },

  // 3. FORMULARIOS
  initFormularios: function () {
    const self = this;
    const handleForm = async (formId, url, btnText, modalId, reset = false) => {
      $(`#${formId}`).on("submit", async function (e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        btn.prop("disabled", true).text("Procesando...");
        try {
          let formData = new FormData(this);
          if (!formData.has("estado_whatsapp"))
            formData.append("estado_whatsapp", 0);
          let res = await fetch(`${BASE_URL}${url}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(Object.fromEntries(formData)),
          });
          let data = await res.json();
          if (data.success) {
            bootstrap.Modal.getInstance(
              document.getElementById(modalId),
            ).hide();
            // Forzar limpieza de fondo modal solamente
            $(".modal-backdrop").remove();
            $("body")
              .removeClass("modal-open")
              .css("overflow", "")
              .css("padding-right", "");

            self.tabla.ajax.reload(null, false);
            if (reset) this.reset();
            self.mostrarToast(data.message, "success");
          } else {
            self.mostrarToast(data.message, "danger");
          }
        } catch (err) {
          self.mostrarToast("Error", "danger");
        } finally {
          btn.prop("disabled", false).text(btnText);
        }
      });
    };
    handleForm(
      "registrarcliente",
      "/admin/cliente/registrarcliente",
      "GUARDAR",
      "modalRegistrar",
      true,
    );
    handleForm(
      "formEditarCliente",
      "/admin/cliente/editarcliente",
      "ACTUALIZAR",
      "modalEditar",
    );
    handleForm(
      "formEliminarCliente",
      "/admin/cliente/eliminarcliente",
      "SÍ, ELIMINAR",
      "modalEliminar",
    );
  },

  // 4. FIXES MODALES SOLAMENTE (No tocar Offcanvas aquí)
  initModalFixes: function () {
    $(".modal").on("hidden.bs.modal", function () {
      $(".modal-backdrop").remove();
      $("body")
        .removeClass("modal-open")
        .css("overflow", "auto")
        .css("padding-right", "");
    });
  },

  mostrarToast: function (msg, tipo) {
    let toastEl = document.getElementById("toastSistema");
    toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    toastEl.style.zIndex = "11000";
    $("#toastMensaje").text(msg);
    new bootstrap.Toast(toastEl).show();
  },

  consultarReniec: async function () {
    let dni = $("#dni").val().trim();
    let feedback = $("#dniFeedback");
    if (dni.length !== 8) {
      feedback.html('<span class="text-danger">8 dígitos.</span>');
      return;
    }
    $("#btnBuscarDni").prop("disabled", true);
    try {
      let res = await fetch(`${BASE_URL}/api/dni`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ dni: dni }),
      });
      let data = await res.json();
      if (data.success) {
        $("#nombres").val(data.data.nombres);
        $("#apellidos").val(
          `${data.data.apellido_paterno} ${data.data.apellido_materno}`,
        );
        $("#nombres, #apellidos").prop("readonly", true);
        feedback.html('<span class="text-success">Encontrado</span>');
      } else {
        feedback.html('<span class="text-danger">No encontrado</span>');
        $("#nombres, #apellidos").val("").prop("readonly", false);
      }
    } catch (e) {
      feedback.html('<span class="text-danger">Error</span>');
    } finally {
      $("#btnBuscarDni").prop("disabled", false);
    }
  },
};

document.addEventListener("DOMContentLoaded", () => {
  ClienteModule.init();
});
