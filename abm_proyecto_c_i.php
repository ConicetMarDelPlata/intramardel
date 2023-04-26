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

	$error_proyecto_c_i = 0;
	$id_proyecto_c_i = $_POST["id_proyecto_c_i"]??0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$descripcion = $_POST["descripcion"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	$ultimo_id_proyecto_c_i = $bd->ultimo_id_proyecto_c_i();
	$ultimo_id_proyecto_c_i++;
	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_proyecto_c_i . $nombre;	
		}else
			$nombre_nuevo = $id_proyecto_c_i . $nombre;	
		rename('../PDF/'.$nombre_viejo, '../PDF/'.$nombre_nuevo);
	}
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_proyecto_c_i($id_proyecto_c_i);
			if ($row["id_proyecto_c_i"] == ""){
				$bd->agregar_proyecto_c_i($descripcion, $nombre_nuevo);
				break;
			}else
			{
				$error_proyecto_c_i = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_proyecto_c_i($id_proyecto_c_i);
			$archivo = $row["archivo"];
			$bd->borrar_proyecto_c_i($id_proyecto_c_i, $archivo);
			break;	
		case 3: // ACTUALIZAR 
			//$bd->modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $firma);
			$row = $bd->consultar_proyecto_c_i($id_proyecto_c_i);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				//$bd->actualizar_firmante($id_firmante , $codigo_articulo, $descripcion, $peso_neto, $precio, $precio_x_2, $precio_x_3, $precio_oferta, $marca, $categoria, $oferta, $nombre_nuevo);
				$bd->modificar_proyecto_c_i($id_proyecto_c_i, $descripcion, $nombre_nuevo);				
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					unlink('../PDF/'.$row["archivo"]);
				}
				$bd->modificar_proyecto_c_i($id_proyecto_c_i, $descripcion, $nombre_nuevo);				
			}			
			break;
		case 4:
			$row = $bd->consultar_proyecto_c_i($id_proyecto_c_i);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_proyecto_c_i($id_proyecto_c_i, $archivo);
			break;			
	}
	$bd = NULL;
	if ($error_proyecto_c_i == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Proyecto CI elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_proyectos_c_i.php");
	}
?>
