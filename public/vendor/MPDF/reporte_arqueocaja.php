<?php

require_once __DIR__ . '/vendor/autoload.php';
require '../conexion_reportes/r_conexion.php';
//require 'numeroletras/CifrasEnLetras.php';
//Incluímos la clase pago
//$v=new CifrasEnLetras(); 
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [80, 100]]);
$query = "SELECT caja.caja_id, 
				caja.caja_descripcion, 
				caja.caja_monto_inicial, 
				caja.caja_monto_final, 
				caja.caja_monto_egreso, 
				caja.caja_fecha_ap, 
				caja.caja_fecha_cie, 
				caja.caja_total_ingreso, 
				caja.caja_total_egreso, 
				caja.caja_monto_total, 
				caja.caja_hora_aper, 
				caja.caja_estado, 
				caja.caja_monto_servicio, 
				caja.caja_total_servicio, 
				caja.caja_hora_cierre, 
				configuracion.confi_razon_social
				FROM
				caja,
				configuracion
			WHERE caja.caja_id =   '".$_GET['codigo']."'";

	$resultado = $mysqli ->query($query);
while ($row1 = $resultado-> fetch_assoc()){
	//$totalpagar=($row1['servicio_monto']);
	//Convertimos el total en letras
	//$letra=($v->convertirEurosEnLetras($totalpagar));

	//para ver el logo en la i,presion
	//<h3 style="text-align:center;display: inline-block;margin: 0px;padding: 0px; "><img src="../'.$row1['config_foto'].'" width="45px"></h3><br>

$html.='	
	<h5 style="text-align:center;display: inline-block;margin: 0px;padding: 0px; ">'.$row1['confi_razon_social'].'</h5><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arqueo de Caja<br>
	-----------------------------------------<br>
	
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-size:11px">Ticket N.:&nbsp; 000'.$row1['caja_id'].'&nbsp;</h6>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Apertura&nbsp;:&nbsp; '.$row1['caja_fecha_ap'].' - '.$row1['caja_hora_aper'].'</h6>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Cierre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; '.$row1['caja_fecha_cie'].' - '.$row1['caja_hora_cierre'].'</h6>
	
		 
	------------------------------------------<br>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Monto Apertura&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S/.  '.$row1['caja_monto_inicial'].'</h6> 
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Monto Ventas&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;S/. '.$row1['caja_monto_final'].'&nbsp;('.$row1['caja_total_ingreso'].')</h6>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Monto Servicio&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;S/.  '.$row1['caja_monto_servicio'].'&nbsp;&nbsp;('.$row1['caja_total_servicio'].')</h6>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-weight:normal;">Monto Egresos&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;S/.  '.$row1['caja_monto_egreso'].'&nbsp;&nbsp;('.$row1['caja_total_egreso'].')</h6></b>
	------------------------------------------<br>
	<h6 style="display: inline-block;margin: 0px;padding: 0px;  font-size:11px">Monto Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;S/.  '.$row1['caja_monto_total'].' </h6>
	

	';








}

//$css = file_get_contents('');
//$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output();