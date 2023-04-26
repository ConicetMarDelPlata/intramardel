<?php
	include "seguridad_bd.php";
	$id_album = (int)$_GET['id_album'];
	$sesion = new Sesion;	
	$bd = new Bd;
	$bd->AbrirBd();
	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$bd->borrar_album($id_album);
	header("location: lista_galeria.php");
?>
