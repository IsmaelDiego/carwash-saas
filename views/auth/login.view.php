<!DOCTYPE html>
<html lang="es" class="layout-wide customizer-hide" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login | Carwash XP</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/template/assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
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
                        <div class="app-brand justify-content-center">
                            <a href="<?= BASE_URL ?>" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-heading fw-bold">CARWASH XP</span>
                            </a>
                        </div>
                        <h4 class="mb-1">Bienvenido! 👋</h4>
                        <p class="mb-6">Ingresa tus credenciales para administrar el negocio.</p>

                        <div id="loginMessage" class="alert text-center" role="alert" style="display:none;"></div>

                        <form id="loginForm" class="mb-6">
                            <div class="mb-6">
                                <label for="identifier" class="form-label">Usuario (Email o DNI)</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="identifier" 
                                    name="identifier" 
                                    placeholder="Ej: admin@carwash.com o 45892122" 
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
                            </div>
                            
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100" type="submit">Acceder</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>¿Nuevo empleado?</span>
                            <a href="<?= BASE_URL ?>/register">
                                <span>Crear cuenta</span>
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
    
    <script src="<?= BASE_URL ?>/public/js/login.js"></script>
</body>
</html>