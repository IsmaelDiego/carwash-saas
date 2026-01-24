<?php require VIEW_PATH . '/layouts/header.view.php'; ?>


<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->

  <div class="container-fluid flex-grow-1 container-p-y">

    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header">LISTA DE CLIENTES</h5>
        <div class="box d-flex justify-content-between align-items-center">
          <!-- Custom style2 Breadcrumb -->
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon mb-0">
              <li class="breadcrumb-item text-primary ">
                <a href="#" class="text-primary ">Clientes</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
              </li>
              <li class="breadcrumb-item active text-primary ">Lista de Clientes</li>
            </ol>
          </nav>
          <!--/ Custom style2 Breadcrumb -->
          <div class="btns">
            <button
              type="button"
              class="btn rounded-pill btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#largeModal"> <i class="icon-base bx  bx-plus"></i> &nbsp
              NUEVO
            </button>
            <button
              class="btn rounded-pill btn-outline-secondary"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasDark"
              aria-controls="offcanvasDark"><i class="icon-base bx  bx-filter"></i> &nbsp
              FILTRAR
            </button>
            <button class="btn rounded-pill btn-outline-secondary" type="button" id="btnExportar">
    <i class="icon-base bx bx-export"></i> &nbsp EXPORTAR
</button>

          </div>

        </div>
       <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel" data-bs-theme="light">
  <div class="offcanvas-header border-bottom">
    <h4 class="offcanvas-title fw-bold" id="offcanvasDarkLabel"><i class="bx bx-filter-alt me-1"></i> Filtros</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body my-auto">
    <h6 class="text-muted mb-3 fw-semibold text-uppercase" style="font-size: 0.75rem;">Búsqueda por coincidencias</h6>
    <div class="mb-4">
      <label for="buscadorGlobal" class="form-label">DNI, Nombres, Apellidos o Correo</label>
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="bx bx-search"></i></span>
        <input type="text" id="buscadorGlobal" class="form-control" placeholder="Escriba para buscar..." />
      </div>
    </div>

    <hr class="my-4">

    <h6 class="text-muted mb-3 fw-semibold text-uppercase" style="font-size: 0.75rem;">Rango de Fechas</h6>
    <div class="row g-3 mb-4">
      <div class="col-6">
        <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
        <input type="date" id="filtroFechaInicio" class="form-control" />
      </div>
      <div class="col-6">
        <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
        <input type="date" id="filtroFechaFin" class="form-control" />
      </div>
    </div>

    <div class="d-grid gap-2 mt-5">
      <button type="button" class="btn btn-primary" data-bs-dismiss="offcanvas">
        Aplicar y Cerrar
      </button>
      <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
        <i class="bx bx-refresh me-1"></i> Limpiar Filtros
      </button>
    </div>
  </div>
</div>

        

      </div>
    </div>

    <!-- Hoverable Table rows -->
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-hover w-100" id="tablaClientes">
          <thead>
            <tr>
      <th>Nombres</th>
      <th>Apellidos</th>
      <th>DNI</th>
      <th>Sexo</th>
      <th>Correo</th>
      <th>Teléfono</th>
      <th>Tel. Alt.</th>
      <th>Fecha Registro</th>
      <th>WhatsApp</th>
      <th>Puntos</th>
      <th>Observaciones</th>
      <th>Acciones</th>
    </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Hoverable Table rows -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 10000;">
  <div id="exportToast" class="bs-toast toast fade bg-success" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="icon-base bx bx-check-circle m-2"></i>
      <div class="me-auto fw-medium">Exportación Exitosa </div>
      <small>Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body text-white">
      El archivo Excel se descargo exitosamente.
    </div>
  </div>
</div>

<!-- Large Modal -->
        <div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel3">NUEVO CLIENTE</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
                 <div id="message-registerCliente" class="alert text-center" role="alert" style="display:none;"></div>

              <div class="modal-body">
                  <form id="registrarcliente" action="index.html">

                    <div class="mb-4">
                      <p class="form-label text-primary text" for="inputDni">Consulta RENIEC</p>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base bx bx-id-card"></i></span>
                        <input
                          type="text"
                          class="form-control"
                          id="dni" name="dni"
                          placeholder="Ingrese DNI y presione Enter..."
                          maxlength="8"
                          autofocus />
                        <button class="btn btn-primary" type="button" id="btnBuscarDni">
                          <i class="icon-base bx bx-search"></i> Buscar
                        </button>
                      </div>
                      <div id="dniFeedback" class="form-text"></div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-6">
                      <label class="form-label" for="basic-icon-default-fullname">Nombres</label>
                      <div class="input-group input-group-merge">
                        <span id="basic-icon-default-fullname2" class="input-group-text"><i class="icon-base bx bx-user"></i></span>
                        <input type="text" class="form-control" id="nombres" name="nombres" readonly />
                      </div>
                    </div>

                    <div class="mb-6">
                      <label class="form-label" for="basic-icon-default-apellidos">Apellidos</label>
                      <div class="input-group input-group-merge">
                        <span id="basic-icon-default-apellidos  " class="input-group-text"><i class="icon-base bx bx-user"></i></span>
                        <input type="text" id="apellidos" name="apellidos" class="form-control" readonly/>
                      </div>
                    </div>

                    <div class="mb-6">
                      <label class="form-label" for="basic-icon-default-email">Correo</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                        <input type="text" id="email" name="email" class="form-control" placeholder="cliente@carwashsaas.com" />
                      </div>
                    </div>

                    <div class="row g-6 mb-5">
                      <div class="col mb-0">
                        <label class="form-label" for="basic-icon-default-telefono">Telefono Principal</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                          <input type="text" id="telefono_principal" name="telefono_principal" class="form-control" placeholder="123456789" />
                        </div>
                      </div>
                      <div class="col mb-0">
                        <label class="form-label" for="basic-icon-default-telefono">Telefono Alternativo</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                          <input type="text" id="telefono_alternativo_w" name="telefono_alternativo_w" class="form-control" placeholder="987654321" />
                        </div>
                      </div>
                    </div>
                    <div class="row g-6 mb-5">
                      <div class="col mb-0">
                        <label class="form-label" for="basic-icon-default-telefono">Sexo</label>
                        <div class=" input-group-merge">
                          <select class="form-select" id="sexo" name="sexo" aria-label="Default select example">
                          <option value="Masculino" selected>Masculino</option>
                          <option value="Femenino">Femenino</option>
                        </select>
                        </div>
                      </div>
                      <div class="col mb-0">
                        <label class="form-label" for="basic-icon-default-telefono">WhatsApp</label>
                        <style>
                          /* Tamaño más grande */
                          .switch-grande {
                            font-size: 1.8rem;
                            margin-left: 0.5rem;
                          }

                          /* Gris cuando está apagado (0) */
                          .switch-grande .form-check-input {
                            background-color: #8592a3;
                            border-color: #8592a3;
                          }

                          /* Verde cuando está encendido (1) */
                          .switch-grande .form-check-input:checked {
                            background-color: #71dd37;
                            border-color: #71dd37;
                          }
                        </style>

                        <div class="form-check form-switch mb-2 switch-grande">

                          <input type="hidden" name="estado_whatsapp" value="0">

                          <input class="form-check-input" type="checkbox" id="switchEstado" name="estado_whatsapp" value="1" />

                        </div>
                      </div>
                      <div class="col mb-0">
                        <label class="form-label" for="basic-icon-default-telefono">Puntos</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="icon-base bx bx-star"></i></span>
                          <input type="hidden"  name="puntos"  value="0" />
                          <input type="number" id="puntos" name="puntos" class="form-control text-center" value="0" readonly  />
                        </div>
                      </div>
                    </div>
                    <div class="mb-6">
                      <label class="form-label" for="basic-icon-default-apellidos">Observaciones</label>

                      <textarea name="observaciones" class="form-control" id="observaciones" w-100 >Sin observaciones</textarea>
                    </div>


                    <div class="modal-footer mt-4">
                      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-primary">REGISTRAR</button>
                    </div>
                  </form>

                <script>
                  document.addEventListener('DOMContentLoaded', function() {

                    // Obtenemos la BASE_URL desde PHP para evitar el error de rutas relativas
                    const BASE_URL = '<?= BASE_URL ?>';

                    const inputDni = document.getElementById('dni');
                    const btnBuscar = document.getElementById('btnBuscarDni');
                    const feedback = document.getElementById('dniFeedback');

                    // Inputs a rellenar
                    const inputNombres = document.getElementById('nombres');
                    const inputApellidos = document.getElementById('apellidos');

                    async function consultarDni() {
                      const dni = inputDni.value.trim();

                      if (dni.length !== 8) {
                        feedback.innerHTML = '<span class="text-danger">⚠️ Ingrese 8 dígitos válidos.</span>';
                        return;
                      }

                      // Estado Cargando
                      const originalIcon = btnBuscar.innerHTML;
                      btnBuscar.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
                      btnBuscar.disabled = true;
                      feedback.innerHTML = '<span class="text-primary">Consultando...</span>';

                      try {
                        // PETICIÓN A TU PROPIO CONTROLADOR MVC
                        const response = await fetch(`${BASE_URL}/api/dni`, {
                          method: 'POST',
                          headers: {
                            'Content-Type': 'application/json'
                          },
                          body: JSON.stringify({
                            dni: dni
                          })
                        });

                        const data = await response.json();

                        if (data.success) {
                          // Rellenar Campos con la data del JSON
                          inputNombres.value = data.data.nombres;
                          inputApellidos.value = `${data.data.apellido_paterno} ${data.data.apellido_materno}`;

                          feedback.innerHTML = '<span class="text-success "> DNI Encontrado.</span>';
                        } else {
                          feedback.innerHTML = `<span class="text-danger">❌ ${data.message || 'No se encontró el DNI.'}</span>`;
                          inputNombres.value = '';
                          inputApellidos.value = '';
                        }

                      } catch (error) {
                        feedback.innerHTML = '<span class="text-danger">❌ Error de conexión con el servidor.</span>';
                      } finally {
                        btnBuscar.innerHTML = originalIcon;
                        btnBuscar.disabled = false;
                      }
                    }

                    // Disparadores del evento
                    btnBuscar.addEventListener('click', consultarDni);
                    inputDni.addEventListener('keypress', function(e) {
                      if (e.key === 'Enter') {
                        e.preventDefault();
                        consultarDni();
                      }
                    });
                  });
                </script>


              </div>

            </div>
          </div>
        </div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-primary"><i class='bx bx-user-circle'></i> Detalle del Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="contenidoDetalle">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 10000;">
  <div id="toastSistema" class="bs-toast toast fade" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i id="toastIcono" class="icon-base bx me-2"></i>
      <div id="toastTitulo" class="me-auto fw-medium">Notificación</div>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div id="toastMensaje" class="toast-body text-white"></div>
  </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-primary"><i class='bx bx-user-circle'></i> Detalle del Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="contenidoDetalle"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary fw-bold"><i class="bx bx-edit"></i> Editar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditarCliente">
        <div class="modal-body">
          <input type="hidden" id="edit_id_cliente" name="id_cliente">
          <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label text-muted">DNI / RUC</label><input type="text" id="edit_dni" class="form-control bg-light" readonly></div>
            <div class="col-md-4"><label class="form-label text-muted">Nombres</label><input type="text" id="edit_nombres" class="form-control bg-light" readonly></div>
            <div class="col-md-5"><label class="form-label text-muted">Apellidos</label><input type="text" id="edit_apellidos" class="form-control bg-light" readonly></div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label text-muted">Sexo</label><input type="text" id="edit_sexo" class="form-control bg-light" readonly></div>
            <div class="col-md-6"><label class="form-label text-muted">Puntos Actuales</label><input type="text" id="edit_puntos" class="form-control bg-light text-center fw-bold" readonly></div>
          </div>
          <hr class="my-4"><h6 class="text-primary mb-3">Datos Editables</h6>
          <div class="row g-3 mb-3">
            <div class="col-md-12"><label class="form-label">Correo Electrónico</label><input type="email" id="edit_email" name="email" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Teléfono Principal</label><input type="text" id="edit_tel1" name="telefono_principal" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Tel. Alternativo</label><input type="text" id="edit_tel2" name="telefono_alternativo_w" class="form-control"></div>
          </div>
          <div class="row g-3 mb-3 align-items-center">
            <div class="col-md-6">
              <label class="form-label mb-2 d-block">WhatsApp Activo</label>
              <div class="form-check form-switch mb-2" style="font-size: 1.5rem;">
                <input type="hidden" name="estado_whatsapp" value="0">
                <input class="form-check-input" type="checkbox" id="edit_whatsapp" name="estado_whatsapp" value="1">
              </div>
            </div>
          </div>
          <div class="mb-3"><label class="form-label">Observaciones</label><textarea id="edit_observaciones" name="observaciones" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <i class="bx bx-error-circle text-danger mb-3" style="font-size: 4rem;"></i>
        <h4 class="mb-2">¿Estás seguro?</h4>
        <p class="text-muted mb-4">Se eliminará a <strong id="nombre_eliminar"></strong>.</p>
        <form id="formEliminarCliente">
          <input type="hidden" id="delete_id_cliente" name="id_cliente">
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Sí, eliminar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  </div>
  <!-- / Content -->



  <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
    <script>
        // 1. Definimos la variable GLOBALMENTE para que todos los JS la puedan ver
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    
    <script src="<?= BASE_URL ?>/public/js/cliente.js"></script>
   

<!-- footer -->
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

