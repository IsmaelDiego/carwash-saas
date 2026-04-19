<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #000; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th { background-color: #f2f2f2; color: #000; text-align: left; padding: 8px; border-bottom: 2px solid #000; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .badge-aprobado { background-color: #e8fadf; color: #71dd37; }
        .badge-pendiente { background-color: #fff2e2; color: #ff9f43; }
        .badge-rechazado { background-color: #ffe5e5; color: #ff4d49; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?></h1>
        <p>Control de Permisos y Vacaciones | Fecha: <?= date('d/m/Y H:i') ?></p>
    </div>

    <?php if ($tipo_rep === 'consolidado'): ?>
        <table>
            <thead>
                <tr>
                    <th width="50%">Colaborador / Empleado</th>
                    <th width="25%" class="text-center">Cant. Permisos</th>
                    <th width="25%" class="text-center">Días Totales Ausencia</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $conso = [];
                    foreach ($lista as $r) {
                        $emp = $r['empleado'];
                        if (!isset($conso[$emp])) $conso[$emp] = ['cant' => 0, 'dias' => 0];
                        $d1 = new \DateTime($r['fecha_inicio']);
                        $d2 = new \DateTime($r['fecha_fin']);
                        $diff = $d1->diff($d2)->days + 1;
                        $conso[$emp]['cant']++;
                        $conso[$emp]['dias'] += $diff;
                    }
                    if (empty($conso)):
                ?>
                    <tr><td colspan="3" class="text-center py-4">Sin datos consolidados.</td></tr>
                <?php else: ?>
                    <?php foreach ($conso as $nombre => $v): ?>
                        <tr>
                            <td class="fw-bold fs-5"><?= mb_strtoupper($nombre, 'UTF-8') ?></td>
                            <td class="text-center"><?= $v['cant'] ?> permisos</td>
                            <td class="text-center fw-bold fs-5"><?= $v['dias'] ?> días</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php elseif ($tipo_rep === 'analisis'): ?>
        <!-- REPORTE DE ANÁLISIS DE MOTIVOS -->
        <table>
            <thead>
                <tr>
                    <th width="30%">Colaborador</th>
                    <th width="15%">Tipo</th>
                    <th width="10%" class="text-center">Duración</th>
                    <th width="45%">Motivo / Justificación Detallada</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lista)): ?>
                    <tr><td colspan="4" class="text-center py-4">No hay datos de análisis.</td></tr>
                <?php else: ?>
                    <?php foreach ($lista as $r): 
                        $d1 = new \DateTime($r['fecha_inicio']);
                        $d2 = new \DateTime($r['fecha_fin']);
                        $dias = $d1->diff($d2)->days + 1;
                    ?>
                        <tr>
                            <td class="small fw-bold"><?= mb_strtoupper($r['empleado'], 'UTF-8') ?></td>
                            <td><?= $r['tipo'] ?></td>
                            <td class="text-center fw-bold"><?= $dias ?> d.</td>
                            <td style="font-style: italic; color: #555; background-color: #fcfcfc;">
                                <?= $r['motivo'] ?: '<span style="color:#ccc;">(Sin justificación registrada)</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- BITÁCORA GENERAL -->
        <table>
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th width="25%">Colaborador</th>
                    <th width="15%">Tipo</th>
                    <th width="12%">Inicio</th>
                    <th width="12%">Fin</th>
                    <th width="8%" class="text-center">Días</th>
                    <th width="20%" class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lista)): ?>
                    <tr><td colspan="7" class="text-center py-4">No se encontraron registros en la bitácora.</td></tr>
                <?php else: ?>
                    <?php foreach ($lista as $r): 
                        $d1 = new \DateTime($r['fecha_inicio']);
                        $d2 = new \DateTime($r['fecha_fin']);
                        $dias = $d1->diff($d2)->days + 1;
                    ?>
                        <tr>
                            <td class="fw-bold">#<?= $r['id_permiso'] ?></td>
                            <td class="small fw-bold"><?= mb_strtoupper($r['empleado'], 'UTF-8') ?></td>
                            <td><?= $r['tipo'] ?></td>
                            <td><?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['fecha_fin'])) ?></td>
                            <td class="text-center fw-bold"><?= $dias ?></td>
                            <td class="text-center">
                                <?php 
                                    $bCls = 'badge-pendiente';
                                    if ($r['estado'] === 'APROBADO') $bCls = 'badge-aprobado';
                                    if ($r['estado'] === 'RECHAZADO') $bCls = 'badge-rechazado';
                                ?>
                                <span class="badge <?= $bCls ?>"><?= $r['estado'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        Este documento es para control interno de RRHH. Los días calculados incluyen feriados y fines de semana según el rango de fechas.
    </div>
</body>
</html>
