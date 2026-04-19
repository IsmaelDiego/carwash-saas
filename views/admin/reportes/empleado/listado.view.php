<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_reporte ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #000; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10.5px; }
        th { background-color: #f2f2f2; color: #000; text-align: left; padding: 10px; border-bottom: 2px solid #000; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .badge-active { background-color: #e8fadf; color: #71dd37; }
        .badge-inactive { background-color: #ffe5e5; color: #ff4d49; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $titulo_pdf ?></h1>
        <p>Reporte Estratégico de Recursos Humanos | Fecha: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <?php if ($tipo === 'rendimiento'): ?>
                <tr>
                    <th width="10%">DNI</th>
                    <th width="35%">Nombre Completo</th>
                    <th width="15%">Cargo</th>
                    <th width="15%" class="text-center">Servicios</th>
                    <th width="15%" class="text-right">Recaudación</th>
                    <th width="10%" class="text-center">Estado</th>
                </tr>
            <?php elseif ($tipo === 'seguridad'): ?>
                <tr>
                    <th width="15%">Documento</th>
                    <th width="35%">Nombre</th>
                    <th width="15%">Nivel de Acceso</th>
                    <th width="20%">Fecha Registro</th>
                    <th width="15%" class="text-center">Estado Cuenta</th>
                </tr>
            <?php else: // MAESTRO / GENERAL ?>
                <tr>
                    <th width="10%">DNI</th>
                    <th width="30%">Nombre Completo</th>
                    <th width="20%">Cargo</th>
                    <th width="15%">Teléfono</th>
                    <th width="15%">Email</th>
                    <th width="10%" class="text-center">Estado</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (empty($lista)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4">No se encontraron colaboradores con los filtros seleccionados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista as $r): ?>
                    <tr>
                        <?php if ($tipo === 'rendimiento'): ?>
                            <td class="small fw-bold"><?= $r['dni'] ?></td>
                            <td><?= mb_strtoupper($r['nombres'], 'UTF-8') ?></td>
                            <td><span class="fw-bold"><?= $r['rol_nombre'] ?></span></td>
                            <td class="text-center fw-bold fs-5"><?= $r['total_ordenes'] ?></td>
                            <td class="text-right fw-bold">S/ <?= number_format($r['recaudacion_total'], 2) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $r['estado'] == 1 ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $r['estado'] == 1 ? 'ACTIVO' : 'INACTIVO' ?>
                                </span>
                            </td>
                        <?php elseif ($tipo === 'seguridad'): ?>
                            <td class="small fw-bold"><?= $r['dni'] ?></td>
                            <td><?= mb_strtoupper($r['nombres'], 'UTF-8') ?></td>
                            <td><span class="fw-bold fs-5 text-uppercase"><?= $r['rol_nombre'] ?></span></td>
                            <td><?= date('d/m/Y h:i A', strtotime($r['fecha_creacion'])) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $r['estado'] == 1 ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $r['estado'] == 1 ? 'CUENTA ACTIVA' : 'BLOQUEADO/INACTIVO' ?>
                                </span>
                            </td>
                        <?php else: // MAESTRO ?>
                            <td class="small fw-bold"><?= $r['dni'] ?></td>
                            <td><?= mb_strtoupper($r['nombres'], 'UTF-8') ?></td>
                            <td><span class="fw-bold"><?= $r['rol_nombre'] ?></span></td>
                            <td><?= $r['telefono'] ?: '---' ?></td>
                            <td class="small"><?= $r['email'] ?: '---' ?></td>
                            <td class="text-center">
                                <span class="badge <?= $r['estado'] == 1 ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $r['estado'] == 1 ? 'ACTIVO' : 'INACTIVO' ?>
                                </span>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Este documento es para uso exclusivo de la administración. Software de Gestión BI - <?= date('Y') ?>
    </div>
</body>
</html>
