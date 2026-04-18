<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?? 'CONCILIACIÓN DE PAGOS' ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #71dd37; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #2b2c40; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 13px; font-weight: bold; }
        
        .summary-boxes { margin-bottom: 30px; text-align: center; }
        .box { display: inline-block; width: 22%; padding: 12px; border: 1px solid #e8e8e8; border-radius: 8px; text-align: center; margin-right: 1%; background-color: #fafafa; }
        .box-title { font-size: 10px; color: #8592a3; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
        .box-value { font-size: 15px; font-weight: 900; color: #2b2c40; }
        
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 10px; }
        th { background-color: #71dd37; color: #ffffff; text-align: left; padding: 10px; border: 1px solid #71dd37; text-transform: uppercase; font-weight: bold; }
        td { padding: 9px; border: 1px solid #eee; }
        tr:nth-child(even) { background-color: #f9fdf9; }
        
        .metodo-pill { padding: 3px 8px; border-radius: 12px; font-size: 8px; font-weight: 800; background: #e8fadf; color: #71dd37; border: 1px solid #71dd37; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'CONCILIACIÓN DE PAGOS' ?></h1>
        <p>Reporte de Flujo de Caja por Órdenes</p>
    </div>

    <table style="width: 100%; margin-bottom: 15px; font-size: 11px;">
        <tr>
            <td><strong>Desde:</strong> <?= $f_inicio ?> <strong>Hasta:</strong> <?= $f_fin ?></td>
            <td style="text-align: right;"><strong>Usuario:</strong> <?= $usuario_label ?></td>
        </tr>
    </table>

    <?php 
    $efectivo = 0; $yape = 0; $plin = 0; $tarjeta = 0;
    foreach ($pagos as $p) {
        $m = strtoupper($p['metodo_pago']);
        if ($m === 'EFECTIVO') $efectivo += (float)$p['monto'];
        elseif ($m === 'YAPE') $yape += (float)$p['monto'];
        elseif ($m === 'PLIN') $plin += (float)$p['monto'];
        elseif ($m === 'TARJETA') $tarjeta += (float)$p['monto'];
    }
    ?>

    <table style="width: 100%; border: none; margin-bottom: 20px;">
        <tr>
            <td width="24%" style="border: 1px solid #e8e8e8; background: #fafafa; padding: 10px; border-radius: 8px; text-align: center;">
                <div style="font-size: 9px; color: #8592a3; font-weight: 800; text-transform: uppercase;">Efectivo</div>
                <div style="font-size: 14px; font-weight: 900; color: #2b2c40;">S/ <?= number_format($efectivo, 2) ?></div>
            </td>
            <td width="1%"></td>
            <td width="24%" style="border: 1px solid #e8e8e8; background: #fafafa; padding: 10px; border-radius: 8px; text-align: center;">
                <div style="font-size: 9px; color: #8592a3; font-weight: 800; text-transform: uppercase;">Yape</div>
                <div style="font-size: 14px; font-weight: 900; color: #2b2c40;">S/ <?= number_format($yape, 2) ?></div>
            </td>
            <td width="1%"></td>
            <td width="24%" style="border: 1px solid #e8e8e8; background: #fafafa; padding: 10px; border-radius: 8px; text-align: center;">
                <div style="font-size: 9px; color: #8592a3; font-weight: 800; text-transform: uppercase;">Plin</div>
                <div style="font-size: 14px; font-weight: 900; color: #2b2c40;">S/ <?= number_format($plin, 2) ?></div>
            </td>
            <td width="1%"></td>
            <td width="24%" style="border: 1px solid #e8e8e8; background: #fafafa; padding: 10px; border-radius: 8px; text-align: center;">
                <div style="font-size: 9px; color: #8592a3; font-weight: 800; text-transform: uppercase;">Tarjeta</div>
                <div style="font-size: 14px; font-weight: 900; color: #2b2c40;">S/ <?= number_format($tarjeta, 2) ?></div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="15%">ID Orden</th>
                <th width="20%">Fecha / Hora</th>
                <th width="20%">Método</th>
                <th width="25%">Estado Orden</th>
                <th width="20%" style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (empty($pagos)): ?>
                <tr><td colspan="5" style="text-align: center; padding: 30px;">No se registraron pagos finalizados en este periodo.</td></tr>
            <?php else: ?>
                <?php foreach ($pagos as $p): ?>
                <tr>
                    <td style="font-weight: bold;">#<?= $p['id_orden'] ?></td>
                    <td><?= date('d/m/y H:i', strtotime($p['fecha_movimiento'])) ?></td>
                    <td><span class="metodo-pill"><?= $p['metodo_pago'] ?></span></td>
                    <td><?= $p['orden_estado'] ?></td>
                    <td style="text-align: right; font-weight: bold;">S/ <?= number_format($p['monto'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        <p><strong>Nota:</strong> Este reporte solo incluye información de ingresos cobrados de órdenes finalizadas.</p>
    </div>
</body>
</html>
