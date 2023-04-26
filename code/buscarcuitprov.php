<?php
	include "seguridad_bd.php";
	$oResp = new stdClass();
	$bd = new Bd;
	$bd->AbrirBd();

	//CUIT 11CHARS
	
	$q = 'SELECT id_proveedor FROM proveedor WHERE cuit ="'.$_GET['cuit'].'" and baja=0';
	$r = $bd->excecuteQuery($q);
	$row = mysqli_fetch_assoc($r);

	if($row){
		$oResp->OK = true;
		$oResp->id = $row['id_proveedor'];
	}else{
		$oResp->OK = false;
	}
 
	echo json_encode($oResp);
?>