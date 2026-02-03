<!DOCTYPE html>
<html lang="es" class="layout-wide customizer-hide" data-assets-path="<?= BASE_URL ?>/template/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Registrar | Carwash XP</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/template/assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/demo.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/pages/page-auth.css" />
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/helpers.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/js/config.js"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <div class="app-brand justify-content-center mb-6">
                            <a href="<?= BASE_URL ?>" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-heading fw-bold">CARWASH XP</span>
                            </a>
                        </div>
                        
                        <div id="message-auth" class="alert text-center" role="alert" style="display:none;"></div>

                        <h4 class="mb-1">Registro de Personal 🚀</h4>
                        <p class="mb-6">Crea tu cuenta para empezar a operar.</p>

                        <form id="registerForm" class="mb-6">
                            
                            <div class="mb-6">
                                <label for="nombres" class="form-label">Nombres Completos</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Juan Pérez" autofocus required />
                            </div>

                            <div class="mb-6">
                                <label for="dni" class="form-label">DNI (Identificador)</label>
                                <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingresa tu DNI (8 dígitos)" maxlength="20" required />
                            </div>

                            <div class="mb-6">
                                <label for="email" class="form-label">Correo electrónico (Opcional)</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="juan@carwash.com" />
                            </div>

                            <div class="form-password-toggle mb-6">
                                <label class="form-label" for="password">Contraseña</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                            
                            <div class="input-group mb-6">
                                <label class="input-group-text" for="id_rol">Cargo</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="1">Administrador</option>
                                    <option value="2">Cajero</option>
                                    <option value="3">Operario</option>
                                </select>
                            </div>

                            <div class="my-7">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required />
                                    <label class="form-check-label" for="terms-conditions">
                                        Confirmo que los datos son reales
                                    </label>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary d-grid w-100" type="submit">Registrarse</button>
                        </form>

                        <p class="text-center">
                            <span>¿Ya tienes cuenta?</span>
                            <a href="<?= BASE_URL ?>/login">
                                <span>Inicia sesión</span>
                            </a>
                        </p>
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
    
    <script src="<?= BASE_URL ?>/public/js/auth.js"></script>
</body>
</html>