<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* Estilos Generales */
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1rem !important; }
    .dataTables_info { text-align: right !important; margin-top: 1rem !important; color: #b0b0b0 !important; }
    
    /* Cards Promociones */
    .promo-card { transition: all 0.2s; border: 1px solid #e0e0e0; }
    .promo-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #696cff; }
    .discount-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; }

    /* WhatsApp Editor */
    .whatsapp-card { background: #e5ddd5; border-left: 5px solid #25d366; }
    .whatsapp-bubble { background: #dcf8c6; border-radius: 10px; padding: 10px 15px; position: relative; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
    .whatsapp-bubble::after { content: ''; position: absolute; top: 0; right: -10px; width: 0; height: 0; border: 10px solid transparent; border-top-color: #dcf8c6; border-right: 0; margin-top: 10px; margin-right: -10px; }
    .wa-variable { cursor: pointer; font-size: 0.8rem; }
    .wa-variable:hover { background-color: #e0e0e0; }
</style>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Campañas de Marketing</h4>
            <small class="text-muted">Gestiona descuentos y comunícate con tus clientes.</small>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="bx bx-plus me-1"></i> NUEVA PROMOCIÓN
        </button>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-8">
            <h6 class="text-muted fw-bold text-uppercase mb-3"><i class="bx bx-grid-alt me-1"></i> Campañas Recientes</h6>
            
            <div class="row row-cols-1 row-cols-md-2 g-3">
                <?php if(empty($recientes)): ?>
                    <div class="col-12">
                        <div class="alert alert-secondary text-center p-4">
                            <i class='bx bx-ghost fs-1 opacity-50'></i>
                            <p class="mt-2">No hay promociones registradas.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($recientes as $promo): 
                        $esPorcentaje = $promo['tipo_descuento'] === 'PORCENTAJE';
                        $valorShow = $esPorcentaje ? round($promo['valor']).'%' : 'S/'.number_format($promo['valor'],2);
                        $bgIcon = $esPorcentaje ? 'bg-label-info text-info' : 'bg-label-success text-success';
                        $estadoClass = $promo['estado'] == 1 ? 'success' : 'secondary';
                        $estadoText = $promo['estado'] == 1 ? 'ACTIVA' : 'INACTIVA';
                    ?>
                    <div class="col">
                        <div class="card promo-card h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-<?= $estadoClass ?>"><?= $estadoText ?></span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon p-0" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item btn-editar-card" href="javascript:void(0);" data-id="<?= $promo['id_promocion'] ?>"><i class="bx bx-edit me-2"></i> Editar</a></li>
                                            <li><a class="dropdown-item btn-eliminar-card text-danger" href="javascript:void(0);" data-id="<?= $promo['id_promocion'] ?>" data-nom="<?= $promo['nombre'] ?>"><i class="bx bx-trash me-2"></i> Eliminar</a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="discount-circle <?= $bgIcon ?> me-3 shadow-sm">
                                        <?= $valorShow ?>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;" title="<?= $promo['nombre'] ?>"><?= $promo['nombre'] ?></h5>
                                        <small class="text-muted">
                                            <i class='bx bx-calendar'></i>  Hasta: <?= date('d/m', strtotime($promo['fecha_fin'])) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted fst-italic">
                                        <?= $promo['solo_una_vez_por_cliente'] ? '<i class="bx bx-user-check text-warning"></i> 1 por cliente' : '<i class="bx bx-infinite text-primary"></i> Ilimitado' ?>
                                    </small>
                                    <button class="btn btn-sm btn-outline-primary btn-editar-card" data-id="<?= $promo['id_promocion'] ?>">Ver detalles</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <h6 class="text-muted fw-bold text-uppercase mb-3"><i class="bx bxl-whatsapp me-1 text-success "></i> Difusión Masiva</h6>
            
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0 text-white"><i class="bx bxs-paper-plane me-1"></i> Enviar a Clientes</h6>
                </div>
                <div class="card-body mt-3">
                    
                    <form id="formEnviarWhatsApp">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">1. Selecciona Promoción Activa</label>
                            <select class="form-select form-select-sm" id="selectPromoWS" name="id_promocion">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($activas as $act): ?>
                                    <option value="<?= $act['id_promocion'] ?>" 
                                            data-nombre="<?= $act['nombre'] ?>"
                                            data-valor="<?= $act['tipo_descuento']=='PORCENTAJE'? round($act['valor']).'%' : 'S/'.$act['valor'] ?>"
                                            data-fin="<?= date('d/m/Y', strtotime($act['fecha_fin'])) ?>">
                                        <?= $act['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">2. Vista Previa del Mensaje</label>
                            <div class="whatsapp-bubble mb-2">
                                <p class="mb-0 small text-dark" id="previewMensaje">
                                    Hola <strong>{{nombre}}</strong> 👋,<br>
                                    ¡Aprovecha nuestra promo <strong>...</strong>!<br>
                                    Obtén <strong>...</strong> de descuento en tu próximo lavado 🚗.<br>
                                    Válido hasta: <strong>...</strong>
                                </p>
                                <div class="text-end mt-1">
                                    <small class="text-muted" style="font-size:0.6rem">12:30 pm <i class='bx bx-check-double text-primary'></i></small>
                                </div>
                            </div>
                            <textarea class="form-control text-muted" name="mensaje" id="textoMensaje" rows="4" style="font-size:0.85rem;">Hola {{nombre}} 👋, Aprovecha nuestra promo {{promocion}}! Obten {{valor}} de descuento en tu próximo lavado. Válido hasta: {{fechafin}}</textarea>
                            <div class="form-text" style="font-size: 0.7rem;">Variables: {{nombre}}, {{promocion}}, {{valor}}, {{fechafin}}</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bxl-whatsapp me-1"></i> ENVIAR A TODOS
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-list-ul me-1"></i> Historial General</h5>
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light"><i class="bx bx-search"></i></span>
                    <input type="text" id="buscadorGlobal" class="form-control bg-light" placeholder="Buscar...">
                </div>
                <button class="btn btn-sm btn-outline-success" id="btnExportar"><i class="bx bxs-file-export"></i></button>
            </div>
        </div>
        <div class="table-responsive text-nowrap px-3">
            <table class="table table-hover w-100 my-3" id="tablaPromociones">
                <thead class="bg-primary">
                    <tr>
                        <th class="d-none">ID</th>
                        <th class="d-none">DatosOcultos</th>
                        <th style="color: white">Campaña</th>
                        <th style="color: white">Descuento</th>
                        <th style="color: white">Vigencia</th>
                        <th class="text-center" style="color: white">Estado</th>
                        <th class="text-center" style="color: white">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

  </div>
  <div class="content-backdrop fade"></div>
</div>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>
<script src="<?= BASE_URL ?>/public/js/admin/promocion.js"></script>

<?php require VIEW_PATH . '/partials/promocion/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>
