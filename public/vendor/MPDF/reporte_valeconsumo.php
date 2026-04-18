<?php

require_once __DIR__ . '/vendor/autoload.php';
require '../conexion_reportes/r_conexion.php';
require 'numeroletras/CifrasEnLetras.php';
//Incluímos la clase pago
//$v=new CifrasEnLetras(); 
// $mpdf = new \Mpdf\Mpdf();
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [210, 297],
    'orientation' => 'L'
]);
$query = "SELECT prove_ruc,
                    prove_razon,
                 prove_direccion
                     from proveedor

	where prove_id = '1'";

$resultado = $mysqli ->query($query);
while ($row1 = $resultado-> fetch_assoc()){
	//$totalpagar=($row1['venta_total']);
	//Convertimos el total en letras
	//$letra=($v->convertirEurosEnLetras($totalpagar));	

    $html = '<!DOCTYPE html>
    <html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="#" media="all" />
  </head>
  <body>
    <header class="">
    <table id="cabeza" style="border-collapse; " border="0" >
	    <thead >
	    	<tr>
	    		<th width="10%" ><img src="img/logo.png" width="60px"><br><br>               
                    <h4 style="text-align:center;font-size:11px"><b>SEAFROST S.A.C</b></h4>
                    <h4 style="text-align:center;font-size:11px"><b>20356922311</b></h4><br>
                    <h4 style="text-align:center;font-size:11px"><b></b></h4><br><br>
                    <h4 style="text-align:center;font-size:11px"><b>MZA. D LOTE 01 Z.I II</b></h4>
                    <h4 style="text-align:center;font-size:11px"><b>PAITA-PAITA-PAITA</b></h4>
                </th>

	    		<th id = "mov" width="40%" style="text-align: left;" >
                     <h5 id="nombre" ><b>MOVIMIENTO DE ALMACEN N: P20005024</b></h5>
                     <h4 id="nombre" style="display: inline-block;margin: 0px;padding: 0px;font-size:13px;  font-weight:normal;">_____________________________________________________</h4><br>
                     <h4 style="text-align:center !important;display: inline-block;margin: 0px;padding: 0px;font-size:13px;  font-weight:normal;">20/08/2022 14:00:00</h4><br>
                     <h4 id="alm" style="font-weight:normal;">Almacen: '.$row1['prove_ruc'].'</h4><br>

                     <h4 style="text-align:left; font-weight:normal;">Proveedor: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Tipo Movimiento: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Doc Interno: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Referencia: '.$row1['prove_ruc'].'</h4><br>   			
	    		</th>
                <tr>
                
                </tr>
                
                	    		
	    	</tr>
            <th width="10%" ><img src="img/logo.png" width="60px"><br><br>               
                    <h4 style="text-align:center;font-size:11px"><b>SEAFROST S.A.C</b></h4>
                    <h4 style="text-align:center;font-size:11px"><b>20356922311</b></h4><br>
                    <h4 style="text-align:center;font-size:11px"><b></b></h4><br><br>
                    <h4 style="text-align:center;font-size:11px"><b>MZA. D LOTE 01 Z.I II</b></h4>
                    <h4 style="text-align:center;font-size:11px"><b>PAITA-PAITA-PAITA</b></h4>
                </th>
            <th width="40%" style="border-top:0px; border-left:0px; border-bottom:0px; border-right:0px; ">
                     <h5 style="text-align:center;font-size:12px"><b>MOVIMIENTO DE ALMACEN N: P20005024</b></h5>
                     <h4 style="text-align:center;display: inline-block;margin: 0px;padding: 0px;font-size:13px;  font-weight:normal;">_____________________________________________________</h4><br>
                     <h4 style="text-align:center;display: inline-block;margin: 0px;padding: 0px;font-size:13px;  font-weight:normal;">20/08/2022 14:00:00</h4><br>
                     <h4 id="alm" style="text-align:left; font-weight:normal;">Almacen: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Proveedor: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Tipo Movimiento: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Doc Interno: '.$row1['prove_ruc'].'</h4><br>
                     <h4 style="text-align:left; font-weight:normal;">Referencia: '.$row1['prove_ruc'].'</h4><br>   			
	    		</th>
            
           
	    </thead>
    </table>
    
     

    </header>

 	<table  width="50%" style="border-collapse; " border="0" class="" >
	    <thead >
	    	<tr>
	    
	    		<th width="1%" style="  text-align:center; border-right:0px; ">
                <th class="service"  >COD ARTICULO</th>
                <th class="desc">DESCRIPCION</th>
                <th>UND</th>
                <th>CANTIDAD</th>
                <th>NRO LOTE</th>
	    			
	    			
	    			
	    		</th>
	    		<th width="1%" style="text-align:right; border-left:0px;">
                <th class="service"  >COD ARTICULO</th>
                <th class="desc">DESCRIPCION</th>
                <th>UND</th>
                <th>CANTIDAD</th>
                <th>NRO LOTE</th>
	    			
	    			
	    		</th>

	    	</tr>
	    </thead>
    </table>
    
    <main>
      <table width="50%"  style="border-collapse; " border="0" class="">
        <thead width="10%">
          <tr > 
          <th width="1%" style="  text-align:center; border-right:0px; ">
            <th class="service"  >ITEM</th>
            <th class="desc">PRODUCTO</th>
            <th>PRECIO</th>
            <th>CANTIDAD</th>
            <th>SUB TOTAL</th>
            
          </tr>
        </thead>
        
        <tbody >';
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
						where detalle_venta.venta_id = '".$row1['venta_id']."'";
						$contador=0;
						$resultado2 = $mysqli ->query($query2);
						while ($row2 = $resultado2-> fetch_assoc()){
							$contador++;

        $html.='<tr >
            <td class="service" style="border-bottom:0px; border-top:0px;">'.$contador.'</td>
            <td class="desc" style="border-bottom:0px ;border-top:0px;">'.$row2['producto_nombre'].'</td>
            <td class="unit" style="border-bottom:0px; border-top:0px;">'.$row2['vdetalle_precio'].'</td>
            <td class="qty" style="border-bottom:0px; border-top:0px;">'.$row2['vdetalle_cantidad'].'</td>
            <td class="total" style="border-bottom:0px; border-top:0px;">'.round($row2['subtotal'],2).'</td>
            </tr>';
        }
        if ($row1['compro_id'] ==2) {
        $html.='
          
          <tr>
            <td colspan="4" style="border-bottom:0px;  border-left:0px; border-right:0px; ">SUBTOTAL S/. :</td>
            <td class="total" style="border-bottom:0px;  border-left:0px; border-right:0px;">'.round(($row1['venta_total'] - $row1['venta_impuesto'] ),2).'</td>
          </tr>
             <tr>
            <td colspan="1" rowspan="6" style=" border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px; ">
            <barcode code="'.$row1['cliente_nombres'].'|'.$row1['cliente_dni'].'|'.($row1['venta_comprobante'].'-'. $row1['venta_serie'].'-'. $row1['venta_num_comprobante']).'|'.$row1['venta_total'].'" type="QR" class="barcode" size="1" disableborder="1" />
            </td>
          </tr>
       	
          <tr> 
            <td colspan="3" style="border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px;">IGV '.($row1['venta_porcentaje']*100).'% :</td>
            <td class="total" style="border-bottom:0px; border-top:0px;  border-left:0px; border-right:0px; ">'.round($row1['venta_impuesto'],2).'</td>
          </tr>
          <tr>
            <td colspan="3" class="grand total" style="border-bottom:0px; border-top:0px;border-right:0px;  border-left:0px;"><b>TOTAL S/. :</b></td>
	            <td class="grand total" style="border-bottom:0px; border-top:0px;  border-left:0px;border-right:0px ">'.$row1['venta_total'].'</td>
          </tr>';
        }else{
        	$html.='
        	<tr>
            <td colspan="4" class="grand total" style="border-bottom:0px; border-right:0px;  border-left:0px;"><b>TOTAL S/. :</b></td>
	            <td class="grand total" style="border-bottom:0px; border-left:0px;border-right:0px ">'.$row1['venta_total'].'</td>
          </tr>';


        }

          $html.='
           
        </tbody>
      </table>
    
      <div id="notices">
        <div>SON:</div>
        <div class="notice">'.strtoupper($letra).'</div>
		<br>
		<br>
		<div><b>Condiciones:</b></div><br>
        <div>Forma de Pago &nbsp;&nbsp;&nbsp; :&nbsp;&nbsp; '.$row1['fpago_descripcion'].'</div>
      </div>
    </main>
    
    <footer>

    </footer>
  </body>
</html>';

}


$css = file_get_contents('css/style_consumo.css');
$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output();