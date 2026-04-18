<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #2b2c40; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #2b2c40; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 12px; }
        
        table.main-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.main-table th { 
            background-color: #2b2c40; 
            color: #ffffff; 
            text-align: left; 
            padding: 10px; 
            text-transform: uppercase;
        }
        table.main-table td { 
            padding: 10px; 
            border-bottom: 1px solid #eee; 
        }
        
        .badge-placa {
            padding: 4px 10px;
            background-color: #f8f9fa;
            color: #2b2c40;
            border: 2px solid #2b2c40;
            border-radius: 6px;
            font-weight: bold;
            font-family: monospace;
            font-size: 12px;
        }
        
        .badge-cat {
            padding: 3px 8px;
            background-color: #e7e7ff;
            color: #696cff;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE DE VEHÍCULOS' ?></h1>
        <p>Sistema de Gestión de Flota Automotriz - CarWash Pro BI</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px; font-size: 11px;">
        <tr>
            <td><strong>Fecha de Emisión:</strong> <?= date('d/m/Y H:i') ?></td>
            <td style="text-align: right;"><strong>Vehículos Registrados:</strong> <?= count($lista) ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Placa</th>
                <th width="15%">Categoría</th>
                <th width="35%">Propietario / Cliente</th>
                <th width="12%">Color</th>
                <th width="26%">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">No se encontraron vehículos registrados con estos criterios.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $v): ?>
                <tr>
                    <td style="text-align: center;">
                        <span class="badge-placa"><?= strtoupper($v['placa']) ?></span>
                    </td>
                    <td>
                        <span class="badge-cat"><?= $v['categoria'] ?></span>
                    </td>
                    <td>
                        <div style="font-weight: bold; font-size: 11px;"><?= strtoupper($v['cliente']) ?></div>
                    </td>
                    <td><?= $v['color'] ?: '---' ?></td>
                    <td style="color: #666; font-style: italic;"><?= $v['observaciones'] ?: 'Sin observaciones' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento es un reporte técnico de auditoría de flota. Generado por Antigravity BI Engine.</p>
    </div>
</body>
</html>
