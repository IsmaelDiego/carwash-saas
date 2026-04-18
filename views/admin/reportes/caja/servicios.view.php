<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?? 'REPORTE DETALLADO DE CAJA' ?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; font-size: 10px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #696cff; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #111; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .filters-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee; }
        .filters-info table { width: 100%; border: none; }
        .filters-info td { border: none; padding: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #696cff; color: #ffffff; padding: 8px; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { padding: 7px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; color: #fff; }
        .bg-service { background-color: #03c3ec; }
        .bg-product { background-color: #ffab00; }
        
        .footer { text-align: right; font-size: 9px; color: #999; position: fixed; bottom: 0; width: 100%; }
        .totals-section { margin-top: 20px; border-top: 2px solid #333; text-align: right; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE DETALLADO DE CAJA' ?></h1>
        <p>Análisis de Operaciones y Servicios - Carwash Pro</p>
    </div>

    <div class="filters-info">
        <table>
            <tr>
                <td><strong>Periodo:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
                <td><strong>Responsable:</strong> <?= $idUsuario == 'TODOS' ? 'Todos' : 'Filtro específico' ?></td>
            </tr>
            <tr>
                <td><strong>Generado por:</strong> Antigravity BI Engine</td>
                <td><strong>Fecha Generación:</strong> <?= date('d/m/Y h:i A') ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Orden</th>
                <th>Fecha/Hora</th>
                <th>Responsable</th>
                <th>Tipo</th>
                <th>Ítem / Concepto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_general = 0;
            $total_servicios = 0;
            $total_productos = 0;
            foreach ($operaciones as $op): 
                $total_general += $op['total'];
                if($op['tipo_item'] === 'SERVICIO') $total_servicios += $op['total'];
                else $total_productos += $op['total'];
            ?>
            <tr>
                <td class="fw-bold">#<?= $op['id_orden'] ?></td>
                <td><?= date('d/m/y H:i', strtotime($op['fecha_registro'])) ?></td>
                <td><?= htmlspecialchars($op['cajero_nombre'] ?: 'N/A') ?></td>
                <td>
                    <span class="badge <?= $op['tipo_item'] === 'SERVICIO' ? 'bg-service' : 'bg-product' ?>">
                        <?= $op['tipo_item'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($op['item_nombre']) ?></td>
                <td class="text-center"><?= $op['cantidad'] ?></td>
                <td class="text-right">S/ <?= number_format($op['precio_unitario'], 2) ?></td>
                <td class="text-right fw-bold">S/ <?= number_format($op['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-section">
        <div style="font-size: 11px; margin-bottom: 5px;">Total en Servicios: <strong>S/ <?= number_format($total_servicios, 2) ?></strong></div>
        <div style="font-size: 11px; margin-bottom: 5px;">Total en Productos: <strong>S/ <?= number_format($total_productos, 2) ?></strong></div>
        <div style="font-size: 16px; color: #696cff; margin-top: 10px;">
            <strong>INGRESO BRUTO TOTAL: S/ <?= number_format($total_general, 2) ?></strong>
        </div>
    </div>

    <div class="footer">
        Página {PAGENO} de {nbpg} | Carwash SaaS BI | <?= date('Y') ?>
    </div>
</body>
</html>
