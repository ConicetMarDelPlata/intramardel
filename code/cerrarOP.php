<?php
	include "seguridad_bd.php";
	$bd = new Bd;
	$bd->AbrirBd();

	$opID  = (int)$_GET['opid'];
	$iYear = (int)$_GET['anio'];
	$fechaAvisoPago = convertir_fecha_sql($_GET['fechaavisopago']);

	echo json_encode($bd->closeOP($opID, $iYear, $fechaAvisoPago));
?>
