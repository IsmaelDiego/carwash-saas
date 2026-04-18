<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?? 'ANÁLISIS DE ÍTEMS' ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #03c3ec; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #2b2c40; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 13px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 10px; }
        th { background-color: #03c3ec; color: #ffffff; font-weight: bold; text-align: left; padding: 10px; border: 1px solid #03c3ec; text-transform: uppercase; }
        td { padding: 8px; border: 1px solid #e8e8e8; }
        
        .row-item { background-color: #fff; }
        .tipo-badge { 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            display: block;
            text-align: center;
            color: #ffffff;
            line-height: 1;
        }
        .tipo-servicio { background-color: #03c3ec; }
        .tipo-producto { background-color: #71dd37; }
        
        .summary-table { width: 45%; float: right; margin-top: 20px; border: 1px solid #eee; border-radius: 6px; overflow: hidden; }
        .summary-table td { padding: 10px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'ANÁLISIS DETALLADO DE ÍTEMS' ?></h1>
        <p>Desglose por Servicios y Productos</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px; font-size: 11px; font-style: italic;">
        <tr>
            <td><strong>Periodo:</strong> <?= $f_inicio ?> - <?= $f_fin ?></td>
            <td style="text-align: right;"><strong>Generado:</strong> <?= date('d/m/Y H:i') ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="8%"># Orden</th>
                <th width="12%">Fecha</th>
                <th width="12%">Tipo</th>
                <th width="33%">Descripción del Ítem</th>
                <th width="8%" style="text-align: center;">Cant.</th>
                <th width="12%" style="text-align: right;">P. Unit (S/)</th>
                <th width="15%" style="text-align: right;">Subtotal (S/)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totServicios = 0; $totProductos = 0; $totItems = 0;
            if (empty($detalles)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 30px;">No hay movimientos detallados para el filtro seleccionado.</td></tr>
            <?php else: ?>
                <?php foreach ($detalles as $d): 
                    $isServicio = ($d['tipo_item'] === 'SERVICIO');
                    if($isServicio) $totServicios += (float)$d['subtotal'];
                    else $totProductos += (float)$d['subtotal'];
                    $totItems += (float)$d['subtotal'];
                ?>
                <tr class="row-item">
                    <td style="font-weight: bold;">#<?= $d['id_orden'] ?></td>
                    <td><?= date('d/m/y', strtotime($d['fecha_registro'])) ?></td>
                    <td style="text-align: center;">
                        <span class="tipo-badge tipo-<?= strtolower($d['tipo_item']) ?>"><?= $d['tipo_item'] ?></span>
                    </td>
                    <td><?= $d['item_nombre'] ?></td>
                    <td style="text-align: center;"><?= $d['cantidad'] ?></td>
                    <td style="text-align: right;">S/ <?= number_format($d['precio_unitario'], 2) ?></td>
                    <td style="text-align: right; font-weight: bold;">S/ <?= number_format($d['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="border-bottom: 1px solid #ddd;">Total Servicios:</td>
            <td style="text-align: right; border-bottom: 1px solid #ddd;">S/ <?= number_format($totServicios, 2) ?></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #ddd;">Total Productos:</td>
            <td style="text-align: right; border-bottom: 1px solid #ddd;">S/ <?= number_format($totProductos, 2) ?></td>
        </tr>
        <tr style="background-color: #f5f5f9;">
            <td style="font-weight: bold;">TOTAL GENERAL:</td>
            <td style="text-align: right; font-weight: bold; font-size: 14px; color: #2b2c40;">S/ <?= number_format($totItems, 2) ?></td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999;">
        Documento de Auditoría Interna - Carwash Cloud Solution v2.0
    </div>
</body>
</html>
