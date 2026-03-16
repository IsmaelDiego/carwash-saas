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
                
                $fileName = time() . '_' . basename($_FILES['logo']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    $input['logo_path'] = 'public/uploads/' . $fileName;
                }
            }

            $model = new ConfiguracionSistema($pdo);
            if ($model->actualizar($input)) {
                echo json_encode(['success' => true, 'message' => '¡Configuración guardada!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
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
            if ($minutos > 1440) $minutos = 1440; // Máx 24h

            $model = new TokenSeguridad($pdo);
            $token = $model->generar($_SESSION['user']['id'], $input['motivo'], $minutos);

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
                // Marcar como usado
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
