<?php
	include "seguridad_bd.php";
	include_once('includes/class.Resize.php'); 
	
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

	$bd = new Bd;
	$bd->AbrirBd();

	$error_noticia = 0;
	$id_noticia = ($_POST["id_noticia"])??0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $_POST["titulo"];
		$bajada = $_POST["bajada"];	
		$fecha = $_POST["fecha"];
		$texto = $_POST["texto"];
		$mostrar = $_POST["mostrar"];		
	}	

	$archivo1 = $_FILES['archivo1']['tmp_name']??'';
	$archivo2 = $_FILES['archivo2']['tmp_name']??'';
	$archivo3 = $_FILES['archivo3']['tmp_name']??'';
	$archivo4 = $_FILES['archivo4']['tmp_name']??'';
	$archivo5 = $_FILES['archivo5']['tmp_name']??'';	
	
	$image = new SimpleImage(); 
	$maxW = 285;
	
	if ($archivo1) {
		$nombre1 = explode(".", $_FILES['archivo1']['name']);
		$ext = $nombre1[count($nombre1)-1];
		$nombre1 = sha1($_FILES['archivo1']['name']).".".$ext;
		$dest = '../fotos_seg_e_hig/'.$nombre1;
		
		
		move_uploaded_file($_FILES['archivo1']['tmp_name'], $dest);
		$image->load($dest); 
		$image->resizeToWidth($maxW);
		unlink($dest);
		$image->save($dest);
	}else {
		$nombre1 = '';
	}
	if ($archivo2) {
		$nombre2 = explode(".", $_FILES['archivo2']['name']);
		$ext = $nombre2[count($nombre2)-1];
		$nombre2 = sha1($_FILES['archivo2']['name']).".".$ext;
		$dest = '../fotos_seg_e_hig/'.$nombre2;

		move_uploaded_file($_FILES['archivo2']['tmp_name'], $dest);
		$image->load($dest); 
		$image->resizeToWidth($maxW);
		unlink($dest);
		$image->save($dest);
	}else {
		$nombre2 = '';
	}
	if ($archivo3) {
		$nombre3 = explode(".", $_FILES['archivo3']['name']);
		$ext = $nombre3[count($nombre3)-1];
		$nombre3 = sha1($_FILES['archivo3']['name']).".".$ext;
		$dest = '../fotos_seg_e_hig/'.$nombre3;
		
		move_uploaded_file($_FILES['archivo3']['tmp_name'], $dest);
		$image->load($dest); 
		$image->resizeToWidth($maxW);
		unlink($dest);
		$image->save($dest);
	}else {
		$nombre3 = '';
	}
	if ($archivo4) {
		$nombre4 = explode(".", $_FILES['archivo4']['name']);
		$ext = $nombre4[count($nombre4)-1];
		$nombre4 = sha1($_FILES['archivo4']['name']).".".$ext;
		$dest = '../fotos_seg_e_hig/'.$nombre4;
		
		move_uploaded_file($_FILES['archivo4']['tmp_name'], $dest);
		$image->load($dest); 
		$image->resizeToWidth($maxW);
		unlink($dest);
		$image->save($dest);
	}else {
		$nombre4 = '';
	}
	if ($archivo5) {
		$nombre5 = explode(".", $_FILES['archivo5']['name']);
		$ext = $nombre5[count($nombre5)-1];
		$nombre5 = sha1($_FILES['archivo5']['name']).".".$ext;
		$dest = '../fotos_seg_e_hig/'.$nombre5;

		move_uploaded_file($_FILES['archivo5']['tmp_name'], $dest);
		$image->load($dest); 
		$image->resizeToWidth($maxW);
		unlink($dest);
		$image->save($dest);
	}	else {
		$nombre5 = '';
	}
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_seg_e_hig($id_noticia);
			if ($row["id_noticia"] == ""){
				$fecha = convertir_fecha_sql($fecha);			
				$bd->agregar_seg_e_hig($titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5);
				break;
			}else
			{
				$error_noticia = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$bd->borrar_seg_e_hig($id_noticia, $row["foto1"], $row["foto2"], $row["foto3"], $row["foto4"], $row["foto5"]);
			break;	
		case 3: // ACTUALIZAR 
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$fechatmp = explode("-",$fecha);
			if(strlen($fechatmp[0]) < 4){
				$fecha = convertir_fecha_sql($fecha);
			}
			if ($archivo1){
				if ($nombre1 != $row["foto1"] && ($row["foto1"] != NULL) ){
					unlink('../fotos_seg_e_hig/'.$row["foto1"]);
				}			
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, 1);
			}else
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo2){
				if ($nombre2 != $row["foto2"] && ($row["foto2"] != NULL) ){
					unlink('../fotos_seg_e_hig/'.$row["foto2"]);
				}			
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre2, 2);
			}else
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo3){
				if ($nombre3 != $row["foto3"] && ($row["foto3"] != NULL) ){
					unlink('../fotos_seg_e_hig/'.$row["foto3"]);
				}			
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre3, 3);
			}else
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo4){
				if ($nombre4 != $row["foto4"] && ($row["foto4"] != NULL) ){
					unlink('../fotos_seg_e_hig/'.$row["foto4"]);
				}			
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre4, 4);
			}else
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			if ($archivo5){
				if ($nombre5 != $row["foto5"] && ($row["foto5"] != NULL) ){
					unlink('../fotos_seg_e_hig/'.$row["foto5"]);
				}			
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre5, 5);
			}else
				$bd->modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, "", 0);
			break;
		case 4:
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$foto1 = $row["foto1"];
			$bd->borrar_imagen_seg_e_hig($id_noticia, $foto1, 1);
			break;
		case 5:
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$foto2 = $row["foto2"];
			$bd->borrar_imagen_seg_e_hig($id_noticia, $foto2, 2);
			break;
		case 6:
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$foto3 = $row["foto3"];
			$bd->borrar_imagen_seg_e_hig($id_noticia, $foto1, 3);
			break;
		case 7:
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$foto4 = $row["foto4"];
			$bd->borrar_imagen_seg_e_hig($id_noticia, $foto4, 4);
			break;
		case 8:
			$row = $bd->consultar_seg_e_hig($id_noticia);
			$foto5 = $row["foto5"];
			$bd->borrar_imagen_seg_e_hig($id_noticia, $foto5, 5);
			break;						
	}
	$bd = NULL;
	if ($error_noticia == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID de la noticia elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_seg_hig.php");
	}
?>
