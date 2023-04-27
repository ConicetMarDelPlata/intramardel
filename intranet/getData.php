<?php
	include "seguridad_bd.php";
	$numero_tramite	=	$_POST['tramite'];
	$bd = new Bd;
	$bd->AbrirBd();
	echo json_encode($bd->getDataMesaSalida($numero_tramite));
?>