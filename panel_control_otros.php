<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	include_once ('includes/class.Tpl.php');

	$tpl = new tpl("templates/panel_control/otros.html");

	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	$sesion = NULL;	

	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_USERS');
	
	$tpl->setVar("user",$nombre_usuario);
	$tpl->setVar("sBack","Volver");
	$tpl->setVar("linkBack","panel_control.php");
	
	$oModulos   = $bd->getPanel("panel_control_otros");
	$oPanelCtrl = $bd->getPanel("panel_control");
	
	if($oModulos){
		$sBlockData = $tpl->beginBlock("REG");
		foreach($oModulos as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
			if ($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){
				$vVars = array(
				'link'=>$Item['link'],
				'icono'=>'iconos/'.$Item['icono'],
				'nombre'=>$Item['nombre']
				);
				$tpl->addToBlock($sBlockData,$vVars);
			}
		}
		$tpl->endBlock();
	}

	if($oPanelCtrl){
		$sBlockData = $tpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
			if ($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){
				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$tpl->addToBlock($sBlockData,$vVars);
			}
		}
		$tpl->endBlock();
	}
	$tpl->printTpl();
?>
