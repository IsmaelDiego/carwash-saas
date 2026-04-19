<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #1a1a1a; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #1a1a1a; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th { background-color: #f2f2f2; color: #333; text-align: left; padding: 8px; border-bottom: 2px solid #1a1a1a; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .badge-pagado { background-color: #e8fadf; color: #71dd37; }
        .badge-pendiente { background-color: #fff2e2; color: #ff9f43; }
        .badge-retrasado { background-color: #ffe5e5; color: #ff4d49; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .summary-box { margin-top: 30px; width: 300px; float: right; border: 1px solid #eee; padding: 10px; background-color: #fafafa; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; }
        .total-row { border-top: 2px solid #1a1a1a; margin-top: 10px; padding-top: 5px; font-size: 13px; font-weight: bold; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?></h1>
        <p>Reporte de Egresos y Planilla | Fecha de Emisión: <?= date('d/m/Y H:i') ?></p>
    </div>

    <?php if ($tipo_rep === 'consolidado'): ?>
        <!-- VISTA CONSOLIDADA POR EMPLEADO -->
        <table>
            <thead>
                <tr>
                    <th width="40%">Colaborador / Empleado</th>
                    <th width="20%" class="text-right">Total Pagado</th>
                    <th width="20%" class="text-right">Total Pendiente</th>
                    <th width="20%" class="text-right">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $consolidado = [];
                    $g_pagado = 0; $g_pendiente = 0;
                    foreach($lista as $r) {
                        $emp = $r['empleado'];
                        if(!isset($consolidado[$emp])) $consolidado[$emp] = ['pagado'=>0, 'pendiente'=>0];
                        if($r['estado'] === 'PAGADO') {
                            $consolidado[$emp]['pagado'] += $r['monto'];
                            $g_pagado += $r['monto'];
                        } else {
                            $consolidado[$emp]['pendiente'] += $r['monto'];
                            $g_pendiente += $r['monto'];
                        }
                    }

                    if (empty($consolidado)):
                ?>
                    <tr><td colspan="4" class="text-center py-4">No hay datos para consolidar.</td></tr>
                <?php else: ?>
                    <?php foreach ($consolidado as $nombre => $d): ?>
                        <tr>
                            <td class="fw-bold"><?= mb_strtoupper($nombre, 'UTF-8') ?></td>
                            <td class="text-right fw-bold text-success">S/ <?= number_format($d['pagado'], 2) ?></td>
                            <td class="text-right fw-bold text-danger">S/ <?= number_format($d['pendiente'], 2) ?></td>
                            <td class="text-right fw-bold" style="background-color: #f9f9f9;">S/ <?= number_format($d['pagado'] + $d['pendiente'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Resumen Consolidado Final -->
        <div class="summary-box">
            <div class="summary-item">
                <span>Total General Pagado:</span>
                <span class="fw-bold">S/ <?= number_format($g_pagado, 2) ?></span>
            </div>
            <div class="summary-item">
                <span>Total General Pendiente:</span>
                <span class="fw-bold">S/ <?= number_format($g_pendiente, 2) ?></span>
            </div>
            <div class="summary-item total-row">
                <span>INVERSIÓN TOTAL:</span>
                <span>S/ <?= number_format($g_pagado + $g_pendiente, 2) ?></span>
            </div>
        </div>

    <?php else: ?>
        <!-- VISTA DETALLADA (BITÁCORA / PENDIENTES) -->
        <table>
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th width="25%">Colaborador</th>
                    <th width="12%">Concepto</th>
                    <th width="15%">Periodo/Ref</th>
                    <th width="12%" class="text-right">Monto</th>
                    <th width="12%" class="text-center">F. Pago</th>
                    <th width="16%" class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $total_pagado = 0;
                    $total_pendiente = 0;
                    if (empty($lista)): 
                ?>
                    <tr><td colspan="7" class="text-center py-4">No se registraron movimientos en este periodo.</td></tr>
                <?php else: ?>
                    <?php foreach ($lista as $r): 
                        if ($r['estado'] === 'PAGADO') $total_pagado += $r['monto'];
                        else $total_pendiente += $r['monto'];
                    ?>
                        <tr>
                            <td class="fw-bold">#<?= $r['id_pago'] ?></td>
                            <td><?= mb_strtoupper($r['empleado'], 'UTF-8') ?></td>
                            <td><?= $r['tipo'] ?></td>
                            <td><?= $r['periodo'] ?: 'Unico' ?></td>
                            <td class="text-right fw-bold">S/ <?= number_format($r['monto'], 2) ?></td>
                            <td class="text-center"><?= $r['fecha_pago'] ? date('d/m/Y', strtotime($r['fecha_pago'])) : '---' ?></td>
                            <td class="text-center">
                                <?php 
                                    $bCls = 'badge-pendiente';
                                    if ($r['estado'] === 'PAGADO') $bCls = 'badge-pagado';
                                    if ($r['estado'] === 'RETRASADO') $bCls = 'badge-retrasado';
                                ?>
                                <span class="badge <?= $bCls ?>"><?= $r['estado'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-item">
                <span>Total Pagado:</span>
                <span class="fw-bold">S/ <?= number_format($total_pagado, 2) ?></span>
            </div>
            <div class="summary-item">
                <span>Total Pendiente:</span>
                <span class="fw-bold">S/ <?= number_format($total_pendiente, 2) ?></span>
            </div>
            <div class="summary-item total-row">
                <span>TOTAL BRUTO:</span>
                <span>S/ <?= number_format($total_pagado + $total_pendiente, 2) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer">
        Documento oficial de control de pagos. Los datos reflejan el estado financiero al momento de su extracción.
    </div>
</body>
</html>
