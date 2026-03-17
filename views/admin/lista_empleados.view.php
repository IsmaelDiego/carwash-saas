<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter,
    .dataTables_length {
        display: none !important;
    }
    .dataTables_paginate {
        display: flex !important;
        justify-content: flex-start !important;
        margin-top: 1rem !important;
    }
    .dataTables_info {
        text-align: right !important;
        margin-top: 1rem !important;
        color: #b0b0b0 !important;
    }
    /* Switch Estado Custom */
    .switch-estado {
        cursor: pointer;
        width: 3em !important;
        height: 1.5em !important;
        background-color: #e0e0e0 !important;
        border-color: #d1d1d1 !important;
        transition: all 0.3s ease;
    }

    .switch-estado:checked {
        background-color: #25d366 !important;
        border-color: #25d366 !important;
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.4);
    }

    .switch-estado:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 211, 102, 0.25);
    }

    /* ─── STAT CARDS ─── */
    .stat-card {
        border: none;
        border-radius: 14px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }

    /* ─── AVATAR TABLA ─── */
    .avatar-empleado {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* ─── ROL BADGES ─── */
    .rol-administrador { background-color: rgba(105,108,255,0.16); color: #696cff; }
    .rol-cajero { background-color: rgba(3,195,236,0.16); color: #03c3ec; }
    .rol-operario { background-color: rgba(255,171,0,0.16); color: #ffab00; }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ════════════════════════════════════════════════ -->
        <!-- STATS CARDS                                     -->
        <!-- ════════════════════════════════════════════════ -->
        <div class="row mb-4" id="statsContainer">
            <!-- Se llenan por JS -->
        </div>

        <!-- ════════════════════════════════════════════════ -->
        <!-- HEADER + ACCIONES                               -->
        <!-- ════════════════════════════════════════════════ -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-group text-primary me-1"></i> GESTIÓN DE PERSONAL
                </h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Personal</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar empleado..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        </div>

                        <select id="filtroRol" class="form-select" style="width: 160px;">
                            <option value="">Todos los Roles</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Cajero">Cajero</option>
                            <option value="Operario">Operario</option>
                        </select>

                        <button type="button" class="btn btn-primary shadow-sm" id="btnNuevoEmpleado">
                            <i class="bx bx-user-plus me-1"></i> Nuevo Empleado
                        </button>

                        <button class="btn btn-outline-success" type="button" id="btnExportar">
                            <i class="bx bxs-file-export p-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════ -->
        <!-- LEYENDA                                         -->
        <!-- ════════════════════════════════════════════════ -->
        <div class="card shadow-sm">
            

            <!-- ════════════════════════════════════════════════ -->
            <!-- TABLA                                           -->
            <!-- ════════════════════════════════════════════════ -->
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tablaEmpleados">
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none">ID</th>
                            <th class="d-none">id_rol</th>
                            <th class="d-none">email</th>
                            <th class="d-none">telefono</th>
                            <th class="d-none">avatar</th>
                            <th class="d-none">fecha</th>
                            <th style="color: #f0f0f0;">Empleado</th>
                            <th style="color: #f0f0f0;">DNI</th>
                            <th class="text-center" style="color: #f0f0f0;">Rol</th>
                            <th class="text-center" style="color: #f0f0f0;">Contacto</th>
                            <th class="text-center" style="color: #f0f0f0;">Estado</th>
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

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php require VIEW_PATH . '/partials/empleado/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/empleado.js"></script>
