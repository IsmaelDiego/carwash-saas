<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?? 'REPORTE DE PAGOS POR CAJA' ?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #696cff; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #111; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .filters-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee; }
        .filters-info table { width: 100%; border: none; }
        .filters-info td { border: none; padding: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #696cff; color: #ffffff; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .metodo-badge { border-radius: 4px; padding: 4px 8px; color: #fff; font-weight: bold; font-size: 9px; }
        .bg-efectivo { background-color: #71dd37; }
        .bg-yape { background-color: #00d2d3; }
        .bg-plin { background-color: #5f27cd; }
        .bg-tarjeta { background-color: #ff9f43; }
        
        .footer { text-align: right; font-size: 9px; color: #999; position: fixed; bottom: 0; width: 100%; }
        .totals-grid { display: flex; justify-content: flex-end; margin-top: 30px; }
        .total-box { border: 2px solid #696cff; background: #f8f9ff; padding: 15px; border-radius: 10px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE ESTRATÉGICO DE PAGOS' ?></h1>
        <p>Análisis de Flujo por Métodos de Pago</p>
    </div>

    <div class="filters-info">
        <table>
            <tr>
                <td><strong>Periodo:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
                <td><strong>Responsable:</strong> <?= $idUsuario == 'TODOS' ? 'Todos' : 'Filtro específico' ?></td>
            </tr>
            <tr>
                <td><strong>Estado de Órdenes:</strong> Solamente Finalizadas</td>
                <td><strong>Fecha Generación:</strong> <?= date('d/m/Y h:i A') ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Orden</th>
                <th>ID Pago</th>
                <th>Fecha de Pago</th>
                <th>Método de Pago</th>
                <th class="text-right">Monto Recaudado</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_pago = 0;
            $resumen_metodos = [];
            foreach ($pagos as $p): 
                $total_pago += $p['monto'];
                $metodo = $p['metodo_pago'];
                if(!isset($resumen_metodos[$metodo])) $resumen_metodos[$metodo] = 0;
                $resumen_metodos[$metodo] += $p['monto'];
                
                $fPago = !empty($p['fecha_pago']) ? date('d/m/y H:i', strtotime($p['fecha_pago'])) : '---';
            ?>
            <tr>
                <td class="fw-bold">ORD #<?= $p['id_orden'] ?></td>
                <td>#<?= $p['id_pago'] ?></td>
                <td><?= $fPago ?></td>
                <td>
                    <span class="metodo-badge bg-<?= strtolower($p['metodo_pago']) ?>">
                        <?= $p['metodo_pago'] ?>
                    </span>
                </td>
                <td class="text-right fw-bold" style="font-size: 13px;">S/ <?= number_format($p['monto'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 30px;">
        <div style="float: left; width: 40%;">
            <h3 style="margin-bottom: 10px; font-size: 12px; border-bottom: 1px solid #ddd;">RESUMEN POR MÉTODO</h3>
            <table style="border: none;">
                <?php foreach($resumen_metodos as $m => $total): ?>
                <tr>
                    <td style="border: none; padding: 4px 0;"><strong><?= $m ?>:</strong></td>
                    <td style="border: none; padding: 4px 0;" class="text-right fw-bold">S/ <?= number_format($total, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div style="float: right; width: 50%; text-align: right;">
            <div class="total-box">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">TOTAL NETO RECAUDADO PERIODOS SELECCIONADOS</div>
                <div style="font-size: 24px; color: #696cff; font-weight: bold;">
                    S/ <?= number_format($total_pago, 2) ?>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Página {PAGENO} de {nbpg} | Carwash SaaS BI | <?= date('Y') ?>
    </div>
</body>
</html>
