<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?? 'REPORTE DE ÓRDENES' ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #2b2c40; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #2b2c40; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 13px; font-weight: bold; }
        
        .info-tab { width: 100%; margin-bottom: 25px; font-size: 12px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-tab td { padding: 4px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px; }
        th { background-color: #2b2c40; color: #ffffff; font-weight: bold; text-align: left; padding: 10px; border: 1px solid #2b2c40; text-transform: uppercase; }
        td { padding: 9px; border: 1px solid #e8e8e8; }
        tr:nth-child(even) { background-color: #fafafa; }
        
        .status-badge { padding: 3px 7px; border-radius: 4px; font-size: 9px; font-weight: 800; text-transform: uppercase; color: #fff; display: inline-block; }
        .bg-success { background-color: #71dd37; }
        .bg-warning { background-color: #ffab00; }
        .bg-danger { background-color: #ff3e1d; }
        .bg-info { background-color: #03c3ec; }
        .bg-secondary { background-color: #8592a3; }
        
        .total-box { float: right; width: 260px; background-color: #f5f5f9; border: 2px solid #2b2c40; padding: 12px; margin-top: 15px; border-radius: 6px; }
        .total-item { text-align: right; }
        .total-label { font-weight: bold; color: #2b2c40; font-size: 11px; text-transform: uppercase; }
        .total-val { font-weight: 900; color: #2b2c40; font-size: 18px; display: block; margin-top: 2px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE CONSOLIDADO DE SOCIOS' ?></h1>
        <p>Sistema de Gestión Carwash - Panel Administrativo</p>
    </div>

    <table class="info-tab">
        <tr>
            <td width="15%"><strong>Rango de Fechas:</strong></td>
            <td width="35%"><?= $f_inicio ?> al <?= $f_fin ?></td>
            <td width="15%"><strong>Fecha Emisión:</strong></td>
            <td width="35%"><?= date('d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <td><strong>Filtro Estado:</strong></td>
            <td><?= $estado_label ?></td>
            <td><strong>Usuario Filtro:</strong></td>
            <td><?= $usuario_label ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="7%">ID</th>
                <th width="12%">Fecha</th>
                <th width="18%">Cliente</th>
                <th width="12%">Vehículo</th>
                <th width="28%">Servicios / Productos</th>
                <th width="10%">Estado</th>
                <th width="13%" style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $granTotal = 0;
            if (empty($ordenes)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No se encontraron órdenes en el rango seleccionado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ordenes as $o): 
                    $color = 'secondary';
                    if($o['estado'] === 'FINALIZADO') $color = 'success';
                    elseif($o['estado'] === 'POR_COBRAR') $color = 'warning';
                    elseif($o['estado'] === 'EN_PROCESO') $color = 'info';
                    elseif($o['estado'] === 'ANULADO') $color = 'danger';
                    
                    if($o['estado'] !== 'ANULADO') $granTotal += (float)$o['total_final'];
                ?>
                <tr>
                    <td style="font-weight: bold;">#<?= $o['id_orden'] ?></td>
                    <td><?= date('d/m/y H:i', strtotime($o['fecha_creacion'])) ?></td>
                    <td><?= $o['cliente'] ?></td>
                    <td><?= $o['vehiculo'] ?></td>
                    <td style="font-size: 9px; color: #555;"><?= $o['servicios_resumen'] ?: '---' ?></td>
                    <td style="text-align: center;"><span class="status-badge bg-<?= $color ?>"><?= $o['estado'] ?></span></td>
                    <td style="text-align: right; font-weight: bold;">S/ <?= number_format($o['total_final'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total-box">
        <div style="text-align: right;">
            <span class="total-label">INGRESOS NETOS:</span><br>
            <span class="total-val">S/ <?= number_format($granTotal, 2) ?></span>
        </div>
        <p style="font-size: 9px; margin: 5px 0 0 0; color: #666; text-align: right;">* Excluye órdenes anuladas</p>
    </div>

    <div class="footer">
        Generado automáticamente por el Sistema Carwash SaaS - Página {PAGENO} de {nb}
    </div>
</body>
</html>
