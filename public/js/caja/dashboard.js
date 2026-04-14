let todasOrdenes = [];
let filtroActual = "TODAS";
let busquedaActual = "";
let carrito = [];
let ordenCobrar = null;
let metodoCobro = "EFECTIVO";
let metodoVenta = "EFECTIVO";
let ordenAnulando = null;
let ordenIniciando = null;
let ordenTerminando = null;
let _saldoEsperado = 0;

// ═══ INIT ═══
document.addEventListener("DOMContentLoaded", () => {
  cargarOrdenes();
  // Iniciar cronómetros cada 30 segundos
  setInterval(actualizarCronometros, 30000);
});

// ═══ CARGAR ÓRDENES ═══
async function cargarOrdenes() {
  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/getordenes`);
    const json = await res.json();
    todasOrdenes = json.data || [];
    window.historialHoy = json.historial || [];
    renderOrdenes();
    actualizarBadges();
    actualizarDropdownTienda();
  } catch (e) {
    mostrarToast("Error cargando órdenes", "danger");
  }
}

function actualizarDropdownTienda() {
  const sel = document.getElementById("sel_anexar_orden");
  if (!sel) return;
  const currentVal = $(sel).val();

  const activas = todasOrdenes.filter(
    (o) => o.estado !== "FINALIZADO" && o.estado !== "ANULADO",
  );

  let html = '<option value="">-- VENTA DIRECTA LIBRE --</option>';
  activas.forEach((o) => {
    html += `<option value="${o.id_orden}">Orden #${o.id_orden} - ${o.placa || "S/P"} - ${o.cli_nombres || ""} ${o.cli_apellidos || ""}</option>`;
  });

  sel.innerHTML = html;

  if (activas.some((o) => o.id_orden == currentVal)) {
    $(sel).val(currentVal).trigger("change");
  } else {
    $(sel).val("").trigger("change");
  }
}

function actualizarBadges() {
  const proceso = todasOrdenes.filter((o) => o.estado === "EN_PROCESO").length;
  const cobrar = todasOrdenes.filter((o) => o.estado === "POR_COBRAR").length;
  const cola = todasOrdenes.filter(
    (o) => o.estado === "EN_COLA" || o.estado === "EN_ESPERA",
  ).length;
  const historial = (window.historialHoy || []).length;
  const total = todasOrdenes.length;

  if (document.getElementById("badgeActivas"))
    document.getElementById("badgeActivas").textContent = proceso + cobrar;
  if (document.getElementById("sEnProceso"))
    document.getElementById("sEnProceso").textContent = proceso;
  if (document.getElementById("sPorCobrar"))
    document.getElementById("sPorCobrar").textContent = cobrar;

  // Actualizar Texto de los Tabs con contadores
  document.querySelectorAll(".order-tab").forEach((tab) => {
    const filter = tab.dataset.filter;
    let count = 0;
    let label = "";

    switch (filter) {
      case "TODAS":
        count = total;
        label = "Todos";
        break;
      case "EN_COLA":
        count = cola;
        label = "En Cola";
        break;
      case "EN_PROCESO":
        count = proceso;
        label = "En Proceso";
        break;
      case "POR_COBRAR":
        count = cobrar;
        label = "Por Cobrar";
        break;
      case "HISTORIAL":
        count = historial;
        label = "Historial Hoy";
        break;
    }

    if (label) {
      tab.textContent = `${label} (${count})`;
    }
  });
}

// ═══ FILTRADO Y BÚSQUEDA ═══
function setFiltro(f, el) {
  filtroActual = f;
  document
    .querySelectorAll(".order-tab")
    .forEach((t) => t.classList.remove("active"));
  if (el) el.classList.add("active");
  renderOrdenes();
}

function actualizarVista() {
  busquedaActual = document
    .getElementById("searchOrders")
    .value.toLowerCase()
    .trim();
  renderOrdenes();
}

function renderOrdenes() {
  const container = document.getElementById("listaOrdenes");
  if (!container) return;
  let rawData = [];

  if (filtroActual === "HISTORIAL") {
    // Here we could query backend for history or rely on a global history var if it exists.
    // Wait, 'historialHoy' was used previously
    rawData = typeof historialHoy !== "undefined" ? historialHoy : [];
  } else if (filtroActual === "TODAS") {
    rawData = todasOrdenes;
  } else {
    rawData = todasOrdenes.filter((o) => o.estado === filtroActual);
  }

  let data = rawData;
  if (busquedaActual) {
    data = rawData.filter(
      (o) =>
        (o.placa || "").toLowerCase().includes(busquedaActual) ||
        (o.dni || "").toLowerCase().includes(busquedaActual) ||
        (o.cli_nombres || "").toLowerCase().includes(busquedaActual) ||
        (o.cli_apellidos || "").toLowerCase().includes(busquedaActual) ||
        (o.id_orden + "").includes(busquedaActual) ||
        (o.servicios_vendidos || "").toLowerCase().includes(busquedaActual),
    );
  }

  if (!data.length) {
    container.innerHTML = `<div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border mt-3 w-100">
            <i class="bx bx-search-alt-2" style="font-size:3.5rem; opacity:0.1"></i>
            <p class="mt-3 mb-0 fw-bold">Sin órdenes ${busquedaActual ? 'para "' + busquedaActual + '"' : "en esta sección"}</p>
        </div>`;
    return;
  }

  if (filtroActual === "HISTORIAL") {
    // VISTA LISTA PARA HISTORIAL
    container.innerHTML = `
            <div class="card shadow-none border-0 mt-3">
                <div class="table-responsive text-nowrap rounded-3 overflow-hidden border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-label-secondary">
                            <tr>
                                <th class="py-3">ID / HORA</th>
                                <th class="py-3">CLIENTE</th>
                                <th class="py-3">VEHÍCULO</th>
                                <th class="py-3">SERVICIOS</th>
                                <th class="py-3 text-end">TOTAL</th>
                                <th class="py-3 text-center">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data
                              .map((o) => {
                                const t = o.fecha_cierre
                                  ? new Date(o.fecha_cierre)
                                  : new Date(o.fecha_creacion);
                                const h = t.toLocaleTimeString("es-PE", {
                                  hour: "2-digit",
                                  minute: "2-digit",
                                });
                                return `
                                    <tr>
                                        <td><span class="fw-bold">#${o.id_orden}</span><br><small class="text-muted">${h}</small></td>
                                        <td><div class="fw-bold text-dark">${o.cli_nombres} ${o.cli_apellidos}</div></td>
                                        <td><span class="badge bg-dark text-white rounded-pill">${o.placa || "S/P"}</span></td>
                                        <td class="small text-truncate" style="max-width:200px">${o.servicios_vendidos || "-"}</td>
                                        <td class="text-end fw-bold text-primary">S/ ${parseFloat(o.total_final).toFixed(2)}</td>
                                        <td class="text-center"><span class="badge bg-label-success rounded-pill px-3 py-1 fw-bold">PAGADO</span></td>
                                    </tr>
                                `;
                              })
                              .join("")}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
  } else {
    // VISTA CARDS PARA ACTIVAS
    container.innerHTML =
      `<div class="orders-grid">` +
      data
        .map((o) => {
          const est = o.estado;
          const badgeColors = {
            EN_COLA: "bg-label-primary",
            EN_ESPERA: "bg-label-danger",
            EN_PROCESO: "bg-label-warning",
            POR_COBRAR: "bg-label-success",
          };

          let btnAction = "";
          const deuda =
            parseFloat(o.total_final) - parseFloat(o.pagado_total || 0);

          if (est === "POR_COBRAR") {
            if (deuda > 0) {
              btnAction = `<button class="btn btn-success w-100 fw-bold rounded-pill shadow-sm py-2 mb-2" onclick="abrirCobro(${o.id_orden}, ${deuda})"><i class="bx bx-dollar-circle me-1 fs-5"></i>FINALIZAR Y COBRAR S/ ${deuda.toFixed(2)}</button>`;
            } else {
              btnAction = `<button class="btn btn-primary bg-gradient w-100 fw-bold rounded-pill shadow-sm py-2 mb-2" onclick="cobrarOrdenPagada(${o.id_orden})"><i class="bx bx-check-double me-1 fs-5"></i>FINALIZAR (PAGADO)</button>`;
            }
            btnAction += `<button class="btn btn-label-danger w-100 btn-sm fw-bold border-0 bg-label-danger" onclick="iniciarAnulacion(${o.id_orden})"><i class="bx bx-trash me-1"></i>ANULAR ORDEN</button>`;
          } else if (est === "EN_PROCESO") {
            btnAction = `<button class="btn btn-warning w-100 fw-bold rounded-pill shadow-sm py-2 text-dark" onclick="pasaraPorCobrar(${o.id_orden})"><i class="bx bx-check-double me-1 fs-5"></i>TERMINAR LAVADO</button>`;
          } else if (est === "EN_COLA" || est === "EN_ESPERA") {
            btnAction = `<button class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm py-2 text-white" onclick="pasaraProceso(${o.id_orden})"><i class="bx bx-play-circle me-1 fs-5"></i>INICIAR LAVADO</button>`;
          }

          const servs = o.servicios_vendidos || "-";
          const prods = o.productos_vendidos || "";
          const dctoPromo = parseFloat(o.descuento_promo || 0);
          const ptsGanados = parseInt(o.puntos_ganados || 0);
          const ptsAcum = parseInt(o.puntos_acumulados || 0);
          const nombrePromo = o.nombre_promocion || "Promo Aplicada";

          // --- Lógica de Cronómetro para Órdenes en Proceso ---
          let timerHtml = "";
          if (est === "EN_PROCESO" && o.fecha_inicio_proceso) {
            timerHtml = `<div class="mt-2 d-flex justify-content-center">
                    <span class="timer-badge timer-normal" id="timer-${o.id_orden}" 
                        data-inicio="${o.fecha_inicio_proceso}" 
                        data-estimado="${o.tiempo_total_estimado || 0}">
                        <i class="bx bx-stopwatch"></i> Calculando...
                    </span>
                </div>`;
          }

          return `<div class="order-col" id="card-orden-${o.id_orden}">
                <div class="order-card st-${est}">
                    <div class="status-indicator"></div>
                    <div class="oc-header mb-2">
                        <div>
                            <span class="oc-id fw-bold">#${o.id_orden}</span>
                            ${o.estado_pago === "PAGADO" ? '<span class="badge bg-success ms-2" style="font-size:0.6rem"><i class="bx bx-check-circle me-1"></i>PAGADO</span>' : '<span class="badge bg-secondary ms-2" style="font-size:0.6rem">PENDIENTE</span>'}
                        </div>
                        <div class="d-flex flex-column text-end">
                            <span class="badge ${badgeColors[est]} mb-1">${est.replace("_", " ")}</span>
                            <span class="oc-total fs-5 fw-bold text-primary">S/ ${parseFloat(o.total_final || 0).toFixed(2)}</span>
                        </div>
                    </div>
                    ${o.fecha_inicio_proceso ? `<div class="text-secondary small fw-bold mb-1 text-center" style="font-size:0.75rem;"><i class="bx bx-time"></i> Inicio: ${o.fecha_inicio_proceso.substring(11, 16)}</div>` : ""}
                    ${timerHtml}
                    
                    <div class="oc-client border-bottom pb-2 mb-3 mt-2">
                        <div class="oc-name text-truncate fw-bold text-dark"><i class="bx bxs-user text-muted me-1"></i>${o.cli_nombres || ""} ${o.cli_apellidos || ""}</div>
                        <div class="oc-vehicle mt-2 d-flex align-items-center">
                            <span class="badge bg-dark text-white p-1 px-3 rounded-pill fw-bold" style="font-size:0.75rem"><i class="bx bxs-car me-1"></i>${o.placa || "S/P"}</span>
                        </div>
                    </div>
                    <div class="oc-services mb-2 bg-light p-2 rounded">
                        <div class="fw-bold fs-tiny text-muted text-uppercase mb-1">SERVICIOS</div>
                        <div class="oc-serv-list small fw-bold text-dark">${servs}</div>
                    </div>
                    ${
                      prods !== ""
                        ? `<div class="oc-services mb-2 bg-label-info p-2 rounded">
                        <div class="fw-bold fs-tiny text-info text-uppercase mb-1"><i class="bx bx-store me-1"></i>PRODUCTOS TIENDA</div>
                        <div class="oc-serv-list small fw-bold text-info">${prods}</div>
                    </div>`
                        : ""
                    }
                    ${
                      o.tiempo_total_estimado > 0
                        ? `<div class="d-flex justify-content-between mb-2 px-2 py-1 rounded bg-label-secondary border border-secondary" style="font-size: 0.7rem;">
                        <span class="fw-bold text-secondary"><i class="bx bx-time-five me-1"></i>Est: ${o.tiempo_total_estimado} min</span>
                    </div>`
                        : ""
                    }
                    ${
                      ptsGanados > 0 || ptsAcum > 0
                        ? `<div class="d-flex justify-content-between mb-2 px-2 py-1 rounded bg-label-warning border border-warning">
                        <span class="fw-bold small text-warning"><i class="bx bx-star me-1"></i>Ganará: +${ptsGanados} pts</span>
                        <span class="fw-bold small text-dark opacity-75">Pts Actuales: ${ptsAcum}</span>
                    </div>`
                        : ""
                    }
                    ${
                      dctoPromo > 0
                        ? `<div class="mb-2 px-2 py-1 rounded border-dashed border-danger bg-label-danger" style="border: 1px dashed #ff3e1d">
                        <div class="fw-bold small text-danger"><i class="bx bx-gift me-1"></i>${nombrePromo} (- S/ ${dctoPromo.toFixed(2)})</div>
                    </div>`
                        : ""
                    }
                    <div class="oc-footer mt-auto">
                        <div class="text-center mb-3">
                            <span class="badge ${badgeColors[est]} rounded-pill px-3 py-1 fw-bold" style="font-size:0.65rem">${est.replace(/_/g, " ")}</span>
                        </div>
                        ${btnAction}
                    </div>
                </div>
            </div>`;
        })
        .join("") +
      `</div>`;
  }
  // Disparar primer cálculo inmediatamente
  setTimeout(actualizarCronometros, 100);
}

function actualizarCronometros() {
  const timers = document.querySelectorAll(".timer-badge");
  if (!timers.length) return;

  const ahora = new Date();

  timers.forEach((t) => {
    try {
      const inicioRaw = t.dataset.inicio; // Formato SQL: YYYY-MM-DD HH:MM:SS
      if (!inicioRaw) return;

      // Convertir SQL Date a JS Date (Manejando Safari/Firefox tmb)
      const d = inicioRaw.split(/[- :]/);
      const fechaInicio = new Date(d[0], d[1] - 1, d[2], d[3], d[4], d[5]);

      const minutosEstimados = parseInt(t.dataset.estimado) || 0;
      const diffMs = ahora - fechaInicio;
      const minutosTranscurridos = Math.floor(diffMs / 60000);

      const card = t.closest(".order-card");

      if (minutosTranscurridos >= minutosEstimados) {
        // RETRASADO
        const retraso = minutosTranscurridos - minutosEstimados;
        t.className = "timer-badge timer-overdue";
        t.innerHTML = `<i class="bx bxs-alarm-exclamation"></i> RETRASO: ${retraso} min`;
        card.classList.add("delayed");
      } else {
        // EN TIEMPO
        const restante = minutosEstimados - minutosTranscurridos;
        t.className = `timer-badge ${restante <= 5 ? "timer-warning" : "timer-normal"}`;
        t.innerHTML = `<i class="bx bx-stopwatch"></i> Restan: ${restante} min`;
        card.classList.remove("delayed");
      }
    } catch (e) {
      console.error("Error en cronómetro", e);
    }
  });
}

// ═══ CLIENTE ═══
function abrirModalRegistrarCliente() {
  new bootstrap.Modal(document.getElementById("modalRegistrar")).show();
}

document.getElementById("btnBuscarDni")?.addEventListener("click", async () => {
  let dni = $("#dni").val().trim();
  let feedback = $("#dniFeedback");
  if (dni.length !== 8 && dni.length !== 11) {
    feedback.html(
      '<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> Debe tener 8 (DNI) u 11 (RUC) dígitos.</span>',
    );
    return;
  }

  $("#btnBuscarDni")
    .html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>',
    )
    .prop("disabled", true);
  feedback.html(
    '<span class="text-primary fw-bold"><i class="bx bx-loader-circle bx-spin"></i> Consultando...</span>',
  );

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
        a =
          `${obj.data.apellido_paterno || ""} ${obj.data.apellido_materno || ""}`.trim();
      } else if (dni.length === 11 && obj.data) {
        n = obj.data.nombre_o_razon_social || "";
        a = "RUC";
      }

      $("#nombres").val(n).prop("readonly", true);
      $("#apellidos").val(a).prop("readonly", true);
      feedback.html(
        '<span class="text-success fw-bold"><i class="bx bx-check-circle"></i> ¡Datos encontrados!</span>',
      );
    } else {
      feedback.html(
        `<span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> ${obj.message || "No encontrado"}</span>`,
      );
      $("#nombres, #apellidos").val("").prop("readonly", false);
    }
  } catch (e) {
    feedback.html(
      '<span class="text-danger fw-bold"><i class="bx bx-wifi-off"></i> Error de conexión con el servidor.</span>',
    );
    $("#nombres, #apellidos").val("").prop("readonly", false);
  } finally {
    $("#btnBuscarDni")
      .html('<i class="bx bx-search fs-5"></i>')
      .prop("disabled", false);
  }
});

document
  .getElementById("registrarcliente")
  ?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
      dni: document.getElementById("dni").value.trim(),
      nombres: document.getElementById("nombres").value,
      apellidos: document.getElementById("apellidos").value,
      sexo: document.getElementById("sexo").value,
      telefono: document.getElementById("telefono").value,
    };
    if (!data.dni || !data.nombres)
      return mostrarToast("Documento y nombres requeridos", "warning");
    try {
      const res = await fetch(`${BASE_URL}/caja/dashboard/registrarcliente`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });
      const r = await res.json();
      if (r.success) {
        mostrarToast(r.message, "success");
        setTimeout(() => location.reload(), 800);
      } else {
        mostrarToast(r.message, "danger");
      }
    } catch (err) {
      mostrarToast("Error de registro", "danger");
    }
  });

// ═══ ACCIONES DE ORDEN ═══
async function pasaraProceso(id) {
  const res = await fetch(`${BASE_URL}/caja/dashboard/pasara_proceso`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: id }),
  });
  const r = await res.json();
  if (r.success) cargarOrdenes();
  mostrarToast(r.message, r.success ? "success" : "danger");
}

async function pasaraPorCobrar(id) {
  const res = await fetch(`${BASE_URL}/caja/dashboard/pasara_por_cobrar`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: id }),
  });
  const r = await res.json();
  if (r.success) cargarOrdenes();
  mostrarToast(r.message, r.success ? "success" : "danger");
}

async function abrirCobro(id, total) {
  if (!_cajaActivaId) return mostrarToast("Apertura caja primero", "danger");
  ordenCobrar = id;
  document.getElementById("cobrar_id").textContent = id;
  document.getElementById("cobrar_total").textContent =
    "S/ " + parseFloat(total).toFixed(2);
  const res = await fetch(`${BASE_URL}/caja/dashboard/getdetalle?id=${id}`);
  const data = await res.json();
  document.getElementById("cobrar_detalle").innerHTML =
    (data.detalles || [])
      .map(
        (d) =>
          `<div class="d-flex justify-content-between small px-2"><span>${d.servicio_nombre || "Producto"}</span><strong>S/ ${parseFloat(d.subtotal).toFixed(2)}</strong></div>`,
      )
      .join("") || "Sin detalles";
  new bootstrap.Modal(document.getElementById("modalCobrar")).show();
}

async function confirmarCobro() {
  const res = await fetch(`${BASE_URL}/caja/dashboard/finalizarorden`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: ordenCobrar, metodo_pago: metodoCobro }),
  });
  const data = await res.json();
  if (data.success) {
    bootstrap.Modal.getInstance(document.getElementById("modalCobrar")).hide();
    cargarOrdenes();
  }
  mostrarToast(data.message, data.success ? "success" : "danger");
}

function selMetodo(el) {
  document
    .querySelectorAll("#modalCobrar .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  el.classList.add("selected");
  metodoCobro = el.dataset.metodo;
}

// ═══ NUEVA ORDEN ═══
function abrirModalNuevaOrden() {
  if (!_cajaActivaId) return mostrarToast("Apertura caja primero", "danger");

  // Resetear campos del modal para que esté limpio
  const selCli = document.getElementById("sel_cliente_orden");
  if (selCli) $(selCli).val("").trigger("change");

  const selVeh = document.getElementById("sel_vehiculo_orden");
  if (selVeh) selVeh.innerHTML = '<option value="">-- Seleccionar --</option>';

  document.getElementById("nv_placa").value = "";
  document.getElementById("nv_color").value = "";
  document.getElementById("camposNuevoVehiculo").style.display = "none";

  document.querySelectorAll(".service-selectable").forEach((s) => s.classList.remove("selected"));
  document.querySelectorAll(".promo-option").forEach((p) => p.classList.remove("selected"));
  document.querySelector(".promo-option[data-id='']")?.classList.add("selected");

  const chkAnt = document.getElementById("chk_pago_anticipado");
  if (chkAnt) {
    chkAnt.checked = false;
    document.getElementById("panel_metodos_pago_anticipado").style.display = "none";
  }

  const lblTotal = document.getElementById("lbl_total_nueva_orden");
  if (lblTotal) lblTotal.textContent = "S/ 0.00";

  new bootstrap.Modal(document.getElementById("modalNuevaOrden")).show();
}

async function cargarVehiculosCliente(id) {
  if (!id) return;
  const res = await fetch(
    `${BASE_URL}/caja/dashboard/getvehiculos?id_cliente=${id}`,
  );
  const data = await res.json();
  const sel = document.getElementById("sel_vehiculo_orden");
  sel.innerHTML =
    '<option value="">-- Seleccionar --</option>' +
    (data.data || [])
      .map(
        (v) =>
          `<option value="${v.id_vehiculo}">${v.placa} (${v.categoria})</option>`,
      )
      .join("") +
    '<option value="NUEVO">+ REGISTRAR NUEVO</option>';
}

function checkNuevoVeh(val) {
  document.getElementById("camposNuevoVehiculo").style.display =
    val === "NUEVO" ? "block" : "none";
}

function toggleServicioOrden(el) {
  document
    .querySelectorAll(".service-selectable")
    .forEach((s) => s.classList.remove("selected"));
  el.classList.add("selected");
  calcularTotalNuevaOrden();
}

function selectPromoOrden(el) {
  document
    .querySelectorAll(".promo-option")
    .forEach((p) => p.classList.remove("selected"));
  el.classList.add("selected");
  calcularTotalNuevaOrden();
}

function calcularTotalNuevaOrden() {
  let maxServicio = null;
  let totalServicios = 0;
  document.querySelectorAll(".service-selectable.selected").forEach((s) => {
    totalServicios += parseFloat(s.dataset.precio || 0);
  });

  let descPromo = 0;
  const promoNode = document.querySelector(".promo-option.selected");
  if (promoNode && promoNode.dataset.id && promoNode.dataset.id !== "") {
    const tipo = promoNode.dataset.tipo;
    const valor = parseFloat(promoNode.dataset.valor || 0);
    if (tipo === "PORCENTAJE") {
      descPromo = totalServicios * (valor / 100);
    } else {
      descPromo = Math.min(valor, totalServicios);
    }
  }

  const totalFinal = Math.max(0, totalServicios - descPromo);
  const lbl = document.getElementById("lbl_total_nueva_orden");
  if (lbl) {
    if (descPromo > 0) {
      lbl.innerHTML = `<span class="text-danger text-decoration-line-through fs-6 fw-normal me-2">S/ ${totalServicios.toFixed(2)}</span>S/ ${totalFinal.toFixed(2)}`;
    } else {
      lbl.innerHTML = `S/ ${totalFinal.toFixed(2)}`;
    }
  }
}

function togglePagoAnticipado(chk) {
  const pnl = document.getElementById("panel_metodos_pago_anticipado");
  pnl.style.display = chk.checked ? "block" : "none";
}

function selMetodoAnticipado(elem) {
  document
    .querySelectorAll("#panel_metodos_pago_anticipado .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  elem.classList.add("selected");
  document.getElementById("metodo_anticipado").value = elem.dataset.metodo;
}

async function confirmarCreacionOrden() {
  const idCliente = document.getElementById("sel_cliente_orden").value;
  let idVehiculo = document.getElementById("sel_vehiculo_orden").value;

  if (!idCliente || !idVehiculo)
    return mostrarToast("Cliente y vehículo requeridos", "warning");

  if (idVehiculo === "NUEVO") {
    const nvData = {
      id_cliente: idCliente,
      placa: document.getElementById("nv_placa").value.trim(),
      color: document.getElementById("nv_color").value,
      id_categoria: document.getElementById("nv_categoria").value,
    };
    const resV = await fetch(`${BASE_URL}/caja/dashboard/registrarvehiculo`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(nvData),
    });
    const rV = await resV.json();
    if (rV.success) idVehiculo = rV.id_vehiculo;
    else return mostrarToast(rV.message, "danger");
  }

  const servicios = [];
  document.querySelectorAll(".service-selectable.selected").forEach((s) => {
    servicios.push({
      id_servicio: s.dataset.id,
      precio_unitario: s.dataset.precio,
    });
  });

  if (servicios.length === 0)
    return mostrarToast("Seleccione al menos un servicio", "warning");

  const promoNode = document.querySelector(".promo-option.selected");
  const idPromocion = promoNode ? promoNode.dataset.id : null;

  // Pago Anticipado?
  const chkPago = document.getElementById("chk_pago_anticipado");
  const pagoAnticipado = chkPago && chkPago.checked;
  const metodoPagoAnt = pagoAnticipado
    ? document.getElementById("metodo_anticipado").value
    : null;

  const res = await fetch(`${BASE_URL}/caja/dashboard/crearorden`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_cliente: idCliente,
      id_vehiculo: idVehiculo,
      servicios: servicios,
      id_promocion: idPromocion,
      pago_anticipado: pagoAnticipado,
      metodo_pago: metodoPagoAnt,
    }),
  });
  const r = await res.json();
  if (r.success) {
    // Blur button to avoid aria-hidden focus warnings
    document.activeElement.blur();

    bootstrap.Modal.getInstance(
      document.getElementById("modalNuevaOrden"),
    ).hide();
    cargarOrdenes();

    // Reset form
    document
      .querySelectorAll(".service-selectable")
      .forEach((s) => s.classList.remove("selected"));
    document
      .querySelectorAll(".promo-option")
      .forEach((s) => s.classList.remove("selected"));
    if (chkPago) chkPago.checked = false;
    document.getElementById("panel_metodos_pago_anticipado").style.display =
      "none";

    if (pagoAnticipado && r.id_orden) {
      const ticketUrl = `${BASE_URL}/public/generar_ticket.php?id=${r.id_orden}&t=${new Date().getTime()}`;
      console.log("Cargando ticket en iframe:", ticketUrl);
      imprimirTicket(ticketUrl);
    }

    mostrarToast(r.message, "success");
  } else {
    mostrarToast(r.message, "danger");
  }
}

function pasaraProceso(id) {
  ordenIniciando = id;
  document.getElementById("iniciar_id").textContent = id;
  new bootstrap.Modal(document.getElementById("modalIniciar")).show();
}

async function confirmarIniciarLavado() {
  if (!ordenIniciando) return;
  const res = await fetch(`${BASE_URL}/caja/dashboard/pasara_proceso`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: ordenIniciando }),
  });
  const r = await res.json();
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById("modalIniciar")).hide();
    cargarOrdenes();
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

function pasaraPorCobrar(id) {
  ordenTerminando = id;
  document.getElementById("terminar_id").textContent = id;
  new bootstrap.Modal(document.getElementById("modalTerminar")).show();
}

async function confirmarTerminarLavado() {
  if (!ordenTerminando) return;
  const res = await fetch(`${BASE_URL}/caja/dashboard/pasara_por_cobrar`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: ordenTerminando }),
  });
  const r = await res.json();
  if (r.success) {
    bootstrap.Modal.getInstance(
      document.getElementById("modalTerminar"),
    ).hide();
    cargarOrdenes();
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

// ═══ COBRAR (FINALIZAR) ═══
function abrirCobro(id, total) {
  ordenCobrar = id;
  metodoCobro = "EFECTIVO";
  document.getElementById("cobrar_id").textContent = id;
  document.getElementById("cobrar_total").textContent =
    "S/ " + parseFloat(total).toFixed(2);
  document
    .querySelectorAll("#modalCobrar .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  document
    .querySelector(`#modalCobrar .pay-method-btn[data-metodo="EFECTIVO"]`)
    .classList.add("selected");
  new bootstrap.Modal(document.getElementById("modalCobrar")).show();
}

function selMetodo(elem) {
  document
    .querySelectorAll("#modalCobrar .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  elem.classList.add("selected");
  metodoCobro = elem.dataset.metodo;
}

async function confirmarCobro() {
  const btn = document.getElementById("btnConfirmarCobro");
  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>COBRANDO...`;

  const res = await fetch(`${BASE_URL}/caja/dashboard/finalizarorden`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: ordenCobrar, metodo_pago: metodoCobro }),
  });
  const r = await res.json();

  btn.disabled = false;
  btn.innerHTML = `<i class="bx bx-check-double me-1"></i>FINALIZAR ORDEN`;

  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById("modalCobrar")).hide();
    cargarOrdenes();
    // Generar ticket solo si backend lo pide
    if (r.imprimir_ticket) {
      imprimirTicket(
        `${BASE_URL}/public/generar_ticket.php?id=${ordenCobrar}&t=${new Date().getTime()}`,
      );
    }
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

async function cobrarOrdenPagada(id) {
  const btn = document.activeElement;
  const oldHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>FINALIZANDO...`;

  const res = await fetch(`${BASE_URL}/caja/dashboard/finalizarorden`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: id, metodo_pago: "NA" }),
  });
  const r = await res.json();
  btn.disabled = false;
  btn.innerHTML = oldHtml;

  if (r.success) {
    cargarOrdenes();
    if (r.imprimir_ticket) {
      imprimirTicket(
        `${BASE_URL}/public/generar_ticket.php?id=${id}&t=${new Date().getTime()}`,
      );
    }
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

// Imprimir abriendo una ventana emergente con tamaño de ticket
function imprimirTicket(url) {
  const w = 450;
  const h = 700;
  const left = screen.width / 2 - w / 2;
  const top = screen.height / 2 - h / 2;
  window.open(
    url,
    "ImprimirTicket",
    `width=${w},height=${h},top=${top},left=${left},scrollbars=yes,status=no,toolbar=no,menubar=no`,
  );
}

// ═══ SESIÓN DE CAJA ═══
document
  .getElementById("formAperturaCaja")
  ?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const res = await fetch(`${BASE_URL}/caja/dashboard/abrir_sesion_caja`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(Object.fromEntries(new FormData(e.target))),
    });
    const r = await res.json();
    if (r.success) location.reload();
    else mostrarToast(r.message, "danger");
  });

function abrirModalArqueo() {
  fetch(`${BASE_URL}/caja/dashboard/resumen_sesion_caja`)
    .then((r) => r.json())
    .then((res) => {
      if (res.success) {
        document.getElementById("arqueoApertura").textContent =
          "S/ " + parseFloat(res.apertura).toFixed(2);
        document.getElementById("arqueoVentas").textContent =
          "S/ " + parseFloat(res.ingresos).toFixed(2);
        _saldoEsperado = parseFloat(res.esperado_en_caja);
        document.getElementById("arqueoEsperado").textContent =
          "S/ " + _saldoEsperado.toFixed(2);
        document.getElementById("cierreIdSesion").value = res.id_sesion;
        new bootstrap.Modal(document.getElementById("modalCierreCaja")).show();
      } else mostrarToast(res.message, "danger");
    });
}

document.getElementById("montoRealCierre")?.addEventListener("input", (e) => {
  const diff = (parseFloat(e.target.value) || 0) - _saldoEsperado;
  const msg = document.getElementById("mensajeDiferencia");
  if (diff === 0)
    msg.innerHTML = '<span class="text-success">Caja Cuadrada</span>';
  else
    msg.innerHTML = `<span class="text-${diff > 0 ? "primary" : "danger"}">${diff > 0 ? "Sobrante" : "Faltante"}: S/ ${Math.abs(diff).toFixed(2)}</span>`;
});

document.getElementById("formCierreCaja")?.addEventListener("submit", (e) => {
  e.preventDefault();
  mostrarConfirmacion("¿Cerrar caja definitivamente?", async () => {
    const res = await fetch(`${BASE_URL}/caja/dashboard/cerrar_sesion_caja`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(Object.fromEntries(new FormData(e.target))),
    });
    const r = await res.json();
    if (r.success) location.reload();
    else mostrarToast(r.message, "danger");
  });
});

// ═══ ANULACIÓN ═══
function iniciarAnulacion(id) {
  ordenAnulando = id;
  document.getElementById("anular_id").textContent = id;
  new bootstrap.Modal(document.getElementById("modalAnular")).show();
}

async function confirmarAnulacion() {
  const res = await fetch(`${BASE_URL}/caja/dashboard/anularregistro`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_orden: ordenAnulando,
      codigo_token: document.getElementById("anular_token").value,
      motivo: document.getElementById("anular_motivo").value,
    }),
  });
  const r = await res.json();
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById("modalAnular")).hide();
    cargarOrdenes();
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

// ═══ UTILS ═══
function mostrarToast(msg, tipo) {
  const el = document.getElementById("toastSistema");
  if (!el) return console.log(msg);
  el.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3 show`;
  document.getElementById("toastMensaje").textContent = msg;
  setTimeout(() => el.classList.remove("show"), 3000);
}

// ═══ CUSTOM CONFIRM MODAL ═══
function mostrarConfirmacion(mensaje, callback) {
  let modalEl = document.getElementById("modalCustomConfirm");
  if (!modalEl) {
    const html = `
        <div class="modal fade" id="modalCustomConfirm" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bx bx-question-mark fw-bold" style="font-size: 3.5rem; color: #696cff; background: #e7e7ff; border-radius: 50%; padding: 10px;"></i>
                        </div>
                        <h5 class="mb-3 fw-bold text-dark" id="customConfirmMessage">¿Estás seguro?</h5>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-label-secondary w-50 fw-bold rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary w-50 fw-bold rounded-pill" id="btnCustomConfirmOk">Sí, continuar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.insertAdjacentHTML("beforeend", html);
    modalEl = document.getElementById("modalCustomConfirm");
  }

  document.getElementById("customConfirmMessage").textContent = mensaje;
  const modal = new bootstrap.Modal(modalEl);

  const btnOk = document.getElementById("btnCustomConfirmOk");
  btnOk.onclick = () => {
    modal.hide();
    callback();
  };

  modal.show();
}

// ═══ TIENDA / CARRITO ═══

function agregarAlCarrito(id, nombre, precio, stock) {
  let item = carrito.find((c) => c.id == id);
  if (item) {
    if (item.cantidad < stock) item.cantidad++;
    else mostrarToast("No hay más stock disponible", "warning");
  } else {
    carrito.push({ id, nombre, precio, cantidad: 1, stock });
  }
  renderCarrito();
}

function procesarCarrito() {
  const idOrden = document.getElementById("sel_anexar_orden").value;
  if (idOrden) {
    // Añadir a orden existente
    mostrarConfirmacion(`¿Añadir productos a la Orden #${idOrden}?`, () => {
      const btn = document.getElementById("btnVenta");
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Procesando...`;

      fetch(`${BASE_URL}/caja/dashboard/anexarproductos`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_orden: idOrden, productos: carrito }),
      })
        .then((r) => r.json())
        .then((res) => {
          if (res.success) {
            mostrarToast(res.message, "success");
            carrito = [];
            
            // Reset select
            $("#sel_anexar_orden").val("").trigger("change");
            
            renderCarrito();
            cargarOrdenes();
            btn.disabled = false;
            actualizarEtiquetaBoton();
          } else {
            mostrarToast(res.message, "danger");
            btn.disabled = false;
            actualizarEtiquetaBoton();
          }
        })
        .catch(() => {
          mostrarToast("Error al procesar. Intenta nuevamente", "danger");
          btn.disabled = false;
          actualizarEtiquetaBoton();
        });
    });
  } else {
    // Venta Directa
    abrirModalVenta();
  }
}

// ═══ VENTA DIRECTA (TIENDA) ═══
function abrirModalVenta() {
  if (!_cajaActivaId) return mostrarToast("Apertura caja primero", "danger");
  const total = carrito.reduce((s, c) => s + c.precio * c.cantidad, 0);
  document.getElementById("venta_total").textContent = "S/ " + total.toFixed(2);

  // Reset selection
  metodoVenta = "EFECTIVO";
  document
    .querySelectorAll("#modalVenta .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  document
    .querySelector(`#modalVenta .pay-method-btn[data-metodo="EFECTIVO"]`)
    .classList.add("selected");

  new bootstrap.Modal(document.getElementById("modalVenta")).show();
}

function selMetodoVenta(elem) {
  document
    .querySelectorAll("#modalVenta .pay-method-btn")
    .forEach((b) => b.classList.remove("selected"));
  elem.classList.add("selected");
  metodoVenta = elem.dataset.metodo;
}

async function confirmarVenta() {
  const btn = document.querySelector("#modalVenta .btn-primary");
  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Procesando...`;

  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/ventadirecta`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ productos: carrito, metodo_pago: metodoVenta }),
    });
    const r = await res.json();

    btn.disabled = false;
    btn.innerHTML = `<i class="bx bx-check me-1"></i>CONFIRMAR VENTA`;

    if (r.success) {
      bootstrap.Modal.getInstance(document.getElementById("modalVenta")).hide();
      mostrarToast(r.message, "success");

      // Imprimir ticket de venta directa
      if (r.id_orden) {
        imprimirTicket(
          `${BASE_URL}/public/generar_ticket.php?id=${r.id_orden}&t=${new Date().getTime()}`,
        );
      }

      carrito = [];
      
      // Reset select
      $("#sel_anexar_orden").val("").trigger("change");

      renderCarrito();
      cargarOrdenes();
    } else {
      mostrarToast(r.message, "danger");
    }
  } catch (e) {
    mostrarToast("Error de conexión", "danger");
    btn.disabled = false;
    btn.innerHTML = `<i class="bx bx-check me-1"></i>CONFIRMAR VENTA`;
  }
}

// Update button label based on selected order
function actualizarEtiquetaBoton() {
  const sel = document.getElementById("sel_anexar_orden");
  const btn = document.getElementById("btnVenta");
  if (!sel || !btn) return;
  const texto =
    sel.value && sel.value !== "" ? "Añadir al Lavado" : "Venta Directa";
  btn.innerHTML =
    `<i class="bx ${sel.value ? "bx-plus-circle" : "bx-credit-card"} me-1"></i>` +
    texto;
  if (sel.value && sel.value !== "") {
    btn.className = "btn btn-primary w-100 fw-bold rounded-pill shadow-sm";
  } else {
    btn.className = "btn btn-success w-100 fw-bold rounded-pill shadow-sm";
  }
}

// Listen for changes in the order dropdown
$(document).ready(function () {
  $(".select2-ordenes-activas").on("change", actualizarEtiquetaBoton);
  actualizarEtiquetaBoton();
});

function quitarDelCarrito(id) {
  let idx = carrito.findIndex((c) => c.id == id);
  if (idx > -1) {
    carrito[idx].cantidad--;
    if (carrito[idx].cantidad <= 0) carrito.splice(idx, 1);
  }
  renderCarrito();
}

function renderCarrito() {
  const total = carrito.reduce((s, c) => s + c.precio * c.cantidad, 0);
  const totalVal = total.toFixed(2);

  const containerAnexar = document.getElementById("containerAnexar");
  const containerTotal = document.getElementById("containerTotal");
  const btnVenta = document.getElementById("btnVenta");
  const listEl = document.getElementById("carritoLista");
  const emptyEl = document.getElementById("carritoEmpty");

  if (carrito.length === 0) {
    if (containerAnexar) containerAnexar.style.display = "none";
    if (containerTotal) containerTotal.style.display = "none";
    if (btnVenta) btnVenta.style.display = "none";
    if (emptyEl) emptyEl.style.display = "block";
    listEl.innerHTML = "";
    $("#sel_anexar_orden").val("").trigger("change");
  } else {
    if (containerAnexar) containerAnexar.style.display = "block";
    if (containerTotal) containerTotal.style.display = "block";
    if (btnVenta) btnVenta.style.display = "block";
    if (emptyEl) emptyEl.style.display = "none";

    listEl.innerHTML = carrito
      .map(
        (c) =>
          `<div class="cart-item py-2 border-bottom d-flex align-items-center" style="padding-left: 0; padding-right: 0;">
            <div class="flex-grow-1 pe-2">
                <span class="fw-bold text-dark" style="font-size:0.85rem; word-break:break-word;">
                    ${c.nombre}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-minus-circle text-danger cursor-pointer fs-4" onclick="quitarDelCarrito(${c.id})"></i>
                <b class="text-primary fs-5" style="min-width: 20px; text-align:center;">${c.cantidad}</b>
                <i class="bx bx-plus-circle text-success cursor-pointer fs-4" onclick="agregarAlCarrito(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', ${c.precio}, ${c.stock})"></i>
                
                <strong class="px-2 py-1 rounded border shadow-sm ms-1" style="width:75px; text-align:center; background:white; color:black; font-size: 0.8rem; font-weight: 800;">
                    S/ ${(c.precio * c.cantidad).toFixed(2)}
                </strong>

                <button class="btn btn-sm btn-danger border p-1 ms-1 text-white shadow-sm" onclick="eliminarDelCarrito(${c.id})" title="Eliminar" style="border-radius: 8px;">
                    <i class="bx bx-trash fs-6"></i>
                </button>
            </div>
        </div>`
      )
      .join("");
  }

  document.getElementById("carritoTotal").textContent = "S/ " + totalVal;
  document.getElementById("carritoCount").textContent = carrito.length;
  if (carrito.length) actualizarEtiquetaBoton();
}

function eliminarDelCarrito(id) {
  let idx = carrito.findIndex((c) => c.id == id);
  if (idx > -1) carrito.splice(idx, 1);
  renderCarrito();
  actualizarEtiquetaBoton();
}

function filtrarTienda() {
  const query = document.getElementById("buscadorTienda").value.toLowerCase().trim();
  const items = document.querySelectorAll(".prod-item");
  
  items.forEach((item) => {
    const nombre = item.textContent.toLowerCase();
    if (nombre.includes(query)) {
      item.style.setProperty("display", "flex", "important");
    } else {
      item.style.setProperty("display", "none", "important");
    }
  });

  // Mostrar mensaje si no hay resultados en el tab actual
  const tabs = ["tabDisponibles", "tabVencidos"];
  tabs.forEach(tabId => {
    const tabEl = document.getElementById(tabId);
    if(!tabEl) return;
    const visibleItems = tabEl.querySelectorAll(".prod-item:not([style*='display: none'])");
    let msg = tabEl.querySelector(".no-results-msg");
    
    if (visibleItems.length === 0 && query !== "") {
      if (!msg) {
        msg = document.createElement("div");
        msg.className = "text-center py-4 text-muted no-results-msg small";
        msg.innerHTML = ` No se encontraron productos`;
        tabEl.appendChild(msg);
      }
    } else if (msg) {
      msg.remove();
    }
  });
}
