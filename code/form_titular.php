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

	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],31,''); //31-Titulares
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
	   
		if (inForm.apellido.value.trim() == "") 
		{
			alert("Por favor, complete el apellido del titular.");
			inForm.apellido.focus();
			return false;
		}
		else if (inForm.nombre.value.trim() == "") 
		{
			alert("Por favor, complete el nombre del titular.");
			inForm.nombre.focus();
			return false;
		}
		else if (validaEmail(inForm.email.value.trim())) 
		{
			alert("Por favor, complete el email del titular con un email valido.");	
			inForm.email.focus();
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
    <td align="left" valign="middle"><span class="TITULO">:: Titulares ::</span><a href="form_titular.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
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
				$id_titular = 0;
				$apellido = "";
				$nombre = "";
				$dni = "";
				$email = "";
				break;
			case 2: // OPCION BAJA 
			case 3: // OPCION MODIFICACION
			case 4: //CONSULTA
				$id_titular = $_GET['id'];
				$row = $bd->consultar_titular($id_titular);
				$apellido = $row['apellido'];				
				$nombre = $row["nombre"];	
				$dni = $row["dni"];
				$email = $row["email"];
				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_titular.php" method="post" enctype="multipart/form-data" name="frmABMC">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="245"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Apellido*:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="apellido" type="text" value="' . $apellido .'"' . 'size="55" maxlength="100" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nombre*:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="nombre" type="text"  value="' . $nombre .'"' . 'size="55" maxlength="100" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>DNI:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="dni" type="text" value="' . $dni .'"' . 'size="55" maxlength="50" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Email*:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="email" type="text"  value="' . $email .'"' . 'size="55" maxlength="75" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* Datos obligatorios.</font></td>
          </tr>
        </table>
        <?php	
		echo '<input type="hidden" name="id_titular" value="'.$id_titular.'">';				

		switch ($opcion){
			case 1: // ALTA  
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 4: // CONSULTA DE USUARIO
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onclick="window.history.go(-1); return false;" alt="Volver"><img src="iconos/arrow-back-1.png" width="30" heigth="30" border="0"></button></p>';
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
	document.frmABMC.apellido.focus();
</script>
</body>
</html>
