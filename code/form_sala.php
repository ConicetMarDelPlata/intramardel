<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	include_once("./includes/class.Salas.php");

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

	$sala = new Salas($bd);
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],35,''); //35-Salas
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
<link rel="stylesheet" type="text/css" href="css/base.css" media="all">
<script language="javascript" >
function enviar(inForm){
	if (document.frmABMC.opcion.value != 2){ //SI NO ELIJE ELIMINAR
	   
		if (inForm.nombre.value.trim() == "") 
		{
			alert("Por favor, complete el nombre de la sala.");
			inForm.nombre.focus();
			return false;
		}
		else
			inForm.submit();
		
	}
	else { //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		inForm.submit();
	}
}

</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101"></p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: Salas ::</span><a href="form_sala.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
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
				$id_sala= 0;
				$nombre = "";
				break;
			case 2: // OPCION BAJA 
			case 3: // OPCION MODIFICACION
			case 4: //CONSULTA
				$id_sala= $_GET['id'];
                $row = $sala->consultar_sala($id_sala);
                $nombre = $row['nombre'];		
                $equipos = $row['equipos'];		
				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      	<form action="abm_sala.php" method="post" enctype="multipart/form-data" name="frmABMC">
			<table align="center" class="tabla_form">
				<tr> 
					<td width="245"> </td>
				</tr>
				<tr> 
					<td class="modo1"><div align="right"><font color="#000099"><strong>Nombre*:</strong></font></div></td>
					<?php 
						echo '<td class="modo2"><input name="nombre" type="text" value="' . $nombre .'" size="55" ' . $textoReadOnly. '></td>';
					?>
				</tr>
				<?php 
					if ($opcion != 1) {
						echo '<tr>';				 
						echo '<td class="modo1"><div align="right"><font color="#000099"><strong>Equipo:</strong></font></div></td>';
						echo '<td class="modo1" style="text-align:left">';
						foreach ($equipos as $eq) {
							if ($eq['checked'] == 1){
								if ($opcion == 4) {
									echo '<input type="checkbox" name="equipos[]" value="'.$eq['id'].'" disabled checked>';
								} else {
									echo '<input type="checkbox" name="equipos[]" value="'.$eq['id'].'" checked>';
								}
							} else {
								if ($opcion == 4){
									echo '<input type="checkbox" name="equipos[]" value="'.$eq['id'].'" disabled>';
								} else {
									echo '<input type="checkbox" name="equipos[]" value="'.$eq['id'].'">'; 
								}
							}
							echo '<label for="'.$eq['id'].'">'. $eq['nombre'].'</label><br>';
						}
						echo '</td>';
						echo '</tr>';
					} 
				?>
				
				<tr> 
					<td colspan="2" class="modo1" align="center"><font color="#000099">* Datos obligatorios.</font></td>
				</tr>
			</table>
        <?php	
		echo '<input type="hidden" name="id_sala" value="'.$id_sala.'">';				

		switch ($opcion){
			case 1: // ALTA  
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" title="Grabar datos" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" title="Eliminar" alt="Eliminar"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" title="Actualizar datos" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 4: // CONSULTA DE USUARIO
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onclick="window.history.go(-1); return false;" title="Volver" alt="Volver"><img src="iconos/arrow-back-1.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	

	?>
      </form>
	</td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
<script>
	document.frmABMC.nombre.focus();
</script>
</body>
</html>
