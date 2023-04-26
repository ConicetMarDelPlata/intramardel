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
	$error_licitacion = 0;
	$bd = new Bd;
	$bd->AbrirBd();

	$id_licitacion = $_POST["id_licitacion"]??0;	
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $_POST["titulo"];	
		$fecha_publicacion = $_POST["fecha_publicacion"];			
		$fecha_apertura = $_POST["fecha_apertura"];
		$horario_apertura = $_POST["horario_apertura"];
		$unidad_ejecutora = $_POST["unidad_ejecutora"];
		$numero_licitacion = $_POST["numero_licitacion"];
		$comentario = $_POST["comentario"];
		$estado = $_POST["estado"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	$ultimo_id_licitacion = $bd->ultimo_id_licitacion();
	$ultimo_id_licitacion++;	
	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_licitacion . $nombre;	
		}else
			$nombre_nuevo = $id_licitacion . $nombre;	
		rename('../PDF/'.$nombre_viejo, '../PDF/'.$nombre_nuevo);
	}
	switch ($opcion){
		case 1: // NUEVO
			$row = $bd->consultar_licitacion($id_licitacion);
			if ($row["id_licitacion"] == ""){
				$fecha_publicacion = convertir_fecha_sql($fecha_publicacion);
				$fecha_apertura = convertir_fecha_sql($fecha_apertura);
				$bd->agregar_licitacion($titulo, $fecha_publicacion, $fecha_apertura, $horario_apertura, $unidad_ejecutora, $numero_licitacion, $comentario, $nombre_nuevo, $estado);
				break;
			}else
			{
				$error_licitacion = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$bd->borrar_licitacion($id_licitacion, $archivo);
			break;	
		case 3: // ACTUALIZAR 
			$fecha_publicacion = convertir_fecha_sql($fecha_publicacion);
			$fecha_apertura = convertir_fecha_sql($fecha_apertura);		
			$row = $bd->consultar_licitacion($id_licitacion);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				//$bd->actualizar_firmante($id_firmante , $codigo_articulo, $descripcion, $peso_neto, $precio, $precio_x_2, $precio_x_3, $precio_oferta, $marca, $categoria, $oferta, $nombre_nuevo);
				$bd->modificar_licitacion($id_licitacion, $titulo, $fecha_publicacion, $fecha_apertura, $horario_apertura, $unidad_ejecutora, $numero_licitacion, $comentario, $nombre_nuevo, $estado);
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					unlink('../PDF/'.$row["archivo"]);
				}
				$bd->modificar_licitacion($id_licitacion, $titulo, $fecha_publicacion, $fecha_apertura, $horario_apertura, $unidad_ejecutora, $numero_licitacion, $comentario, $nombre_nuevo, $estado);
			}
			break;
		case 4:
			$row = $bd->consultar_licitacion($id_licitacion);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_licitacion($id_licitacion, $archivo);
			break;
	}
	$bd = NULL;
	if ($error_licitacion == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El Npumero de ID de Licitacion ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_licitaciones.php");
	}
?>
