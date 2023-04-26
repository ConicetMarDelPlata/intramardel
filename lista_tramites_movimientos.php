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
	
	$id_permiso = 33;//33-Seguimiento Rendiciones Administracion = Reclamos = Tramites
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_NOT_GRAL');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],$id_permiso,''); 
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}

	$id_tramite = $_GET["id_tramite"];	
	$rowTramite = $bd->consultar_tramite($id_tramite);
	
?>
<html>
<head>
<title>PANEL CONTROL</title>
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
.TITULO {
	font-family: Verdana, Geneva, sans-serif;
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
<link href="tabla.css" rel="stylesheet" type="text/css" />

</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="lista_tramites.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
	<?php
  		echo '<td align="left" valign="middle"><span class="TITULO">:: M&oacute;dulo Seguimiento Rendiciones Administraci&oacute;n ::<br><br> &nbsp;&nbsp;Movimientos del tr&aacute;mite '.$rowTramite['numero'].'/'.$rowTramite['anio'].' </span></td>';
	?>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> 
	<table width="709" border="0" cellpadding="10" cellspacing="1" class="tabla" align="center">
		<tr>
			<th>Fecha</th>
			<th>Movimiento</th> 
			<th>Usuario</th>
			<th>Detalle</th>

		 	<?php
			$iMaxRows = 15;
			$iPagActual = (isset($_GET['pag']))?$_GET['pag']:1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			$iTotalPag = 0;
			
			$r = $bd->lista_tramites_movimientos($id_tramite, $iMaxRows, $iLimit, $iTotalPag);
			if($r){
				while ( $row = mysqli_fetch_array($r) ){
					echo '<tr class="modo1">';
					echo '<td style="text-align: left;padding:5px">'.$row['fecha'].' hs.</td>';
					echo '<td style="text-align: left;padding:5px">'.$row['tramite_movimiento_tipo_nombre'].'</td>';				
					echo '<td style="text-align: left;padding:5px">'.$row['apellido'].", ".$row['nombre'].'</td>';
					echo '<td style="text-align: left;padding:5px">'.$row['detalle'].'</td>';
				}
			}

			$iTotalPag = round(mysqli_num_rows ($r)/$iMaxRows);

			$iNextPage = ($iPagActual < $iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='4'>
						<a href='lista_tramites_movimientos.php?id_tramite=$id_tramite&pag=$iPrevPage'>Prev </a> 
						$iPagActual / $iTotalPag
						<a href='lista_tramites_movimientos.php?id_tramite=$id_tramite&pag=$iNextPage' value='sig'>Sig</a>
					</td>
				</tr>";
			echo '</table>';
		?>
    </td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<?php
if (isset($_SESSION["message"])) {
	echo '<script language=javascript>';
	$message=$_SESSION["message"];
	if ($message!="") { 
		echo 'alert("'.$message.'")'; 
		$_SESSION["message"]="";
		}
	echo '</script>';
}
?>
</body>
</html>
