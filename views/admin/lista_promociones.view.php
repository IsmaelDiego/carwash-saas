<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* Estilos Generales Premium */
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1rem !important; }
    .dataTables_info { text-align: right !important; margin-top: 1rem !important; color: #b0b0b0 !important; }
    
    /* Stats Cards */
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Cards Promociones Premium */
    .promo-card { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .promo-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); 
    }
    .discount-circle { 
        width: 60px; 
        height: 60px; 
        border-radius: 15px; 
        display: flex; 
        flex-direction: column;
        align-items: center; 
        justify-content: center; 
        font-weight: 800; 
        font-size: 1.2rem;
        line-height: 1;
    }

    /* WhatsApp UI Premium */
    .whatsapp-container {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e0e0e0;
    }
    .whatsapp-header { background: #075e54; color: white; padding: 15px; }
    .whatsapp-body { 
        background-color: #e5ddd5;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-repeat: repeat;
        padding: 20px;
    }
    .whatsapp-bubble { 
        background: #fff; 
        border-radius: 0 15px 15px 15px; 
        padding: 12px 15px; 
        position: relative; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        max-width: 90%;
        margin-left: 10px;
    }
    .whatsapp-bubble::before {
        content: "";
        position: absolute;
        top: 0;
        left: -10px;
        width: 0;
        height: 0;
        border-top: 0px solid transparent;
        border-bottom: 15px solid transparent;
        border-right: 15px solid #fff;
    }
    
    .btn-massive {
        background: #25d366;
        color: white;
        border: none;
        border-radius: 15px;
        padding: 12px;
        font-weight: bold;
        transition: all 0.3s;
    }
    .btn-massive:hover {
        background: #128c7e;
        transform: scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(37, 211, 102, 0.3);
    }

    /* Switch Estado Custom */
    .switch-estado, .form-check-input {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-input:checked {
        background-color: #25d366 !important;
        border-color: #25d366 !important;
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.4);
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3 w-100">
            <div>
                <h4 class="fw-bold mb-0 text-primary">Marketing & Fidelización</h4>
                <small class="text-muted">Gestiona tus campañas y comunícate directamente con tus clientes.</small>
            </div>
            <div class="d-flex flex-nowrap gap-2 align-items-center">
                <button class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="bx bx-plus-circle me-1"></i> NUEVA CAMPAÑA
                </button>
                <button class="btn btn-dark rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalReportesPromocion">
                    <i class="bx bx-bar-chart-alt-2 me-1"></i> Centro de Reportes BI
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-4" id="statsContainer">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100 bg-white border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-label-primary"><i class="bx bxs-megaphone text-primary"></i></div>
                        <div>
                            <div class="fs-4 fw-bold text-dark" id="stat_total">0</div>
                            <small class="text-muted fw-medium">Campañas</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100 bg-white border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-label-success"><i class="bx bx-check-double text-success"></i></div>
                        <div>
                            <div class="fs-4 fw-bold text-dark" id="stat_activas">0</div>
                            <small class="text-muted fw-medium">Activas hoy</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100 bg-white border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-label-warning"><i class="bx bx-timer text-warning"></i></div>
                        <div>
                            <div class="fs-4 fw-bold text-dark" id="stat_proximas">0</div>
                            <small class="text-muted fw-medium">Por vencer</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100 bg-white border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-label-info"><i class="bx bx-paper-plane text-info"></i></div>
                        <div>
                            <div class="fs-4 fw-bold text-dark" id="stat_difusiones">0</div>
                            <small class="text-muted fw-medium">Difusiones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h6 class="text-muted fw-bold text-uppercase mb-0"><i class="bx bx-grid-alt me-1"></i> Campañas Destacadas (Top 4)</h6>
                    <a href="javascript:void(0);" class="text-primary small fw-bold text-uppercase" id="scrolToTable">Ver historial completo <i class="bx bx-chevron-down"></i></a>
                </div>

                <div class="row row-cols-1 row-cols-md-2 g-4" id="cardsContainer">
                    <!-- Dinámico vía JS -->
                </div>
            </div>

            <div class="col-lg-4">
                <div class="whatsapp-container shadow-lg h-100 bg-white">
                    <div class="whatsapp-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-left-arrow-alt me-2"></i>
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-white text-success fw-bold">W</span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white fw-bold">Difusión SaaS</h6>
                                <small class="text-white opacity-75" style="font-size: 0.65rem;">WhatsApp Marketing</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <i class="bx bx-video"></i>
                            <i class="bx bx-phone"></i>
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </div>
                    </div>

                    <div class="whatsapp-body d-flex flex-column" style="min-height: 520px;">
                        <div class="text-center mb-4">
                            <span class="badge bg-label-secondary text-uppercase py-1 px-3" style="font-size: 0.6rem;">HOY</span>
                        </div>

                        <div class="whatsapp-bubble mb-4">
                            <p class="mb-0 small text-dark" id="previewMensaje" style="line-height: 1.5;">
                                Hola <strong>Cliente</strong> 👋,<br><br>
                                ¡Selecciona una promoción activa para generar una difusión increíble! 🚀
                            </p>
                            <div class="text-end mt-1">
                                <small class="text-muted" style="font-size:0.6rem">Justo ahora <i class='bx bx-check-double text-primary'></i></small>
                            </div>
                        </div>

                        <form id="formEnviarWhatsApp" class="bg-white p-3 rounded-4 shadow-sm mt-auto border mx-1 mb-1">
                            <div class="mb-3 ">
                                <label class="form-label small fw-bold text-uppercase text-primary" style="font-size: 0.65rem;">Campaña Activa</label>
                                <select class="form-select border bg-white fw-bold" id="selectPromoWS" name="id_promocion" required>
                                    <option value="">-- Seleccionar --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-primary" style="font-size: 0.65rem;">Mensaje</label>
                                <textarea class="form-control bg-white border" name="mensaje" id="textoMensaje" rows="3" style="font-size:0.8rem;" placeholder="Escribe tu mensaje..."></textarea>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <span class="badge bg-label-primary wa-variable cursor-pointer py-1 px-2" style="font-size: 0.6rem;" data-var="{{nombre}}">Nombre</span>
                                    <span class="badge bg-label-primary wa-variable cursor-pointer py-1 px-2" style="font-size: 0.6rem;" data-var="{{promocion}}">Promo</span>
                                    <span class="badge bg-label-primary wa-variable cursor-pointer py-1 px-2" style="font-size: 0.6rem;" data-var="{{valor}}">Valor</span>
                                    <span class="badge bg-label-primary wa-variable cursor-pointer py-1 px-2" style="font-size: 0.6rem;" data-var="{{fechafin}}">Fecha</span>
                                </div>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-massive btn-outline-success">
                                    <i class="bx bxl-whatsapp me-2 fs-5"></i> ENVIAR A CLIENTES
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-list-ul me-1"></i> Historial General</h5>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 240px;">
                        <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar campaña..." autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tablaPromociones">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="d-none" style="color: #f0f0f0;">ID</th>
                            <th class="d-none" style="color: #f0f0f0;">DatosOcultos</th>
                            <th class="fw-bold" style="color: #f0f0f0;">Campaña</th>
                            <th class="fw-bold" style="color: #f0f0f0;">Descuento</th>
                            <th class="fw-bold text-center" style="color: #f0f0f0;">Vigencia</th>
                            <th class="text-center fw-bold" style="color: #f0f0f0;">Estado</th>
                            <th class="text-center fw-bold" style="color: #f0f0f0;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/js/admin/promocion.js"></script>

<?php require VIEW_PATH . '/partials/promocion/modals.php'; ?>
<?php require VIEW_PATH . '/partials/promocion/modal_reporte.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>