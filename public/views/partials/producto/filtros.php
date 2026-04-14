<!-- Extra Offcanvas Filtro -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltroProductos">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title text-white"><i class="bx bx-filter-alt me-2"></i> Filtros</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
        <div class="mb-3">
        <label for="filtroFechaInicio" class="form-label">Desde:</label>
        <input type="date" id="filtroFechaInicio" class="form-control">
    </div>
    <div class="mb-4">
        <label for="filtroFechaFin" class="form-label">Hasta:</label>
        <input type="date" id="filtroFechaFin" class="form-control">
    </div>
    <div class="d-grid gap-2 mt-auto">
        <button type="button" class="btn btn-primary" id="btnAplicarFiltros">
            <i class="bx bx-check"></i> Aplicar
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
            <i class="bx bx-refresh"></i> Limpiar Todo
        </button>
    </div>
  </div>
</div>
