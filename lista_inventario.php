<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	include_once ("includes/class.Tpl.php");
	
	$oTpl = new tpl("templates/rendiciones/inventario/lista.php");
	
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
	$cnx = $bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_INV');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],10,''); //10-inventario
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}
//=======================================================================
	$oTpl->setVar("sBack","Volver");
	$oTpl->setVar("linkBack","panel_control_modulos.php");

	$oPanelCtrl = $bd->getPanel("panel_control_capital");
	if($oPanelCtrl){
		$sBlockData = $oTpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			if ($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){
				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$oTpl->addToBlock($sBlockData,$vVars);
			}
		}
		$oTpl->endBlock();
	}

	$oTpl->setVar("User",$nombre_usuario);
	
	$vUE = $bd->getUEs();
	if($vUE){
		$sBlock = $oTpl->beginBlock("UE");
		foreach($vUE as $Item){
			if($Item['id_unidad_ejecutora'] != 13){
				$vVars = array(
					'iUE'=>$Item['id_unidad_ejecutora'], 
					'sUE'=>$Item['nombre']
				);
				$oTpl->addToBlock($sBlock,$vVars);
				$vVars=null;
			}
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("UE");
	}
	
	$oTpl->clearFields();
	$oTpl->printTpl();

?>
