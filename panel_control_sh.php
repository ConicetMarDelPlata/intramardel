<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	$sesion = NULL;	

	$bd = new Bd;
	$bd->AbrirBd();
	$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	$puede_entrar = true;//TODO vani//$bd->getPermisos($userData,'CAN_ACCESS_WEB');
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
</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr class="cerrar" >
    <td><strong> <img src="images/bullet20.gif" width="9" height="9" />  <?php echo 'Usuario: '. $nombre_usuario ?></strong>
      <p>&nbsp;</p></td>
    <td width="722" rowspan="2" align="center" valign="top"><a href="index.php?salir=1"></a>
      <table width="610" height="237" border="0" align="center" cellspacing="20">
        <tr align="center">
          <td valign="bottom">
			<p>
				<font size="2">
					<font color="#000000">
						<img src="images/1358445452_Location News.png" width="55" height="55" vspace="5"><br>
					</font>
				</font>
				<font size="2">
					<font color="#000000">
						<font size="2">
							<a href="lista_seg_hig.php" class="tituloweb2Copia">Noticias</a>
						</font>
					</font>
				</font>
			</p>
		  </td>
          <td valign="bottom">
			<p>
				<font size="2">
					<font color="#000000">
						<img src="images/1358445618_human-user-home.png" width="55" height="55" vspace="5"><br>
					</font>
				</font>
				<font size="2">
					<font color="#000000">
						<font size="2">
							<a href="lista_sh_files.php" class="tituloweb2Copia">Documentos por UE</a>
						</font>
					</font>
				</font>
			</p>
		  </td>
          <td valign="bottom">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
	<?php include_once("templates/menuLateral-web.php"); ?>
	  </td>
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
