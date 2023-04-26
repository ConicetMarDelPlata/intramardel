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

	$id_banco = $_POST["id_banco"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$nombre = $_POST["nombre"];
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['ba_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],13,'alta')){
				$row = $bd->consultar_banco($id_banco);
				if ($row["id_banco"] == ""){
					$bd->agregar_banco($nombre);
					break;
				}else
				{
					$error_banco = 1;
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['ba_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],13,'baja')){
				$bd->borrar_banco($id_banco);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['ba_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],13,'modificacion')){
				$bd->modificar_banco($id_banco, $nombre);
			}
			break;
	}
	$bd = NULL;
	if ($error_banco == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Banco  elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_bancos.php");
	}
?>
