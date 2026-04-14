<!-- Modal -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formRegistrarPago">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="bx bx-wallet-alt me-2"></i>Registrar Nuevo Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Empleado (Solo Cajeros/Operarios)</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select name="id_usuario" class="form-select" required>
                                <option value="">Seleccione personal...</option>
                                <?php foreach($empleados as $e): if($e['id_rol'] == 1) continue; ?>
                                    <option value="<?= $e['id_usuario'] ?>"><?= htmlspecialchars($e['nombres']) ?> - <span class="text-muted">(<?= htmlspecialchars($e['rol_nombre'] ?? 'Operario') ?>)</span></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Tipo de Pago</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <select name="tipo" class="form-select fw-medium" required>
                                    <option value="" hidden disabled selected>Seleccione tipo...</option>
                                    <option value="SALARIO">💰 Salario</option>
                                    <option value="ADELANTO">💵 Adelanto</option>
                                    <option value="BONO">🎁 Bono</option>
                                    <option value="DESCUENTO">📉 Descuento</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Monto (S/)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-money"></i></span>
                                <input type="number" step="0.01" min="0.1" name="monto" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Periodo</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="month" name="periodo" class="form-control" min="<?= date('Y-m') ?>" required>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Fecha Programada</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar-event"></i></span>
                                <input type="date" name="fecha_programada" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Estado Inicial</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                            <select name="estado" class="form-select fw-medium" required>
                                <option value="PENDIENTE" selected>⏳ Pendiente</option>
                                <option value="PAGADO">✅ Pagado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Observaciones</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-comment-detail"></i></span>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Detalles del pago...">Sin observaciones específicas</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top pb-0 mt-3 pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Pago -->
<div class="modal fade" id="modalConfirmarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bx bx-wallet text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-2">¿Confirmar Pago?</h4>
                <p class="text-muted mb-4">Se marcará este registro como pagado el día de hoy.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnConfirmarPago">Sí, pagar</button>
                </div>
            </div>
        </div>
    </div>
</div>


