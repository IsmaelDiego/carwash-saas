<!doctype html>

<html lang="en" class="layout-wide" data-assets-path="<?= BASE_URL ?>/template/assets/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Demo: Error - Pages | Sneat - Bootstrap Dashboard FREE</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/pages/page-misc.css" />

    <!-- Helpers -->
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="<?= BASE_URL ?>/template/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <!-- Error -->
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">403</h1>
        <h4 class="mb-2 mx-2">Sin autorización ⚠️</h4>
        <p class="mb-6 mx-2">No tienes permiso para ver esta sección.</p>
        <a href="javascript:void(0);" onclick="volverAtrasSinRastro()" class="btn btn-primary">
    Volver
</a> 
        <div class="mt-6">
          <img
            src="<?= BASE_URL ?>/template/assets/img/illustrations/page-misc-error-light.png"
            alt="page-misc-error-light"
            width="500"
            class="img-fluid" />
        </div>
      </div>
    </div>
    <!-- /Error -->

    <!-- / Content -->

    <div class="buy-now">
      <a
        href="https://themeselection.com/item/sneat-dashboard-pro-bootstrap/"
        target="_blank"
        class="btn btn-danger btn-buy-now"
        >Upgrade to Pro</a
      >
    </div>

<script>
function volverAtrasSinRastro() {
    // Si el navegador sabe de dónde vienes:
    if (document.referrer) {
        // Reemplaza la página actual (403) por la anterior.
        // Al reemplazarla, NO se puede usar el botón "Adelante" para volver al error.
        window.location.replace(document.referrer);
    } else {
        // Si no hay información (ej. escribieron la URL a mano),
        // intentamos la función nativa de volver atrás como último recurso.
        window.history.back();
    }
}
</script>
    <!-- Core JS -->

    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/jquery/jquery.js"></script>

    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/bootstrap.js"></script>

    <script src="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="<?= BASE_URL ?>/template/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="<?= BASE_URL ?>/template/assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
