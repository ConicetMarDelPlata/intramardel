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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_UE_GRAL');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],15,''); //15-Unidades ejecutoras
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}?>
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
function enviar(inForm){
	//alert("Llega");
	//alert(inForm.opcion.value);
	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (inForm.nombre.value.trim() == "") 
		{
			alert("El nombre de la Unidad Ejecutora es obligatorio.");
			inForm.nombre.focus();
			return (false);
		}
		else if (inForm.nombre_completo.value.trim() == "") 
		{
			alert("El nombre completo de la Unidad Ejecutora es obligatorio.");	
			inForm.nombre_completo.focus();
			return (false);
		}
		else if (validaEmail(inForm.mail_referente.value.trim())) 
		{
			alert("El email del contacto administrativo es obligatorio y posee formato invalido.");	
			inForm.mail_referente.focus();
			return (false);
		}
		else
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)?form.submit():'return false';
			inForm.submit();
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		inForm.submit();
}
function enviar_cuenta(){
	//Puede ser que este enviando un alta o una modificacion
	form = document.getElementById("newReg");
	if (form.nroCuenta.value.trim() == ""){ 
		alert("Complete el numero de cuenta.");
		form.nroCuenta.focus();
		return (false);
	}
	else {
		if (form.idCuenta.value != "") {
			//Es una modificacion de cuenta
			form.opcion.value = 7;
		}
		form.submit();
	}
}
	
function elim_cuenta(idcuenta){
	if(confirm("Est\u00E1 seguro de eliminar esta cuenta?")){
		form = document.getElementById("newReg");
		form.idCuenta.value = idcuenta;
		form.opcion.value = 6;
		form.submit();
	}else{
		return false;
	}
}
	
function modif_cuenta(idUnidad, idCuenta){
	location.href ="form_unidad_ejecutora.php?id_unidad_ejecutora="+idUnidad+"&id_cuenta="+idCuenta+"&opcion=3";
}

	
function cancelar_cuenta(idUnidad){
	location.href ="form_unidad_ejecutora.php?id_unidad_ejecutora="+idUnidad+"&opcion=3";
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
      Unidades ::</span><a href="form_unidad_ejecutora.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-gral.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		$id_cuenta = "";
		$nro_cuenta = "";		
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_unidad_ejecutora=0;
				$nombre = "";
				$nombre_completo = "";
				$cuit = "";
				$iibb = "";
				$domicilio = "";
				$telefono = "";
				$referente = "";
				$mail_referente = "";
				$mail_rerente = "";
				$director = "";
				$mail_director = "";
				$agente_retencion = 0;
				break;
			case 3: // OPCION MODIFICACION
				// puede ser que este modificando una cuenta tambien
				if(isset($_GET['id_cuenta'])) {
					$id_cuenta = $_GET['id_cuenta'];
					if ($id_cuenta != "") {
						$rowCuenta = $bd->consultar_cta_unidad_ejecutora($id_cuenta);
						$nro_cuenta = $rowCuenta["nro_cuenta"];
						}
				}
			case 2: // OPCION BAJA 
				//break;
			case 4: // OPCION CONSULTA
				$id_unidad_ejecutora = $_GET['id_unidad_ejecutora'];
				$row = $bd->consultar_unidad_ejecutora($id_unidad_ejecutora);
				$cuit = $row['cuit'];
				$iibb = $row['iibb'];
				$nombre = $row["nombre"];
				$nombre_completo = $row["nombre_completo"];
				$domicilio = $row["domicilio"];
				$telefono = $row["telefono"];				
				$referente = $row["referente"];
				$mail_referente = $row["mail_referente"];
				$director = $row["director"];
				$mail_director = $row["mail_director"];
				$agente_retencion = $row["agente_retencion"];					
				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_unidad_ejecutora.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table class="tabla_form">
          <tr> 
            <td class="modo1">Nombre Unidad *</td>
            <?php 
		echo '<td class="modo2"><input name="nombre" type="text" id="nombre" value="' . $nombre .'"' . 'size="55" maxlength="60" '.$textoReadOnly.'></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1">Nombre Completo Unidad&nbsp;*</td>
            <?php 
		echo '<td class="modo2"><input name="nombre_completo" type="text" id="nombre_completo" value="' . $nombre_completo .'"' . 'size="55" maxlength="250" '.$textoReadOnly.'></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1">CUIT</td>
            <?php 
		echo '<td class="modo2"><input name="cuit" type="text" id="cuit" onblur="PonerGuiones()" value="' . $cuit .'"' . 'size="55" maxlength="25" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1">IIBB</td>
            <?php 
		echo '<td class="modo2"><input name="iibb" type="text" id="iibb" onblur="PonerGuiones()" value="' . $iibb .'"' . 'size="55" maxlength="25" '.$textoReadOnly. '></td>';
	  ?>
          </tr>		  
          <tr> 
            <td class="modo1">Domicilio</td>
            <?php 
			echo '<td class="modo2"><input name="domicilio" type="text" id="domicilio" value="' . $domicilio .'"' . 'size="55" maxlength="55" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1">Tel&eacute;fono</td>
            <?php 
			echo '<td class="modo2"><input name="telefono" type="text" id="telefono" value="' . $telefono .'"' . 'size="55" maxlength="55" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1">Contacto Administrativo</td>
            <?php 
					echo '<td class="modo2"><input name="referente" type="text" id="referente" value="' . $referente .'"' . 'size="55" maxlength="55" '.$textoReadOnly. '></td>';
					?>
          </tr>
          <tr> 
            <td class="modo1">Mail Contacto Administrativo *</td>
            <?php 
			echo '<td class="modo2"><input name="mail_referente" type="text" id="mail_referente" value="' . $mail_referente .'"' . 'size="55" maxlength="75" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1">Director</td>
            <?php 
			echo '<td class="modo2"><input name="director" type="text" id="director" value="' . $director .'"' . 'size="55" maxlength="55" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1">Mail Director</td>
		<?php 
		echo '<td class="modo2"><input name="mail_director" type="text" id="mail_director" value="' . $mail_director .'"' . 'size="55" maxlength="75" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1">Agente Retenci&oacute;n</td>
		<?php 
		if ($agente_retencion==1) {$checked ="checked";}
		else {$checked = "";}
		echo '<td class="modo2" style="text-align:left;"><input name="agente_retencion" type="checkbox" id="agente_retencion" '.$checked.' value="1" '. $textoReadOnly. '></td>';
		?>
          </tr>
	  <tr><td colspan="2" align="center">
        <?php		
	switch ($opcion){
		case 1: // ALTA  
			echo '<input type="hidden" name="opcion" id="opcion" value="1">';
			echo '<button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button>';
			break;
		case 2: // BAJA 
		  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
			echo '<input type="hidden" name="id_unidad_ejecutora" id="id_unidad_ejecutora" value="'.$id_unidad_ejecutora.'">';				
			echo '<button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Articulo"><img src="eliminar.png" width="30" heigth="30" border="0"></button>';
			break;
		case 3: // MODIFICACION 
			echo '<input type="hidden" name="opcion" id="opcion" value="3">';
			echo '<input type="hidden" name="id_unidad_ejecutora" id="id_unidad_ejecutora" value="'.$id_unidad_ejecutora.'">';
			echo '<button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button>';
			break;
	}	
	//En todos los casos muestro el boton Volver
	echo '<button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="history.back()" alt="Actualizar datos"><img src="iconos/arrow-back-1.png" width="30" heigth="30" border="0"></button>';
	$bd = NULL;
	?>
      </form>
	</td></tr>
	<tr><td colspan="2"><br>
	 <?php if ($opcion != 1) { ?>
		<form name="newReg" id="newReg" action="abm_unidad_ejecutora.php" method="POST">
			<input type="hidden" name="opcion" id="opcion" value="5">
			<input type="hidden" name="idCuenta" id="idCuenta" value="<?php echo $id_cuenta; //Solo tiene valor para la modificacion de una cuenta?>">
			<?php echo '<input type="hidden" name="id_unidad_ejecutora" id="id_unidad_ejecutora" value="'.$id_unidad_ejecutora.'">';?>
			
			<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
				<tr>
					<th colspan="3">Cuentas bancarias</th>
				</tr>
				<tr class="modo1">
					<td>Nro. Cuenta*</td>
					<td>Acciones</td>
				</tr>
				<tr class="modo1">				
					<td style="padding-left:0px; padding-right:0px">
						<input style="width:300px" type="text" name="nroCuenta" id="nroCuenta" maxlength="200" value="<?php echo $nro_cuenta; ?>"/>
					</td>
					<td align="center">
						<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="enviar_cuenta();" alt="Grabar datos" <?php echo $textoReadOnly;?>>
							<img src="grabar_datos.png" width="16" height="16" border="0">
						</button>
						<button type="button" name="Btn_cancelar" id="Btn_cancelar" onclick="cancelar_cuenta(<?php echo $id_unidad_ejecutora;?>);" alt="Cancelar Edici&oacute;n" <?php echo $textoReadOnly;?>>
							<img src="iconos/arrow-back-1.png" width="16" height="16" border="0">
						</button>
					</td>
				</tr>
			</table>
		</form>
		<br/>
		<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
			<tr>
				<th>Nro. Cuenta</th>
				<th>Acciones</th>
			</tr>
			<?php 
			$bd = new Bd;
			$bd->AbrirBd();
			$ctasUnidades = $bd->getCuentasUnidadesPorUnidad($id_unidad_ejecutora);
			if (count($ctasUnidades)==0) {
				echo '<tr class="modo1"><td colspan="2">Por el momento no existen cuentas bancarias para esta unidad.</td></tr>';
			}
			else {
				foreach($ctasUnidades as $cuenta){ ?>
				<tr class="modo1">				
					<td style="width:130px"><?php echo $cuenta['nro_cuenta']; ?></td>
					<td style="width:40px;" align="center">
						<button type="button" name="btnEliminar" id="btnEliminar" onclick="elim_cuenta('<?php echo $cuenta['id']; ?>');" alt="Eliminar" <?php echo $textoReadOnly;?>>
							<img src="eliminar.png" width="16" height="16" border="0">
						</button>
						<button type="button" name="btnModificar" id="btnModificar" onclick="modif_cuenta('<?php echo $id_unidad_ejecutora.'\',\''.$cuenta['id']; ?>');" alt="Modificar" <?php echo $textoReadOnly;?>>
							<img src="actualizar_datos.png" width="16" height="16" border="0">
						</button>
					</td>
				</tr>
				<?php } 
			}?>
		</table>
	<?php } ?>
	</td></tr>
        </table>
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
document.form3.nombre.focus();
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
</script>
</body>
</html>
