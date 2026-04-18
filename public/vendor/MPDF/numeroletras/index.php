<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Numero A Letras</title>
	<link rel="shortcut icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTgOSmqvZ4mKXzUT9ok_wYiAfagJFAAGn9ohsg5ItGyJAyDigtH" />
	<style>
div {
  width: 350px;
  padding: 10px;
  border: 5px solid gray;
  margin: 0;
  display: inline-block;
}
</style>
</head>
<body>
	

<h4 style="color: black">Reporte</h4>

<?php 
//Incluímos la clase pago
$totalpagar=876.03;
require_once "CifrasEnLetras.php";
$v=new CifrasEnLetras(); 
//Convertimos el total en letras
$letra=($v->convertirEurosEnLetras($totalpagar));
 ?>
<div>
	<center><span style="color: black;background-color: #78CE31"> Total Pagar En Letras:</span> <br></center>
<span style="color: blue"><?php echo $letra; ?></span>
</div>

	<div>
	<span style="color: black;background-color: #78CE31">  Total Pagar:</span> <br>
<span style="color: blue"><?php echo '$.'.$totalpagar; ?></span>
</div>

</body>
</html>