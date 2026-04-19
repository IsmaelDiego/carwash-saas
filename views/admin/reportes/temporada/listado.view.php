<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #003366; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #003366; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #666; font-size: 11px; }
        
        table.main-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.main-table th { 
            background-color: #003366; 
            color: #ffffff; 
            text-align: left; 
            padding: 10px; 
            text-transform: uppercase;
        }
        table.main-table td { 
            padding: 10px; 
            border-bottom: 1px solid #eee; 
        }
        
        .badge-status {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .active { background-color: #e8fadf; color: #71dd37; }
        .closed { background-color: #f8f9fa; color: #999; }

        .points-box {
            background-color: #e7e7ff;
            color: #696cff;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?></h1>
        <p>Control de Ciclos y Fidelización de Clientes - CarWash Pro BI</p>
    </div>

    <table style="width: 100%; margin-bottom: 15px; font-size: 10px;">
        <tr>
            <td><strong>Generado:</strong> <?= date('d/m/Y H:i') ?></td>
            <td style="text-align: right;"><strong>Analista:</strong> Admin BI Engine</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <?php if ($tipo === 'general'): ?>
                <tr>
                    <th width="40%">Nombre de la Temporada</th>
                    <th width="20%" class="text-center">Fecha Inicio</th>
                    <th width="20%" class="text-center">Fecha Fin</th>
                    <th width="20%" class="text-center">Estado</th>
                </tr>
            <?php elseif ($tipo === 'rendimiento'): ?>
                <tr>
                    <th width="30%">Temporada</th>
                    <th width="20%" class="text-center">Puntos Emitidos</th>
                    <th width="20%" class="text-center">Canjes Realizados</th>
                    <th width="15%" class="text-center">Ratio Canje</th>
                    <th width="15%" class="text-center">Estado</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th width="30%">Temporada</th>
                    <th width="20%" class="text-center">Duración (Días)</th>
                    <th width="25%" class="text-center">Promedio Puntos/Día</th>
                    <th width="25%" class="text-center">Intensidad</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">No existen periodos registrados en el sistema.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $r): ?>
                    <?php if ($tipo === 'general'): ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?></td>
                            <td class="text-center"><?= $r['fecha_fin'] ? date('d/m/Y', strtotime($r['fecha_fin'])) : '---' ?></td>
                            <td class="text-center">
                                <span class="badge-status <?= $r['estado'] ? 'active' : 'closed' ?>">
                                    <?= $r['estado'] ? 'EN CURSO' : 'FINALIZADA' ?>
                                </span>
                            </td>
                        </tr>
                    <?php elseif ($tipo === 'rendimiento'): ?>
                        <?php 
                            $ratio = $r['puntos_gen'] > 0 ? round(($r['puntos_red'] / $r['puntos_gen']) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center"><span class="points-box"><?= number_format($r['puntos_gen']) ?> PTS</span></td>
                            <td class="text-center"><span class="points-box" style="background-color:#dff3ff; color:#03c3ec;"><?= number_format($r['puntos_red']) ?> CANJES</span></td>
                            <td class="text-center fw-bold"><?= $ratio ?>%</td>
                            <td class="text-center">
                                <span class="badge-status <?= $r['estado'] ? 'active' : 'closed' ?>">
                                    <?= $r['estado'] ? 'ACTIVA' : 'CERRADA' ?>
                                </span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                            $ini = new DateTime($r['fecha_inicio']);
                            $fin = $r['fecha_fin'] ? new DateTime($r['fecha_fin']) : new DateTime();
                            $dias = $ini->diff($fin)->days + 1;
                            $promedio = round($r['puntos_gen'] / $dias, 1);
                        ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-center"><?= $dias ?> días activos</td>
                            <td class="text-center fw-bold"><?= $promedio ?> pts/día</td>
                            <td class="text-center">
                                <span class="badge-status <?= $promedio > 10 ? 'active' : 'closed' ?>">
                                    <?= $promedio > 10 ? 'ALTA DEMANDA' : 'ESTÁNDAR' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este reporte muestra el balance de puntos generados y redimidos durante los periodos de fidelización. Información generada para control administrativo.</p>
    </div>
</body>
</html>
