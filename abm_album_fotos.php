<?php
	set_time_limit(0);
	ini_set('max_execution_time', 3000);
	include "seguridad_bd.php";
	include_once("includes/class.Resize.php");
	include_once("includes/functions.php");
	
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_REQUEST["opcion"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;

	$bd = new Bd;
	$bd->AbrirBd();

	$id_album = $_POST["id_album"]??null;	
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		echo 'en el if<br/>';
		$fecha = convertir_fecha_sql($_POST["fecha"]);
		$nombre_album = $_POST["nombre_album"];	
		$comentario = $_POST["comentario"];	
		$id_foto_album = $_POST["id_foto"]??0;	

		if($opcion == 1){
			list($usec, $sec) = explode(" ", microtime());
			$sFolder_id = ((int)$usec + (int)$sec);
		}else{
			$sFolder_id = $bd->getAlbumFolder($id_album);
		}

		$sDirDest = '../fotos_album/'.$sFolder_id;
	}

	$oImage = new SimpleImage();
	
	//$archivo = $_FILES['archivo1']['tmp_name'];
	$vImagenes = reArrayFiles($_FILES['imagenes']);
	
	$ultimo_id_album = $bd->ultimo_id_album();
	$ultimo_id_album++;
	
	//$ultimo_id_foto_album = $bd->ultimo_id_foto_album($id_album);
	//$ultimo_id_foto_album++;

	if(!$id_album){
		$id_album = $ultimo_id_album;
	}
	
	if($vImagenes){
		if(!is_dir($sDirDest)){
			mkdir($sDirDest, 0755);
			$i=0;
		}else{
			$vFiles = scandir($sDirDest);
			$i = count($vFiles)-2;
		}
		foreach($vImagenes as $IMG){
			$nombre = str_pad($i,3,'0',STR_PAD_LEFT) . getFileName($IMG['name']);
			move_uploaded_file($IMG['tmp_name'], '../fotos_album/'.$sFolder_id."/".$nombre);
			
			if(is_file('../fotos_album/'.$sFolder_id."/".$nombre)){
				$oImage->load('../fotos_album/'.$sFolder_id."/".$nombre);
				$oImage->resizeToWidth(800);
				$oImage->save('../fotos_album/'.$sFolder_id."/".$nombre);
			}
			$i++;
		}	

		switch ($opcion){
			case 1: // NUEVO ALBUM
				$bd->agregar_album($id_album, $fecha, $nombre_album, $comentario, $sFolder_id);
				//header("Location: form_album_fotos.php?opcion=4&id_album=$id_album");	
				$opcion = 3;
				break;
			case 2: // BORRAR UN ALBUM		
				$bd->borrar_album($id_album);
				header("Location: lista_galeria.php");			
				break;	
			case 3: // ACTUALIZAR 	
			case 4: // NUEVO MOVIMIENTO DE UN ALBUM EXISTENTE
				//$row = $bd->consultar_album_fotos($id_album);
				$bd->modificar_album($id_album, $nombre_album, $comentario, $fecha);
				break;
		}
		
	}
	header("Location: form_album_fotos.php?opcion=3&id_album=$id_album");
	$bd = NULL;
	
	function reArrayFiles(&$file_post) {

		if($file_post){
			$file_ary = array();
			$file_count = count($file_post['name']);
			$file_keys = array_keys($file_post);

			for ($i=0; $i<$file_count; $i++) {
				foreach ($file_keys as $key) {
					$file_ary[$i][$key] = $file_post[$key][$i];
				}
			}

			return $file_ary;
		}else{
			return false;
		}
	}
?>
