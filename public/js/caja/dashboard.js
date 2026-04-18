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
  cargarRampas();
  // Iniciar cronómetros cada 30 segundos
  setInterval(actualizarCronometros, 30000);
  // Auto-refresco completo cada 60 segundos para evitar datos estáticos
  setInterval(() => {
    cargarOrdenes();
    cargarRampas();
  }, 60000);

  // --- AUTO-FORMATO DE PLACA ---
  const inputPlaca = document.getElementById("nv_placa");
  if (inputPlaca) {
    inputPlaca.setAttribute("maxlength", "7");
    inputPlaca.addEventListener("input", function (e) {
      let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
      let formatted = "";

      if (val.length > 0) {
        // Primeros 3: Alfanuméricos
        formatted = val.substring(0, 3);
        if (val.length > 3) {
          // Guion automático + siguientes 3 solo números
          let numbersPart = val.substring(3, 6).replace(/[^0-9]/g, "");
          formatted += "-" + numbersPart;
        }
      }
      e.target.value = formatted;
    });
  }
});

// ═══ CARGAR ÓRDENES ═══
async function cargarOrdenes(btn = null) {
  let icon = null;
  if (btn) {
    icon = btn.querySelector("i");
    if (icon) icon.classList.add("bx-spin");
    btn.disabled = true;
  }
  const mainCont = document.querySelector(".pos-layout");
  if (mainCont) mainCont.style.opacity = "0.5";

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
  } finally {
    if (btn) {
      if (icon) icon.classList.remove("bx-spin");
      btn.disabled = false;
    }
    if (mainCont) mainCont.style.opacity = "1";
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
    rawData = window.historialHoy ? window.historialHoy : [];
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
                                <th class="py-3 text-center">ACCIÓN</th>
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
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-icon btn-label-primary rounded-pill" onclick="verDetalleOrden(${o.id_orden})" title="Ver Detalle">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </td>
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
            btnAction = `<button class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm py-2 text-white mb-2" onclick="pasaraProceso(${o.id_orden})"><i class="bx bx-play-circle me-1 fs-5"></i>INICIAR LAVADO</button>
                         <button class="btn btn-outline-warning w-100 fw-bold rounded-pill shadow-sm py-2" onclick="abrirModalAdelantar(${o.id_orden})"><i class="bx bx-up-arrow-circle me-1 fs-5 align-middle"></i> ADELANTAR AL INICIO</button>`;
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
                            ${
                              o.prioridad_adelanto > 0
                                ? `<span class="badge bg-danger ms-2 position-relative" style="font-size:0.6rem" title="Quitar Prioridad" onclick="event.stopPropagation(); quitarPrioridad(${o.id_orden})">
                                <i class="bx bxs-zap me-1"></i>PRIORIDAD #${o.prioridad_adelanto} 
                                <i class="bx bx-x ms-1 border rounded-circle" style="cursor:pointer"></i>
                            </span>`
                                : ""
                            }
                        </div>
                        <div class="d-flex flex-column text-end">
                            <span class="badge ${badgeColors[est]} mb-1">${est.replace("_", " ")}</span>
                            ${o.rampa_numero ? `<span class="badge bg-label-info mb-1" style="font-size:0.7rem"><i class="bx bxs-car-wash me-1"></i>RAMPA ${o.rampa_numero}</span>` : ""}
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
  const form = document.getElementById("registrarcliente");
  if (form) {
    form.reset();
    document.getElementById("nombres").readOnly = false;
    document.getElementById("apellidos").readOnly = false;
    const fb = document.getElementById("dniFeedback");
    if (fb) fb.innerHTML = "";
  }
  new bootstrap.Modal(document.getElementById("modalRegistrar")).show();
}

document.getElementById("dni")?.addEventListener("keydown", function (e) {
  if (e.key === "Enter" || e.keyCode === 13) {
    e.preventDefault();
    document.getElementById("btnBuscarDni")?.click();
  }
});

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
      nombres: document.getElementById("nombres").value.trim(),
      apellidos: document.getElementById("apellidos").value.trim(),
      sexo: document.getElementById("sexo").value,
      telefono: document.getElementById("telefono").value.trim(),
    };
    if (!data.dni || !data.nombres)
      return mostrarToast("Documento y nombres requeridos", "warning");

    if (data.telefono) {
      if (!data.telefono.startsWith("9") || data.telefono.length !== 9) {
        return mostrarToast(
          "El teléfono debe empezar con el número 9 y tener 9 dígitos",
          "warning",
        );
      }
    }

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
  if (r.success) {
    cargarOrdenes();
    cargarRampas();
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

async function pasaraPorCobrar(id) {
  const res = await fetch(`${BASE_URL}/caja/dashboard/pasara_por_cobrar`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: id }),
  });
  const r = await res.json();
  if (r.success) {
    cargarOrdenes();
    cargarRampas();
  }
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
    cargarRampas();
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

  document
    .querySelectorAll(".service-selectable")
    .forEach((s) => s.classList.remove("selected"));
  document
    .querySelectorAll(".promo-option")
    .forEach((p) => p.classList.remove("selected"));
  document
    .querySelector(".promo-option[data-id='']")
    ?.classList.add("selected");

  const chkAnt = document.getElementById("chk_pago_anticipado");
  if (chkAnt) {
    chkAnt.checked = false;
    document.getElementById("panel_metodos_pago_anticipado").style.display =
      "none";
  }

  const lblTotal = document.getElementById("lbl_total_nueva_orden");
  if (lblTotal) lblTotal.textContent = "S/ 0.00";

  new bootstrap.Modal(document.getElementById("modalNuevaOrden")).show();
}

async function cargarVehiculosCliente(id) {
  const seccionFid = document.getElementById("seccion_fidelizacion");
  const camposNuevoVeh = document.getElementById("camposNuevoVehiculo");

  // Limpiar y ocultar campos de nuevo vehículo cada vez que se cambia de cliente
  if (camposNuevoVeh) {
    camposNuevoVeh.style.display = "none";
    document.getElementById("nv_placa").value = "";
    document.getElementById("nv_color").value = "";
    document.getElementById("nv_categoria").selectedIndex = 0;
  }

  if (!id) {
    if (seccionFid) seccionFid.style.display = "none";
    return;
  }
  const res = await fetch(
    `${BASE_URL}/caja/dashboard/getvehiculos?id_cliente=${id}`,
  );
  const data = await res.json();
  const sel = document.getElementById("sel_vehiculo_orden");
  sel.innerHTML =
    '<option value="" data-factor="1.0">-- Seleccionar --</option>' +
    (data.data || [])
      .map(
        (v) =>
          `<option value="${v.id_vehiculo}" data-factor="${v.factor_precio || 1.0}">${v.placa} (${v.categoria})</option>`,
      )
      .join("") +
    '<option value="NUEVO" data-factor="1.0">+ REGISTRAR NUEVO</option>';

  // --- FIDELIZACIÓN ---
  if (seccionFid && data.fidelizacion) {
    seccionFid.style.display = "block";
    const pts = parseInt(data.fidelizacion.puntos_acumulados) || 0;
    const canjeado = data.fidelizacion.ya_canjeo_temporada_actual == 1;

    const icon = document.getElementById("icon_pts_status");
    const titulo = document.getElementById("titulo_pts_status");
    const msg = document.getElementById("msg_pts_status");
    const badge = document.getElementById("badge_pts_count");
    const chkCanje = document.getElementById("chk_canjear_puntos");

    // Resetear estilos
    seccionFid.className = "mb-4 p-3 rounded-3 border";
    icon.className = "bx bxs-star fs-3";
    badge.className = "badge";
    badge.textContent = `${pts} pts`;
    chkCanje.checked = false;

    if (pts >= 10 && !canjeado) {
      // PREMIO DISPONIBLE
      seccionFid.classList.add("bg-label-warning", "border-warning");
      icon.classList.add("text-warning");
      badge.classList.add("bg-warning");
      titulo.textContent = "¡PREMIO DISPONIBLE!";
      msg.innerHTML =
        '<b class="text-dark">¡Este lavado puede ser GRATIS usando sus puntos!</b>';
      chkCanje.checked = true; // Auto-activar canje
    } else if (canjeado) {
      // YA CANJEADO
      seccionFid.classList.add("bg-light");
      icon.classList.add("text-secondary");
      badge.classList.add("bg-secondary");
      titulo.textContent = "TEMPORADA COMPLETADA";
      msg.textContent = "El cliente ya canjeó su premio en esta temporada.";
    } else {
      // FALTAN PUNTOS
      seccionFid.classList.add("bg-label-info", "border-info");
      icon.classList.add("text-info");
      badge.classList.add("bg-info");
      titulo.textContent = "ESTADO DE PUNTOS";
      const faltan = 10 - pts;
      msg.textContent = `Le faltan solo ${faltan} puntos para su próximo lavado GRATIS.`;
    }
  }

  // --- BLOQUEO DE PROMOS USADAS ---
  const usadas = (data.promos_usadas || []).map(String);
  let promoReseted = false;

  document.querySelectorAll(".promo-option").forEach((opt) => {
    const idPromo = opt.dataset.id;
    const isOnce = opt.dataset.once == "1";

    if (idPromo && isOnce && usadas.includes(String(idPromo))) {
      opt.style.display = "none";
      if (opt.classList.contains("selected")) {
        opt.classList.remove("selected");
        promoReseted = true;
      }
    } else {
      opt.style.display = "block";
    }
  });

  // Si la promo que estaba seleccionada se ocultó, seleccionar "Sin promoción"
  if (promoReseted) {
    const sinPromo = document.querySelector(".promo-option[data-id='']");
    if (sinPromo) sinPromo.classList.add("selected");
  }

  calcularTotalNuevaOrden();
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
  let totalBaseServicios = 0;
  document.querySelectorAll(".service-selectable.selected").forEach((s) => {
    totalBaseServicios += parseFloat(s.dataset.precio || 0);
  });

  // Determinar Factor de Precio según Categoría de Vehículo
  let factor = 1.0;
  const selVeh = document.getElementById("sel_vehiculo_orden");
  if (selVeh.value === "NUEVO") {
    const selCat = document.getElementById("nv_categoria");
    const optCat = selCat.options[selCat.selectedIndex];
    factor = parseFloat(optCat ? optCat.dataset.factor : 1.0);
  } else if (selVeh.value !== "") {
    const optVeh = selVeh.options[selVeh.selectedIndex];
    factor = parseFloat(optVeh ? optVeh.dataset.factor : 1.0);
  }

  const totalConFactor = totalBaseServicios * factor;

  let descPromo = 0;
  let descPuntos = 0;

  const promoNode = document.querySelector(".promo-option.selected");
  if (promoNode && promoNode.dataset.id && promoNode.dataset.id !== "") {
    const tipo = promoNode.dataset.tipo;
    const valor = parseFloat(promoNode.dataset.valor || 0);
    if (tipo === "PORCENTAJE") {
      descPromo = totalConFactor * (valor / 100);
    } else {
      descPromo = Math.min(valor, totalConFactor);
    }
  }

  // Canje de Puntos (Servicio Gratis)
  const chkPuntos = document.getElementById("chk_canjear_puntos");
  if (chkPuntos && chkPuntos.checked) {
    descPuntos = totalConFactor - descPromo;
  }

  const totalFinal = Math.max(0, totalConFactor - descPromo - descPuntos);
  const lbl = document.getElementById("lbl_total_nueva_orden");
  if (lbl) {
    if (factor !== 1.0 || descPromo > 0 || descPuntos > 0) {
      lbl.innerHTML = `<span class="text-secondary opacity-75 text-decoration-line-through fs-6 fw-normal me-2">S/ ${totalBaseServicios.toFixed(2)}</span>S/ ${totalFinal.toFixed(2)}`;
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
    const placa = document
      .getElementById("nv_placa")
      .value.trim()
      .toUpperCase();
    const regexPlaca = /^[A-Z0-9]{3}-[0-9]{3}$/;

    if (!regexPlaca.test(placa)) {
      return mostrarToast(
        "Formato de placa inválido. Debe ser: 3 letras/números, un guion y 3 números (ej: ABC-123)",
        "warning",
      );
    }

    const nvData = {
      id_cliente: idCliente,
      placa: placa,
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

  const chkCanje = document.getElementById("chk_canjear_puntos");

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
      canjear_puntos: chkCanje && chkCanje.checked,
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
        </div>`,
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
  const query = document
    .getElementById("buscadorTienda")
    .value.toLowerCase()
    .trim();
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
  tabs.forEach((tabId) => {
    const tabEl = document.getElementById(tabId);
    if (!tabEl) return;
    const visibleItems = tabEl.querySelectorAll(
      ".prod-item:not([style*='display: none'])",
    );
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

// ════════════════════════════════════════════════════════
// ═══ SISTEMA DE RAMPAS ═══
// ════════════════════════════════════════════════════════

let _rampasData = [];
let _operariosData = [];

async function cargarRampas(btn = null) {
  let icon = null;
  if (btn) {
    icon = btn.querySelector("i");
    if (icon) icon.classList.add("bx-spin");
    btn.disabled = true;
  }
  const contenedor = document.getElementById("contenedorRampas");
  if (contenedor) contenedor.style.opacity = "0.5";

  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/getrampas`);
    const data = await res.json();
    if (data.success) {
      _rampasData = data.rampas || [];
      _operariosData = data.operarios || [];
      renderizarRampas();
    }
  } catch (e) {
    console.warn("Error al cargar rampas:", e);
  } finally {
    if (btn) {
      if (icon) icon.classList.remove("bx-spin");
      btn.disabled = false;
    }
    if (contenedor) contenedor.style.opacity = "1";
  }
}

function renderizarRampas() {
  const contenedor = document.getElementById("contenedorRampas");
  if (!contenedor) return;

  if (!_rampasData.length) {
    contenedor.innerHTML = `<div class="col-12 text-center text-muted py-3">
      <i class="bx bx-info-circle me-1"></i>No hay rampas configuradas. El admin debe configurarlas en Ajustes.
    </div>`;
    return;
  }

  contenedor.innerHTML = _rampasData
    .map((r) => {
      const estadoConfig = {
        ACTIVA: {
          cls: "success",
          icon: "bx-check-circle",
          label: "ACTIVA",
          bg: "#e8f5e9",
          border: "#4caf50",
        },
        DESCANSO: {
          cls: "warning",
          icon: "bx-coffee",
          label: "DESCANSO",
          bg: "#fff8e1",
          border: "#ffab00",
        },
        INACTIVA: {
          cls: "danger",
          icon: "bx-x-circle",
          label: "INACTIVA",
          bg: "#ffeaea",
          border: "#ff3e1d",
        },
      };
      const ec = estadoConfig[r.estado] || estadoConfig["INACTIVA"];
      const operadorNombre = r.operador_nombre || "— Sin operario —";
      const tieneOrden = r.orden_activa > 0;

      return `
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid ${ec.border} !important; background: ${ec.bg}; border-radius: 12px; cursor:pointer;"
           onclick="abrirModalRampa(${r.id_rampa})">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="fw-bold mb-0">Rampa ${r.numero}</h6>
            <span class="badge bg-${ec.cls}" style="font-size:0.65rem">${ec.label}</span>
          </div>
          <div class="small text-muted mb-1">
            <i class="bx bx-user me-1"></i>${operadorNombre}
          </div>
          ${tieneOrden ? `<div class="small text-primary fw-bold"><i class="bx bx-car me-1"></i>En proceso</div>` : ""}
          ${r.proximo_estado ? `<div class="badge bg-label-danger py-1 px-2 mt-2" style="font-size:0.6rem; width:100%; white-space: normal;"><i class="bx bx-error-circle me-1"></i>CIERRE DIFERIDO: ${r.proximo_estado}</div>` : ""}
          ${r.motivo_estado ? `<div class="small text-muted fst-italic mt-1" style="font-size:0.7rem"><i class="bx bx-info-circle me-1"></i>${r.motivo_estado}</div>` : ""}
          <div class="mt-2 text-center">
            <small class="text-muted" style="font-size:0.65rem"><i class="bx bx-pencil me-1"></i>Click para gestionar</small>
          </div>
        </div>
      </div>
    </div>`;
    })
    .join("");
}

function abrirModalRampa(id_rampa) {
  const rampa = _rampasData.find((r) => r.id_rampa == id_rampa);
  if (!rampa) return;

  document.getElementById("gr_id_rampa").value = rampa.id_rampa;
  document.getElementById("gr_numero").textContent = rampa.numero;
  document.getElementById("gr_estado").value = rampa.estado || "ACTIVA";

  // Poblar operarios
  const sel = document.getElementById("gr_operador");
  // Obtener IDs de operarios ya ocupados en otras rampas
  const ocupados = _rampasData
    .filter((r) => r.id_rampa != rampa.id_rampa && r.id_operador)
    .map((r) => String(r.id_operador));

  sel.innerHTML =
    '<option value="">— Sin asignar —</option>' +
    _operariosData
      .map((o) => {
        const isTaken = ocupados.includes(String(o.id_usuario));
        const isCurrent = rampa.id_operador == o.id_usuario;
        return `<option value="${o.id_usuario}" 
                  ${isCurrent ? "selected" : ""} 
                  ${isTaken ? "disabled" : ""}>
                    ${o.nombres} ${isTaken ? "(Ocupado en otra rampa)" : ""}
                  </option>`;
      })
      .join("");

  // Marcar estado activo visualmente
  document.querySelectorAll(".rampa-estado-btn").forEach((btn) => {
    const isActive = btn.dataset.estado === rampa.estado;
    btn.style.background = isActive ? "#696cff" : "";
    btn.style.color = isActive ? "#fff" : "";
    btn.style.borderColor = isActive ? "#696cff" : "#dee2e6";
  });

  // Mostrar/ocultar campo motivo
  const mc = document.getElementById("gr_motivo_container");
  mc.style.display = rampa.estado !== "ACTIVA" ? "block" : "none";
  if (rampa.motivo_estado) {
    document.getElementById("gr_motivo").value = rampa.motivo_estado;
  }

  // Mostrar aviso de cierre diferido en el modal
  const aviso = document.getElementById("avisoCierreDiferido");
  if (aviso) {
    if (rampa.proximo_estado) {
      aviso.innerHTML = `
        <div class="alert alert-danger p-2 mb-2 d-flex justify-content-between align-items-center" style="font-size:0.75rem">
          <span><i class="bx bx-time me-1"></i>Cierre programado a: <b>${rampa.proximo_estado}</b></span>
          <button type="button" class="btn btn-xs btn-outline-danger border-0" onclick="cancelarCierreDiferido(${rampa.id_rampa})"><i class="bx bx-trash"></i></button>
        </div>`;
      aviso.style.display = "block";
    } else {
      aviso.style.display = "none";
    }
  }

  new bootstrap.Modal(document.getElementById("modalGestionRampa")).show();
}

let _ordenQuitarPrio = 0;
async function quitarPrioridad(id) {
  _ordenQuitarPrio = id;
  document.getElementById("quitar_prio_id").textContent = id;
  new bootstrap.Modal(document.getElementById("modalQuitarPrioridad")).show();
}

async function confirmarQuitarPrioridad() {
  const res = await fetch(`${BASE_URL}/caja/dashboard/quitar_prioridad`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_orden: _ordenQuitarPrio }),
  });
  const r = await res.json();
  if (r.success) {
    bootstrap.Modal.getInstance(
      document.getElementById("modalQuitarPrioridad"),
    ).hide();
    cargarOrdenes();
  }
  mostrarToast(r.message, r.success ? "success" : "danger");
}

async function cancelarCierreDiferido(id) {
  const res = await fetch(`${BASE_URL}/caja/dashboard/actualizarrampa`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_rampa: id, estado: "ACTIVA" }), // Volver a activar limpia el diferido
  });
  const r = await res.json();
  if (r.success) {
    cargarRampas();
    bootstrap.Modal.getInstance(
      document.getElementById("modalGestionRampa"),
    ).hide();
  }
  mostrarToast(
    r.success ? "Cierre programado cancelado" : r.message,
    r.success ? "success" : "danger",
  );
}

function selEstadoRampa(el) {
  const estado = el.dataset.estado;

  document.querySelectorAll(".rampa-estado-btn").forEach((btn) => {
    btn.style.background = "";
    btn.style.color = "";
    btn.style.borderColor = "#dee2e6";
  });
  el.style.background = "#696cff";
  el.style.color = "#fff";
  el.style.borderColor = "#696cff";

  document.getElementById("gr_estado").value = estado;

  // Lógica: Inactiva quita operador, descanso lo mantiene
  if (estado === "INACTIVA") {
    document.getElementById("gr_operador").value = "";
  }

  // Mostrar motivo si no es activa
  const mc = document.getElementById("gr_motivo_container");
  mc.style.display = estado !== "ACTIVA" ? "block" : "none";
}

async function confirmarGestionRampa() {
  const id_rampa = document.getElementById("gr_id_rampa").value;
  const estado = document.getElementById("gr_estado").value;
  const id_operador = document.getElementById("gr_operador").value;
  const motivo = document.getElementById("gr_motivo")?.value || null;

  if (!id_rampa || !estado) return mostrarToast("Datos incompletos", "warning");

  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/actualizarrampa`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_rampa,
        estado,
        id_operador: id_operador || null,
        motivo,
      }),
    });
    const r = await res.json();
    if (r.success) {
      bootstrap.Modal.getInstance(
        document.getElementById("modalGestionRampa"),
      ).hide();
      mostrarToast(r.message, "success");
      cargarRampas();
      cargarOrdenes(); // Actualizar ordenes porque puede haber avanzado la cola
    } else {
      mostrarToast(r.message, "danger");
    }
  } catch (e) {
    mostrarToast("Error de conexión", "danger");
  }
}

// ════════════════════════════════════════════════════════
// ═══ ADELANTAR ORDEN EN COLA ═══
// ════════════════════════════════════════════════════════

let _ordenAdelantando = null;

function abrirModalAdelantar(id_orden) {
  _ordenAdelantando = id_orden;
  document.getElementById("adelantar_id").textContent = id_orden;
  new bootstrap.Modal(document.getElementById("modalAdelantar")).show();
}

async function confirmarAdelantar() {
  if (!_ordenAdelantando) return;

  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/adelantarorden`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id_orden: _ordenAdelantando }),
    });
    const r = await res.json();
    if (r.success) {
      bootstrap.Modal.getInstance(
        document.getElementById("modalAdelantar"),
      ).hide();
      mostrarToast(r.message, "success");
      cargarOrdenes();
      cargarRampas();
    } else {
      mostrarToast(r.message, "danger");
    }
  } catch (e) {
    mostrarToast("Error de conexión", "danger");
  }
  _ordenAdelantando = null;
}

// Cargar rampas al iniciar
document.addEventListener("DOMContentLoaded", () => {
  cargarRampas();
});

// Solicitud de apertura al Admin
async function solicitarAperturaCaja() {
  const btn = document.getElementById("btnSolicitarCajaVirtual");
  if (!btn) return;
  const original = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="bx bx-loader-alt bx-spin me-1"></i> Enviando solicitud...`;

  try {
    const res = await fetch(`${BASE_URL}/caja/dashboard/solicitar_apertura`, {
      method: "POST",
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById("msjCajaVirtualEstado").innerHTML =
        `<span class="badge bg-success p-2 fs-6 mt-3"><i class="bx bx-check-double me-1"></i> SOLICITUD ENVIADA CON ÉXITO</span><br><br>Por favor, espera a que el Administrador responda.`;
      btn.style.display = "none";
    } else {
      mostrarToast(data.message, "danger");
      btn.disabled = false;
      btn.innerHTML = original;
    }
  } catch (e) {
    mostrarToast("Error de conexión", "danger");
    btn.disabled = false;
    btn.innerHTML = original;
  }
}

async function verDetalleOrden(id) {
  try {
    const res = await fetch(
      `${BASE_URL}/caja/dashboard/getdetalle?id_orden=${id}`,
    );
    const data = await res.json();

    if (!data.success) return mostrarToast(data.message, "danger");

    const o = data.orden;
    document.getElementById("det_id_orden").textContent = o.id_orden;
    document.getElementById("det_cliente").textContent =
      `${o.nombres} ${o.apellidos}`;
    document.getElementById("det_vehiculo").textContent =
      `${o.placa || "S/P"} (${o.categoria || "S/CAT"})`;

    const sub =
      parseFloat(o.total_servicios || 0) + parseFloat(o.total_productos || 0);
    const desc =
      parseFloat(o.descuento_promo || 0) + parseFloat(o.descuento_puntos || 0);
    const total = parseFloat(o.total_final || 0);

    document.getElementById("det_subtotal").textContent =
      `S/ ${sub.toFixed(2)}`;
    document.getElementById("det_descuento").textContent =
      `- S/ ${desc.toFixed(2)}`;
    document.getElementById("det_total").textContent = `S/ ${total.toFixed(2)}`;

    const lista = document.getElementById("det_lista_servicios");
    lista.innerHTML = (data.detalles || [])
      .map(
        (d) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom border-light">
                <div>
                    <div class="fw-bold small text-dark">${d.nombre_item}</div>
                    <div class="text-muted" style="font-size:0.7rem">Cant: ${d.cantidad}</div>
                </div>
                <div class="fw-bold text-dark small">S/ ${parseFloat(d.subtotal).toFixed(2)}</div>
            </div>
        `,
      )
      .join("");

    // Configurar botón de reimpresión
    const btnPrint = document.getElementById("btnReimprimirDetalle");
    if (btnPrint) {
      btnPrint.onclick = () => {
        window.open(
          `${BASE_URL}/public/generar_ticket.php?id_orden=${id}`,
          "_blank",
          "width=400,height=600",
        );
      };
    }

    new bootstrap.Modal(document.getElementById("modalDetalleOrden")).show();
  } catch (e) {
    console.error(e);
    mostrarToast("Error al obtener detalles", "danger");
  }
}

// Sobreescribir mostrarToast para usar el sistema de notificaciones disponible
function mostrarToast(mensaje, tipo = "success") {
  console.log("Notificación (" + tipo + "): " + mensaje);

  // 1. Intentar llamar al padre (Tunnel Layout / mFrame)
  if (
    window.parent &&
    window.parent !== window &&
    typeof window.parent.mostrarToast === "function"
  ) {
    window.parent.mostrarToast(mensaje, tipo);
    return;
  }

  // 2. Intentar usar SweetAlert2 si está disponible localmente
  if (typeof Swal !== "undefined") {
    Swal.fire({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      icon: tipo === "danger" || tipo === "error" ? "error" : tipo,
      title: mensaje,
    });
    return;
  }

  // 3. Intentar usar el Toast de Bootstrap 5 nativo (#toastSistema)
  const toastEl = document.getElementById("toastSistema");
  if (toastEl && typeof bootstrap !== "undefined") {
    const toastMsj = document.getElementById("toastMensaje");
    if (toastMsj) toastMsj.textContent = mensaje;

    // Ajustar color
    toastEl.classList.remove(
      "bg-success",
      "bg-danger",
      "bg-warning",
      "bg-info",
      "bg-primary",
    );
    const colorMap = {
      success: "bg-success",
      danger: "bg-danger",
      warning: "bg-warning",
      info: "bg-info",
      error: "bg-danger",
    };
    toastEl.classList.add(colorMap[tipo] || "bg-primary");

    try {
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
      return;
    } catch (e) {
      console.error("Error BS Toast:", e);
    }
  }

  // 4. Fallback final (Alert) solo si es crítico
  if (tipo === "danger" || tipo === "error" || tipo === "warning") {
    alert(mensaje);
  }
}
