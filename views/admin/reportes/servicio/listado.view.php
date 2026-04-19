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
        
        .badge-price {
            padding: 4px 10px;
            background-color: #f8f9fa;
            color: #2b2c40;
            border: 2px solid #2b2c40;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .badge-info {
            padding: 3px 8px;
            background-color: #e7e7ff;
            color: #696cff;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .badge-success {
            padding: 3px 8px;
            background-color: #e8fadf;
            color: #71dd37;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE DE SERVICIOS' ?></h1>
        <p>Sistema de Gestión de Tarifarios y Ventas</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px; font-size: 11px;">
        <tr>
            <td><strong>Fecha de Generación:</strong> <?= date('d/m/Y H:i') ?></td>
            <td style="text-align: right;"><strong>Periodo:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <?php if ($tipo === 'tarifario'): ?>
                <tr>
                    <th width="45%">Nombre del Servicio</th>
                    <th width="20%" class="text-right">Precio Base</th>
                    <th width="20%" class="text-center">Genera Puntos</th>
                    <th width="15%" class="text-center">Estado</th>
                </tr>
            <?php elseif ($tipo === 'fidelidad'): ?>
                <tr>
                    <th width="40%">Nombre del Servicio</th>
                    <th width="30%" class="text-center">Acumula Puntos</th>
                    <th width="30%" class="text-center">Permite Canje</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th width="50%">Nombre del Servicio</th>
                    <th width="20%" class="text-center">Frecuencia de Uso</th>
                    <th width="30%" class="text-right">Recaudación Total (S/)</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">No se encontraron datos para los filtros seleccionados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $r): ?>
                    <?php if ($tipo === 'tarifario'): ?>
                        <tr>
                            <td class="fw-bold"><?= strtoupper($r['nombre']) ?></td>
                            <td class="text-right">
                                <span class="badge-price">S/ <?= number_format($r['precio_base'], 2) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-info"><?= $r['acumula_puntos'] == 1 ? 'SÍ ACUMULA' : 'NO ACUMULA' ?></span>
                            </td>
                            <td class="text-center">
                                <span class="<?= $r['estado'] ? 'badge-success' : 'badge-info' ?>">
                                    <?= $r['estado'] ? 'ACTIVO' : 'INACTIVO' ?>
                                </span>
                            </td>
                        </tr>
                    <?php elseif ($tipo === 'fidelidad'): ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center">
                                <span class="badge-info"><?= $r['acumula_puntos'] == 1 ? 'HABILITADO' : '---' ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-info"><?= $r['permite_canje'] == 1 ? 'PERMITIDO' : '---' ?></span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="fw-bold" style="font-size: 11px;"><?= strtoupper($r['nombre']) ?></td>
                            <td class="text-center">
                                <span class="badge-price" style="border-width:1px;"><?= $r['total_usos'] ?> ATENCIONES</span>
                            </td>
                            <td class="text-right">
                                <span class="fw-bold" style="color: #2b2c40;">S/ <?= number_format($r['total_recaudado'], 2) ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento es un reporte administrativo oficial de servicios. Generado por el Sistema BI.</p>
    </div>
</body>
</html>
