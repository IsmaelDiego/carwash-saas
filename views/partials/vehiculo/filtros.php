<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel" data-bs-theme="light">
  <div class="offcanvas-header border-bottom">
    <h4 class="offcanvas-title fw-bold" id="offcanvasDarkLabel"><i class="bx bx-filter-alt me-1"></i> Filtros</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body my-auto">
    <h6 class="text-muted mb-3 fw-semibold text-uppercase" style="font-size: 0.75rem;">Búsqueda por coincidencias</h6>
    <div class="mb-4">
      <label for="buscadorGlobal" class="form-label">Placa, Dueño, Modelo, etc...</label>
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="bx bx-search"></i></span>
        <input type="text" id="buscadorGlobal" class="form-control" placeholder="Escriba para buscar..." />
      </div>
    </div>

    <hr class="my-4">

    <h6 class="text-muted mb-3 fw-semibold text-uppercase" style="font-size: 0.75rem;">Rango de Fechas</h6>
    <div class="row g-3 mb-4">
      <div class="col-6">
        <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
        <input type="date" id="filtroFechaInicio" class="form-control" />
      </div>
      <div class="col-6">
        <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
        <input type="date" id="filtroFechaFin" class="form-control" />
      </div>
    </div>

    <div class="d-grid gap-2 mt-5">
      <button type="button" class="btn btn-primary" data-bs-dismiss="offcanvas">
        Aplicar y Cerrar
      </button>
      <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
        <i class="bx bx-refresh me-1"></i> Limpiar Filtros
      </button>
    </div>
  </div>
</div>