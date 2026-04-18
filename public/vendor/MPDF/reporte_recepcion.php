<?php

require_once __DIR__ . '/vendor/autoload.php';
require '../conexion_reportes/r_conexion.php';
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [80, 230]]);
$query = "SELECT
	configuracion.confi_razon_social, 
	configuracion.confi_ruc, 
	configuracion.confi_nombre_representante, 
	configuracion.confi_direccion, 
	configuracion.confi_celular, 
	configuracion.confi_telefono, 
	configuracion.confi_correo, 
	configuracion.config_foto, 
	configuracion.confi_estado, 
	configuracion.confi_url, 
	recepcion.rece_id, 
	recepcion.cliente_id, 
	cliente.cliente_nombres, 
	cliente.cliente_celular, 
	cliente.cliente_dni, 
	recepcion.rece_equipo, 
	recepcion.rece_caracteristicas, 
	recepcion.motivo_id, 
	motivo.motivo_descripcion, 
	CONCAT_WS(' - ',recepcion.rece_equipo,recepcion.rece_concepto) as motivo,
	recepcion.rece_concepto, 
	recepcion.rece_monto, 
	recepcion.rece_fregistro, 
	recepcion.rece_estado, 
	recepcion.rece_adelanto, 
	recepcion.rece_debe,
	recepcion.rece_accesorios, 
	recepcion.rece_fentrega, 
	recepcion.marca_id, 
	recepcion.serie,
	marca.marca_descripcion
FROM
	configuracion,
	recepcion
	INNER JOIN
	cliente
	ON 
		recepcion.cliente_id = cliente.cliente_id
	INNER JOIN
	motivo
	ON 
		recepcion.motivo_id = motivo.motivo_id
	INNER JOIN
	marca
	ON 
		recepcion.marca_id = marca.marca_id 

	where recepcion.rece_id =  '".$_GET['codigo']."'";

	$resultado = $mysqli ->query($query);
while ($row1 = $resultado-> fetch_assoc()){



$html.='
	<h3 style="text-align:center;display: inline-block;margin: 0px;padding: 0px; "><img src="../' . $row1['config_foto'] . '" width="150px"></h3>
	<h3 style="text-align:center;display: inline-block;margin: 0px;padding: 0px; ">'.$row1['confi_razon_social'].'</h3>
	<h5 style="text-align:center;display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">'.$row1['confi_direccion'].'</h5>	
	<h5 style="text-align:center;display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">R.U.C '.$row1['confi_ruc'].'</h5>
	<h5 style="text-align:center;display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Cel. '.$row1['confi_celular'].'</h5>

	

	N. Orden:&nbsp; 00'.$row1['rece_id'].'&nbsp;&nbsp; - &nbsp;&nbsp;'.$row1['rece_fregistro'].' <br>
	Cliente:&nbsp; '.$row1['cliente_nombres'].'<br>
	------------------------------------------------<br>
		        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Datos del Equipo<br>
	------------------------------------------------<br>
	Equipo:&nbsp;  '.$row1['rece_equipo'].'<br> 
	Marca:&nbsp; '.$row1['marca_descripcion'].'<br>
	Accesorios:&nbsp; '.$row1['rece_accesorios'].'<br>
	Caracteristicas:&nbsp; '.$row1['rece_caracteristicas'].'<br>
	Serie:&nbsp; '.$row1['serie'].'<br>
	------------------------------------------------<br>
		      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Caracteristicas de falla<br>
	------------------------------------------------<br>
	Falla:&nbsp;  '.$row1['rece_concepto'].'<br> 
	Concepto:&nbsp;  '.$row1['motivo_descripcion'].' <br>
	F. Entrega: &nbsp;'.$row1['rece_fentrega'].'<br>
	Estado:&nbsp;  <b>'.$row1['rece_estado'].' </b><br>
	------------------------------------------------<br>';


	if ($row1['rece_adelanto'] > 0) {
        $html.='
	<h4 style="text-align:right;display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Adelanto S/.: '.$row1['rece_adelanto'].'</h4>
	<h4 style="text-align:right;display: inline;margin: 0px;padding: 0px;  font-weight:normal;">Pendiente S/.: '.$row1['rece_debe'].'</h4>
	<h4 style="text-align:right; margin: 0px;padding: 0px; ">Monto S/.: '.$row1['rece_monto'].'</h4><br>';
	}else{
        	$html.='<h4 style="text-align:right; margin: 0px;padding: 0px; ">Monto S/.: '.$row1['rece_monto'].'</h4><br>';
        }

$html.='
	<h4 style="text-align:center; margin: 0px;padding: 0px; "><barcode code="'.$row1['confi_url'].'" type="QR" class="barcode" size="0.7" disableborder="1" /></h4><br>
	<h6 style="text-align:center; margin: 0px;padding: 0px; "><b>Escanea o b&uacute;sca el estado de tu Operaci&oacute;n</b>  '.$row1['confi_url'].'</h6><br>

         
         ';

}

$css = file_get_contents('css/style_rece.css');
$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output();