<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel">
  
  <div class="offcanvas-header bg-dark text-white">
    <h5 id="offcanvasDarkLabel" class="offcanvas-title text-white">
        <i class="bx bx-filter-alt me-2"></i> Filtros Avanzados
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body">
    
    <div class="mb-4">
        <label for="buscadorGlobal" class="form-label fw-bold text-dark">Buscador General</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-light"><i class="bx bx-search"></i></span>
            <input type="text" id="buscadorGlobal" class="form-control bg-light" placeholder="Nombre, DNI..." autofocus>
        </div>
        <div class="form-text">Busca en tiempo real mientras escribes.</div>
    </div>

    <hr class="my-4">

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