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

	$error_convocatoria = 0;
	$bd = new Bd;
	$bd->AbrirBd();

	$id_convocatoria = $_POST["id_convocatoria"]??0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $_POST["titulo"];
		$texto = $_POST["texto"];
		$link = $_POST["link"];
		$fecha_desde = $_POST["fecha_desde"];
		$fecha_hasta = $_POST["fecha_hasta"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	$ultimo_id_convocatoria = $bd->ultimo_id_convocatoria();
	$ultimo_id_convocatoria++;	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_convocatoria . $nombre;	
		}else
			$nombre_nuevo = $id_convocatoria . $nombre;	
		rename('../PDF/'.$nombre_viejo, '../PDF/'.$nombre_nuevo);
	}
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_convocatoria($id_convocatoria);
			if ($row["id_convocatoria"] == ""){
				$bd->agregar_convocatoria($titulo, $texto, $link, $nombre_nuevo, $fecha_desde, $fecha_hasta);
				break;
			}else
			{
				$error_convocatoria = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_convocatoria($id_convocatoria);
			$archivo = $row["archivo"];		
			$bd->borrar_convocatoria($id_convocatoria, $archivo);
			break;	
		case 3: // ACTUALIZAR 	
			$row = $bd->consultar_convocatoria($id_convocatoria);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				$bd->modificar_convocatoria($id_convocatoria, $titulo, $texto, $link, $nombre_nuevo, $fecha_desde, $fecha_hasta);
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					if (file_exists('../fotos_album/'.$row["archivo"]) ) {
						unlink('../PDF/'.$row["archivo"]);
					}
				}
				$bd->modificar_convocatoria($id_convocatoria, $titulo, $texto, $link, $nombre_nuevo, $fecha_desde, $fecha_hasta);				
			}
			break;
		case 4:
			$row = $bd->consultar_convocatoria($id_convocatoria);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_convocatoria($id_convocatoria, $archivo);
			break;			
	}
	$bd = NULL;
	if ($error_convocatoria == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del texto para la convocatoria elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_convocatorias.php");
	}
?>
