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
        
        .badge-formal {
            padding: 4px 10px;
            background-color: #f8f9fa;
            color: #2b2c40;
            border: 1px solid #2b2c40;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
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
        <h1><?= $titulo_pdf ?></h1>
        <p>Sistema de Gestión de Marketing y Fidelización</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px; font-size: 11px;">
        <tr>
            <td><strong>Fecha de Generación:</strong> <?= date('d/m/Y H:i') ?></td>
            <td style="text-align: right;"><strong>Periodo:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <?php if ($tipo === 'general'): ?>
                <tr>
                    <th width="45%">Campaña / Descripción</th>
                    <th width="20%">Descuento</th>
                    <th width="20%">Tipo</th>
                    <th width="15%">Estado</th>
                </tr>
            <?php elseif ($tipo === 'rendimiento'): ?>
                <tr>
                    <th width="40%">Campaña Estratégica</th>
                    <th width="20%" class="text-center">Valor Promo</th>
                    <th width="20%" class="text-center">Tipo</th>
                    <th width="20%" class="text-center">Total Canjes</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th width="35%">Cliente</th>
                    <th width="30%">Campaña Aplicada</th>
                    <th width="15%" class="text-center">Valor</th>
                    <th width="20%" class="text-center">Fecha Canje</th>
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
                    <?php if ($tipo === 'general'): ?>
                        <tr>
                            <td class="fw-bold"><?= strtoupper($r['nombre']) ?></td>
                            <td class="fw-bold" style="color:#2b2c40">
                                <?= $r['valor'] ?><?= $r['tipo_descuento'] == 'PORCENTAJE' ? '%' : ' S/' ?>
                            </td>
                            <td>
                                <span class="badge-info"><?= $r['tipo_descuento'] ?></span>
                            </td>
                            <td>
                                <span class="<?= $r['estado'] ? 'badge-success' : 'badge-info' ?>">
                                    <?= $r['estado'] ? 'ACTIVA' : 'EXPIRADA' ?>
                                </span>
                            </td>
                        </tr>
                    <?php elseif ($tipo === 'rendimiento'): ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center fw-bold"><?= $r['valor'] ?><?= $r['tipo_descuento'] == 'PORCENTAJE' ? '%' : ' S/' ?></td>
                            <td class="text-center small"><?= $r['tipo_descuento'] ?></td>
                            <td class="text-center">
                                <span class="badge-formal"><?= $r['total_usos'] ?> USOS</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombres'].' '.$r['apellidos'], 'UTF-8') ?></td>
                            <td><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center fw-bold"><?= $r['valor'] ?><?= $r['tipo_descuento'] == 'PORCENTAJE' ? '%' : ' S/' ?></td>
                            <td class="text-center"><?= date('d/m/Y H:i', strtotime($r['fecha_uso'])) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento es un reporte administrativo oficial de marketing. Generado por el Sistema BI.</p>
    </div>
</body>
</html>
