<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?? $titulo_reporte ?? 'REPORTE DETALLADO' ?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #696cff; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #111; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .filters-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee; }
        .filters-info table { width: 100%; border: none; }
        .filters-info td { border: none; padding: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #696cff; color: #ffffff; padding: 8px; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { padding: 7px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .status-badge { padding: 3px 6px; border-radius: 4px; color: #fff; font-size: 8px; font-weight: bold; }
        .status-abierta { background-color: #71dd37; }
        .status-cerrada { background-color: #8592a3; }
        
        .diff-pos { color: #71dd37; font-weight: bold; }
        .diff-neg { color: #ff3e1d; font-weight: bold; }
        
        .footer { text-align: right; font-size: 9px; color: #999; position: fixed; bottom: 0; width: 100%; }
        
        .totals-box { margin-top: 10px; border-top: 2px solid #333; padding-top: 10px; }
        .totals-table { width: 300px; margin-left: auto; }
        .totals-table td { border: none; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?? 'REPORTE DE ARQUEOS' ?></h1>
        <p>Sistema de Gestión Carwash - Panel Administrativo</p>
    </div>

    <div class="filters-info">
        <table>
            <tr>
                <td><strong>Periodo:</strong> <?= $f_inicio ?> al <?= $f_fin ?></td>
                <td><strong>Responsable:</strong> <?= $idUsuario == 'TODOS' ? 'Todos' : 'Filtro específico' ?></td>
            </tr>
            <tr>
                <td><strong>Estado Sesiones:</strong> <?= $estado ?></td>
                <td><strong>Fecha Generación:</strong> <?= date('d/m/Y h:i A') ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cajero</th>
                <th>Apertura</th>
                <th>Cierre</th>
                <th class="text-right">Inicial</th>
                <th class="text-right">Ventas</th>
                <th class="text-right">Esperado</th>
                <th class="text-right">Real</th>
                <th class="text-right">Diff.</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $t_inicial = 0; $t_ventas = 0; $t_esperado = 0; $t_real = 0; $t_diff = 0;
            foreach ($arqueos as $cs): 
                if ($idUsuario !== 'TODOS' && $cs['id_usuario'] != $idUsuario) continue;
                if ($estado !== 'TODOS' && $cs['estado'] !== $estado) continue;

                $mStatus = strtolower($cs['estado']);
                $mEsperado = floatval($cs['monto_apertura']) + floatval($cs['recaudado_acumulado']);
                $mReal = $cs['monto_cierre_real'] !== null ? floatval($cs['monto_cierre_real']) : null;
                $mDiff = $cs['diferencia'] !== null ? floatval($cs['diferencia']) : null;

                $t_inicial += $cs['monto_apertura'];
                $t_ventas += $cs['recaudado_acumulado'];
                $t_esperado += $mEsperado;
                if($mReal !== null) $t_real += $mReal;
                if($mDiff !== null) $t_diff += $mDiff;
            ?>
            <tr>
                <td class="fw-bold">#<?= $cs['id_sesion'] ?></td>
                <td><?= htmlspecialchars($cs['cajero_nombre']) ?><br><small style="color:#888"><?= $cs['rol_apertura_nombre'] ?></small></td>
                <td><?= date('d/m/y H:i', strtotime($cs['fecha_apertura'])) ?></td>
                <td><?= $cs['fecha_cierre'] ? date('d/m/y H:i', strtotime($cs['fecha_cierre'])) : '---' ?></td>
                <td class="text-right">S/ <?= number_format($cs['monto_apertura'], 2) ?></td>
                <td class="text-right">S/ <?= number_format($cs['recaudado_acumulado'], 2) ?></td>
                <td class="text-right fw-bold">S/ <?= number_format($mEsperado, 2) ?></td>
                <td class="text-right"><?= $mReal !== null ? 'S/ '.number_format($mReal, 2) : '---' ?></td>
                <td class="text-right <?= $mDiff < 0 ? 'diff-neg' : ($mDiff > 0 ? 'diff-pos' : '') ?>">
                    <?= $mDiff !== null ? 'S/ '.number_format($mDiff, 2) : '---' ?>
                </td>
                <td class="text-center">
                    <span class="status-badge status-<?= $mStatus ?>"><?= $cs['estado'] ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-box">
        <table class="totals-table">
            <tr>
                <td>Total Inicial Acumulado:</td>
                <td class="text-right fw-bold">S/ <?= number_format($t_inicial, 2) ?></td>
            </tr>
            <tr>
                <td>Total Ventas (Ingresos Netos):</td>
                <td class="text-right fw-bold">S/ <?= number_format($t_ventas, 2) ?></td>
            </tr>
            <tr>
                <td style="font-size: 14px;">TOTAL GENERAL ESPERADO:</td>
                <td class="text-right fw-bold" style="font-size: 14px; color: #696cff;">S/ <?= number_format($t_esperado, 2) ?></td>
            </tr>
            <tr>
                <td>Efectivo Real Declarado:</td>
                <td class="text-right fw-bold">S/ <?= number_format($t_real, 2) ?></td>
            </tr>
            <tr>
                <td>Balance Final (Diferencias):</td>
                <td class="text-right fw-bold <?= $t_diff < 0 ? 'diff-neg' : 'diff-pos' ?>">
                    S/ <?= number_format($t_diff, 2) ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Página {PAGENO} de {nbpg} | Generado por Carwash SaaS BI | <?= date('Y') ?>
    </div>
</body>
</html>
