<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #03c3ec; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #2b2c40; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 13px; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-size: 11px; }
        
        table.main-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.main-table th { 
            background-color: #03c3ec; 
            color: #ffffff; 
            text-align: left; 
            padding: 12px 10px; 
            text-transform: uppercase;
        }
        table.main-table td { 
            padding: 10px; 
            border-bottom: 1px solid #f0f0f0; 
        }
        
        .badge-point {
            padding: 3px 8px;
            background-color: #fff3e0;
            color: #ff9f43;
            border: 1px solid #ff9f43;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .badge-wa {
            padding: 3px 8px;
            background-color: #e8fadf;
            color: #71dd37;
            border: 1px solid #71dd37;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'LISTADO DE CLIENTES' ?></h1>
        <p>Sistema de Inteligencia de Clientes - Dashboard BI</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Fecha Generación:</strong> <?= date('d/m/Y H:i') ?></td>
            <td style="text-align: right;"><strong>Total Clientes:</strong> <?= count($lista) ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th width="35%">Nombre del Cliente</th>
                <th width="15%">DNI</th>
                <th width="15%">Teléfono</th>
                <th width="12%" style="text-align: center;">Puntos</th>
                <th width="15%" style="text-align: center;">WhatsApp</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">No se encontraron clientes con los filtros seleccionados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $c): ?>
                <tr>
                    <td style="font-weight: bold; color: #666;">#<?= $c['id_cliente'] ?></td>
                    <td style="font-weight: bold;"><?= strtoupper($c['nombres'] . ' ' . $c['apellidos']) ?></td>
                    <td><?= $c['dni'] ?></td>
                    <td><?= $c['telefono'] ?: '---' ?></td>
                    <td style="text-align: center;">
                        <span class="badge-point"><?= $c['puntos_acumulados'] ?> pts</span>
                    </td>
                    <td style="text-align: center;">
                        <?php if($c['estado_whatsapp'] == 1): ?>
                            <span class="badge-wa">SI</span>
                        <?php else: ?>
                            <span style="color: #999;">NO</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este reporte es para uso exclusivo administrativo. Generado de forma automatizada por el motor BI del sistema.</p>
    </div>
</body>
</html>
