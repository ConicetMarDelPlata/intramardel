<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}
	$bd = new Bd;
	$bd->AbrirBd();
	
	$cuit = $_POST['CUIT'];
	$idProveedor = $_POST['idProveedor'];
	//Devuelve true (si sí lo encuentra) o false (si no lo encuentra)
	$result = $bd->check_cuit_proveedor($cuit, $idProveedor);
	
	echo json_encode($result);	
?>
