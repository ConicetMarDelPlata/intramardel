<?php
	include "seguridad_bd.php";
	$oResp = new stdClass();
	$bd = new Bd;
	$bd->AbrirBd();
	$q = 'SELECT banco1, banco2, numero_cuenta1, numero_cuenta2 FROM proveedor WHERE id_proveedor ='.$_GET['prov'];
	
	// ESTO ESTÁ ASÍ SOLO CON MOTIVOS DE PRUEBA YA QUE EL MÉTODO CONSULTAR BANCO NO FUNCIONA CORRECTAMENTE Y NO ES EFICAZ
	$r = $bd->excecuteQuery($q);
	$row = mysqli_fetch_assoc($r);
	
	$q = 'SELECT nombre FROM banco WHERE id_banco ='.(int)$row['banco1'];
	
	$r = $bd->excecuteQuery($q);
	$banco1 = mysqli_fetch_assoc($r);
	
	$q = 'SELECT nombre FROM banco WHERE id_banco ='.(int)$row['banco2'];
	
	$r = $bd->excecuteQuery($q);
	$banco2 = mysqli_fetch_assoc($r);
	
	
//	$b1 = $bd->consultar_banco($row['banco1']);
//	$b2 = $bd->consultar_banco($row['banco2']);

	$oResp->banco1 = utf8_encode($banco1['nombre']);
	$oResp->banco2 = utf8_encode($banco2['nombre']);
	$oResp->cuenta1 = $row['numero_cuenta1'];
	$oResp->cuenta2 = $row['numero_cuenta2'];
 
	echo json_encode($oResp);
?>