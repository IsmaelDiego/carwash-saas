<?php
namespace Controllers\Admin;

use Temporada;

class TemporadaController {

    public function __construct() {
        // En V3.2, id_rol 1 = Admin.
        requireRole(1);
    }

    public function index() {
        requireAuth();
        global $pdo;
        
        $model = new Temporada($pdo);
        $dash = $model->getDashboardData();

        // Variables para la vista
        $tActual = $dash['actual'];
        $tAnt    = $dash['anterior'];
        $sAct    = $dash['stats_act'];
        $sAnt    = $dash['stats_ant'];

        // Cálculo simple de variación para las flechitas
        $calcVar = function($act, $ant) {
            if($ant == 0) return $act > 0 ? 100 : 0;
            return round((($act - $ant) / $ant) * 100, 1);
        };
        $varGen = $calcVar($sAct['gen'], $sAnt['gen']);
        $varRed = $calcVar($sAct['red'], $sAnt['red']);

        require VIEW_PATH . '/admin/lista_temporadas.view.php';
    }

    // API GET ALL
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Temporada($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // REGISTRAR
    public function registrartemporada() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Temporada($pdo);

            // Validar si ya hay activa
            if ($model->hayTemporadaActiva()) {
                echo json_encode(['success' => false, 'message' => '¡Ya existe una temporada activa! Ciérrala antes de crear otra.']);
                return;
            }

            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Temporada iniciada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en BD.']);
            }
        }
    }

    // EDITAR
    public function editartemporada() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Temporada($pdo);
            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Datos actualizados.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
            }
        }
    }

    // =========================================================
    // ESTA ES LA FUNCIÓN QUE FALTABA Y EL JS ESTABA BUSCANDO
    // =========================================================
    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            
            // Decodificar JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Temporada($pdo);
            
            // Si el estado es 0, usamos la lógica de cerrar (pone fecha fin hoy)
            if (isset($input['estado']) && $input['estado'] == 0) {
                if ($model->cerrarTemporada($input['id_temporada'])) {
                    echo json_encode(['success' => true, 'message' => 'Temporada finalizada correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo cerrar la temporada.']);
                }
            } else {
                // Si fuera otro cambio de estado genérico
                if ($model->cambiarEstado($input['id_temporada'], $input['estado'])) {
                    echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
                }
            }
        }
    }
}