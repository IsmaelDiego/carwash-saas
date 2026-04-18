<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; font-size: 10px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #696cff; padding-bottom: 8px; }
        .header h1 { margin: 0; color: #111; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 3px 0 0; color: #666; font-size: 9px; }
        
        .filters-info { background: #f8f9fa; padding: 8px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #eee; }
        .filters-info table { width: 100%; border: none; }
        .filters-info td { border: none; padding: 1px 0; font-size: 9px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #696cff; color: #ffffff; padding: 6px; text-align: left; text-transform: uppercase; font-size: 8.5px; }
        td { padding: 5px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .footer { text-align: right; font-size: 8px; color: #999; position: fixed; bottom: 0; width: 100%; }
        
        .summary-totals { margin-top: 5px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_reporte ?></h1>
        <p>Sistema de Gestión Carwash - Inteligencia de Negocios</p>
    </div>

    <div class="filters-info">
        <table>
            <tr>
                <td><strong>Filtro Fecha:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
                <td><strong>Tipo Reporte:</strong> <?= strtoupper($tipo) ?></td>
            </tr>
            <tr>
                <td><strong>Responsable:</strong> <?= $idUsuario == 'TODOS' ? 'Consolidado Global' : 'Usuario Específico' ?></td>
                <td><strong>Generado:</strong> <?= date('d/m/Y h:i A') ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $h): ?>
                    <th class="<?= strpos($h, '(S/)') !== false ? 'text-right' : '' ?>"><?= $h ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_acumulado = 0;
            foreach ($data_rows as $row): 
                $val_final = end($row);
                if(is_numeric($val_final)) $total_acumulado += $val_final;
            ?>
            <tr>
                <?php foreach ($row as $index => $cell): ?>
                    <td class="<?= (is_numeric($cell) && $index > 0) ? 'text-right' : '' ?>">
                        <?= (is_numeric($cell) && $index > 3) ? 'S/ '.number_format($cell, 2) : htmlspecialchars($cell) ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($data_rows)): ?>
            <tr><td colspan="<?= count($headers) ?>" class="text-center text-muted py-4">No se encontraron movimientos en este periodo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($total_acumulado > 0): ?>
    <div class="summary-totals">
        <strong>TOTAL RECAUDADO EN ESTE REPORTE: <span style="color:#696cff; font-size:14px;">S/ <?= number_format($total_acumulado, 2) ?></span></strong>
    </div>
    <?php endif; ?>

    <div class="footer">
        Página {PAGENO} de {nbpg} | Generado por Carwash SaaS BI | <?= date('Y') ?>
    </div>
</body>
</html>
