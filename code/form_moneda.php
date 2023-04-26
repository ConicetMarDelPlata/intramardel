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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_MON');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],14,''); //14=Monedas
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PANEL CONTROL</title>
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
function enviar(form){
	//alert("Llega");
	//alert(document.form3.opcion.value);
	if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (document.form3.signo.value == "") 
		{
			alert("El signo de la moneda es obligatorio.");
			return (false);
		}
		else
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)?form.submit():'return false';
			document.form3.submit();
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		document.form3.submit();
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
    <td align="left" valign="middle"><span class="TITULO">:: 
      Monedas ::</span><a href="form_moneda.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-gral.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_moneda=0;
				$signo = "";
				$descripcion = "";
				break;
			case 2: // OPCION BAJA 
				//break;
			case 3: // OPCION MODIFICACION
				$id_moneda = $_GET['id_moneda'];
				$row = $bd->consultar_moneda($id_moneda);							
				$signo = $row["signo"];
				$descripcion = $row["descripcion"];
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_moneda.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="276"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Signo 
                *</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="signo" type="text" id="signo" value="' . $signo .'"' . 'size="55" maxlength="25"></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Descripci&oacute;n:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="descripcion" type="text" id="descripcion" value="' . $descripcion .'"' . 'size="55" maxlength="25"></td>';
	  ?>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datos obligatorios.</font></td>
          </tr>
        </table>
        <?php		
		switch ($opcion){
			case 1: // ALTA  
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_moneda" id="id_moneda" value="'.$id_moneda.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Moneda"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_moneda" id="id_moneda" value="'.$id_moneda.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      <script>
	document.form3.signo.focus();
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
