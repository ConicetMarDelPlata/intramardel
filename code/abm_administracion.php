<?php
	include "seguridad_bd.php";
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
	$error_administracion = 0;
	
	$bd = new Bd;
	$bd->AbrirBd();

	$id_administracion = $_POST["id_administracion"]??0;	
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $_POST["titulo"];	
		$unidad_ejecutora = $_POST["unidad_ejecutora"];
		$comentario = $_POST["comentario"];
	}
	$archivo = $_FILES['archivo1']['tmp_name']; //modifico Vanina HTTP_POST_FILES POR _FILES
	$ultimo_id_administracion = $bd->ultimo_id_administracion();
	$ultimo_id_administracion++;	
	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_administracion . $nombre;	
		}else
			$nombre_nuevo = $id_administracion . $nombre;	
		rename('../PDF/'.$nombre_viejo, '../PDF/'.$nombre_nuevo);
	}
	switch ($opcion){
		case 1: // NUEVO
			$row = $bd->consultar_administracion($id_administracion);
			if ($row["id_administracion"] == ""){
				$bd->agregar_administracion($titulo, $unidad_ejecutora, $comentario, $nombre_nuevo);
				break;
			}else
			{
				$error_administracion = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$bd->borrar_administracion($id_administracion, $archivo);
			break;	
		case 3: // ACTUALIZAR 
			$row = $bd->consultar_administracion($id_administracion);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				//$bd->actualizar_firmante($id_firmante , $codigo_articulo, $descripcion, $peso_neto, $precio, $precio_x_2, $precio_x_3, $precio_oferta, $marca, $categoria, $oferta, $nombre_nuevo);
				$bd->modificar_administracion($id_administracion, $titulo, $unidad_ejecutora, $comentario, $nombre_nuevo);
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					unlink('../PDF/'.$row["archivo"]);
				}
				$bd->modificar_administracion($id_administracion, $titulo, $unidad_ejecutora, $comentario, $nombre_nuevo);
			}
			break;
		case 4:
			$row = $bd->consultar_administracion($id_administracion);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_administracion($id_administracion, $archivo);
			break;
	}
	$bd = NULL;
	if ($error_administracion == 1){
		echo "El Número de ID de Administreación ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_administracion.php");
	}
?>
