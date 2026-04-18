<?php

require_once __DIR__ . '/vendor/autoload.php';
require '../conexion_reportes/r_conexion.php';
require 'numeroletras/CifrasEnLetras.php';
//Incluímos la clase pago
$v=new CifrasEnLetras(); 
$mpdf = new \Mpdf\Mpdf();
$query = "SELECT
			cotizacion.coti_id, 
			cotizacion.coti_comprobante, 
			cotizacion.coti_serie, 
			cotizacion.coti_num_comprobante, 
			cotizacion.coti_fregistro, 
			cotizacion.coti_hora, 
			cotizacion.coti_total, 
			cotizacion.coti_impuesto, 
			cotizacion.coti_porcentaje, 
			cotizacion.cliente_id, 
			cotizacion.compro_id, 
			cotizacion.coti_atiende, 
			cotizacion.coti_dias, 
			CONCAT_WS(' ',cliente.cliente_nombres, cliente.cliente_ape_p, cliente.cliente_ape_m) as cliente, 
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
			configuracion.confi_cnta01, 
			configuracion.confi_nro_cuenta01, 
			configuracion.confi_cnta02, 
			configuracion.confi_nro_cuenta02, 
			cotizacion.fpago_id, 
			forma_pago.fpago_id, 
			forma_pago.fpago_descripcion
			FROM
			cotizacion
			INNER JOIN
			cliente
			ON 
				cotizacion.cliente_id = cliente.cliente_id
			INNER JOIN
			forma_pago
			ON 
				cotizacion.fpago_id = forma_pago.fpago_id,
			configuracion

		where cotizacion.coti_id = '".$_GET['codigo']."'";

$resultado = $mysqli ->query($query);
while ($row1 = $resultado-> fetch_assoc()){
	$totalpagar=($row1['venta_total']);
	//Convertimos el total en letras
	$letra=($v->convertirEurosEnLetras($totalpagar));	


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
	    			<h3><b>                '.$row1['confi_razon_social'].'</b></h3><br>
	    			<b>R.U.C. &nbsp; &nbsp; &nbsp; : </b>'.$row1['confi_ruc'].'<br>
	    			<b>Direcci&oacute;n &nbsp;: </b>     '.$row1['confi_direccion'].'<br>
	    			<b>Tel &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: </b>           '.$row1['confi_telefono'].' -  <b>Cel: </b>'.$row1['confi_celular'].'<br>
	    		
	    			<b>Correo &nbsp; &nbsp; &nbsp;: </b>'.$row1['confi_correo'].'<br>
	    		</th>

	    		<th width="30%" style="border-top:0px; border-left:0px; border-bottom:0px; border-right:0px; text-align:center;">
	    			<h2 style="color:black;">'.$row1['coti_comprobante'].' </h2><br>
	    			<h3 style="">N. :            '.$row1['coti_num_comprobante'].' </h3>
	    		</th>
	    	</tr>
	    </thead>
    </table>
     

    </header>
 	<table  style="border-collapse; " border="1" class="round_table" >
	    <thead >
	    	<tr>
	    
	    		<th width="50%" style="  text-align:left; border-right:0px; ">
	    			<b>Nombre &nbsp;&nbsp; &nbsp;: </b>'.$row1['cliente'].'<br>
	    			<br><b>Dni/Ruc &nbsp; &nbsp;  : </b>'.$row1['cliente_dni'].'<br>
	    			<br><b>Direcci&oacute;n  &nbsp;: </b>'.$row1['cliente_direccion'].'<br>
	    			<br><b>Atenci&oacute;n  &nbsp; : </b>'.$row1['coti_atiende'].'<br>
	    			
	    		</th>
	    		<th width="50%" style="text-align:right; border-left:0px;">
	    			<b>Fecha: </b>'.$row1['coti_fregistro'].'<br><br>
	    			<br><br>
	    			<br><br>
	    			<br><br>
	    			
	    		</th>

	    	</tr>
	    </thead>
    </table>
    De acuerdo a su requerimiento le hacemos llegar la siguiente propuesta econ&oacute;mica:
    <main>
      <table  style="border-collapse; " border="1" >
        <thead>
          <tr>
            <th   class="service"  >ITEM</th>
            <th class="desc">DESCRIPCION</th>
            <th>PRECIO</th>
            <th>CANTIDAD</th>
            <th>SUB TOTAL</th>

          </tr>
        </thead>
        <tbody>';
        $query2 = "SELECT
					cotizacion_detalle.coti_detalle_id, 
					cotizacion_detalle.producto_id, 
					producto.producto_nombre, 
					cotizacion_detalle.coti_detalle_cantidad, 
					cotizacion_detalle.coti_detalle_precio,
					cotizacion_detalle.coti_detalle_cantidad * cotizacion_detalle.coti_detalle_precio as subtotal
				FROM
					cotizacion_detalle
					INNER JOIN
					producto
					ON 
						cotizacion_detalle.producto_id = producto.producto_id
						where cotizacion_detalle.coti_id = '".$row1['coti_id']."'";

						$contador=0;
						$resultado2 = $mysqli ->query($query2);
						while ($row2 = $resultado2-> fetch_assoc()){
							$contador++;

        $html.='<tr >
            <td class="service" style="border-bottom:0px; border-top:0px;">'.$contador.'</td>
            <td class="desc" style="border-bottom:0px ;border-top:0px;">'.$row2['producto_nombre'].'</td>
            <td class="unit" style="border-bottom:0px; border-top:0px;">'.$row2['coti_detalle_precio'].'</td>
            <td class="qty" style="border-bottom:0px; border-top:0px;">'.$row2['coti_detalle_cantidad'].'</td>
            <td class="total" style="border-bottom:0px; border-top:0px;">'.round($row2['subtotal'],2).'</td>
            </tr>';
        }
        if ($row1['coti_impuesto'] >0) {
        $html.='
          
          <tr>
            <td colspan="4" style="border-bottom:0px;  border-left:0px; border-right:0px; ">SUBTOTAL</td>
            <td class="total" style="">'.round(($row1['coti_total'] - $row1['coti_impuesto'] ),2).'</td>
          </tr>

       	
          <tr> 
            <td colspan="4" style="border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px;">IGV '.($row1['coti_porcentaje']*100).'% </td>
            <td class="total" style=" ">'.round($row1['coti_impuesto'],2).'</td>
          </tr>
          <tr>
            <td colspan="4" class="grand total" style="border-bottom:0px; border-top:0px;border-right:0px;  border-left:0px;"><b>TOTAL</b></td>
	            <td class="grand total" style=" ">'.$row1['coti_total'].'</td>
          </tr>';
        }else{
        	$html.='
        	<tr>
            <td colspan="4" class="grand total" style="border-bottom:0px; border-right:0px;  border-left:0px;"><b>TOTAL</b></td>
	            <td class="grand total" style=" ">'.$row1['coti_total'].'</td>
          </tr>';


        }

          $html.='
           
        </tbody>
      </table>
      <br>
      Agradecemos enviar Orden de compra o Cotizaci&oacute;n firmada a: '.$row1['confi_correo'].'
       <br> <br>
      <div id="notices">
       <br>
        <div><b>Dep&oacute;sitos:</b></div><br>
        <div class="notice">'.$row1['confi_cnta01'].' : '.$row1['confi_nro_cuenta01'].'</div>
        <div class="notice">'.$row1['confi_cnta02'].' : '.$row1['confi_nro_cuenta02'].'</div>
      </div>
      <div id="notices">
       <br>
        <div><b>Condiciones:</b></div><br>
        <div>V&aacute;lida hasta &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$row1['coti_dias'].' dias.</div>
        <div>Pago &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : '.$row1['fpago_descripcion'].'</div>
      </div>
    </main>
    <footer>

    </footer>
  </body>
</html>';
}
$css = file_get_contents('css/style_coti.css');
$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output();