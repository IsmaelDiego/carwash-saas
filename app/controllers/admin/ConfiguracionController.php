<?php
namespace Controllers\Admin;

use ConfiguracionSistema;
use TokenSeguridad;
use Exception;

class ConfiguracionController {

    public function __construct() {
        requireRole(1);
    }

    // VISTA PRINCIPAL
    public function index() {
        requireAuth();
        global $pdo;
        $operarios = $pdo->query("SELECT id_usuario, nombres FROM usuarios WHERE id_rol = 3 AND estado = 1 ORDER BY nombres")->fetchAll(\PDO::FETCH_ASSOC);
        require VIEW_PATH . '/admin/configuracion.view.php';
    }

    // API: OBTENER CONFIGURACIÓN
    public function getconfig() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new ConfiguracionSistema($pdo);
        echo json_encode(['success' => true, 'data' => $model->get()]);
    }

    // API: GUARDAR CONFIGURACIÓN
    public function guardar() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = $_POST;
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $fileName = 'logo.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                $existingLogos = glob($uploadDir . 'logo.*');
                if ($existingLogos) {
                    foreach ($existingLogos as $el) {
                        @unlink($el);
                    }
                }
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    $input['logo_path'] = 'public/uploads/' . $fileName;
                }
            }

            $model = new ConfiguracionSistema($pdo);
            if ($model->actualizar($input)) {
                // ★ Sincronizar rampas con el nuevo número configurado
                require_once __DIR__ . '/../../models/Rampa.php';
                $rampaModel = new \Rampa($pdo);
                $rampaModel->sincronizarRampas((int)($input['num_rampas'] ?? 3));

                echo json_encode(['success' => true, 'message' => '¡Configuración guardada!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
        }
    }

    // ═════════════════════════════════════════
    // API: RAMPAS (Solo Admin puede configurar)
    // ═════════════════════════════════════════

    // API: LISTAR RAMPAS CON OPERADORES
    public function getrampas() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        require_once __DIR__ . '/../../models/Rampa.php';
        $model = new \Rampa($pdo);
        $operarios = $pdo->query(
            "SELECT id_usuario, nombres, dni FROM usuarios WHERE id_rol IN (2,3) AND estado = 1 ORDER BY nombres"
        )->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'rampas' => $model->getAll(), 'operarios' => $operarios]);
    }

    // API: ACTUALIZAR ESTADO/OPERADOR DE RAMPA (Admin)
    public function actualizarrampa() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_rampa']) || empty($input['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $estados_validos = ['ACTIVA', 'INACTIVA', 'DESCANSO'];
        if (!in_array($input['estado'], $estados_validos)) {
            echo json_encode(['success' => false, 'message' => 'Estado no válido.']);
            return;
        }

        require_once __DIR__ . '/../../models/Rampa.php';
        $model = new \Rampa($pdo);
        $ok = $model->actualizarEstado(
            (int)$input['id_rampa'],
            $input['estado'],
            !empty($input['id_operador']) ? (int)$input['id_operador'] : null,
            $input['motivo'] ?? null
        );

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Rampa actualizada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar rampa.']);
        }
    }

    // ═════════════════════════════════════════
    // TOKENS DE SEGURIDAD
    // ═════════════════════════════════════════

    // API: GENERAR TOKEN
    public function generartoken() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['motivo'])) {
                echo json_encode(['success' => false, 'message' => 'El motivo es obligatorio.']);
                return;
            }

            $minutos = (int)($input['minutos_validez'] ?? 60);
            if ($minutos < 5) $minutos = 5;
            if ($minutos > 1440) $minutos = 1440;

            $limite = (int)($input['limite_usos'] ?? 1);
            if ($limite < 0) $limite = 1;

            $model = new TokenSeguridad($pdo);
            
            if ($model->contarActivos() >= 1) {
                echo json_encode(['success' => false, 'message' => 'Ya existe un token activo. Úsalo o espera a que expire para generar uno nuevo.']);
                return;
            }

            $token = $model->generar($_SESSION['user']['id'], $input['motivo'], $minutos, $limite);

            if ($token) {
                echo json_encode([
                    'success' => true,
                    'message' => '¡Token generado exitosamente!',
                    'token'   => $token
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al generar token.']);
            }
        }
    }

    // API: LISTAR TOKENS
    public function gettokens() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new TokenSeguridad($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // API: VALIDAR TOKEN (Accesible por cualquier rol logueado)
    public function validartoken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['codigo'])) {
                echo json_encode(['success' => false, 'message' => 'Código de token requerido.']);
                return;
            }

            $model = new TokenSeguridad($pdo);
            $token = $model->validar($input['codigo']);

            if ($token) {
                $model->marcarUsado($token['id_token']);
                echo json_encode([
                    'success'  => true,
                    'message'  => '¡Token válido! Funciones desbloqueadas.',
                    'id_token' => $token['id_token']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Token inválido, expirado o ya fue utilizado.']);
            }
        }
    }
}
