<?php
	include_once("seguridad_bd.php");
	
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}
	$bd = new Bd;
	$bd->AbrirBd();

	$IDAlbum = $_GET['IDAlbum'];
	$IDImg = $_GET['IDImg'];
	
	if($bd->setPortadaImage($IDAlbum, $IDImg)){
		echo "OK";
	}else{
		echo "ERROR";
	}
?>
