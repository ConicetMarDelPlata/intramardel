<?php
	set_time_limit(0);
	ini_set('max_execution_time', 3000);

	include "./includes/header.php";
	include "seguridad_bd.php";
	include "includes/class.Tpl.php";
	
	$oTpl = new tpl("templates/galeria/abm.html");
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$id_album = $_GET['id_album']??null;
	$oTpl->setVar("id_album",$id_album);
	$opcion = $_GET['opcion'];
	
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_GI');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],29,''); //29=Galeria de imagenes
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}
	$webGIAlta = $bd->checkPerm($_SESSION["id_usuario"],29,'alta');
	$sLink = ($webGIAlta)?"form_album_fotos.php?opcion=1":"#";
	$sImg  = ($webGIAlta)?"agregar.png":"iconos_grises/agregarg.png";
	
	$oTpl->setVar("userName",$nombre_usuario);
	$oTpl->setVar("newAlbum",$sLink);
	$oTpl->setVar("imgAlbum",$sImg);
	$oTpl->setVar("opcion",$opcion);

	// BARRA LATERAL
	$oPanelCtrl = $bd->getPanel("panel_control");
	if($oPanelCtrl){
		$sBlockData = $oTpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
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

	
	if(isset($id_album)){
	// IMAGENES PREVIAMENTE CARGADAS
		$oImgs = $bd->getAlbumImages($id_album);
		$sImgPortada = $bd->getAlbumPortada($id_album);
		$sFolder = $bd->getAlbumFolder($id_album);
		if($oImgs){
			$sBlockData = $oTpl->beginBlock("IMG");
			$i=0;
			foreach($oImgs as $Item){
				if($Item == $sImgPortada){
					$sClass=" portada";
				}else{
					$sClass="";
				}
				$vVars = array(
				'sTitle'=>$Item,
				'iIDAlbum'=>$id_album,
				'iIDImag'=>$Item,
				'sClass'=>$sClass,
				'sPath'=>"../fotos_album/$sFolder/$Item"
				);
				$oTpl->addToBlock($sBlockData,$vVars);
			}
			$oTpl->endBlock();
		}else{
			$oTpl->deleteBlock("IMG");
		}

		$row = $bd->consultar_album_fotos($id_album, 0);
		$fecha = convertir_fecha($row["fecha"]);
		$nombre_album = $row["nombre_album"];
		$comentario = $row["comentario"];
		$id_foto = $bd->ultimo_id_foto_album($id_album);

		$oTpl->setVar("fecha",$fecha);
		$oTpl->setVar("albumName",$nombre_album);
		$oTpl->setVar("comentarioAlbum",$comentario);
	}else{
		$oTpl->deleteBlock("IMG");
		$oTpl->setVar("fecha",date("d-m-Y"));
		$oTpl->setVar("tmp_id",date("YmdHis"));
		$oTpl->setVar("albumName","");
		$oTpl->setVar("comentarioAlbum","");		
	}
	$oTpl->printTpl();
?>
