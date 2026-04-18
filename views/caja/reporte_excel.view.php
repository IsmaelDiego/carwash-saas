<?php
$subtotal_metodos = 0;
?>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th colspan="2" style="background:#555; color:#fff; font-size:16px;">REPORTE DE CAJA <?= $fecha_reporte ?> - SESIÓN #<?= $id_sesion ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Cajero:</strong></td>
            <td><?= htmlspecialchars($cajero_nombre) ?></td>
        </tr>
        <tr>
            <td><strong>Fecha Apertura:</strong></td>
            <td><?= date('d/m/Y H:i:s', strtotime($cajaActiva['fecha_apertura'])) ?></td>
        </tr>
        <tr>
            <td><strong>Mondo Incial (Apertura):</strong></td>
            <td>S/ <?= number_format($monto_apertura, 2) ?></td>
        </tr>
        <tr>
            <td><strong>Total Ventas Registradas:</strong></td>
            <td>S/ <?= number_format($ventas, 2) ?></td>
        </tr>
        <tr>
            <td><strong>Efectivo Esperado en Caja:</strong></td>
            <td>S/ <?= number_format($monto_apertura + $ventas, 2) ?></td>
        </tr>
    </tbody>
</table>

<br>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th colspan="2" style="background:#555; color:#fff;">INGRESOS POR MÉTODO DE PAGO</th>
        </tr>
        <tr>
            <th>MÉTODO</th>
            <th>TOTAL (S/)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($metodos as $m): $subtotal_metodos += $m['total']; ?>
            <tr>
                <td><?= htmlspecialchars($m['metodo_pago']) ?></td>
                <td align="right">S/ <?= number_format($m['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($metodos)): ?><tr>
                <td colspan="2" align="center">No hay ingresos registrados</td>
            </tr><?php endif; ?>
    </tbody>
</table>

<br>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th colspan="3" style="background:#555; color:#fff;">PRODUCTOS DE COMPRA DIRECTA VENDIDOS</th>
        </tr>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Total (S/)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td align="center"><?= $p['total_cant'] ?></td>
                <td align="right">S/ <?= number_format($p['total_monto'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?><tr>
                <td colspan="3" align="center">Ninguno</td>
            </tr><?php endif; ?>
    </tbody>
</table>

<br>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th colspan="3" style="background:#555; color:#fff;">SERVICIOS REALIZADOS</th>
        </tr>
        <tr>
            <th>Servicio</th>
            <th>Cantidad</th>
            <th>Total (S/)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($servicios as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['nombre']) ?></td>
                <td align="center"><?= $s['cant'] ?></td>
                <td align="right">S/ <?= number_format($s['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($servicios)): ?><tr>
                <td colspan="3" align="center">Ninguno</td>
            </tr><?php endif; ?>
    </tbody>
</table>