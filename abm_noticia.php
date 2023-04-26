<?php
	include "seguridad_bd.php";
	include_once("includes/class.Resize.php");
	
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;
	/*echo "Datos de session: " . '<br>';
	echo $nombre_usuario . '<br>';
	echo $contrasenia . '<br>';
	*/

	$error_noticia = 0;
	$bd = new Bd;
	$bd->AbrirBd();

	$id_noticia = $_POST["id_noticia"]??0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $bd->realEscapeString($_POST["titulo"]);
		$bajada = $bd->realEscapeString($_POST["bajada"]);	
		$fecha = $_POST["fecha"];
		$texto = $bd->realEscapeString($_POST["texto"]);
		$mostrar = $_POST["mostrar"];		
	}
	$archivo1 = $_FILES['archivo1']['tmp_name']??'';
	$archivo2 = $_FILES['archivo2']['tmp_name']??'';
	$archivo3 = $_FILES['archivo3']['tmp_name']??'';
	$archivo4 = $_FILES['archivo4']['tmp_name']??'';
	$archivo5 = $_FILES['archivo5']['tmp_name']??'';	
	$archivo6 = $_FILES['adjunto']['tmp_name']??'';	
	$oImage = new SimpleImage();
	
	if ($archivo1) {
		$nombre1 = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../fotos_noticias/'.$nombre1);
		$oImage->load('../fotos_noticias/'.$nombre1);
		$oImage->resizeToWidth(247);
		$oImage->save('../fotos_noticias/'.$nombre1);
	} else {
		$nombre1 = '';
	}
	if ($archivo2) {
		$nombre2 = $_FILES['archivo2']['name'];
		move_uploaded_file($_FILES['archivo2']['tmp_name'], '../fotos_noticias/'.$nombre2);
		$oImage->load('../fotos_noticias/'.$nombre2);
		$oImage->resizeToWidth(247);
		$oImage->save('../fotos_noticias/'.$nombre2);
	} else {
		$nombre2 = '';
	}
	if ($archivo3) {
		$nombre3 = $_FILES['archivo3']['name'];
		move_uploaded_file($_FILES['archivo3']['tmp_name'], '../fotos_noticias/'.$nombre3);
		$oImage->load('../fotos_noticias/'.$nombre3);
		$oImage->resizeToWidth(247);
		$oImage->save('../fotos_noticias/'.$nombre3);
	} else {
		$nombre3='';
	}
	if ($archivo4) {
		$nombre4 = $_FILES['archivo4']['name'];
		move_uploaded_file($_FILES['archivo4']['tmp_name'], '../fotos_noticias/'.$nombre4);
		$oImage->load('../fotos_noticias/'.$nombre4);
		$oImage->resizeToWidth(247);
		$oImage->save('../fotos_noticias/'.$nombre4);
	} else {
		$nombre4 = '';
	}
	if ($archivo5) {
		$nombre5 = $_FILES['archivo5']['name'];
		move_uploaded_file($_FILES['archivo5']['tmp_name'], '../fotos_noticias/'.$nombre5);
		$oImage->load('../fotos_noticias/'.$nombre5);
		$oImage->resizeToWidth(247);
		$oImage->save('../fotos_noticias/'.$nombre5);
	}	else {
		$nombre5 = '';
	}
	if ($archivo6) {
		$adjunto = $_FILES['adjunto']['name'];
		move_uploaded_file($_FILES['adjunto']['tmp_name'], '../fotos_noticias/'.$adjunto);
	}	
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_noticia($id_noticia);
			if ($row["id_noticia"] == ""){
				$fecha = convertir_fecha_sql($fecha);			
				$bd->agregar_noticia($titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5, $adjunto);
				break;
			}else
			{
				$error_noticia = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_noticia($id_noticia);
			$foto1 = $row["foto1"];
			$foto2 = $row["foto2"];
			$foto3 = $row["foto3"];
			$foto4 = $row["foto4"];
			$foto5 = $row["foto5"];									
			$adjunto = $row["adjunto"];									
			$bd->borrar_noticia($id_noticia, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5, $adjunto);
			break;	
		case 3: // ACTUALIZAR 
			$row = $bd->consultar_noticia($id_noticia);
			$fechatmp = explode("-",$fecha);
			if(strlen($fechatmp[0]) < 4){
				$fecha = convertir_fecha_sql($fecha);
			}
			if ($archivo1){
				if ($nombre1 != $row["foto1"] && ($row["foto1"] != NULL) ){
					unlink('../fotos_noticias/'.$row["foto1"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, 1);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo2){
				if ($nombre2 != $row["foto2"] && ($row["foto2"] != NULL) ){
					unlink('../fotos_noticias/'.$row["foto2"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre2, 2);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo3){
				if ($nombre3 != $row["foto3"] && ($row["foto3"] != NULL) ){
					unlink('../fotos_noticias/'.$row["foto3"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre3, 3);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo4){
				if ($nombre4 != $row["foto4"] && ($row["foto4"] != NULL) ){
					unlink('../fotos_noticias/'.$row["foto4"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre4, 4);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo5){
				if ($nombre5 != $row["foto5"] && ($row["foto5"] != NULL) ){
					unlink('../fotos_noticias/'.$row["foto5"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre5, 5);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);

			if ($archivo6){
				if ($adjunto != $row["adjunto"] && ($row["adjunto"] != NULL) ){
					unlink('../fotos_noticias/'.$row["adjunto"]);
				}			
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $adjunto, 6);
			}else
				$bd->modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			break;
		case 4:
			$row = $bd->consultar_noticia($id_noticia);
			$foto1 = $row["foto1"];
			$bd->borrar_imagen_noticia($id_noticia, $foto1, 1);
			break;
		case 5:
			$row = $bd->consultar_noticia($id_noticia);
			$foto2 = $row["foto2"];
			$bd->borrar_imagen_noticia($id_noticia, $foto2, 2);
			break;
		case 6:
			$row = $bd->consultar_noticia($id_noticia);
			$foto3 = $row["foto3"];
			$bd->borrar_imagen_noticia($id_noticia, $foto1, 3);
			break;
		case 7:
			$row = $bd->consultar_noticia($id_noticia);
			$foto4 = $row["foto4"];
			$bd->borrar_imagen_noticia($id_noticia, $foto4, 4);
			break;
		case 8:
			$row = $bd->consultar_noticia($id_noticia);
			$foto5 = $row["foto5"];
			$bd->borrar_imagen_noticia($id_noticia, $foto5, 5);
			break;						
		case 9:
			$row = $bd->consultar_noticia($id_noticia);
			$adjunto = $row["adjunto"];
			$bd->borrar_imagen_noticia($id_noticia, $adjunto, 6);
			break;						
	}
	$bd = NULL;
	if ($error_noticia == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID de la noticia elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_noticias.php");
	}
?>
