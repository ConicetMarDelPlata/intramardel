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

	$bd = new Bd;
	$bd->AbrirBd();
	$error_varios = 0;

	$id_varios = $_POST["id_varios"]??0;
	//var_dump($_POST);exit;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo = $_POST["titulo"];
		$texto = $_POST["texto"];
		$link = $_POST["link"];
		$fechaD = convertir_fecha_sql($_POST["fecha_desde"]);
		$fechaH = convertir_fecha_sql($_POST["fecha_hasta"]);
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	$ultimo_id_varios = $bd->ultimo_id_varios();
	$ultimo_id_varios++;	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_varios . $nombre;	
		}else
			$nombre_nuevo = $id_varios . $nombre;	
		rename('../PDF/'.$nombre_viejo, '../PDF/'.$nombre_nuevo);
	}
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_varios($id_varios);
			if ($row["id_varios"] == ""){
				$bd->agregar_varios($titulo, $texto, $link, $nombre_nuevo, $fechaD, $fechaH);
				break;
			}else
			{
				$error_varios = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_varios($id_varios);
			$archivo = $row["archivo"];
			$bd->borrar_varios($id_varios, $archivo);
			break;	
		case 3: // ACTUALIZAR 
			$row = $bd->consultar_varios($id_varios);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				$bd->modificar_varios($id_varios, $titulo, $texto, $link, $nombre_nuevo, $fechaD, $fechaH);
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					if (file_exists('../fotos_album/'.$row["archivo"]) ) {
						unlink('../PDF/'.$row["archivo"]);
					}
				}
				$bd->modificar_varios($id_varios, $titulo, $texto, $link, $nombre_nuevo, $fechaD, $fechaH);				
			}
			break;
		case 4:
			$row = $bd->consultar_varios($id_varios);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_varios($id_varios, $archivo);
			break;			
	}
	$bd = NULL;
	if ($error_varios == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del texto para varios elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_varios.php");
	}
?>
