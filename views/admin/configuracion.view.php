<?php require VIEW_PATH . '/layouts/header.view.php'; ?>
<style>
    .config-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        transition: all 0.3s;
    }

    .config-card:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    }

    .config-card .card-header {
        background: linear-gradient(135deg, #1e2035, #3b3f5c);
        color: #fff;
        padding: 1.5rem;
        border-bottom: none;
    }

    .config-input {
        border-radius: 10px;
        border: 1px solid #e0e6ed;
        padding: 0.6rem 1rem;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .config-input:focus {
        background: #fff;
        border-color: #696cff;
        box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1);
    }

    .logo-preview-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
        border-radius: 16px;
        border: 2px dashed #d9dee3;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8fafc;
        transition: all 0.3s ease;
    }

    .logo-preview-container:hover {
        border-color: #696cff;
        background: #f0f0ff;
    }

    .btn-save-cfg {
        background: linear-gradient(135deg, #696cff, #4e51d8);
        border: none;
        border-radius: 10px;
        padding: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3);
        transition: all 0.3s;
    }

    .btn-save-cfg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
    }

    .token-wrapper {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .token-card {
        border: none;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border-left: 5px solid #1fff3dff;
        background: #fff;
        transition: all 0.25s;
        border-right: 1px solid #f0f0f0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
        
    }

    .token-card:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
    }

    .token-card.usado {
        opacity: 0.6;
        border-left-color: #8592a3;
        filter: grayscale(100%);
    }

    .token-card.expirado {
        opacity: 0.5;
        border-left-color: #ff3e1d;
    }

    .token-codigo {
        font-family: 'Courier New', monospace;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 4px;
        color: #696cff;
        background: #f3f4fb;
        padding: 8px 16px;
        border-radius: 10px;
        display: inline-block;
    }

    .token-card.usado .token-codigo {
        color: #8592a3;
        background: #f5f5f5;
    }

    .token-card.expirado .token-codigo {
        color: #ff3e1d;
        background: #ffeceb;
    }

    /* Pulsación cuando hay cambios */
    @keyframes pulse-save {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3); }
        50% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(105, 108, 255, 0.5); }
        100% { transform: scale(1); box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3); }
    }
    .pulse-save {
        animation: pulse-save 2s infinite;
        background: linear-gradient(135deg, #28a745, #218838) !important;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-4" style="color: #3b3f5c;">
            <i class="bx bx-cog text-primary me-2" style="font-size: 1.5rem;"></i> Ajustes de Sistema
        </h4>

        <div class="row">
            <!-- ═══ CONFIGURACIÓN GENERAL ═══ -->
            <div class="col-lg-5 mb-4">
                <div class="card config-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-white"><i class="bx bx-buildings me-2"></i> Identidad del Negocio</h5>
                            <small class="text-white-50">Personaliza la apariencia y moneda</small>
                        </div>
                        <span id="badgeUnsaved" class="badge bg-warning animate__animated animate__fadeIn" style="display:none;">Cambios pendientes</span>
                    </div>
                    <div class="card-body p-4 pt-4">
                        <!-- Vista Previa Live -->
                        <div class="mb-4 p-3 rounded-3 bg-light border border-dashed border-primary" style="background: rgba(105, 108, 255, 0.03) !important;">
                            <div class="small fw-bold text-primary mb-3 text-uppercase" style="letter-spacing: 1px;"><i class="bx bx-show me-1"></i> Previsualización en Tiempo Real</div>
                            <div class="d-flex align-items-center p-2 rounded bg-white shadow-sm" style="max-width: 300px; margin: 0 auto; border: 1px solid #f0f0f0;">
                                <div id="previewSidebarLogo" class="me-2" style="width: 35px; height: 35px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="bx bxs-car text-muted"></i>
                                </div>
                                <span id="previewSidebarAbrev" class="fw-bold text-dark" style="font-size: 0.9rem;">C-SAAS</span>
                                <div class="ms-auto"><span id="previewMoneda" class="badge bg-label-success">S/</span></div>
                            </div>
                        </div>
                        <form id="formConfig" enctype="multipart/form-data">
                            <div class="mb-4 text-center">
                                <div class="logo-preview-container cursor-pointer" onclick="document.getElementById('cfg_logo').click()" title="Click para cambiar logo">
                                    <img src="" id="logoPreview" style="max-height:100px; max-width:100px; object-fit:contain;">
                                </div>
                                <div class="mt-2 text-muted small"><i class="bx bx-image-add"></i> Cambiar Logo</div>
                            </div>
                            <input type="file" class="d-none" name="logo" id="cfg_logo" accept="image/*" onchange="previewLogo(this)">

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Nombre del Negocio</label>
                                <input type="text" class="form-control config-input" name="nombre_negocio" id="cfg_nombre" required placeholder="Ej: Carwash Express">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-dark">Abreviatura Nav</label>
                                    <input type="text" class="form-control config-input" name="abreviatura" id="cfg_abrev" maxlength="10" placeholder="Ej: CW-FAST">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-dark">Moneda Principal</label>
                                    <select class="form-select config-input" name="moneda" id="cfg_moneda">
                                        <option value="S/">S/ (Soles)</option>
                                        <option value="$">$ (Dólares)</option>
                                        <option value="€">€ (Euros)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Puntos para Canje <small>(Metas Mágicas)</small></label>
                                <input type="number" class="form-control config-input" name="meta_puntos_canje" id="cfg_meta" min="1" placeholder="Ej: 10">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark"><i class="bx bx-car-wash me-1 text-primary"></i>Número de Rampas / Bahías</label>
                                <input type="number" class="form-control config-input" name="num_rampas" id="cfg_num_rampas" min="1" max="20" placeholder="Ej: 3">
                                <small class="text-muted">Define cuántas rampas/bahías tiene el negocio. Esto controla cuántas órdenes pueden estar en proceso simultáneamente.</small>
                            </div>
                            <div class="mb-4 mt-4 p-3 rounded" style="background:#f8fafc; border-left:4px solid #00d4ff;">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="cfg_modo_sin_cajero" name="modo_sin_cajero" value="1" style="width:2.5em; height:1.25em;">
                                    <label class="form-check-label fw-bold ms-2" for="cfg_modo_sin_cajero">
                                        Modo Operación Libre <br><small class="text-muted fw-normal">Permitir a operarios cobrar sin necesitar un Token de Admin</small>
                                    </label>
                                </div>
                                <div class="form-group mb-3 ms-4" id="box_operador_responsable" style="display: none;">
                                    <label class="form-label fw-bold text-dark mb-1 small text-uppercase">Operador Responsable en Libre</label>
                                    <select class="form-select config-input" name="id_operador_responsable" id="cfg_id_operador_responsable">
                                        <option value="">Selecciona Operador...</option>
                                        <?php if(isset($operarios)): foreach($operarios as $op): ?>
                                            <option value="<?= $op['id_usuario'] ?>"><?= htmlspecialchars($op['nombres']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="text-muted">Si se activa, este operador tomará el lugar del cajero y accederá a caja.</small>
                                </div>
                                <div class="form-check form-switch mt-1 pt-3 border-top">
                                    <input class="form-check-input" type="checkbox" id="cfg_cajero_abre_caja" name="cajero_puede_abrir_caja" value="1" style="width:2.5em; height:1.25em;">
                                    <label class="form-check-label fw-bold ms-2" for="cfg_cajero_abre_caja">
                                        Cajero puede aperturar caja<br><small class="text-muted fw-normal">Si está desactivado, solo tú (Admin) podrás abrir la caja.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-label-secondary w-100 fw-bold" id="btnDiscardConfig" style="display:none;">
                                    <i class="bx bx-undo me-1"></i> Descartar
                                </button>
                                <button type="submit" class="btn btn-primary btn-save-cfg w-100 text-white">
                                    <i class="bx bx-save me-2"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ═══ TOKENS DE SEGURIDAD ═══ -->
            <div class="col-lg-7 mb-4">
                <div class="card token-wrapper bg-white">
                    <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="bx bx-key text-warning me-1"></i> Tokens de Seguridad</h6>
                            <small class="text-muted">Genera códigos temporales para operarios y cajeros</small>
                        </div>
                        <button class="btn btn-primary rounded-pill" id="btnShowModalToken">
                            <i class="bx bx-plus me-1"></i>Generar Token
                        </button>
                    </div>
                    <div class="card-body pt-0" id="listaTokens" style="max-height:500px;overflow-y:auto">
                        <div class="text-center py-4 text-muted">Cargando tokens...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- MODAL: Generar Token -->
<div class="modal fade" id="modalToken" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-top border-5 border-primary">
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="bx bx-key text-primary" style="font-size:3rem"></i>
                    <h5 class="fw-bold mt-2">Generar Token</h5>
                </div>
                <div class="alert alert-warning mb-3 d-flex align-items-center gap-2" style="border-radius:12px; font-size: 0.8rem;">
                    <i class="bx bx-error-circle fs-4"></i>
                    <span>Solo puedes tener <strong>un token activo</strong> a la vez. Úsalo o espera a que expire para crear otro.</span>
                </div>
                <form id="formToken">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">¿Para qué es este token?</label>
                        <select class="form-select" name="motivo" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="Anulación autorizada">Anulación autorizada</option>
                            <option value="Otro">Otro motivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Duración (minutos)</label>
                        <input type="number" class="form-control" name="minutos_validez" value="60" min="5" max="1440">
                        <small class="text-muted">Mín: 5 min — Máx: 24 horas (1440 min)</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Límite de Usos</label>
                        <select class="form-select" name="limite_usos" id="selLimiteUsos">
                            <option value="1">Un solo uso (Más seguro)</option>
                            <option value="5">Hasta 5 usos</option>
                            <option value="10">Hasta 10 usos</option>
                            <option value="0">Uso ilimitado (Mientras no expire)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">GENERAR TOKEN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Token Generado -->
<div class="modal fade" id="modalTokenResult" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-top border-5 border-success">
            <div class="modal-body p-4 text-center">
                <i class="bx bx-check-circle text-success" style="font-size:3rem"></i>
                <p class="text-muted small">Haz clic para copiar el código:</p>
                <div class="position-relative mb-4">
                    <div class="token-codigo w-100 text-center py-3 cursor-pointer" id="tokenResultCodigo" style="font-size:2.5rem;letter-spacing:8px; border: 2px dashed #696cff;"></div>
                    <div id="copyFeedback" class="badge bg-success position-absolute top-100 start-50 translate-middle-x mt-2 animate__animated animate__fadeInUp" style="display:none;">¡Token Copiado!</div>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary fw-bold" id="btnCopyToken">
                        <i class="bx bx-copy me-1"></i> COPIAR TOKEN
                    </button>
                    <button class="btn btn-secondary w-100" data-bs-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/js/admin/configuracion.js"></script>