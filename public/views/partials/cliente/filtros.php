<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel">
  
  <div class="offcanvas-header bg-dark text-white">
    <h5 id="offcanvasDarkLabel" class="offcanvas-title text-white">
        <i class="bx bx-filter-alt me-2"></i> Filtros Avanzados
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body">
    


    <h6 class="fw-bold mb-3 text-dark">Rango de Fechas</h6>
    
    <div class="mb-3">
        <label for="filtroFechaInicio" class="form-label">Desde:</label>
        <input type="date" id="filtroFechaInicio" class="form-control">
    </div>
    
    <div class="mb-4">
        <label for="filtroFechaFin" class="form-label">Hasta:</label>
        <input type="date" id="filtroFechaFin" class="form-control">
    </div>

    <div class="d-grid gap-2 mt-auto">
        <button type="button" class="btn btn-primary btn-lg" id="btnAplicarFiltros">
            <i class="bx bx-check"></i> Listo / Cerrar
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
            <i class="bx bx-refresh"></i> Limpiar Todo
        </button>
    </div>

  </div>
</div>