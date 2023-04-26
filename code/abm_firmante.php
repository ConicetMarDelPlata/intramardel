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

	$id_firmante = $_POST["id_firmante"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$titulo_apellido_nombre = $_POST["titulo_apellido_nombre"];
		$cargo = $_POST["cargo"];	
		$lugar = $_POST["lugar"];
		$firma = $_POST["firma"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
		move_uploaded_file($_FILES['archivo1']['tmp_name'], $nombre);
		$nombre_viejo = $nombre;
		$nombre_nuevo = $id_firmante . $nombre;				
		rename($nombre_viejo, $nombre_nuevo);
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['fi_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],16,'alta')){
				$row = $bd->consultar_firmante($id_firmante);
				if ($row["id_firmante"] == ""){
					$bd->agregar_firmante($titulo_apellido_nombre, $cargo, $lugar, $nombre_nuevo);
					break;
				}else
				{
					$error_firmante = 1;
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['fi_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],16,'baja')){
				//$row = $bd->consultar_firmante($id_firmante);
				//$firma = $row["firma"];
				$bd->borrar_firmante($id_firmante);
			}
			break;	
		case 3: // ACTUALIZAR 
			//$bd->modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $firma);
			//if($row_usuario['fi_modificacion'] == 1){			
			if($bd->checkPerm($_SESSION["id_usuario"],16,'modificacion')){
				$row = $bd->consultar_firmante($id_firmante);
				if (!$archivo){ //VERIFICO SI AL ACTUALIZAR MANDA IMAGEN, CASO NEGATIVO DEJO LA ANTERIOR SI ES QUE LA HAY
					$nombre_nuevo = $row["firma"];
					//$bd->actualizar_firmante($id_firmante , $codigo_articulo, $descripcion, $peso_neto, $precio, $precio_x_2, $precio_x_3, $precio_oferta, $marca, $categoria, $oferta, $nombre_nuevo);
					$bd->modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $nombre_nuevo);				
				}else{
					if ($nombre_nuevo != $row["firma"] && ($row["firma"] != NULL) ){
						unlink($row["firma"]);
					}
					$bd->modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $nombre_nuevo);				
				}			
			}
			break;
		case 4:
			//if($row_usuario['fi_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],16,'modificacion')){
				$row = $bd->consultar_firmante($id_firmante);
				$firma = $row["firma"];
				$bd->borrar_firma_firmante($id_firmante, $firma);
			}
			break;			
	}
	$bd = NULL;
	if ($error_firmante == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Firmante elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_firmantes.php");
	}
?>
