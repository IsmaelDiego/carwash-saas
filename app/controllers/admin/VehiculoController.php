<?php
namespace Controllers\Admin;

use Exception;
use Vehiculo;
use Cliente; 
use CategoriaVehiculo;

class VehiculoController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD
    // ==========================================
    public function __construct()
    {
        requireRole(1);
    }

    public function index()
    {
        $this->lista();
    }

    // ==========================================
    // 1. VISTA HTML
    // ==========================================
    public function lista()
    {
        requireAuth();
        global $pdo;

        $vehiculoModel = new Vehiculo($pdo);
        $clienteModel = new Cliente($pdo);

        // Obtenemos datos para llenar los SELECTS en los Modales
        $clientes = $clienteModel->getAll(); 
        $categorias = $vehiculoModel->getCategorias();

        require VIEW_PATH . '/admin/lista_vehiculos.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (API JSON)
    // ==========================================
    public function getall()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $vehiculoModel = new Vehiculo($pdo);
        $data = $vehiculoModel->getAll();

        echo json_encode(['data' => $data]);
    }

    // ==========================================
    // 3. REGISTRAR VEHÍCULO
    // ==========================================
    public function registrarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            // Validaciones
            if (empty($input['id_cliente']) || empty($input['placa']) || empty($input['id_categoria'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Propietario, Placa y Categoría son obligatorios.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Validar Duplicado
            if ($vehiculoModel->existePlaca($input['placa'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'La placa ya está registrada.']);
                return;
            }

            try {
                if ($vehiculoModel->registrar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo registrado correctamente.']);
                } else {
                    throw new Exception("Error al insertar en BD.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 4. EDITAR VEHÍCULO
    // ==========================================
    public function editarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID no identificado.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            try {
                if ($vehiculoModel->editar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo actualizado correctamente.']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Datos guardados (Sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 5. ELIMINAR VEHÍCULO
    // ==========================================
    public function eliminarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            try {
                if ($vehiculoModel->eliminar($input['id_vehiculo'])) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo eliminado exitosamente.']);
                } else {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 6. CATEGORÍAS VISTA
    // ==========================================
    public function categorias()
    {
        requireAuth();
        require VIEW_PATH . '/admin/lista_categorias.view.php';
    }

    // ==========================================
    // 7. CATEGORÍAS API (JSON)
    // ==========================================
    public function getallcategorias()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $categoriaModel = new CategoriaVehiculo($pdo);
        $data = $categoriaModel->getAll();

        echo json_encode(['data' => $data]);
    }

    public function registrarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio.']);
                return;
            }

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->registrar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Categoría registrada correctamente.']);
                } else {
                    throw new Exception("Error al insertar.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }

    public function editarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_categoria']) || empty($input['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
                return;
            }

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->editar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Categoría actualizada.']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Sin cambios.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }

    public function eliminarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->eliminar($input['id_categoria'])) {
                    echo json_encode(['success' => true, 'message' => 'Categoría eliminada.']);
                } else {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar, posiblemente tiene vehículos asignados.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }
    // ==========================================
    // 8. EXPORTAR REPORTES BI (PDF/CSV)
    // URL: /admin/vehiculo/exportar
    // ==========================================
    public function exportar()
    {
        requireAuth();
        global $pdo;

        // Sincronización Horaria local
        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $idCategoria = $_GET['id_categoria'] ?? 'TODAS';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['f_fin'] ?? date('Y-m-d');

        // Lógica de consulta BI con filtro de FECHA
        $query = "SELECT v.*, 
                         CONCAT(c.nombres, ' ', COALESCE(c.apellidos, '')) as cliente,
                         cv.nombre as categoria 
                  FROM vehiculos v 
                  INNER JOIN clientes c ON v.id_cliente = c.id_cliente 
                  INNER JOIN categorias_vehiculos cv ON v.id_categoria = cv.id_categoria 
                  WHERE DATE(v.fecha_registro) BETWEEN ? AND ?";
        $params = [$f_inicio, $f_fin];

        if ($tipo === 'por_categoria' && $idCategoria !== 'TODAS') {
            $query .= " AND v.id_categoria = ?";
            $params[] = $idCategoria;
        }

        $query .= " ORDER BY v.placa ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $vehiculos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Títulos profesionales diferenciados
        $titulos_base = [
            'general'       => "PADRÓN GENERAL DE LA FLOTA",
            'por_categoria' => "ANÁLISIS DE FLOTA POR CATEGORÍA"
        ];
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE VEHÍCULOS";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // --- EXPORTACIÓN ---
        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Vehiculos_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($output, ['ID', 'PLACA', 'CATEGORIA', 'PROPIETARIO', 'COLOR', 'OBSERVACIONES', 'REGISTRO'], ';');
            foreach ($vehiculos as $v) {
                fputcsv($output, [
                    $v['id_vehiculo'],
                    $v['placa'],
                    $v['categoria'],
                    $v['cliente'],
                    $v['color'],
                    $v['observaciones'],
                    $v['fecha_registro']
                ], ';');
            }
            fclose($output);
            exit;
        } else {
            // PDF con mPDF
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8', 'format' => 'A4',
                    'margin_top' => 15, 'margin_bottom' => 15
                ]);

                ob_start();
                $lista = $vehiculos;
                require VIEW_PATH . '/admin/reportes/vehiculo/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte); // Título profesional en pestaña
                $mpdf->Output('Reporte_Vehiculos_' . date('Ymd_His') . '.pdf', 'I');
            } catch (Exception $e) {
                die("Error al generar PDF: " . $e->getMessage());
            }
        }
    }
}