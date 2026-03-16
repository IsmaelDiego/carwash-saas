<?php
namespace Controllers\Admin;

// 1. CARGA MANUAL DEL MODELO (Para asegurar que lo encuentre sí o sí)
// Ajusta la ruta si tu carpeta models está en otro nivel, pero esto suele ser estándar:
require_once __DIR__ . '/../../models/Promocion.php'; 

// 2. CARGA MANUAL DEL SERVICIO WHATSAPP
require_once __DIR__ . '/../WhapiService.php'; 

// Usamos las clases globales (ya que las cargamos manualmente arriba)
use Promocion; 
use Services\WhapiService;

class PromocionController {

    public function __construct() {
        requireRole(1);
    }

    public function index() {
        requireAuth();
        global $pdo;
        
        $model = new Promocion($pdo);
        $recientes = $model->getRecientes();
        $activas = array_filter($recientes, function($p) { return $p['estado'] == 1; });

        require VIEW_PATH . '/admin/lista_promociones.view.php';
    }

    // =======================================================
    // API PARA LA TABLA
    // =======================================================
    public function getall() {
        requireAuth();
        global $pdo;
        // Limpiamos cualquier salida previa para evitar JSON inválido
        ob_clean(); 
        header('Content-Type: application/json');
        
        try {
            $model = new Promocion($pdo);
            $data = $model->getAll();
            echo json_encode(['data' => $data]);
        } catch (\Exception $e) {
            // Si falla, enviamos array vacío para que la tabla no se rompa
            echo json_encode(['data' => []]); 
        }
    }

    // =======================================================
    // API DASHBOARD (Cards)
    // =======================================================
    public function getdashboarddata() {
        requireAuth();
        global $pdo;
        ob_clean();
        header('Content-Type: application/json');
        
        $model = new Promocion($pdo);
        $recientes = $model->getRecientes();
        $activas = array_filter($recientes, function($p) { return $p['estado'] == 1; });
        
        echo json_encode([
            'todas'     => $model->getAll(),
            'recientes' => array_values($recientes),
            'activas'   => array_values($activas)
        ]);
    }

    // =======================================================
    // CRUD
    // =======================================================
    public function registrarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Promocion($pdo);
            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Campaña creada exitosamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
        }
    }

    public function editarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Promocion($pdo);
            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Promoción actualizada.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
            }
        }
    }

    public function eliminarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Promocion($pdo);
            if ($model->eliminar($input['id_promocion'])) {
                echo json_encode(['success' => true, 'message' => 'Promoción eliminada.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
            }
        }
    }

    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Promocion($pdo);
            $estado = $input['estado'] == 1 ? 1 : 0;
            
            if ($model->cambiarEstado($input['id_promocion'], $estado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        }
    }

    // =======================================================
    // WHATSAPP
    // =======================================================
   // =======================================================
    // WHATSAPP CON DETECCIÓN DE ERRORES (WARNING)
    // =======================================================
    public function enviarwhatsapp() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            set_time_limit(300);

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id_promocion']) || empty($input['mensaje'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos.']); return;
            }

            $model = new Promocion($pdo);
            $clientes = $model->getClientesWhatsApp();

            if (empty($clientes)) {
                echo json_encode(['success' => false, 'message' => 'No hay clientes activos.']); return;
            }

            $whapi = new WhapiService();
            $enviados = 0;
            $errores = 0;

            foreach ($clientes as $c) {
                $nombre = explode(' ', trim($c['nombres']))[0];
                $mensaje = str_replace('{{nombre}}', $nombre, $input['mensaje']);
                
                $res = $whapi->enviarMensaje($c['telefono'], $mensaje);
                
                if ($res['success']) $enviados++; else $errores++;
                usleep(200000);
            }

            // LÓGICA DE RESPUESTA MODIFICADA
            // Si hubo al menos 1 error, lo tratamos como 'warning'
            $tipoRespuesta = ($errores > 0) ? 'warning' : 'success';
            
            echo json_encode([
                'success' => true, // Mantenemos true para que el JS limpie el formulario
                'type'    => $tipoRespuesta, // Nuevo campo para el color del Toast
                'message' => "Reporte de Envío: ✅ $enviados Enviados | ⚠️ $errores Fallidos"
            ]);
        }
    }
}