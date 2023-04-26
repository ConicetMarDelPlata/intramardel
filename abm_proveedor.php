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
	echo $nivel_acceso . '<br>';*/

	$bd = new Bd;
	$bd->AbrirBd();

	$id_proveedor = $_POST["id_proveedor"];	
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$cuit 			= $_POST["cuit"];	
		$razon_social 	= $_POST["razon_social"];
		$IIBB 			= trim($_POST["IIBB"]);
		$nroIIBB		= (trim($_POST["nroiibb"]))?trim($_POST["nroiibb"]):$cuit;
		$CMPercent		= trim($_POST["cmpercent"]);
		$condicion_iva 	= $_POST["condicion_iva"];
		$domicilio 		= $_POST["domicilio"];
		$provincia 		= $_POST["provincia"];
		$contacto 		= $_POST["contacto"];
		$telefono 		= $_POST["telefono"];
		$email 			= $_POST["email"];
		$contacto2 		= $_POST["contacto2"];
		$email2 		= $_POST["email2"];
		$banco1 		= $_POST["banco1"];		
		$titular_cuenta1= $_POST["titular_cuenta1"];		
		$cuit1 			= $_POST["cuit_cuenta1"];		
		$tipo_cuenta1 	= $_POST["tipo_cuenta1"];				
		$numero_cuenta1 = $_POST["numero_cuenta1"];		
		$cbu1 			= $_POST["cbu1"];
		
		$banco2 		= $_POST["banco2"];		
		$titular_cuenta2= $_POST["titular_cuenta2"];		
		$cuit2 			= $_POST["cuit_cuenta2"];		
		$tipo_cuenta2 	= $_POST["tipo_cuenta2"];				
		$numero_cuenta2 = $_POST["numero_cuenta2"];		
		$cbu2 			= $_POST["cbu2"];
	}
	
/*
	echo $cuit . '<br>';
	echo $razon_social . '<br>';
	echo $domicilio . '<br>';
	echo $provincia . '<br>';
	echo $contacto . '<br>';
	echo $telefono . '<br>';
	echo $email . '<br>';
	
	echo "bco 1 " . $banco1 . '<br>';
	echo $tipo_cuenta1 . '<br>';
	echo $numero_cuenta1 . '<br>';
	echo $cbu1 . '<br>';
	echo "bco 2 " . $banco2 . '<br>';
	echo $tipo_cuenta2 . '<br>';
	echo $numero_cuenta2 . '<br>';
	echo $cbu2 . '<br>';
*/	
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: // NUEVO
			//if($row_usuario['pr_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],12,'alta')){
				//Nota Vanina: esto es como un chequeo adicional para que no se repita el cuit?!?
				//Yo creo que es practicamente innecesario
				if($condicion_iva != 4){
					$row = $bd->consultar_proveedor($cuit);
				}else{
					$row["cuit"]="";
					$provincia = 0;
				}
				//Si es extranjero o consumidor final la condicion de iibb pone 0
				//Nota Vanina: pisa los datos que vienen del form?!? Estaria bueno que ya vengan ok
				if($condicion_iva == 4 || $condicion_iva == 5){
					$nroIIBB = "";
					$IIBB = 0;
				}
				
				if ($row["cuit"] == ""){
					$bd->agregar_proveedor($cuit, $razon_social, $IIBB, $nroIIBB, $condicion_iva, $domicilio, $provincia, $contacto, $telefono, $email, $contacto2, $email2, $banco1, $titular_cuenta1, $cuit1, $tipo_cuenta1, $numero_cuenta1, $cbu1, $banco2, $titular_cuenta2, $cuit2, $tipo_cuenta2, $numero_cuenta2, $cbu2, $CMPercent);
					break;
				}else
				{
					$error_proveedor = 1;
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['pr_baja'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],12,'baja')){
				$bd->borrar_proveedor($id_proveedor);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['pr_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],12,'modificacion')){
				if($condicion_iva == 4 || $condicion_iva == 5){
					$nroIIBB = "";
					$IIBB = 0;
					if ($condicion_iva == 4) {
						$provincia = 0;
					}
				}
				
				$bd->modificar_proveedor($id_proveedor, $cuit, $razon_social, $IIBB, $nroIIBB, $condicion_iva, $domicilio, $provincia, $contacto, $telefono, $email, $contacto2, $email2, $banco1, $titular_cuenta1, $cuit1, $tipo_cuenta1, $numero_cuenta1, $cbu1, $banco2, $titular_cuenta2, $cuit2, $tipo_cuenta2, $numero_cuenta2, $cbu2, $CMPercent);
			}else{
				echo "NO PERMISO";
			}
			break;
	}
	$bd = NULL;
	if ($error_proveedor == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El CUIT del Proveedor elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_proveedores.php");
	}
?>
