<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }
</style>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">
    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header border-bottom mb-3">
          <i class="bx bx-category text-primary me-1"></i> CATEGORÍAS DE VEHÍCULOS
        </h5>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <nav aria-label="breadcrumb" class="me-auto">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/vehiculo">Vehículos</a></li>
                  <li class="breadcrumb-item active text-primary">Categorías</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group" style="width: 240px;">
                    <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar categoría..." autocomplete="off">
                    <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                </div>
                
                <button type="button" class="btn btn-primary shadow-sm" onclick="CategoriaModule.abrirModalRegistro()">
                  <i class="bx bx-plus me-1"></i> Nueva Categoría
                </button>
            </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="table-responsive text-nowrap px-3">
        <table class="table table-hover w-100" id="tbCategorias">
          <thead class="bg-primary">
            <tr>
              <th class="d-none">ID</th>
              <th style="color: #f0f0f0;">Nombre Categoría</th>
              <th style="color: #f0f0f0;" class="text-center">Factor Precio</th>
              <th style="color: #f0f0f0;" class="text-center">Factor Tiempo</th>
              <th class="text-center" style="color: #f0f0f0;">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="content-backdrop fade"></div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalFormCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <!-- Header Premium -->
            <div class="modal-header bg-primary text-white px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-category fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0" id="modalTitleCategoria">Nueva Categoría</h5>
                        <small class="text-white-50">Complete los datos de la categoría</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formCategoria" onsubmit="return false;">
                <div class="modal-body p-4">
                    <input type="hidden" id="cat_id" name="id_categoria">
                    
                    <div class="p-3 rounded-3 border mb-4" style="background-color: rgba(105, 108, 255, 0.04);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-info-circle fs-5 me-2 text-primary"></i>
                            <span class="fw-bold text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Información Principal</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Nombre de Categoría <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cat_nombre" name="nombre" placeholder="Ej. Auto Sedan, Camioneta SUV..." required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 rounded-3 border" style="background-color: rgba(255, 171, 0, 0.04);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-slider fs-5 me-2 text-warning"></i>
                            <span class="fw-bold text-warning text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Multiplicadores de Tarifa</span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-muted">Factor Precio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bx bx-dollar text-success"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0" id="cat_factor_precio" name="factor_precio" value="1.00" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(1.00 = 100% costo)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Factor Tiempo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bx bx-time-five text-info"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0" id="cat_factor_tiempo" name="factor_tiempo" value="1.00" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(1.00 = 100% tiempo)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-white fw-bold border text-muted shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm" id="btnGuardarCategoria"><i class="bx bx-save me-1"></i> Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Categoría -->
<div class="modal fade" id="modalEliminarCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <div class="mb-3">
                    <div class="bg-label-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bx bx-error text-danger fs-1"></i>
                    </div>
                </div>
                <h4 class="mb-2 fw-bold">¿Eliminar Categoría?</h4>
                <p class="text-muted mb-4 text-wrap">
                    Esta acción no se puede deshacer. Los vehículos que usen esta categoría podrían perder temporalmente sus métricas de precio.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminacion">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>
<script src="<?= BASE_URL ?>/public/js/admin/categoria_vehiculo.js"></script>

