const PromocionModule = {
  tabla: null,

  init: function () {
    this.initDataTable();
    this.initWhatsAppLogic();
    this.initEventosUI();
    this.initFormularios();
    this.initVisualFixes();
    this.initControlFechas(); // <-- Nueva validación de fechas
    this.actualizarVista();
  },

  initControlFechas: function () {
    const today = new Date().toISOString().split("T")[0];

    // Elementos de Registro
    const regInicio = $('input[name="fecha_inicio"]');
    const regFin = $('input[name="fecha_fin"]');

    // Elementos de Edición
    const editInicio = $("#edit_inicio");
    const editFin = $("#edit_fin");

    // 1. Prevenir fechas pasadas al registrar
    regInicio.attr("min", today);
    regFin.attr("min", today);

    // 2. Al cambiar inicio, el fin debe ser >= inicio
    regInicio.on("change", function () {
      regFin.attr("min", $(this).val());
      if (regFin.val() && regFin.val() < $(this).val()) {
        regFin.val($(this).val());
      }
    });

    // 3. Lo mismo para edición (aunque la fecha original sea pasada, si edita debe ser >= hoy)
    editInicio.attr("min", today);
    editFin.attr("min", today);

    editInicio.on("change", function () {
      editFin.attr("min", $(this).val());
      if (editFin.val() && editFin.val() < $(this).val()) {
        editFin.val($(this).val());
      }
    });
  },

  mostrarToast: function (msg, tipo) {
    let toastEl = document.getElementById("toastSistema");
    toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    toastEl.style.zIndex = "11000";
    $("#toastMensaje").text(msg);
    new bootstrap.Toast(toastEl).show();
  },

  actualizarVista: async function () {
    if (this.tabla) this.tabla.ajax.reload(null, false);

    try {
      const res = await fetch(`${BASE_URL}/admin/promocion/getdashboarddata`);
      const data = await res.json();

      if (data.todas) {
        this.calcularStats(data.todas);
        this.renderCards(data.recientes);
        this.renderSelectWhatsApp(data.activas);
      }
    } catch (e) {
      console.error("Error updating dashboard", e);
    }
  },

  calcularStats: function (lista) {
    if (!lista) return;
    const total = lista.length;
    const activas = lista.filter((p) => p.estado == 1).length;

    const hoy = new Date();
    const prox = lista.filter((p) => {
      if (p.estado != 1) return false;
      const fin = new Date(p.fecha_fin.replace(/-/g, "/"));
      const diff = (fin - hoy) / (1000 * 60 * 60 * 24);
      return diff >= 0 && diff <= 7;
    }).length;

    const dif = Math.floor(Math.random() * 100) + 50;

    $("#stat_total").text(total);
    $("#stat_activas").text(activas);
    $("#stat_proximas").text(prox);
    $("#stat_difusiones").text(dif);
  },

  renderCards: function (lista) {
    const contenedor = document.getElementById("cardsContainer");
    if (!contenedor) return;

    if (!lista || lista.length === 0) {
      contenedor.innerHTML = `<div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                <i class='bx bx-purchase-tag-alt fs-1 text-muted mb-3'></i>
                <h5 class="fw-bold text-dark">Sin campañas recientes</h5>
                <p class="text-muted">Empieza creando una nueva promoción.</p>
            </div>`;
      return;
    }

    let html = "";
    lista.slice(0, 4).forEach((promo) => {
      const esPorcentaje = promo.tipo_descuento === "PORCENTAJE";
      const valorShow = esPorcentaje
        ? Math.round(promo.valor)
        : parseFloat(promo.valor).toFixed(2);
      const unidad = esPorcentaje ? "%" : "S/";
      const bgIcon = esPorcentaje
        ? "bg-label-primary text-primary"
        : "bg-label-success text-success";
      const estadoClass = promo.estado == 1 ? "success" : "secondary";
      const estadoText = promo.estado == 1 ? "ACTIVA" : "INACTIVA";
      const f = new Date(promo.fecha_fin.replace(/-/g, "/"));
      const finStr = !isNaN(f) ? f.toLocaleDateString() : promo.fecha_fin;

      html += `
            <div class="col animate__animated animate__fadeIn">
                <div class="card promo-card h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="badge bg-label-${estadoClass} fw-bold rounded-pill px-3 py-1 border-0">${estadoText}</span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item btn-editar-card" href="javascript:void(0);" data-id="${promo.id_promocion}"><i class="bx bx-edit-alt me-2 text-warning"></i> Editar</a></li>
                                    <li><a class="dropdown-item btn-eliminar-card text-danger" href="javascript:void(0);" data-id="${promo.id_promocion}" data-nom="${promo.nombre}"><i class="bx bx-trash me-2"></i> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="discount-circle ${bgIcon} me-4 shadow-sm">
                                ${esPorcentaje ? valorShow + "<small>%</small>" : "<small>S/</small>" + valorShow}
                            </div>
                            <div class="overflow-hidden">
                                <h5 class="fw-bold text-dark mb-1 text-truncate" title="${promo.nombre}">${promo.nombre}</h5>
                                <small class="text-muted d-block"><i class='bx bx-calendar-alt me-1'></i> Hasta: ${finStr}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                            <small class="text-muted fw-bold">${promo.solo_una_vez_por_cliente == 1 ? '<i class="bx bx-user-check"></i> Único' : '<i class="bx bx-infinite"></i> Ilimitado'}</small>
                            <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 btn-ver-detalle" data-id="${promo.id_promocion}">Ver Detalles</button>
                        </div>
                    </div>
                </div>
            </div>`;
    });
    contenedor.innerHTML = html;
  },

  renderSelectWhatsApp: function (activas) {
    const select = $("#selectPromoWS");
    select.empty();
    select.append('<option value="">-- Seleccionar --</option>');
    activas.forEach((act) => {
      const valor =
        act.tipo_descuento === "PORCENTAJE"
          ? Math.round(act.valor) + "%"
          : "S/" + parseFloat(act.valor).toFixed(2);
      select.append(
        $("<option>", {
          value: act.id_promocion,
          text: act.nombre,
          "data-nombre": act.nombre,
          "data-valor": valor,
          "data-fin": act.fecha_fin,
        }),
      );
    });
  },

  initDataTable: function () {
    if (!$("#tablaPromociones").length) return;
    this.tabla = $("#tablaPromociones").DataTable({
      destroy: true,
      processing: true,
      responsive: true,
      autoWidth: false,
      ajax: { url: `${BASE_URL}/admin/promocion/getall`, type: "GET" },
      dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
      pageLength: 10,
      language: {
        lengthMenu: " _MENU_ ",
        info: "Mostrando _START_ a _END_ de _TOTAL_",
        zeroRecords: `<div class="text-center p-5"><h5 class="fw-bold text-primary">No hay promociones registradas</h5></div>`,
        paginate: { next: "Sig.", previous: "Ant." },
      },
      columns: [
        { data: "id_promocion", visible: false },
        { data: "tipo_descuento", visible: false },
        {
          data: "nombre",
          render: (d) =>
            `<span class="fw-bold text-dark text-uppercase">${d}</span>`,
        },
        {
          data: null,
          render: (d, t, row) =>
            row.tipo_descuento === "PORCENTAJE"
              ? `<span class="badge bg-label-info border-0">${Math.round(row.valor)}% OFF</span>`
              : `<span class="badge bg-label-success border-0">S/ ${parseFloat(row.valor).toFixed(2)} OFF</span>`,
        },
        {
          data: null,
          render: (d, t, r) =>
            `<small class="text-muted"><i class='bx bx-calendar-event me-1'></i> ${new Date(r.fecha_inicio.replace(/-/g, "/")).toLocaleDateString()} al ${new Date(r.fecha_fin.replace(/-/g, "/")).toLocaleDateString()}</small>`,
        },
        {
          data: "estado",
          className: "text-center",
          render: (d, t, r) =>
            `<div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input switch-estado" type="checkbox" data-id="${r.id_promocion}" ${d == 1 ? "checked" : ""}></div>`,
        },
        {
          data: null,
          className: "text-center",
          render: () =>
            `<div class="dropdown"><button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button><div class="dropdown-menu dropdown-menu-end shadow-sm border-0"><a class="dropdown-item btn-ver" href="javascript:void(0);"><i class="bx bx-show text-info me-2"></i> Ver Detalle</a><a class="dropdown-item btn-editar" href="javascript:void(0);"><i class="bx bx-edit text-warning me-2"></i> Editar</a><div class="dropdown-divider"></div><a class="dropdown-item btn-eliminar text-danger" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Eliminar</a></div></div>`,
        },
      ],
      buttons: [
        {
          extend: "excelHtml5",
          className: "d-none",
          filename: "Reporte_Promociones",
          title: "",
          exportOptions: { columns: [2, 3, 4], orthogonal: "export" },
          customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets["sheet1.xml"];
            var styles = xlsx.xl["styles.xml"];
            var fillIndex = $("fills fill", styles).length;
            $("fills", styles).append(
              '<fill><patternFill patternType="solid"><fgColor rgb="FF00BFFF" /><bgColor indexed="64" /></patternFill></fill>',
            );
            var fontIndex = $("fonts font", styles).length;
            $("fonts", styles).append(
              '<font><b /><color rgb="FFFFFFFF" /><sz val="11" /><name val="Calibri" /></font>',
            );
            var styleIndex = $("cellXfs xf", styles).length;
            $("cellXfs", styles).append(
              '<xf numFmtId="0" fontId="' +
                fontIndex +
                '" fillId="' +
                fillIndex +
                '" applyFont="1" applyFill="1" />',
            );
            $("row:first c", sheet).attr("s", styleIndex);
          },
        },
      ],
    });
  },

  initWhatsAppLogic: function () {
    const self = this;
    const textarea = $("#textoMensaje");
    const preview = $("#previewMensaje");
    const templateBase =
      "Hola {{nombre}} 👋, Aprovecha nuestra promo *{{promocion}}*! Obtén *{{valor}}* de descuento en tu próximo lavado 🚗. Válido hasta: {{fechafin}}";

    $(document).on("change", "#selectPromoWS", function () {
      let selected = $(this).find(":selected");
      if (selected.val() === "") {
        textarea.val("");
        preview.html(
          "Selecciona una promoción activa para generar una difusión increíble! 🚀",
        );
        return;
      }
      let texto = templateBase
        .replace("{{promocion}}", selected.data("nombre"))
        .replace("{{valor}}", selected.data("valor"))
        .replace("{{fechafin}}", selected.data("fin"));
      textarea.val(texto);
      actualizarPreview(texto);
    });

    $(document).on("click", ".wa-variable", function () {
      const variable = $(this).data("var");
      const cursorPos = textarea.prop("selectionStart");
      const text = textarea.val();
      textarea.val(
        text.substring(0, cursorPos) + variable + text.substring(cursorPos),
      );
      actualizarPreview(textarea.val());
      textarea.focus();
    });

    textarea.on("input", function () {
      actualizarPreview($(this).val());
    });

    function actualizarPreview(txt) {
      let html = txt
        .replace(/\n/g, "<br>")
        .replace(/{{nombre}}/g, "<strong>Cliente</strong>")
        .replace(/{{promocion}}/g, "<strong>Promoción Verano</strong>")
        .replace(/{{valor}}/g, "<strong>20% OFF</strong>")
        .replace(/{{fechafin}}/g, "<strong>31/12/2026</strong>")
        .replace(/\*(.*?)\*/g, "<strong>$1</strong>");
      preview.html(html);
    }
  },

  initEventosUI: function () {
    const self = this;
    $("#buscadorGlobal").on("keyup", function () {
      self.tabla.search(this.value).draw();
    });
    $("#btnExportar").on("click", () =>
      self.tabla.button(".buttons-excel").trigger(),
    );

    $("#scrolToTable").on("click", function () {
      $("html, body").animate(
        { scrollTop: $("#tablaPromociones").offset().top - 100 },
        500,
      );
    });

    function getData(el) {
      let tr = $(el).closest("tr");
      if (tr.hasClass("child")) tr = tr.prev();
      return self.tabla.row(tr).data();
    }

    $("#tablaPromociones tbody").on(
      "change",
      ".switch-estado",
      async function () {
        const el = $(this);
        el.prop("disabled", true);
        try {
          const res = await fetch(`${BASE_URL}/admin/promocion/cambiarestado`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              id_promocion: el.data("id"),
              estado: el.is(":checked") ? 1 : 0,
            }),
          });
          const data = await res.json();
          if (data.success) {
            self.mostrarToast("Estado actualizado correctamente", "success");
            self.actualizarVista();
          } else {
            el.prop("checked", !el.is(":checked"));
            self.mostrarToast("Error al cambiar estado", "danger");
          }
        } catch (e) {
          el.prop("checked", !el.is(":checked"));
        } finally {
          el.prop("disabled", false);
        }
      },
    );

    const abrirEditar = (data) => {
      if (data) {
        $("#edit_id_promocion").val(data.id_promocion);
        $("#edit_nombre").val(data.nombre);
        $("#edit_tipo").val(data.tipo_descuento);
        $("#edit_valor").val(data.valor);
        $("#edit_inicio").val(data.fecha_inicio);
        $("#edit_fin").val(data.fecha_fin);
        $("#edit_solo_una").prop("checked", data.solo_una_vez_por_cliente == 1);
        $("#edit_mensaje").val(data.mensaje_whatsapp);

        // LÓGICA DE BLOQUEO DE FECHAS (EDICIÓN)
        const today = new Date().toISOString().split("T")[0];

        // Si la fecha de inicio original ya pasó o es hoy, bloqueamos el cambio para no "volver atrás"
        if (data.fecha_inicio <= today) {
          $("#edit_inicio")
            .attr("readonly", true)
            .css("background-color", "#f8f9fa");
        } else {
          $("#edit_inicio")
            .attr("readonly", false)
            .attr("min", today)
            .css("background-color", "");
        }

        // La fecha de fin siempre debe ser de hoy en adelante en edición
        $("#edit_fin").attr("min", today);

        new bootstrap.Modal(document.getElementById("modalEditar")).show();
      }
    };

    const abrirDetalle = (data) => {
      if (!data) return self.mostrarToast("Registro no encontrado", "danger");

      const esP = data.tipo_descuento === "PORCENTAJE";
      const val = esP ? Math.round(data.valor) + "%" : "S/ " + data.valor;
      const estado =
        data.estado == 1
          ? '<span class="badge bg-label-success">ACTIVA</span>'
          : '<span class="badge bg-label-secondary">INACTIVA</span>';

      let html = `
                <div class="row g-4 p-4 text-center border-bottom bg-white">
                    <div class="col-12">
                        <div class="discount-circle ${esP ? "bg-label-primary text-primary" : "bg-label-success text-success"} mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                            ${val}<span class="d-block text-danger m-1 fw-bold" style="font-size: 0.6rem;">OFF</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">${data.nombre}</h4>
                        ${estado}
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-6 ">
                            <label class="form-label small text-muted text-uppercase fw-bold">Periodo de Validez</label>
                            <div class="small fw-bold"><i class="bx bx-calendar me-1"></i> ${new Date(data.fecha_inicio.replace(/-/g, "/")).toLocaleDateString()} al ${new Date(data.fecha_fin.replace(/-/g, "/")).toLocaleDateString()}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Restricción de Uso</label>
                            <div class="small fw-bold"><i class="bx bx-lock-alt me-1"></i> ${data.solo_una_vez_por_cliente == 1 ? "Sólo 1 vez por cliente" : "Uso ilimitado por cliente"}</div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label small text-muted text-uppercase fw-bold">Nota Administrativa / Mensaje</label>
                            <div class="p-3 bg-label-secondary rounded-4 small border border-light" style="min-height: 80px;">
                                ${data.mensaje_whatsapp || "<em>Sin descripción adicional.</em>"}
                            </div>
                        </div>
                    </div>
                </div>`;

      $("#contenidoDetalle").html(html);
      new bootstrap.Modal(document.getElementById("modalDetalle")).show();
    };

    const abrirEliminar = (id, nombre) => {
      $("#delete_id_promocion").val(id);
      $("#nombre_eliminar").text(nombre);
      new bootstrap.Modal(document.getElementById("modalEliminar")).show();
    };

    // Eventos para Cards
    $(document).on("click", ".btn-editar-card", function () {
      let id = $(this).data("id");
      let data = self.tabla
        .rows()
        .data()
        .toArray()
        .find((r) => r.id_promocion == id);
      abrirEditar(data);
    });

    $(document).on("click", ".btn-ver-detalle", function () {
      let id = $(this).data("id");
      let data = self.tabla
        .rows()
        .data()
        .toArray()
        .find((r) => r.id_promocion == id);
      abrirDetalle(data);
    });

    $(document).on("click", ".btn-eliminar-card", function () {
      abrirEliminar($(this).data("id"), $(this).data("nom"));
    });

    // Eventos para Tabla
    $("#tablaPromociones tbody").on("click", ".btn-ver", function () {
      abrirDetalle(getData(this));
    });
    $("#tablaPromociones tbody").on("click", ".btn-editar", function () {
      abrirEditar(getData(this));
    });
    $("#tablaPromociones tbody").on("click", ".btn-eliminar", function () {
      let data = getData(this);
      abrirEliminar(data.id_promocion, data.nombre);
    });
  },

  initFormularios: function () {
    const self = this;

    const handleForm = async (formId, url, btnText, modalId) => {
      $(`#${formId}`).on("submit", async function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let obj = Object.fromEntries(formData);

        if (formId.includes("registrar") || formId.includes("Editar")) {
          if (new Date(obj.fecha_fin) < new Date(obj.fecha_inicio)) {
            return self.mostrarToast(
              "La fecha de fin no puede ser anterior al inicio",
              "warning",
            );
          }
          if (parseFloat(obj.valor) <= 0) {
            return self.mostrarToast(
              "El valor del descuento debe ser mayor a 0",
              "warning",
            );
          }
        }

        let btn = $(this).find('button[type="submit"]');
        let originalText = btn.text();
        btn.prop("disabled", true).text("Procesando...");

        try {
          if (formId.includes("registrar") || formId.includes("Editar")) {
            let chk = $(this).find('input[name="solo_una_vez_por_cliente"]');
            formData.set(
              "solo_una_vez_por_cliente",
              chk.is(":checked") ? 1 : 0,
            );
          }
          let jsonData = JSON.stringify(Object.fromEntries(formData));

          let res = await fetch(`${BASE_URL}${url}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: jsonData,
          });
          let data = await res.json();

          if (data.success) {
            if (modalId) {
              const modalInstance = bootstrap.Modal.getInstance(
                document.getElementById(modalId),
              );
              if (modalInstance) modalInstance.hide();
            }
            self.initVisualFixes();
            self.mostrarToast(data.message, "success");
            self.actualizarVista();
            if (formId.includes("registrar")) this.reset();
          } else {
            self.mostrarToast(data.message, "danger");
          }
        } catch (err) {
          self.mostrarToast("Error de conexión", "danger");
        } finally {
          btn.prop("disabled", false).text(originalText);
        }
      });
    };

    handleForm(
      "registrarPromocion",
      "/admin/promocion/registrarpromocion",
      "LANZAR",
      "modalRegistrar",
    );
    handleForm(
      "formEditarPromocion",
      "/admin/promocion/editarpromocion",
      "GUARDAR",
      "modalEditar",
    );
    handleForm(
      "formEliminarPromocion",
      "/admin/promocion/eliminarpromocion",
      "RETIRAR",
      "modalEliminar",
    );

    $("#formEnviarWhatsApp").on("submit", async function (e) {
      e.preventDefault();
      let btn = $(this).find('button[type="submit"]');
      let originalHtml = btn.html();
      btn
        .prop("disabled", true)
        .html('<i class="bx bx-loader-alt bx-spin"></i> ENVIANDO...');

      try {
        let formData = new FormData(this);
        let jsonData = JSON.stringify(Object.fromEntries(formData));
        let res = await fetch(`${BASE_URL}/admin/promocion/enviarwhatsapp`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: jsonData,
        });
        let data = await res.json();

        if (data.success) {
          self.mostrarToast(data.message, "success");
          this.reset();
          $("#selectPromoWS").val("").trigger("change");
        } else {
          self.mostrarToast(data.message, "danger");
        }
      } catch (e) {
        self.mostrarToast("Error de conexión", "danger");
      } finally {
        btn.prop("disabled", false).html(originalHtml);
      }
    });
  },

  initVisualFixes: function () {
    $(".modal-backdrop, .offcanvas-backdrop").remove();
    $("body").removeClass("modal-open offcanvas-open").css({
      overflow: "",
      "padding-right": "",
    });
  },
};

document.addEventListener("DOMContentLoaded", () => {
  PromocionModule.init();
});
