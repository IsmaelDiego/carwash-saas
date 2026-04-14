<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offLabel">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title text-white"><i class="bx bx-filter-alt me-2"></i> Filtros</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">

    <h6 class="fw-bold mb-3 text-dark">Periodo de Permisos</h6>
    <div class="mb-3">
        <label>Mes y Año:</label>
        <input type="month" id="filtroMesAnio" class="form-control">
    </div>

    <div class="d-grid gap-2 mt-5">
        <button type="button" class="btn btn-primary btn-lg" id="btnAplicarFiltros"><i class="bx bx-check"></i> Aplicar Filtro</button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros"><i class="bx bx-refresh"></i> Limpiar</button>
    </div>
  </div>
</div>
