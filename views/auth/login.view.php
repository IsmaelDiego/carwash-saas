<?php
$config_sys = getSystemConfig();
$logo_path = !empty($config_sys['logo']) ? BASE_URL . '/' . $config_sys['logo'] : BASE_URL . '/template/assets/img/favicon/favicon.ico';
$nombre_negocio = !empty($config_sys['nombre_negocio']) ? $config_sys['nombre_negocio'] : 'CARWASH-SYS';
?>
<!DOCTYPE html>
<html lang="es" class="layout-wide customizer-hide" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login | <?= htmlspecialchars($nombre_negocio) ?></title>
    <meta name="description" content="" />
    <link rel="icon" id="favicon-icon" type="image/x-icon" href="<?= $logo_path ?>?v=<?= $config_sys['logo_version'] ?? '1' ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/demo.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/dark-mode.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/pages/page-auth.css" />
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/helpers.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/js/config.js"></script>
    <script>
        // Init Theme Settings
        (function() {
            const storedTheme = localStorage.getItem('theme') || 'light';
            if (storedTheme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.classList.add('dark-style');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.classList.remove('dark-style');
            }
        })();
    </script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <div class="app-brand justify-content-center mb-4">
                            <a href="<?= BASE_URL ?>" class="app-brand-link gap-2 flex-column align-items-center text-center">
                                <img src="<?= $logo_path ?>?v=<?= $config_sys['logo_version'] ?? '1' ?>" alt="Logo" style="max-height: 80px; width: auto; object-fit: contain;">
                                <span class="app-brand-text demo text-heading fw-bold fs-4 mt-2"><?= htmlspecialchars($nombre_negocio) ?></span>
                            </a>
                        </div>
                        <h4 class="mb-1">Bienvenido! 👋</h4>
                        <p class="mb-6">Ingresa tus credenciales para administrar el negocio.</p>

                        <div id="loginMessage" class="alert text-center" role="alert" style="display:none;"></div>

                        <form id="loginForm" class="mb-6">
                            
                            <div class="mb-4 text-center">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="login_type" id="login_email" value="email" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="login_email"><i class="bx bx-envelope"></i>&nbsp;Email</label>

                                    <input type="radio" class="btn-check" name="login_type" id="login_dni" value="dni" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="login_dni"><i class="bx bx-id-card"></i>&nbsp;DNI</label>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="identifier" id="identLabel" class="form-label">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="identifier" 
                                    name="identifier" 
                                    placeholder="Ej: admin@carwash.com" 
                                    autofocus 
                                    required 
                                />
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Contraseña</label>
                                    </div>
                                <div class="input-group input-group-merge">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        class="form-control" 
                                        name="password" 
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                        aria-describedby="password" 
                                        required 
                                    />
                                    <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                                <div class="mt-2 text-end">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalRecuperar" class="small fw-bold">¿Olvidaste tu contraseña?</a>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100" type="submit">Acceder</button>
                            </div>
                        </form>

                        <!-- <p class="text-center">
                            <span>¿Nuevo empleado?</span>
                            <a href="<?= BASE_URL ?>/register">
                                <span>Crear cuenta</span>
                            </a>
                        </p> -->
                    </div>
        </div>
    </div>

    <!-- MODAL: CUENTA INACTIVA -->
    <div class="modal fade" id="modalInactivo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-top border-5 border-danger shadow-lg">
                <div class="modal-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bx bx-error-circle text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-danger">CUENTA INACTIVA</h4>
                    <p class="text-muted small">Tu acceso ha sido restringido temporalmente. Por favor, comunícate con la administración para reactivar tu perfil.</p>
                    <button type="button" class="btn btn-danger w-100 fw-bold" data-bs-dismiss="modal">ENTENDIDO</button>
                    <div class="mt-4 pt-2 border-top">
                        <small class="text-muted">Desarrollado por <b>Ismael Diego</b></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: RECUPERAR CONTRASEÑA (MULTI-STEP) -->
    <div class="modal fade" id="modalRecuperar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-top border-5 border-primary">
                <div class="modal-body p-4">
                    <div id="recuperarMessage" class="alert text-center small mb-3" style="display:none;"></div>
                    
                    <!-- STEP 1: SOLICITAR -->
                    <div id="stepRecuperar1">
                        <div class="text-center mb-4">
                            <i class="bx bx-key text-primary" style="font-size:3.5rem"></i>
                            <h5 class="fw-bold mt-2">Recuperar Acceso</h5>
                            <p class="text-muted small">Ingresa tu DNI o Correo para validar tu cuenta.</p>
                        </div>
                        <form id="formRecuperar">
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Identificador</label>
                                <input type="text" class="form-control" name="identifier" required placeholder="Ej: 45893XXX o admin@...">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold">CONTINUAR</button>
                            <button type="button" class="btn btn-label-secondary w-100 mt-2" data-bs-dismiss="modal">CANCELAR</button>
                        </form>
                    </div>

                    <!-- STEP 2: VERIFICAR PIN -->
                    <div id="stepRecuperar2" style="display:none;">
                        <div class="text-center mb-4">
                            <i class="bx bx-mail-send text-info" style="font-size:3.5rem"></i>
                            <h5 class="fw-bold mt-2">Verificar Correo</h5>
                            <p class="text-muted small" id="pinMessage">Ingresa el código que te enviamos.</p>
                        </div>
                        <form id="formVerificarPin">
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Código de 6 dígitos</label>
                                <input type="text" class="form-control text-center fw-bold fs-4" name="pin" maxlength="6" required placeholder="000000">
                            </div>
                            <button type="submit" class="btn btn-info w-100 fw-bold">VERIFICAR CÓDIGO</button>
                            <button type="button" class="btn btn-label-secondary w-100 mt-2" onclick="showStep(1)">VOLVER</button>
                        </form>
                    </div>

                    <!-- STEP 3: NUEVA PASSWORD -->
                    <div id="stepRecuperar3" style="display:none;">
                        <div class="text-center mb-4">
                            <i class="bx bx-lock-open-alt text-success" style="font-size:3.5rem"></i>
                            <h5 class="fw-bold mt-2">Nueva Contraseña</h5>
                            <p class="text-muted small">Establece tu nuevo acceso seguro.</p>
                        </div>
                        <form id="formResetPassword">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Nueva Contraseña</label>
                                <input type="password" class="form-control" name="password" minlength="6" required placeholder="Mínimo 6 caracteres">
                            </div>
                            <button type="submit" class="btn btn-success w-100 fw-bold">CAMBIAR Y ACCEDER</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/bootstrap.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/menu.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/js/main.js"></script>
    
    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    
    <script src="<?= BASE_URL ?>/public/js/login.js"></script>
</body>
</html>