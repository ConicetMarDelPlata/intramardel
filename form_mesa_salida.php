<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET["opcion"];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_MS');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],2,''); //2=Mesa de salida
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
<script language="javascript" >
function enviar(form){
		/*alert(document.form3); */
	if (form==6)
		document.form3.opcion.value = 6;
	
	// Opcion 2 agregar un nuevo remito con un nuevo movimiento
	// Opcion 2 eliminar un remito con todos sus movimientos
	// Opcion 3 actualizar un movimiento de un remito
	// Opcion 4 agregar un nuevo movimiento a un remito existente
	// Opcion 5 borrar un movimiento de unremito
	
	//alert(document.form3.opcion.value);
	//return false;
	/*if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (document.form3.fecha.value == "") 
		{
			alert("La fecha es obligatoria.");
			return (false);
		}else
				//enviar = window.confirm('Se enviarán todos los datos del formulario');
				//(enviar)?form.submit():'return false';
			document.form3.submit();
	}
	else*/ //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		document.form3.submit();
}
function enviar_encabezado(form){
	//alert("Llega");
	//alert(document.form3.opcion.value);
	
	//numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';
	document.form4.fecha2.value = document.form3.fecha.value;					
	document.form4.firmante2.value=document.form3.firmante.value;
	//alert(document.form4.fecha2.value);
	
	document.form4.submit();
}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<script src="js/jquery.js" type="text/javascript"></script>
<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>
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
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="panel_control_modulos.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar" style=""><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Mesa de Salida ::</span><a href="form_mesa_salida.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="370" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p>
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA
				$fecha  = ""; //date('d-m-y');
				//$ultimo_numero_remito = $bd->ultimo_numero_remito();
				//$numero_remito = ++$ultimo_numero_remito;
				$numero_remito = $bd->getConfig('last_ms_id');
				$numero_orden = 1;
				$numero_tramite = "";
				$remitente = "";
				$documento = "";
				$destinatario = 0;
				$copias = 1;
				$cantidad_hojas = 0;
				$firmante = 0;				
			break;
			case 2: // OPCION BAJA
				//break;
			case 3: // OPCION MODIFICACION DE 
				$numero_remito = $_GET['numero_remito']??0;
				$numero_orden = $_GET['numero_orden']??0;
				$iYear = $_GET['anio'];
				$row = $bd->consultar_mesa_salida($numero_remito, $numero_orden, $iYear);
				$fecha = convertir_fecha($row["fecha"]) ?? '';				
				$numero_tramite = $row['numero_tramite'];
				$remitente = $row["remitente"];
				$documento = $row["documento"];
				$destinatario = $row["destinatario"];
				$copias = $row["copias"];
				$cantidad_hojas = $row["cantidad_hojas"];
				$firmante = $row["firmante"];				
				break;
			case 4:
				$numero_remito = $_GET['numero_remito'];
				$iYear = $_GET['anio'];
				$row = $bd->consultar_mesa_salida($numero_remito, 0,$iYear);
/*echo "<pre>";
var_dump($row);*/
				$fecha = convertir_fecha($row["fecha"]) ?? '';
				$firmante = $row["firmante"];				
				$numero_orden = $bd->ultimo_numero_orden_mesa_salida($numero_remito,$iYear);
				++$numero_orden;
				$numero_tramite = "";
				$remitente = "";
				$documento = "";
				$destinatario = 0;
				$copias = 1;
				$cantidad_hojas = 0;						
				break;
			case 5: // ELIJE BORRAR UN MOVIMIENTO DE UN REMITO
				$fecha  = "";
				$numero_remito = $_GET['numero_remito'];
				$numero_orden = $_GET['numero_orden'];
				$numero_tramite = "";
				$copias = 1;
				$cantidad_hojas = 0;
				$remitente = "";
				$documento = "";
				$destinatario = 0;
				$firmante = 0;				
				$iYear = $_GET['anio'];
				$bd->borrar_movimiento_mesa_salida($numero_remito, $numero_orden, $iYear);
				//echo "num. remito: " . $numero_remito . "   num. orden: " . $numero_orden;
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_mesa_salida.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero 
                de Remito:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="numero_remito" type="text" id="numero_remito" value="' . $numero_remito .'"' . 'size="25" maxlength="25"></td>';
   	  ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25">';
		echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador"></td>';
//		echo '<td><input name="image" type="button" src="acrobat.png" width="25" height="25" onClick="enviar(acrobat)" oversrc="acrobat.png"></td>';
		echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(6)" alt="Grabar datos"><img src="acrobat.png" width="15" heigth="20" border="0"></button></td>';
	?>
            <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador"     // el id del botón que lanzará el calendario 
	}); 
	</script>
          </tr>
		  <tr>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firmante :</strong></font></div></td>
            <?php 
				if ( ($opcion == 2 ) || ($opcion == 3) || ($opcion == 4) ){ // BAJA O MODIFICACION
					$bd->listar_firmantes($firmante);
				}else //ALTA
				{				
					$bd->listar_firmantes(0);
				}
			?>
		  </tr>
        </table>
        <table align="center" class="tabla_form">
          <?php 
		if ($opcion != 2){
			echo '<tr>';
			echo '<td class="modo1" colwidth="50px"><div align="center"><font color="#000099"><strong>N&uacute;m. Orden:</strong></font></div></td>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>N&uacute;mero de tr&aacute;mite:</strong></font></div></td>';
			echo '<td class="modo1" width="1800px"><div align="center"><font color="#000099"><strong>Destinatario:</strong></font></div></td>';	  
			echo '<td class="modo1" width="600px"><div align="center"><font color="#000099"><strong>Copias:</strong></font></div></td>';
			echo '<td class="modo1" width="600px"><div align="center"><font color="#000099"><strong>Cantidad Hojas:</strong></font></div></td>';
			echo '<td width="600px">&nbsp;</td>';
			echo '<td width="600px">&nbsp;</td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td class="modo2"><input name="numero_orden" type="text" id="numero_orden" value="' . $numero_orden .'"' . 'size="7" maxlength="7"></td>';
			echo '<td class="modo2"><input name="numero_tramite" type="text" id="numero_tramite" value="' . $numero_tramite .'"' . 'size="8" maxlength="15">';
			if ($opcion == 3){
				$bd->listar_destinatarios($destinatario, "Salida");
			}else
				$bd->listar_destinatarios(0, "Salida");
			echo '<td class="modo2"><input name="copias" type="text" id="copias" value="' . $copias .'"' . 'size="7" maxlength="7"></td>';
			echo '<td class="modo2"><input name="cantidad_hojas" type="text" id="cantidad_hojas" value="' . $cantidad_hojas  .'"' . 'size="7" maxlength="7"></td>';
			echo '<td  width="300px">&nbsp;</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td class="modo1" colspan="2"><div align="center"><font color="#000099"><strong>Remitente:</strong></font></div></td>';
			echo '<td class="modo1" colspan="2"><div align="center"><font color="#000099"><strong>Documento:</strong></font></div></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="modo2" colspan="2"><input style="width:400px" name="remitente" type="text" id="remitente" value="' . $remitente .'"' . ' maxlength="40"></td>';
			echo '<td class="modo2" colspan="2"><input style="width:500px" name="documento" type="text" id="documento" value="' . $documento .'"' . ' maxlength="150"></td>';

		}	
		switch ($opcion){
			case 1: // ALTA 
			case 5: // ELIJE ELIMINAR UN MOVIMIENTO DE UN REMITO ENTONCES LUEGO MUESTRO BOTON GRABAR NUEVO MOVIMIENTO
				echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="1"></td>';				
				echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="15" heigth="20" border="0"></button></td>';
				break;
			case 2: // BAJA 
				$bd->lista_mesa_salida_por_remito($numero_remito, 1, $iYear);  
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Registro"><img src="eliminar.png" width="25" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="3"></td>';
				echo '<td class="modo2"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="15" heigth="20" border="0"></button></td>';
				break;
			case 4: // AGREGAR UN ITEM A UN REMITO EXISTENTE 
				echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="4"></td>';
				echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="15" heigth="20" border="0"></button></td>';
				break;
		}
			echo '</tr>';
	?>
        </table>
      </form>
      <?php
		if ($opcion != 2){
			if(!isset($iYear)){
				$iYear = date("Y");
			}
			$bd->lista_mesa_salida_por_remito($numero_remito, 0, $iYear);  
		}
		$bd = NULL;
	?>
	<form action="abm_mesa_salida_encabezado.php" method="post" enctype="multipart/form-data" name="form4" id="form4">	  
        <?php 
		  if ($opcion == 1){	  	
		  	echo '<tr>';
			echo '<td><input type="hidden" name="opcion2" id="opcion2" value="1"></td>';
			echo '<input type="hidden" name="numero_remito" id="numero_remito" value="'.$numero_remito.'">';			
			echo '<input type="hidden" name="fecha2" id="fecha2" value="">';						
			echo '<input type="hidden" name="firmante2" id="firmante2" value="">';			
			echo '<input type="hidden" name="anio" id="anio" value="'.$iYear.'">';			

			echo '<td align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar_encabezado(this.form)" alt="Grabar datos"><img src="grabar_datos.png" width="25" heigth="20" border="0"></button></td>';		
		  	echo '</tr>';			
		  }else{
		  	echo '<tr>';
			echo '<td><input type="hidden" name="opcion2" id="opcion2" value="3"></td>';
			echo '<input type="hidden" name="numero_remito" id="numero_remito" value="'.$numero_remito.'">';			
			echo '<input type="hidden" name="fecha2" id="fecha2" value="">';						
			echo '<input type="hidden" name="firmante2" id="firmante2" value="">';
			echo '<input type="hidden" name="anio" id="anio" value="'.$iYear.'">';			

			echo '<td align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar_encabezado(this.form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="25" heigth="20" border="0"></button></td>';		
		  	echo '</tr>';			
		  }
		?>	 
      </form>
      <script>
		document.form3.numero_tramite.focus();
	</script> </td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
<script type="text/javascript">
$("#numero_tramite").blur(function(){
	$.ajax({
		type: 'POST',
		url: 'getData.php',
		data:"tramite=" + $("#numero_tramite").val(),
		dataType:'json',
		success: function(data) {
			if(data){
				$('#remitente').val(data.remitente);
				$('#documento').val(data.documento);
				$('#cantidad_hojas').val(data.cantidad);
			}
		}
	});			
});
</script>
</html>
