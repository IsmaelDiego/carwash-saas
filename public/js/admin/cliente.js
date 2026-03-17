const ClienteModule = {
  tabla: null,
  filtroOffcanvas: null, // Variable para guardar la instancia

  init: function () {
    this.initDataTable();
    this.initEventosUI();
    this.initFormularios();
    // Solo fixes para MODALES, no para Offcanvas (Bootstrap maneja bien Offcanvas si no interferimos)
    this.initModalFixes();
    // Stats: computar después de cada carga AJAX de DataTables
    const self = this;
    $('#tablaClientes').on('xhr.dt', function() {
        setTimeout(() => self.computeStats(), 100);
    });
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
        zeroRecords: `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay historial de clientes</h5></div>`, 
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
            if (data === "M" || data === "Masculino")
              return '<span class="badge bg-label-info p-2" title="Masculino"><i class="bx bx-male fs-5"></i></span>';
            if (data === "F" || data === "Femenino")
              return '<span class="badge bg-label-danger p-2" title="Femenino"><i class="bx bx-female fs-5"></i></span>';
            return '<span class="badge bg-label-secondary" title="Sin especificar">-</span>';
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

  computeStats: function() {
      if (!this.tabla) return;
      const data = this.tabla.rows().data().toArray();
      const total = data.length;
      const whatsapp = data.filter(r => r.estado_whatsapp == 1).length;
      const puntos = data.reduce((acc, r) => acc + (parseInt(r.puntos_acumulados) || 0), 0);
      const now = new Date();
      const mes = data.filter(r => {
          if (!r.fecha_registro) return false;
          const f = new Date(r.fecha_registro);
          return f.getMonth() === now.getMonth() && f.getFullYear() === now.getFullYear();
      }).length;

      const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
      el('stat_cli_total', total);
      el('stat_cli_whatsapp', whatsapp);
      el('stat_cli_puntos', puntos);
      el('stat_cli_mes', mes);
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
      let fechaStr = data[5]; // Fecha (Columna oculta 5: fecha_registro)
      if (!fechaStr) return true;

      // Normalizar fechas para comparación (solo YYYY-MM-DD)
      let fv = new Date(fechaStr).getTime();
      if (min && new Date(min).getTime() > fv) return false;
      if (max && new Date(max).getTime() < fv) return false;
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
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3" style="width: 80px; height: 80px;">
                        <span class="avatar-initial rounded-circle ${avatarClass} fs-1 fw-bold placeholder-glow shadow-sm">${iniciales}</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark text-uppercase">${nom} ${ape}</h4>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        <span class="text-muted"><i class="bx bx-id-card me-1"></i>${data.dni}</span>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-phone text-primary"></i> Teléfono</small>
                            <span class="fw-bold text-dark fs-6">${data.telefono || "-"}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bxs-star text-warning"></i> Puntos</small>
                            <span class="fw-bold text-warning fs-5">${data.puntos_acumulados || 0}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light text-center">
                            <small class="text-muted fw-bold mb-1"><i class="bx bxl-whatsapp text-success"></i> WhatsApp</small>
                            <div class="mt-1">${estadoBadge}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-time text-secondary"></i> Registro</small>
                            <span class="fw-semibold text-dark fs-6 text-truncate">${data.fecha_registro ? new Date(data.fecha_registro.replace(/-/g, "/")).toLocaleDateString() : "-"}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 d-flex flex-column h-100 bg-white shadow-sm border-light">
                            <small class="text-muted fw-bold mb-1"><i class="bx bx-notepad text-warning"></i> Observaciones</small>
                            <span class="text-muted small">${data.observaciones || '<i>No hay observaciones adicionales para este cliente.</i>'}</span>
                        </div>
                    </div>
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
      
      // Normalizar sexo para el Select
      let sexoNorm = data.sexo;
      if (sexoNorm === "Masculino") sexoNorm = "M";
      if (sexoNorm === "Femenino") sexoNorm = "F";
      $("#edit_sexo").val(sexoNorm || "-");

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

  // 4. FIXES MODALES SOLAMENTE
  initModalFixes: function () {
    const self = this;
    
    // Al cerrar cualquier modal, limpiar backdrops huérfanos
    $(".modal").on("hidden.bs.modal", function () {
      $(".modal-backdrop").remove();
      $("body").removeClass("modal-open").css("overflow", "auto").css("padding-right", "");
    });

    // Resetear formulario de registro al cerrar
    $("#modalRegistrar").on("hidden.bs.modal", function () {
      const form = document.getElementById("registrarcliente");
      if (form) form.reset();
      $("#dniFeedback").html('Introduce el documento para autocompletar nombre.');
      $("#nombres, #apellidos").prop("readonly", false).val("");
      $("#btnBuscarDni").prop("disabled", false).html('<i class="bx bx-search fs-5"></i>');
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
    if (dni.length !== 8 && dni.length !== 11) {
      feedback.html('<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> Debe tener 8 (DNI) u 11 (RUC) dígitos.</span>');
      return;
    }

    $("#btnBuscarDni").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop("disabled", true);
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
        
        // Es DNI
        if (dni.length === 8 && obj.data) {
          n = obj.data.nombres || "";
          a = `${obj.data.apellido_paterno || ""} ${obj.data.apellido_materno || ""}`.trim();
        } 
        // Es RUC
        else if (dni.length === 11 && obj.data) {
          n = obj.data.nombre_o_razon_social || "";
          a = "RUC"; 
        }

        $("#nombres").val(n);
        $("#apellidos").val(a);
        
        // Bloquear temporalmente - se ve mejor que los datos no se alteren
        $("#nombres, #apellidos").prop("readonly", true);
        feedback.html('<span class="text-success fw-bold"><i class="bx bx-check-circle"></i> ¡Datos encontrados!</span>');
      } else {
        feedback.html(`<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> ${obj.message || "No encontrado"}</span>`);
        $("#nombres, #apellidos").val("").prop("readonly", false);
      }
    } catch (e) {
      feedback.html('<span class="text-danger fw-bold"><i class="bx bx-wifi-off"></i> Error de conexión con el servidor.</span>');
      $("#nombres, #apellidos").val("").prop("readonly", false);
    } finally {
      $("#btnBuscarDni").html('<i class="bx bx-search fs-5"></i>').prop("disabled", false);
    }
  },
};

document.addEventListener("DOMContentLoaded", () => {
  ClienteModule.init();
});
