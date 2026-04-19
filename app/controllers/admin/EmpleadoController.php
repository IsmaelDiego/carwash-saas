<?php
namespace Controllers\Admin;

use Empleado;
use Exception;
use PDO;

class EmpleadoController {

    public function __construct() {
        requireRole(1); // Solo Admin
    }

    // ==========================================
    // VISTA PRINCIPAL
    // URL: /admin/empleado
    // ==========================================
    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/lista_empleados.view.php';
    }

    public function lista() {
        $this->index();
    }

    // ==========================================
    // API: OBTENER TODOS (JSON para DataTables)
    // URL: /admin/empleado/getall
    // ==========================================
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Empleado($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    public function getone() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID faltante']);
            return;
        }
        $model = new Empleado($pdo);
        $user = $model->getById($id);
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No encontrado']);
        }
    }

    // ==========================================
    // API: OBTENER ROLES (JSON para Select)
    // URL: /admin/empleado/getroles
    // ==========================================
    public function getroles() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Empleado($pdo);
        echo json_encode(['data' => $model->getRoles()]);
    }

    // ==========================================
    // API: OBTENER ESTADÍSTICAS
    // URL: /admin/empleado/getstats
    // ==========================================
    public function getstats() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Empleado($pdo);
        echo json_encode($model->getEstadisticas());
    }

    // ==========================================
    // API: REGISTRAR EMPLEADO
    // URL: /admin/empleado/registrarempleado
    // ==========================================
    public function registrarempleado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            // Validación de campos obligatorios
            if (empty($input['dni']) || empty($input['nombres']) || empty($input['id_rol']) || empty($input['password'])) {
                echo json_encode(['success' => false, 'message' => 'DNI, Nombres, Rol y Contraseña son obligatorios.']);
                return;
            }

            // Validar longitud de contraseña
            if (strlen($input['password']) < 6) {
                echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
                return;
            }

            $model = new Empleado($pdo);

            // Validar DNI duplicado
            if ($model->existeDni($input['dni'])) {
                echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado.']);
                return;
            }

            // Validar Email duplicado
            if (!empty($input['email']) && $model->existeEmail($input['email'])) {
                echo json_encode(['success' => false, 'message' => 'Este email ya está registrado.']);
                return;
            }

            try {
                if ($model->registrar($input)) {
                    echo json_encode(['success' => true, 'message' => '¡Empleado registrado correctamente!']);
                } else {
                    throw new Exception("Error al insertar en BD.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
        }
    }

    // ==========================================
    // API: EDITAR EMPLEADO
    // URL: /admin/empleado/editarempleado
    // ==========================================
    public function editarempleado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_usuario']) || empty($input['nombres']) || empty($input['id_rol'])) {
                echo json_encode(['success' => false, 'message' => 'Datos obligatorios incompletos.']);
                return;
            }

            $model = new Empleado($pdo);

            // Validar Email duplicado excluyendo el actual
            if (!empty($input['email']) && $model->existeEmail($input['email'], $input['id_usuario'])) {
                echo json_encode(['success' => false, 'message' => 'Este email ya está registrado por otro usuario.']);
                return;
            }

            try {
                if ($model->editar($input)) {
                    echo json_encode(['success' => true, 'message' => '¡Empleado actualizado correctamente!']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Datos guardados (sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
            }
        }
    }

    // ==========================================
    // API: CAMBIAR CONTRASEÑA
    // URL: /admin/empleado/cambiarpassword
    // ==========================================
    public function cambiarpassword() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_usuario']) || empty($input['nueva_password'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
                return;
            }

            if (strlen($input['nueva_password']) < 6) {
                echo json_encode(['success' => false, 'message' => 'La contraseña debe tener mínimo 6 caracteres.']);
                return;
            }

            $model = new Empleado($pdo);

            try {
                if ($model->cambiarPassword($input['id_usuario'], $input['nueva_password'])) {
                    // Marcar notificaciones de recuperación como atendidas
                    $stmt = $pdo->prepare("UPDATE notificaciones_recuperacion SET estado = 'ATENDIDO' WHERE id_usuario = :id AND estado = 'PENDIENTE'");
                    $stmt->execute([':id' => $input['id_usuario']]);
                    
                    echo json_encode(['success' => true, 'message' => '¡Contraseña actualizada!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error interno.']);
            }
        }
    }

    // ==========================================
    // API: CAMBIAR ESTADO
    // URL: /admin/empleado/cambiarestado
    // ==========================================
    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_usuario'])) {
                echo json_encode(['success' => false, 'message' => 'ID faltante.']);
                return;
            }

            $model = new Empleado($pdo);
            $nuevoEstado = $input['estado'] == 1 ? 1 : 0;

            if ($model->cambiarEstado($input['id_usuario'], $nuevoEstado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        }
    }

    // ==========================================
    // API: ELIMINAR EMPLEADO
    // URL: /admin/empleado/eliminarempleado
    // ==========================================
    public function eliminarempleado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_usuario'])) {
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }

            if (empty($input['password_admin'])) {
                echo json_encode(['success' => false, 'message' => 'Debes ingresar tu contraseña de administrador para confirmar.']);
                return;
            }

            // Proteger al Super Admin (id=1)
            if ($input['id_usuario'] == 1) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar al Super Admin.']);
                return;
            }

            // Proteger al usuario actualmente logueado
            if ($input['id_usuario'] == $_SESSION['user']['id']) {
                echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo.']);
                return;
            }

            $model = new Empleado($pdo);

            // Verificar la contraseña del administrador actual
            $admin = $model->getById($_SESSION['user']['id']);
            if (!$admin || !password_verify($input['password_admin'], $admin['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Contraseña de administrador incorrecta.']);
                return;
            }

            try {
                if ($model->eliminar($input['id_usuario'])) {
                    echo json_encode(['success' => true, 'message' => 'Empleado eliminado.']);
                } else {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar: tiene registros asociados (órdenes, cierres, etc).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: El empleado tiene registros asociados.']);
            }
        }
    }

    // ==========================================
    // API: CENTRAL DE REPORTES BI
    // ==========================================
    public function exportar()
    {
        requireAuth();
        global $pdo;

        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');
        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $rol_filtro = $_GET['rol'] ?? 'todos';
        $estado_filtro = $_GET['estado'] ?? 'todos';

        $titulos_base = [
            'general'     => "LISTADO MAESTRO DE PERSONAL",
            'rendimiento' => "RENDIMIENTO Y PRODUCTIVIDAD OPERATIVA",
            'seguridad'   => "AUDITORÍA DE ACCESOS Y SEGURIDAD"
        ];
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE PERSONAL";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // Construcción de Query Dinámica
        $where = " WHERE 1=1 ";
        $params = [];

        if ($rol_filtro !== 'todos') {
            $where .= " AND u.id_rol = ? ";
            $params[] = $rol_filtro;
        }
        if ($estado_filtro !== 'todos') {
            $where .= " AND u.estado = ? ";
            $params[] = $estado_filtro;
        }

        if ($tipo === 'rendimiento') {
            $sql = "SELECT u.id_usuario, u.dni, u.nombres, r.nombre as rol_nombre, u.estado,
                           COUNT(o.id_orden) as total_ordenes,
                           COALESCE(SUM(o.total_final), 0) as recaudacion_total
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    LEFT JOIN ordenes o ON u.id_usuario = o.id_usuario_creador AND o.estado = 'FINALIZADO'
                    $where
                    GROUP BY u.id_usuario
                    ORDER BY total_ordenes DESC";
        } else {
            $sql = "SELECT u.*, r.nombre as rol_nombre 
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    $where
                    ORDER BY u. nombres ASC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($formato === 'pdf') {
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15]);
                
                ob_start();
                require VIEW_PATH . '/admin/reportes/empleado/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Personal_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) { die("Error PDF: " . $e->getMessage()); }
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Personal_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            if ($tipo === 'rendimiento') {
                fputcsv($output, ['ID', 'DNI', 'Nombres', 'Cargo', 'Total Ordenes', 'Recaudacion Total', 'Estado'], ';');
                foreach ($lista as $r) {
                    fputcsv($output, [
                        $r['id_usuario'], $r['dni'], mb_strtoupper($r['nombres'], 'UTF-8'), 
                        $r['rol_nombre'], $r['total_ordenes'], $r['recaudacion_total'],
                        $r['estado'] == 1 ? 'ACTIVO' : 'INACTIVO'
                    ], ';');
                }
            } else {
                fputcsv($output, ['ID', 'DNI', 'Nombres', 'Email', 'Telefono', 'Cargo', 'Estado', 'Fecha Registro'], ';');
                foreach ($lista as $r) {
                    fputcsv($output, [
                        $r['id_usuario'], $r['dni'], mb_strtoupper($r['nombres'], 'UTF-8'), 
                        $r['email'], $r['telefono'], $r['rol_nombre'],
                        $r['estado'] == 1 ? 'ACTIVO' : 'INACTIVO', $r['fecha_creacion']
                    ], ';');
                }
            }
            fclose($output);
            exit;
        }
    }
}
