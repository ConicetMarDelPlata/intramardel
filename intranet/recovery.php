<?php
	include 'seguridad_bd.php';
	include_once ('includes/class.Tpl.php');
	
	$bd = new Bd;
	$bd->AbrirBd("la");
	if($bd->existeUsuario(null,$_GET['m'])){
		$vUser = $bd->getUserByEmail($_GET['m']);
		$tpl = new tpl("templates/registro/mail.html");
		$tpl->setVar("FullName",$vUser['apellido'].", ".$vUser['nombre']);
		$tpl->setVar("User",$vUser['nombre_usuario']);
		$tpl->setVar("Pass",$vUser['contrasenia']);
		$sEmailData = $tpl->returnTpl();
		$bd->enviarDatos($_GET['m'], $sEmailData);
	}else{
		$tpl = new tpl("templates/registro/error.html");
		$tpl->setVar("Error","No se pudieron enviar sus datos a su casilla de mail.");
		$tpl->setVar("Recovery"," ");
		$tpl->printTpl();
		mail("informatica@mardelplata-conicet.gob.ar", "Error en recuperación", "Error al enviar datos a ".$_GET['m']);
		
	}
?>