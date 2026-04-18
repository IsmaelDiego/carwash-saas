<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja <?= $fecha_reporte ?> #<?= $id_sesion ?></title>
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f4f6f9; color: #333; padding: 20px; }
        .container { 
            max-width: 800px; 
            min-height: calc(100vh - 40px); 
            margin: 0 auto; 
            background: #fff; 
            padding: 30px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            border-radius: 8px; 
            display: flex; 
            flex-direction: column;
        }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #111; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .section-title { font-size: 16px; font-weight: bold; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #eee; padding: 10px; text-align: left; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; color: #333; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { background: #eef2ff; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .summary-row.total { font-size: 18px; font-weight: bold; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; color: #111; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; margin: 0 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-primary { background: #696cff; color: #fff; }
        .btn-success { background: #71dd37; color: #fff; }
        
        /* Asegurar que la firma no se separe */
        .signature-block { 
            page-break-inside: avoid; 
            text-align: center; 
            margin-top: auto; 
            padding-top: 50px;
            padding-bottom: 20px;
        }
        .signature-line {
            width: 250px;
            margin: 0 auto;
            border-top: 1px solid #333;
            margin-bottom: 10px;
        }
        
        @media print {
            body { background: #fff; padding: 0; }
            .container { 
                box-shadow: none; 
                max-width: 100%; 
                padding: 0; 
                min-height: 100vh;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <?php if(!isset($is_mpdf)): ?>
    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
        <a class="btn btn-success" href="<?= BASE_URL ?>/caja/dashboard/generar_reporte?formato=excel">📊 Exportar a Excel</a>
    </div>
    <?php endif; ?>
    <div class="container">
        <div class="header">
            <h1>REPORTE DE CAJA - SESIÓN #<?= $id_sesion ?></h1>
            <p>Generado el: <?= date('d/m/Y h:i A') ?></p>
        </div>

        <div class="summary-box">
            <div class="summary-row">
                <span>Cajero:</span>
                <strong><?= htmlspecialchars($cajero_nombre) ?></strong>
            </div>
            <div class="summary-row">
                <span>Fecha Apertura:</span>
                <strong><?= date('d/m/Y H:i:s', strtotime($cajaActiva['fecha_apertura'])) ?></strong>
            </div>
            <div class="summary-row">
                <span>Monto Inicial (Apertura):</span>
                <strong>S/ <?= number_format($monto_apertura, 2) ?></strong>
            </div>
            <div class="summary-row">
                <span>Total Vendido (Ingresos Netos):</span>
                <strong>S/ <?= number_format($ventas, 2) ?></strong>
            </div>
            <div class="summary-row total">
                <span>Dinero total esperado (Efectivo General):</span>
                <span>S/ <?= number_format($monto_apertura + $ventas, 2) ?></span>
            </div>
        </div>

        <div class="section-title">INGRESOS POR MÉTODO DE PAGO</div>
        <table>
            <thead>
                <tr>
                    <th>MÉTODO DE PAGO</th>
                    <th class="text-right">MONTO (S/)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metodos as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['metodo_pago']) ?></td>
                    <td class="text-right">S/ <?= number_format($m['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($metodos)): ?><tr><td colspan="2" class="text-center text-muted">Aún no hay ingresos</td></tr><?php endif; ?>
            </tbody>
        </table>

        <div style="display:flex; gap: 20px;">
            <div style="flex:1;">
                <div class="section-title">SERVICIOS REALIZADOS</div>
                <table>
                    <thead>
                        <tr><th>Servicio</th><th class="text-center">Cant.</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['nombre']) ?></td>
                            <td class="text-center"><?= $s['cant'] ?></td>
                            <td class="text-right">S/ <?= number_format($s['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($servicios)): ?><tr><td colspan="3" class="text-center text-muted">Ninguno</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="flex:1;">
                <div class="section-title">PRODUCTOS VENDIDOS</div>
                <table>
                    <thead>
                        <tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td class="text-center"><?= $p['total_cant'] ?></td>
                            <td class="text-right">S/ <?= number_format($p['total_monto'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($productos)): ?><tr><td colspan="3" class="text-center text-muted">Ninguno</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($descuentos['promo'] > 0 || $descuentos['puntos'] > 0): ?>
        <div class="summary-box" style="background: #fffafa; border-left: 4px solid #ff3b30; margin-top: 10px; padding: 15px;">
            <div style="font-weight:bold; margin-bottom:10px; font-size:14px; text-transform:uppercase;">Descuentos Globales Aplicados (Deducidos de Totales):</div>
            <?php if ($descuentos['promo'] > 0): ?>
            <div class="summary-row" style="color: #d32f2f;">
                <span>Descuento por Promociones:</span>
                <strong>- S/ <?= number_format($descuentos['promo'], 2) ?></strong>
            </div>
            <?php endif; ?>
            <?php if ($descuentos['puntos'] > 0): ?>
            <div class="summary-row" style="color: #d32f2f;">
                <span>Descuento por Puntos (Temporada):</span>
                <strong>- S/ <?= number_format($descuentos['puntos'], 2) ?></strong>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="signature-block">
            <?php if ($formato === 'print'): ?>
                <div class="signature-line"></div>
                <div style="font-size: 14px; font-weight:bold; color: #555;">Firma de Conformidad - <?= htmlspecialchars($cajero_nombre) ?></div>
                <div style="font-size: 11px; color: #888; margin-top: 5px;">Cajero Responsable</div>
            <?php else: ?>
                <div style="padding: 15px 30px; border: 1px dashed #696cff; background: #f8f9ff; border-radius: 12px; display: inline-block; min-width: 320px; box-shadow: 0 2px 10px rgba(105, 108, 255, 0.05);">
                    <div style="margin-bottom: 5px;">
                        <i class="bx bx-check-shield text-primary" style="font-size: 18px; vertical-align: middle;"></i>
                        <span style="font-size: 14px; font-weight:bold; color: #111; vertical-align: middle; margin-left: 5px; text-transform: uppercase;">
                            Reporte Validado Digitalmente
                        </span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #696cff;"><?= htmlspecialchars($cajero_nombre) ?></div>
                    <div style="font-size: 11px; color: #666; margin-top: 5px;">Este documento ha sido generado tras la declaración de conformidad del cajero.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            <?php if ($formato === 'print'): ?>
                // Dar un pequeño delay para asegurar renderizado en el iframe
                setTimeout(() => {
                    window.print();
                }, 500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
