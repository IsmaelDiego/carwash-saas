<?php

require_once __DIR__ . '/vendor/autoload.php';
require '../conexion_reportes/r_conexion.php';
require 'numeroletras/CifrasEnLetras.php';
//Incluímos la clase pago
$v = new CifrasEnLetras();
$mpdf = new \Mpdf\Mpdf();
$query = "SELECT
		venta.venta_id, 
		venta.venta_comprobante, 
		venta.venta_serie, 
		venta.venta_num_comprobante, 
		venta.venta_fregistro, 
		venta.venta_hora, 
		venta.venta_total, 
		venta.venta_impuesto, 
		venta.venta_porcentaje, 
		venta.cliente_id, 
		venta.compro_id, 
		venta.observacion, 
		cliente.cliente_nombres, 
		cliente.cliente_dni, 
		cliente.cliente_direccion, 
		cliente.cliente_celular, 
		configuracion.confi_id, 
		configuracion.confi_razon_social, 
		configuracion.confi_ruc, 
		configuracion.confi_nombre_representante, 
		configuracion.confi_direccion, 
		configuracion.confi_celular, 
		configuracion.confi_telefono, 
		configuracion.confi_correo, 
		configuracion.config_foto, 
		configuracion.confi_url, 
		venta.fpago_id, 
		forma_pago.fpago_descripcion
		FROM
		venta
		INNER JOIN
		cliente
		ON 
			venta.cliente_id = cliente.cliente_id
		INNER JOIN
		forma_pago
		ON 
			venta.fpago_id = forma_pago.fpago_id,
		configuracion

	where venta.venta_id = '" . $_GET['codigo'] . "'";

$resultado = $mysqli->query($query);
while ($row1 = $resultado->fetch_assoc()) {
	$totalpagar = ($row1['venta_total']);
	//Convertimos el total en letras
	$letra = ($v->convertirEurosEnLetras($totalpagar));


	$html = '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="style.css" media="all" />
  </head>
  <body>
    <header class="clearfix">
    <table style="border-collapse; " border="1" >
	    <thead >
	    	<tr>

	    		<th width="20%" style="border-top:0px; border-left:0px; border-bottom:0px; border-right:0px; "><img src="../' . $row1['config_foto'] . '" width="150px"></th>

	    		<th width="50%" style="border-top:0px; border-left:0px; border-bottom:0px; border-right:0px; text-align:left">
	    			<h3><b>' . $row1['confi_razon_social'] . '</b></h3><br>
	    			<b>Direcci&oacute;n: </b>' . $row1['confi_direccion'] . '<br>
	    			<b>Tel: </b>' . $row1['confi_telefono'] . ' -  <b>Cel: </b>' . $row1['confi_celular'] . '<br>
	    		
	    			<b>Correo: </b>' . $row1['confi_correo'] . '<br>
	    		</th>

	    		<th width="30%" style="text-align:center;">
	    			<h3 style="">R.U.C.  ' . $row1['confi_ruc'] . ' </h3><br>
	    			<h2 style="color:black;">' . $row1['venta_comprobante'] . ' DE VENTA    </h2><br>
	    			<h3 style="">' . $row1['venta_serie'] . '-' . $row1['venta_num_comprobante'] . ' </h3>
	    		</th>
	    	</tr>
	    </thead>
    </table>
     

    </header>
 	<table  style="border-collapse; " border="1" class="round_table" >
	    <thead >
	    	<tr>
	    
	    		<th width="50%" style="  text-align:left; border-right:0px; ">
	    			<b>Cliente   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : </b>' . $row1['cliente_nombres'] . '<br>
	    			<br><b>Doc. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : </b>' . $row1['cliente_dni'] . '<br>
	    			<br><b>Direcci&oacute;n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b>' . $row1['cliente_direccion'] . '<br>
	    			<br><b>Celular   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          : </b>' . $row1['cliente_celular'] . '<br>
					<br><b>Observaci&oacute;n : </b>' . $row1['observacion'] . '<br>
	    			
	    		</th>
	    		<th width="50%" style="text-align:right; border-left:0px;">
	    			<b>Fecha: </b>' . $row1['venta_fregistro'] . ' &nbsp;' . $row1['venta_hora'] . '<br><br>
	    			<br><br>
	    			<br><br>
	    			<br><br>
	    			
	    		</th>

	    	</tr>
	    </thead>
    </table>
    
    <main>
      <table  style="border-collapse; " border="1" >
        <thead>
          <tr>
            <th   class="service"  >ITEM</th>
            <th class="desc">PRODUCTO</th>
            <th>PRECIO</th>
            <th>CANTIDAD</th>
            <th>SUB TOTAL</th>

          </tr>
        </thead>
        <tbody>';
	$query2 = "SELECT
					detalle_venta.vdetalle_id, 
					detalle_venta.producto_id, 
					producto.producto_nombre, 
					detalle_venta.vdetalle_cantidad, 
					detalle_venta.vdetalle_precio,
					detalle_venta.vdetalle_cantidad * detalle_venta.vdetalle_precio as subtotal
				FROM
					detalle_venta
					INNER JOIN
					producto
					ON 
						detalle_venta.producto_id = producto.producto_id
						where detalle_venta.venta_id = '" . $row1['venta_id'] . "'";
	$contador = 0;
	$resultado2 = $mysqli->query($query2);
	while ($row2 = $resultado2->fetch_assoc()) {
		$contador++;

		$html .= '<tr >
            <td class="service" style="border-bottom:0px; border-top:0px;">' . $contador . '</td>
            <td class="desc" style="border-bottom:0px ;border-top:0px;">' . $row2['producto_nombre'] . '</td>
            <td class="unit" style="border-bottom:0px; border-top:0px;">' . $row2['vdetalle_precio'] . '</td>
            <td class="qty" style="border-bottom:0px; border-top:0px;">' . $row2['vdetalle_cantidad'] . '</td>
            <td class="total" style="border-bottom:0px; border-top:0px;">' . round($row2['subtotal'], 2) . '</td>
            </tr>';
	}
	if ($row1['compro_id'] == 2) {
		$html .= '
          
          <tr>
            <td colspan="4" style="border-bottom:0px;  border-left:0px; border-right:0px; ">SUBTOTAL S/. :</td>
            <td class="total" style="border-bottom:0px;  border-left:0px; border-right:0px;">' . round(($row1['venta_total'] - $row1['venta_impuesto']), 2) . '</td>
          </tr>
             <tr>
            <td colspan="1" rowspan="6" style=" border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px; ">
            <barcode code="' . $row1['cliente_nombres'] . '|' . $row1['cliente_dni'] . '|' . ($row1['venta_comprobante'] . '-' . $row1['venta_serie'] . '-' . $row1['venta_num_comprobante']) . '|' . $row1['venta_total'] . '" type="QR" class="barcode" size="1" disableborder="1" />
            </td>
          </tr>
       	
          <tr> 
            <td colspan="3" style="border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px;">IGV ' . ($row1['venta_porcentaje'] * 100) . '% :</td>
            <td class="total" style="border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px; ">' . round($row1['venta_impuesto'], 2) . '</td>
          </tr>
          <tr>
            <td colspan="3" class="grand total" style="border-bottom:0px; border-top:0px;border-right:0px;  border-left:0px;"><b>TOTAL S/. :</b></td>
	            <td class="grand total" style="border-bottom:0px; border-top:0px;  border-left:0px;border-right:0px ">' . $row1['venta_total'] . '</td>
          </tr>';
	} else {
		$html .= '
        	<tr>
            <td colspan="4" class="grand total" style="border-bottom:0px; border-right:0px;  border-left:0px;"><b>TOTAL S/. :</b></td>
	            <td class="grand total" style="border-bottom:0px; border-left:0px;border-right:0px ">' . $row1['venta_total'] . '</td>
          </tr>';
	}

	$html .= '
           
        </tbody>
      </table>
      <div id="notices">
        <div>SON:</div>
        <div class="notice">' . strtoupper($letra) . '</div>
		<br>
		<br>
		<div><b>Condiciones:</b></div><br>
        <div>Forma de Pago &nbsp;&nbsp;&nbsp; :&nbsp;&nbsp; ' . $row1['fpago_descripcion'] . '</div>
      </div>
    </main>
    <footer>

    </footer>
  </body>
</html>';
}
$css = file_get_contents('css/style.css');
$mpdf->WriteHTML($css, 1);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output();