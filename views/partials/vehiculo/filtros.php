<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offLabel">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title text-white"><i class="bx bx-filter-alt me-2"></i> Filtros</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">

    <h6 class="fw-bold mb-3 text-dark">Fecha de Registro</h6>
    <div class="mb-3"><label>Desde:</label><input type="date" id="filtroFechaInicio" class="form-control"></div>
    <div class="mb-3"><label>Hasta:</label><input type="date" id="filtroFechaFin" class="form-control"></div>

    <div class="d-grid gap-2 mt-5">
        <button type="button" class="btn btn-primary btn-lg" id="btnAplicarFiltros"><i class="bx bx-check"></i> Listo</button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros"><i class="bx bx-refresh"></i> Limpiar</button>
    </div>
  </div>
</div>