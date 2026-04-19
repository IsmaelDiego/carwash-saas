<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #003366; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #003366; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 3px 0; color: #666; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-top: 10px; }
        th { background-color: #003366; color: white; text-align: left; padding: 8px; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; }
        .badge-success { background-color: #e8fadf; color: #71dd37; }
        .badge-warning { background-color: #fff2e2; color: #ff9f43; }
        .badge-danger { background-color: #ffe5e5; color: #ff4d49; }
        .badge-info { background-color: #e7e7ff; color: #696cff; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?></h1>
        <p>Sistema Gestor de Inventario Pro BI - Reporte Generado el <?= date('d/m/Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <?php if ($tipo === 'productos'): ?>
                <tr>
                    <th width="5%">ID</th>
                    <th width="35%">Nombre del Producto</th>
                    <th width="12%" class="text-right">P. Venta</th>
                    <th width="12%" class="text-center">Stock Act.</th>
                    <th width="12%" class="text-center">Stock Min.</th>
                    <th width="12%" class="text-center">Estado</th>
                    <th width="12%" class="text-center">Vencimiento</th>
                </tr>
            <?php elseif ($tipo === 'lotes'): ?>
                <tr>
                    <th width="8%"># Lote</th>
                    <th width="30%">Producto</th>
                    <th width="12%" class="text-center">Cant. Act.</th>
                    <th width="12%" class="text-center">Cant. Ini.</th>
                    <th width="12%" class="text-right">P. Compra</th>
                    <th width="13%" class="text-center">Vencimiento</th>
                    <th width="13%" class="text-center">Condición</th>
                </tr>
            <?php else: // KARDEX ?>
                <tr>
                    <th width="15%">Fecha/Hora</th>
                    <th width="10%">Operación</th>
                    <th width="15%">Referencia</th>
                    <th width="25%">Producto / Detalle</th>
                    <th width="10%" class="text-center">Lote</th>
                    <th width="10%" class="text-center">Cantidad</th>
                    <th width="15%">Usuario</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4">No se encontraron registros para el filtro seleccionado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $r): ?>
                    <?php if ($tipo === 'productos'): ?>
                        <tr>
                            <td><?= $r['id_producto'] ?></td>
                            <td class="fw-bold"><?= mb_strtoupper($r['nombre'], 'UTF-8') ?></td>
                            <td class="text-right fw-bold">S/ <?= number_format($r['precio_venta'], 2) ?></td>
                            <td class="text-center <?= $r['stock_actual'] <= $r['stock_minimo'] ? 'fw-bold text-danger' : '' ?>">
                                <?= $r['stock_actual'] ?>
                            </td>
                            <td class="text-center"><?= $r['stock_minimo'] ?></td>
                            <td class="text-center">
                                <?php 
                                    $bCls = $r['condicion_txt'] == 'AL DIA' ? 'badge-success' : ($r['condicion_txt'] == 'POR VENCER' ? 'badge-warning' : 'badge-danger');
                                ?>
                                <span class="badge <?= $bCls ?>"><?= $r['condicion_txt'] ?></span>
                            </td>
                            <td class="text-center"><?= $r['prox_vencimiento'] ?: '---' ?></td>
                        </tr>
                    <?php elseif ($tipo === 'lotes'): ?>
                        <tr>
                            <td class="fw-bold">#<?= $r['id_lote'] ?></td>
                            <td><?= mb_strtoupper($r['producto_nombre'], 'UTF-8') ?></td>
                            <td class="text-center fw-bold"><?= $r['cantidad_actual'] ?></td>
                            <td class="text-center"><?= $r['cantidad_inicial'] ?></td>
                            <td class="text-right">S/ <?= number_format($r['precio_compra'], 2) ?></td>
                            <td class="text-center"><?= $r['fecha_vencimiento'] ?: 'N/A' ?></td>
                            <td class="text-center">
                                <span class="badge <?= $r['condicion_txt'] == 'AL DIA' ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $r['condicion_txt'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php else: // KARDEX ?>
                        <tr>
                            <td><?= $r['fecha_fmt'] ?></td>
                            <td>
                                <span class="badge <?= in_array($r['tipo'], ['ENTRADA','REABASTECIMIENTO']) ? 'badge-success' : ($r['tipo'] == 'VENTA' ? 'badge-info' : 'badge-danger') ?>">
                                    <?= $r['tipo'] ?>
                                </span>
                            </td>
                            <td class="small"><?= $r['referencia'] ?></td>
                            <td class="fw-bold"><?= mb_strtoupper($r['producto_nombre_fmt'], 'UTF-8') ?></td>
                            <td class="text-center">#<?= $r['id_lote'] ?: '---' ?></td>
                            <td class="text-center fw-bold"><?= $r['cantidad'] ?></td>
                            <td class="small"><?= $r['usuario_nombre'] ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte interno de uso administrativo. Los datos reflejan el estado del almacén al momento de la generación.</p>
    </div>
</body>
</html>
