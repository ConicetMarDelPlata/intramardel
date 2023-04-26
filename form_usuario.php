<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET['opcion'];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_USERS');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],19,''); //19=Programa Usuarios
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Registro de Usuarios</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<style type="text/css">
.tituloweb2 {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: bold;
	line-height: 10px;
}
.tituloweb2Copia {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: normal;
	line-height: 10px;
}

a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
a:active {
	text-decoration: none;
	text-align: right;
}
.cerrar {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 9px;
	color: #333;
}
.pie {	font-family: Tahoma, Geneva, sans-serif;
	font-size: 9px;
	color: #FFF;
	padding-top: 5px;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	text-align: center;
}
</style>
<script language="javascript" >
function enviar(inForm){
	j = 0;
	for (i=0;i<inForm.elements.length;i++){ 
    	if(inForm.elements[i].type == "checkbox"){
         	if (inForm.elements[i].name == "alta"+j){
				if (inForm.elements[i].checked){
					inForm.elements[i].value = 1;
					//alert(j + "->" + inForm.elements[i].value);
				}//else{
				//	inForm.elements[i].value = 0;
				//	alert("Alta not checked");
				//}
				j++;
			}
		}
	}
	j = 0;
	for (i=0;i<inForm.elements.length;i++){ 
    	if(inForm.elements[i].type == "checkbox"){
         	if (inForm.elements[i].name == "baja"+j){
				if (inForm.elements[i].checked){
					inForm.elements[i].value = 1;
					//alert(j + "->" + inForm.elements[i].value);
				}//else{
				//	alert("Baja not checked");
				//}
				j++;
			}
		}
	}

	j = 0;
	for (i=0;i<inForm.elements.length;i++){ 
    	if(inForm.elements[i].type == "checkbox"){
         	if (inForm.elements[i].name == "modificacion"+j){
				if (inForm.elements[i].checked){
					inForm.elements[i].value = 1;
					//alert(j + "->" + inForm.elements[i].value);
				}//else{
				//	alert("Modificacion not checked");
				//}
				j++;
			}
		}
	}
		
	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (inForm.nombre_usuario.value == "") 
		{
			alert("El nombre de usuario es obligatorio.");
			inForm.nombre_usuario.focus();
			return (false);
		}
		else if (validaEmail(inForm.email.value.trim())) 
		{
			alert("Por favor, complete el email del usuario con un email valido.");	
			inForm.email.focus();
			return false;
		}
		else if (inForm.contrasenia.value != 'pswdefault') {
			if (validaPassword(inForm.contrasenia.value.trim())) {
				alert("Por favor, ingrese una contraseña válida.\nDebe contener al menos un número y/o caracter especial y una letra mayúscula y minúscula, y al menos 8 o más caracteres");	
				inForm.contrasenia.focus();
				return false;
			}
		}	
	}
	inForm.submit();
}

function checkConsulta(elem,idPermiso){
	//Si estoy chequeando la modificacion o la baja, tambien va la consulta
	if (elem.checked){
		document.getElementById("consulta"+idPermiso).checked = true;
	}
}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<style type="text/css">
.TITULO {font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color: #333;
	font-variant: normal;
	font-weight: bold;
	text-align: center;
	padding-left: 5px;
	padding-right: 30px;
	vertical-align: bottom;
	padding-bottom: 10px;
}
</style>
</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle">
		<span class="TITULO">:: Usuarios ::</span>
		<?php 
		$puedeAlta = $bd->checkPerm($_SESSION["id_usuario"],19,'alta');		
		echo '<a href="'; 
		echo ($puedeAlta)?"form_usuario.php?opcion=1":"#"; 
		echo '"><img src="';
		echo ($puedeAlta)?"agregar.png":"iconos_grises/agregarg.png";
		echo '" width="25" height="25" border="0"></a>';
		//$puedeConsulta = $bd->checkPerm($_SESSION["id_usuario"],19,'consulta');
		?>
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php /*ini_set('display_errors', 'On');
			error_reporting(E_ALL);*/
			include_once("templates/menuLateral-gral.php");
		?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA DE USUARIOS
				$id_usuario = 0;
				$nombre_usuario = "";
				$contrasenia_usuario = "";
				$nombre = "";
				$apellido = "";
				$email = "";
				$titulo = "";
				break;
			case 2: // OPCION BAJA DE USUARIOS
				//$codigo_articulo = $HTTP_GET_VARS['codigo_articulo'];
				//break;
			case 3: // OPCION MODIFICACION DE USUARIOS
			case 4: //CONSULTA
				$id_usuario = $_GET['id_usuario'];
				$row = $bd->consultar_usuario($id_usuario);
				$nombre_usuario = $row['nombre_usuario'];				
				$contrasenia_usuario = "pswdefault";//$row["contrasenia"];	
				$nombre = $row["nombre"];
				$apellido = $row["apellido"];
				$email = $row["email"];
				$titulo = $row["titulo"];
				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_usuario.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="189"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nombre 
                de usuario*:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="nombre_usuario" type="text" id="nombre_usuario" value="' . $nombre_usuario .'"' . 'size="55" maxlength="60" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Contrase&ntilde;a:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="contrasenia" type="password" id="contrasenia" value="' . $contrasenia_usuario .'"' . 'size="55" maxlength="30" '.$textoReadOnly.'></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>T&iacute;tulo:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="titulo" type="text" id="titulo" value="' . $titulo .'"' . 'size="55" maxlength="100" '.$textoReadOnly.'></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nombre:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="nombre" type="text" id="nombre" value="' . $nombre .'"' . 'size="55" maxlength="25" '.$textoReadOnly.'></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Apellido:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="apellido" type="text" id="apellido" value="' . $apellido .'"' . 'size="55" maxlength="25" '.$textoReadOnly.'></td>';
					?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>E-Mail*:</strong></font></div></td>
            <?php 
					echo '<td class="modo2"><input name="email" type="text" id="email" value="' . $email .'"' . 'size="55" maxlength="75" '.$textoReadOnly.'></td>';
					?>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datos obligatorios.</font></td>
          </tr>
        </table>
        <?php	
		$bd->lista_permisos_usuario2($id_usuario, $textoReadOnly);
		switch ($opcion){
			case 1: // ALTA DE USUARIO
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				echo '<input type="hidden" name="id_usuario" id="id_usuario"value="'.$id_usuario.'">';		
				break;
			case 2: // BAJA DE USUARIO
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_usuario" id="id_usuario" value="'.$id_usuario.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Articulo"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION DE USUARIO
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_usuario" id="id_usuario" value="'.$id_usuario.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 4: // CONSULTA DE USUARIO
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onclick="window.history.go(-1); return false;" alt="Volver"><img src="iconos/arrow-back-1.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      <p>&nbsp;</p>
      <script>
	document.form3.nombre_usuario.focus();
</script></td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
