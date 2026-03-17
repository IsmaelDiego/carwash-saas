<!-- Modal -->
<div class="modal fade" id="modalRegistrarPermiso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formRegistrarPermiso">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="bx bx-calendar-plus me-2"></i>Registrar Permiso o Descanso</h5>
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
                                    <option value="<?= $e['id_usuario'] ?>"><?= htmlspecialchars($e['nombres']) ?> - <span class="text-muted">(<?= htmlspecialchars($e['rol_nombre'] ?? 'Operario/Cajero') ?>)</span></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Tipo de Ausencia</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-category"></i></span>
                            <select name="tipo" class="form-select fw-medium" required>
                                <option value="" hidden disabled selected>Seleccione el tipo...</option>
                                <option value="DESCANSO">&nbsp; Día Libre / Descanso Médico</option>
                                <option value="PERMISO"> Permiso por horas/días</option>
                                <option value="VACACION"> Vacaciones</option>
                                <option value="FALTA"> Falta Injustificada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Desde</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="date" id="perm_fecha_inicio" name="fecha_inicio" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-dark fw-bold">Hasta</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar-check"></i></span>
                                <input type="date" id="perm_fecha_fin" name="fecha_fin" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Estado Inicial</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                            <select name="estado" class="form-select fw-medium" required>
                                <option value="PENDIENTE" selected> Pendiente de Aprobación</option>
                                <option value="APROBADO"> Aprobado</option>
                                <option value="RECHAZADO"> Rechazado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Motivo</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"></span>
                            <textarea name="motivo" class="form-control" rows="2" placeholder="Describa el motivo...">Sin motivos específicos</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top pb-0 mt-3 pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Guardar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Estado -->
<div class="modal fade" id="modalConfirmarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3" id="iconoEstadoPermiso">
                    <i class="bx bx-question-mark text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-2" id="tituloConfirmarEstado">Confirmar Acción</h4>
                <p class="text-muted mb-4" id="textoConfirmarEstado">¿Está seguro de realizar esta acción?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarEstado">Sí, confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>


