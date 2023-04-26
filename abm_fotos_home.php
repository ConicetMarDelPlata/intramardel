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

	$error_foto_home = 0;
	$bd = new Bd;
	$bd->AbrirBd();

	$id_foto = $_POST["id_foto"]??0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$mostrar = $_POST["mostrar"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	$ultimo_id_foto_home = $bd->ultimo_id_foto_home();
	$ultimo_id_foto_home++;

	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../fotos_home/'.$nombre);
		$nombre_viejo = $nombre;
		if ($opcion == 1){
			$nombre_nuevo = $ultimo_id_foto . $nombre;	
		}else
			$nombre_nuevo = $id_foto . $nombre;	
		rename('../fotos_home/'.$nombre_viejo, '../fotos_home/'.$nombre_nuevo);
	}
	switch ($opcion){
		case 1: //NUEVO
			$row = $bd->consultar_foto_home($id_foto);
			if ($row["id_foto"] == ""){
				$bd->agregar_foto_home($mostrar, $nombre_nuevo);
				break;
			}else
			{
				$error_foto_home = 1;
				break;
			}
			break;
		case 2: // BORRAR 
			$row = $bd->consultar_foto_home($id_foto);
			$archivo = $row["archivo"];
			$bd->borrar_foto_home($id_foto, $archivo);
			break;	
		case 3: // ACTUALIZAR 
			//$bd->modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $firma);
			$row = $bd->consultar_foto_home($id_foto);
			if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
				$nombre_nuevo = $row["archivo"];
				//$bd->actualizar_firmante($id_firmante , $codigo_articulo, $descripcion, $peso_neto, $precio, $precio_x_2, $precio_x_3, $precio_oferta, $marca, $categoria, $oferta, $nombre_nuevo);
				$bd->modificar_foto_home($id_foto, $mostrar, $nombre_nuevo);				
			}else{
				if ($nombre_nuevo != $row["archivo"] && ($row["archivo"] != NULL) ){
					unlink('../fotos_home/'.$row["archivo"]);
				}
				$bd->modificar_foto_home($id_foto, $mostrar, $nombre_nuevo);				
			}			
			break;
		case 4:
			$row = $bd->consultar_foto_home($id_foto);
			$archivo = $row["archivo"];
			$bd->borrar_archivo_foto_home($id_foto, $archivo);
			break;			
	}
	$bd = NULL;
	if ($error_foto_home == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Archivo elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_fotos_home.php");
	}
?>
