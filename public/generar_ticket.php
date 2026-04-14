<?php
/**
 * generar_ticket.php
 * Genera un ticket en PDF para órdenes de servicio o ventas directas.
 * Basado en mPDF.
 */

require_once __DIR__ . '/../config/database.php';

$autoloadPath = __DIR__ . '/../vendor/MPDF/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: No se encontró el autoloader de mPDF en: " . realpath(__DIR__ . '/../') . "/vendor/MPDF/vendor/autoload.php");
}
require_once $autoloadPath;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;


// Obtener ID de orden
$id_orden = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_orden <= 0) {
    die("ID de orden no válido.");
}

global $pdo;

try {
    // 1. Obtener datos de la orden
    $stmt = $pdo->prepare("
        SELECT o.*, 
               c.nombres AS cli_nombres, c.apellidos AS cli_apellidos, c.dni AS cli_dni,
               v.placa, v.color, cat.nombre AS categoria_vehiculo,
               u.nombres AS usuario_creador
        FROM ordenes o
        LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
        LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
        LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
        LEFT JOIN usuarios u ON o.id_usuario_creador = u.id_usuario
        WHERE o.id_orden = ?
    ");
    $stmt->execute([$id_orden]);
    $orden = $stmt->fetch();

    if (!$orden) {
        die("Orden no encontrada.");
    }

    // 2. Obtener detalles (servicios/productos) agrupando productos idénticos
    $stmtDet = $pdo->prepare("
        SELECT COALESCE(s.nombre, p.nombre) AS nombre_item,
               SUM(d.cantidad) AS cantidad, 
               MAX(d.precio_unitario) AS precio_unitario, 
               SUM(d.subtotal) AS subtotal
        FROM detalle_orden d
        LEFT JOIN servicios s ON d.id_servicio = s.id_servicio
        LEFT JOIN productos p ON d.id_producto = p.id_producto
        WHERE d.id_orden = ?
        GROUP BY COALESCE(s.id_servicio, p.id_producto), s.nombre, p.nombre
    ");
    $stmtDet->execute([$id_orden]);
    $detalles = $stmtDet->fetchAll();

    // 3. Obtener pagos
    $stmtPagos = $pdo->prepare("SELECT metodo_pago, monto FROM pagos_orden WHERE id_orden = ? ORDER BY id_pago ASC");
    $stmtPagos->execute([$id_orden]);
    $pagos = $stmtPagos->fetchAll();

    // 4. Obtener configuración del sistema
    $stmtConf = $pdo->query("SELECT * FROM configuracion_sistema WHERE id_configuracion = 1");
    $config = $stmtConf->fetch();

    $gd_enabled = function_exists('imagecreatefromstring');

    // 5. Preparar el HTML del ticket
    $nombre_negocio = $config['nombre_negocio'] ?? 'CARWASH';
    $moneda = $config['moneda'] ?? 'S/';
    $logo = !empty($config['logo']) ? $config['logo'] : 'public/uploads/logo.webp';
    $fecha = date('d/m/Y H:i', strtotime($orden['fecha_creacion']));


    // Estilos CSS para el ticket (80mm)
    $html = '
    <style>
        body { font-family: "Courier New", Courier, monospace; font-size: 11pt; color: #000; margin: 0; padding: 0; }
        .ticket { width: 100%; max-width: 80mm; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 5px; }
        .logo { max-width: 120px; height: auto; margin-bottom: 5px; }
        .title { font-size: 14pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; }
        .info { font-size: 9pt; margin-bottom: 5px; line-height: 1.2; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .table th { font-size: 9pt; border-bottom: 1px dashed #000; text-align: left; padding: 2px; }
        .table td { font-size: 9pt; padding: 2px; vertical-align: top; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 11pt; }
        .footer { text-align: center; margin-top: 10px; font-size: 8pt; }
        .qr { text-align: center; margin-top: 5px; }
    </style>

    <div class="ticket">
        <div class="header">
            ' . ($gd_enabled && file_exists(__DIR__ . '/../' . $logo) ? '<img src="../' . $logo . '" class="logo">' : '') . '
            <div class="title">' . htmlspecialchars($nombre_negocio) . '</div>
            <div class="info">
                Carwash & Detailing Profesional<br>
                TICKET DE VENTA ELECTRÓNICA<br>
                #' . str_pad($orden['id_orden'], 6, '0', STR_PAD_LEFT) . '
            </div>
        </div>

        <div class="divider"></div>

        <div class="info">
            <b>FECHA:</b> ' . $fecha . '<br>
            <b>CLIENTE:</b> ' . htmlspecialchars($orden['cli_nombres'] . ' ' . $orden['cli_apellidos']) . '<br>
            <b>D.N.I.:</b> ' . ($orden['cli_dni'] ?: '---') . '<br>
            <b>PLACA:</b> ' . ($orden['placa'] ?: '---') . ' (' . ($orden['categoria_vehiculo'] ?: 'S/P') . ')
        </div>

        <div class="divider"></div>

        <table class="table">
            <thead>
                <tr>
                    <th>DESCRIPCIÓN</th>
                    <th class="text-right">CANT</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($detalles as $det) {
        $html .= '
                <tr>
                    <td>' . htmlspecialchars($det['nombre_item']) . '</td>
                    <td class="text-right">' . (float)$det['cantidad'] . '</td>
                    <td class="text-right">' . number_format($det['subtotal'], 2) . '</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="table">
            ';
    
    if ($orden['descuento_promo'] > 0) {
        $html .= '<tr><td colspan="2" class="text-right">DSCTO PROMO:</td><td class="text-right">-' . number_format($orden['descuento_promo'], 2) . '</td></tr>';
    }
    if ($orden['descuento_puntos'] > 0) {
        $html .= '<tr><td colspan="2" class="text-right">DSCTO PUNTOS:</td><td class="text-right">-' . number_format($orden['descuento_puntos'], 2) . '</td></tr>';
    }

    $html .= '
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL FINAL:</td>
                <td class="text-right">' . htmlspecialchars($moneda) . ' ' . number_format($orden['total_final'], 2) . '</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="info text-center" style="margin-bottom:2px;"><b>FORMAS DE PAGO</b></div>
        <table class="table" style="margin-top:0px; margin-bottom: 5px;">';
    
    if (empty($pagos)) {
        $html .= '<tr><td class="text-center">PENDIENTE DE PAGO</td></tr>';
    } else {
        foreach ($pagos as $p) {
            $html .= '<tr><td style="border-bottom:none;">' . htmlspecialchars($p['metodo_pago']) . '</td><td class="text-right" style="border-bottom:none;">' . htmlspecialchars($moneda) . ' ' . number_format($p['monto'], 2) . '</td></tr>';
        }
    }

    $html .= '
        </table>
        </div>

        <div class="divider"></div>

        ' . ($gd_enabled ? '
        <div class="qr">
            <barcode code="' . $id_orden . '|' . $orden['total_final'] . '|' . $fecha . '" type="QR" class="barcode" size="0.8" disableborder="1" />
        </div>' : '
        <div class="footer small text-muted" style="margin-top:10px">
            [Nota: Logo y QR no disponibles (GD Extension deshabilitada)]
        </div>
        ') . '

        <div class="footer">
            ¡GRACIAS POR SU PREFERENCIA!<br>
            Vuelva pronto.
        </div>
    </div>';

    // 6. Generar PDF
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [80, 250], // 80mm de ancho, altura variable (o fija larga)
        'margin_left' => 2,
        'margin_right' => 2,
        'margin_top' => 2,
        'margin_bottom' => 2,
    ]);

    $mpdf->SetDisplayMode('fullpage');
    
    // Comando para abrir diálogo de impresión automáticamente
    $mpdf->SetJS('this.print();');
    
    $mpdf->WriteHTML($html);

    // Salida al navegador
    $mpdf->Output("Ticket_" . $id_orden . ".pdf", Destination::INLINE);

} catch (Throwable $e) {
    die("Error generando ticket: [" . get_class($e) . "] " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
}
