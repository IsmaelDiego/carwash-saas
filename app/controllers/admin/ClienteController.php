<?php
// ==========================================================
// 1. NAMESPACE Y USO DE CLASES
// ==========================================================
namespace Controllers\Admin;

use Exception; // Importamos Excepciones PHP
use Cliente;

class ClienteController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD (Solo Admin)
    // ==========================================
    public function __construct()
    {
        // En V3.2, id_rol 1 = Admin.
        requireRole(1);
    }

    // Método por defecto (Redirige a lista)
    public function index()
    {
        $this->lista();
    }

    // ==========================================
    // 1. VISTA HTML (Renderiza la página)
    // URL: /admin/cliente/lista
    // ==========================================
    public function lista()
    {
        requireAuth();
        // Carga la vista que contiene la tabla HTML y los Scripts JS
        require VIEW_PATH . '/admin/lista_clientes.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (API JSON para DataTables)
    // URL: /admin/cliente/getall
    // ==========================================
    public function getall()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $clienteModel = new Cliente($pdo);

        // Obtenemos array de clientes desde el modelo
        $clientes = $clienteModel->getAll();

        // DataTables espera un objeto con la propiedad "data"
        echo json_encode(['data' => $clientes]);
    }

    // ==========================================
    // 3. REGISTRAR CLIENTE (POST JSON)
    // URL: /admin/cliente/registrarcliente
    // ==========================================
    public function registrarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer cuerpo JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // --- VALIDACIÓN ESTRICTA DE CELULAR ---
            $telefono = trim($data['telefono'] ?? '');
            if (!preg_match('/^9[0-9]{8}$/', $telefono)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El celular debe empezar con 9 y tener exactamente 9 dígitos numéricos.']);
                return;
            }
            $data['telefono'] = $telefono;

            $clienteModel = new Cliente($pdo);

            // Verificar duplicados por DNI
            if ($clienteModel->findByDni($data['dni'])) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado.']);
                return;
            }

            try {
                // Llamar al método crear del modelo
                if ($clienteModel->crearcliente($data)) {
                    echo json_encode(['success' => true, 'message' => '¡Cliente registrado correctamente!']);
                } else {
                    throw new Exception("Error al insertar en BD.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 4. EDITAR CLIENTE (POST JSON)
    // URL: /admin/cliente/editarcliente
    // ==========================================
    public function editarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            // --- VALIDACIÓN ESTRICTA DE CELULAR ---
            $id_cliente = $data['id_cliente'] ?? null;
            $telefono = trim($data['telefono'] ?? '');

            if (!$id_cliente || empty($telefono)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos incompletos para actualizar.']);
                return;
            }

            if (!preg_match('/^9[0-9]{8}$/', $telefono)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El celular debe empezar con 9 y tener exactamente 9 dígitos.']);
                return;
            }
            $data['telefono'] = $telefono;

            $clienteModel = new Cliente($pdo);

            try {
                if ($clienteModel->actualizarCliente($data)) {
                    echo json_encode(['success' => true, 'message' => '¡Cliente actualizado correctamente!']);
                } else {
                    // Si retorna false, puede ser que no hubo cambios reales en los datos
                    // pero para el usuario es un "Éxito" o un "Sin cambios".
                    echo json_encode(['success' => true, 'message' => 'Datos guardados (Sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 5. ELIMINAR CLIENTE (POST JSON)
    // URL: /admin/cliente/eliminarcliente
    // ==========================================
    public function eliminarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_cliente'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de cliente inválido.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            try {
                // Intentar eliminar (El modelo retorna false si falla por FK)
                if ($clienteModel->eliminarCliente($data['id_cliente'])) {
                    echo json_encode(['success' => true, 'message' => 'Cliente eliminado exitosamente.']);
                } else {
                    // Mensaje amigable cuando falla por FK
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar: El cliente tiene historial de ventas o puntos.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }
    public function cambiarestadowhatsapp()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_cliente'])) {
                echo json_encode(['success' => false, 'message' => 'ID faltante']);
                return;
            }

            // Validar estado (asegurar que sea 1 o 0)
            $nuevoEstado = $data['estado'] == 1 ? 1 : 0;

            try {
                // Actualización directa pequeña
                $sql = "UPDATE clientes SET estado_whatsapp = :estado WHERE id_cliente = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':estado' => $nuevoEstado, ':id' => $data['id_cliente']]);

                echo json_encode(['success' => true, 'message' => 'Estado de WhatsApp actualizado.']);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD']);
            }
            return;
        }
    }
    // ==========================================
    // 6. EXPORTAR REPORTES BI (PDF/CSV)
    // URL: /admin/cliente/exportar
    // ==========================================
    public function exportar()
    {
        requireAuth();
        global $pdo;
        
        // Sincronizar con la hora local (UTC-5)
        date_default_timezone_set('America/Lima');

        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $minPuntos = (int)($_GET['min_puntos'] ?? 0);
        $whatsappStatus = $_GET['whatsapp_status'] ?? 'TODOS';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['f_fin'] ?? date('Y-m-d');

        // Lógica de Filtrado dinámico
        $query = "SELECT * FROM clientes WHERE DATE(fecha_registro) BETWEEN ? AND ?";
        $params = [$f_inicio, $f_fin];

        if ($tipo === 'puntos') {
            $query .= " AND puntos_acumulados >= ?";
            $params[] = $minPuntos;
            $query .= " ORDER BY puntos_acumulados DESC";
        } elseif ($tipo === 'marketing') {
            if ($whatsappStatus !== 'TODOS') {
                $query .= " AND estado_whatsapp = ?";
                $params[] = $whatsappStatus;
            }
            $query .= " ORDER BY nombres ASC";
        } else {
            $query .= " ORDER BY nombres ASC";
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $clientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $fecha_actual = date('d/m/Y');
        
        // Títulos profesionales diferenciados
        $titulos_base = [
            'general'   => "LISTADO MAESTRO DE CLIENTES",
            'puntos'    => "RANKING DE FIDELIDAD POR PUNTOS",
            'marketing' => "BASE DE DATOS PARA CAMPAÑAS WHATSAPP"
        ];
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE CLIENTES";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // --- EXPORTACIÓN ---
        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Clientes_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($output, ['ID', 'DNI', 'CLIENTE', 'TELEFONO', 'PUNTOS', 'WHATSAPP', 'REGISTRO'], ';');
            foreach ($clientes as $c) {
                fputcsv($output, [
                    $c['id_cliente'],
                    $c['dni'],
                    $c['nombres'] . ' ' . $c['apellidos'],
                    $c['telefono'],
                    $c['puntos_acumulados'],
                    $c['estado_whatsapp'] ? 'SI' : 'NO',
                    $c['fecha_registro']
                ], ';');
            }
            fclose($output);
            exit;
        } else {
            // PDF con mPDF
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_top' => 15,
                    'margin_bottom' => 15
                ]);

                ob_start();
                // Definimos variables para la vista
                $lista = $clientes;
                $logo_path = BASE_PATH . '/public/img/logo.png'; // Ajustar según config si es necesario

                require VIEW_PATH . '/admin/reportes/cliente/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte); // Fuerza el título en la pestaña
                $mpdf->Output('Reporte_Clientes_' . date('Ymd_His') . '.pdf', 'I');
            } catch (Exception $e) {
                die("Error al generar PDF: " . $e->getMessage());
            }
        }
    }
}
