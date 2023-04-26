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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_OC');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],3,''); //3=Orden de compra
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
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>

<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>
<script src="js/moment.js" type="text/javascript"></script>
<script src="js/validaciones.js" type="text/javascript"></script>

<script language="javascript" >
//Esta funcion se usa para enviar un item
function enviar(form){
	//alert("Llega");
	//alert(document.form3.opcion.value);
	//if (form==6)
	//	document.form3.opcion.value = 6;
	
	// Opcion 2 agregar una nueva orden con un nuevo movimiento
	// Opcion 2 eliminar una orden con todos sus movimientos
	// Opcion 3 actualizar un movimiento de una orden
	// Opcion 4 agregar un nuevo movimiento a una orden existente
	// Opcion 5 borrar un movimiento de una orden de compra
	if (document.form3.descripcion_componente.value.trim() == "") {
		alert("La descripci\u00f3n del componente es obligatoria.");
		document.form3.descripcion_componente.focus();
		return (false);
	}else if (!isInteger(document.form3.cantidad.value.trim())) {
		alert("La cantidad del componente debe ser un número.");
		document.form3.cantidad.focus();
		return (false);
	}else if (document.form3.unidad.value.trim() == "") {
		alert("La unidad del componente es obligatoria.");
		document.form3.unidad.focus();
		return (false);
	}else if (document.form3.precio_unitario.value.trim() == "") {
		alert("El precio unitario del componente es obligatorio.");
		document.form3.precio_unitario.focus();
		return (false);
	}else{
		form.submit();
	}
}
function enviar_encabezado(form){
	console.log(document.form3);
	if (!isInteger(document.form3.anio_numero_orden_compra.value)) 
		{alert("El a\u00f1o de la orden de compra es obligatorio y debe ser un n\u00famero.");
		document.form3.anio_numero_orden_compra.focus();
		return (false);} 
	else if (!moment(document.form3.fecha.value, "DD-MM-YYYY", true).isValid())
		{alert("La fecha es obligatoria y debe tener el formato DD-MM-YYYY.");
		document.form3.fecha.focus();
		return (false);}
	else if (document.form3.objeto.value.trim() == "")
		{alert("El objeto de la orden de compra es obligatorio.");
		document.form3.objeto.focus();
		return (false);}
	else if (document.form3.firma_digital.value.trim() == "")
		{alert("Firma digital es obligatorio (especifique 1 para si, y 0 para no).");
		document.form3.firma_digital.focus();
		return (false);}
	else if (document.form3.cantItems.value == 0)
		{alert("La orden de compra debe tener al menos un item para ser guardada.");
		return (false);}
	//numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';
	document.form4.fecha2.value = document.form3.fecha.value;					
	document.form4.anio_numero_orden_compra2.value=document.form3.anio_numero_orden_compra.value;
	document.form4.usuario2.value=document.form3.usuario.value;
	document.form4.proveedor2.value=document.form3.proveedor.value;
	document.form4.id_unidad.value = document.form3.id_unidad.value;
	document.form4.procedimiento_seleccion2.value=document.form3.procedimiento.value;
	document.form4.objeto2.value=document.form3.objeto.value;
	document.form4.referencia2.value=document.form3.referencia.value;
	document.form4.firmante2.value=document.form3.firmante.value;
	document.form4.firma_digital2.value=document.form3.firma_digital.value;	
	document.form4.submit();
}

function calcular_subtotal(){
	document.form3.subtotal.value = document.form3.cantidad.value * document.form3.precio_unitario.value;
	//alert(document.form3.subtotal.value);
}
</script>

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
			<a href="lista_ordenes_compra.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Orden de Compra ::</span><a href="form_orden_compra.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"><p>
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA
				$fecha  = date('d-m-Y');
				//$ultimo_numero_orden_compra = $bd->ultimo_numero_orden_compra();
				//$numero_orden_compra  = ++$ultimo_numero_orden_compra ;
				//$numero_orden_compra  = $bd->getConfig('last_oc_id'); ;
				//Nota Vani: asigno el numero al momento de grabar.
				$numero_orden_compra = "";
				$anio_numero_orden_compra = date('Y');
				$contacto = 0;
				$proveedor = 0;
				$procedimiento_seleccion = 0;
				$objeto = "";
				$referencia = "";
				$numero_item = 1;
				$descripcion_componente = "";
				$cantidad = 0;
				$unidadd = "UN";
				$id_unidad = 0;
				$precio_unitario = 0;
				$signo_moneda= 0;
				$subtotal = 0;
				$firmante = 0;				
				$firma_digital = 0;
			break;
			case 2: // OPCION BAJA
				//break;
			case 3: // OPCION MODIFICACION 
				$numero_orden_compra = $_GET['numero_orden_compra'];
				$numero_item = $_GET['numero_item']??0;					
				$anio_numero_orden_compra = $_GET["anio"];
				$row = $bd->consultar_orden_compra($numero_orden_compra, $numero_item, $anio_numero_orden_compra);
				$fecha = convertir_fecha($row["fecha"]);
				$contacto = $row["contacto"];
				$proveedor = $row["proveedor"];
				$procedimiento_seleccion = $row["procedimiento_seleccion"];
				$objeto = $row["objeto"];
				$referencia = $row["referencia"];
				$numero_item = $row["numero_item"];
				$descripcion_componente = $row["descripcion_componente"];
				$cantidad = $row["cantidad"];
				$unidadd = $row["unidad"];
				$id_unidad = $row["id_unidad_ejecutora"];
				$precio_unitario = $row["precio_unitario"];
				$signo_moneda= $row["signo_moneda"];
				$subtotal = $row["subtotal"];
				$firmante = $row["firmante"];				
				$firma_digital = $row["firma_digital"];
				break;
			case 4: //NUEVO MOVIMIENTO DE UNA ORDEN DE COMPRA EXISTENTE
				$numero_orden_compra = $_GET['numero_orden_compra'];
				$anio_numero_orden_compra = $_GET["anio"];				
				$row = $bd->consultar_orden_compra($numero_orden_compra, 0, $anio_numero_orden_compra);
				$fecha = convertir_fecha($row["fecha"]);
				$contacto = $row["contacto"];
				$proveedor = $row["proveedor"];
				$procedimiento_seleccion = $row["procedimiento_seleccion"];				
				$objeto = $row["objeto"];				
				$referencia = $row["referencia"];
				$firmante = $row["firmante"];
				$firma_digital = $row["firma_digital"];																				
				$numero_item = $bd->ultimo_numero_item_orden_compra($numero_orden_compra, $anio_numero_orden_compra);
				++$numero_item;
				$descripcion_componente = "";
				$cantidad = 0;
				$unidadd = "";
				$id_unidad = $row["id_unidad_ejecutora"];
				$precio_unitario = 0;
				$signo_moneda= 0;
				$subtotal = 0;		
				break;
			case 5: // ELIJE BORRAR UN MOVIMIENTO DE UNA ORDEN DE COMPRA
				$numero_orden_compra = $_GET['numero_orden_compra'];
				$numero_item = $_GET['numero_item'];
				$anio_numero_orden_compra = $_GET["anio"];				
				$bd->borrar_movimiento_orden_compra($numero_orden_compra, $numero_item, $anio_numero_orden_compra);
				//echo "num. remito: " . $numero_remito . "   num. orden: " . $numero_orden;
				//AGREGADO EL 18-02-13
				$row = $bd->consultar_orden_compra($numero_orden_compra, 0, $anio_numero_orden_compra);
				$fecha = convertir_fecha($row["fecha"]);
				$contacto = $row["contacto"];
				$proveedor = $row["proveedor"];
				$objeto = $row["objeto"];				
				$referencia = $row["referencia"];
				$firmante = $row["firmante"];
				$firma_digital = $row["firma_digital"];																				
				$numero_item = $bd->ultimo_numero_item_orden_compra($numero_orden_compra, $anio_numero_orden_compra);
				++$numero_item;
				$id_unidad = 0;

				$descripcion_componente = "";
				$cantidad = 0;
				$unidadd = "";
				$precio_unitario = 0;
				$signo_moneda= 0;
				$subtotal = 0;						
				break;			
		} // FIN SWITCH

?>
      </p>
      <form action="abm_orden_compra.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table width="296" align="center" class="tabla_form">
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Num. O.C.:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="numero_orden_compra" type="text" id="numero_orden_compra" value="' . $numero_orden_compra .'"' . 'size="25" maxlength="25" disabled>';
   		echo '<input name="anio_numero_orden_compra" type="text" id="anio_numero_orden_compra" value="' . $anio_numero_orden_compra .'"' . 'size="13" maxlength="13"></td>';
//		echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(6)" alt="Grabar datos"><img src="acrobat.png" width="15" heigth="20" border="0"></button></td>';		
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Emisor:</strong></font></div></td>
			<td class="modo2" style="text-align:left">
				<!--El emisor pasa a ser una unidad. Mostrar todas menos CCT Z Influencia-->
				<select name="id_unidad">
					<?php $unidades = $bd->getUEs();
					foreach($unidades as $unidad){
						//MdP Zona de Influencia no debe figurar
						if(11 != (int)$unidad['id_unidad_ejecutora']){
							if((int)$id_unidad == (int)$unidad['id_unidad_ejecutora']){
								echo '		<option value="'.$unidad['id_unidad_ejecutora'].'" selected>'.$unidad['nombre'].'</option>';
							}else{
								echo '		<option value="'.$unidad['id_unidad_ejecutora'].'">'.$unidad['nombre'].'</option>';
							}
						}
					}
					?>
				</select>
			</td>
		  </tr>
          <tr> 
            <td width="346" class="modo1"><div align="right"><font color="#000099"><strong>Fecha:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25">';
		echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador"></td>';
	?>
          </tr>
          <tr> 
            <td width="346" class="modo1"><div align="right"><font color="#000099"><strong>Contacto:</strong></font></div></td>
            <?php $bd->listar_usuarios($contacto);?>
          </tr>
          <tr> 
            <td width="346" class="modo1"><div align="right"><font color="#000099"><strong>Adjudicatario:</strong></font></div></td>
            <?php 
		if ($opcion == 3 || $opcion == 4){	
			$bd->listar_proveedores($proveedor);
		}else
			$bd->listar_proveedores(0);	 
	?>
          </tr>
          <tr> 
            <td width="346" class="modo1"><div align="right"><font color="#000099"><strong>Proc. Selec:</strong></font></div></td>
            <?php 
		if ($opcion == 3 || $opcion == 4){	
			$bd->listar_procedimientos($procedimiento_seleccion);
		}else
			$bd->listar_procedimientos(0);	 
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Objeto:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="objeto" type="text" id="objeto" value="' . $objeto .'"' . 'size="85" maxlength="255">';		
		  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Referencia:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="referencia" type="text" id="referencia" value="' . $referencia .'"' . 'size="85" maxlength="255">';		
	 	 	?>
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
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firma_Digital_:1=SI,0=NO:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="firma_digital" type="text" id="firma_digital" value="' . $firma_digital .'"' . 'size="5" maxlength="5">';
	 	 	?>
          </tr>		  
      <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador"     // el id del botón que lanzará el calendario 
	}); 
	</script>	
        </table>
        <table align="center" class="tabla_form">
         <?php 
		//Si no es baja muestra el formulario para dar de alta un item
		if ($opcion != 2){
			echo '<tr>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>N&uacute;m. Item:</strong></font></div></td>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Descripci&oacute;n del componente:</strong></font></div></td>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Cantidad:</strong></font></div></td>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Unidad:</strong></font></div></td>';
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Moneda:</strong></font></div></td>';	  
			echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Precio:</strong></font></div></td>';
			//echo '<td class="modo1"><div align="center"><font color="#000099"><strong>Subtotal:</strong></font></div></td>';
			echo '</tr>';
			//echo '<td class="modo2"><input name="numero_item" type="text" id="numero_item" value="' . $numero_item .'"' . 'size="7" maxlength="7" disabled></td>';
			echo '<td class="modo2">'.$numero_item.'</td>';			
			echo '<td class="modo2"><textarea name="descripcion_componente" type="text" id="descripcion_componente" value="' . $descripcion_componente .'"' . 'size="55" style="height: 22px;width:325px">' . $descripcion_componente .'</textarea></td> ';
			echo '<td class="modo2"><input name="cantidad" type="text" id="cantidad" value="' . $cantidad .'"' . 'size="10" maxlength="10"></td>';
			echo '<td class="modo2"><input name="unidad" type="text" id="unidad" value="' . $unidadd .'"' . 'size="10" maxlength="10"></td>';
			echo '<td>';
				//Solo habilito el combo para el item 1
				$enabled = ($numero_item == 1);
				//Consulto el item 1 para saber la moneda original y restringirla a esa
				$item1 = $bd->consultar_orden_compra($numero_orden_compra, 1, $anio_numero_orden_compra);
				$signo_moneda_item1 = $item1['signo_moneda'];
				if (!$signo_moneda_item1) $signo_moneda_item1=1;

				$bd->listar_monedas($signo_moneda_item1, $enabled);
			echo '</td>';
			echo '<td class="modo2"><input name="precio_unitario" type="text" id="precio_unitario" value="' . $precio_unitario .'"' . 'size="10" maxlength="10" onblur="calcular_subtotal()"></td>';
			//echo '</tr>';
		}	
		switch ($opcion){
			case 1: // ALTA 
			case 5: // ELIJE ELIMINAR UN MOVIMIENTO DE UNA ORDEN ENTONCES LUEGO MUESTRO BOTON GRABAR NUEVO MOVIMIENTO
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';				
				echo '<input type="hidden" name="numero_item" id="numero_item" value="'.$numero_item.'">';
				echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';
				echo '<input type="hidden" name="subtotal" id="subtotal" value="'.$precio_unitario*$cantidad.'">';				
				echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="15" heigth="20" border="0"></button></td></tr>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="numero_item" id="numero_item" value="'.$numero_item.'">';
				echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';
				echo '<input type="hidden" name="subtotal" id="subtotal" value="'.$precio_unitario*$cantidad.'">';				
				break;
			case 3: // MODIFICACION 
				echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="3"></td>';
				echo '<input type="hidden" name="numero_item" id="numero_item" value="'.$numero_item.'"></td>';
				echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';
				echo '<input type="hidden" name="subtotal" id="subtotal" value="'.$precio_unitario*$cantidad.'">';												
				echo '<td class="modo2"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="15" heigth="20" border="0"></button></td></tr>';
				break;
			case 4: // AGREGAR UN ITEM A UN REMITO EXISTENTE 
				echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="4"></td>';
				echo '<input type="hidden" name="numero_item" id="numero_item" value="'.$numero_item.'"></td>';
				echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';												
				echo '<input type="hidden" name="subtotal" id="subtotal" value="'.$precio_unitario*$cantidad.'">';			
				echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="15" heigth="20" border="0"></button></td></tr>';
				break;
		}
	?>
        </table>
      <?php

		$cantItems = ($numero_orden_compra != '') ? $bd->lista_orden_compra_por_item($numero_orden_compra, $opcion, $anio_numero_orden_compra) : 0; 
		echo '<input type="hidden" name="cantItems" id="cantItems" value="'.$cantItems.'">';			
		if ($opcion == 2){	
			echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="form.submit();" alt="Eliminar Registro"><img src="eliminar.png" width="25" heigth="30" border="0"></button></p>';
		}

		$bd = NULL;
	?>	
	      </form> 
		<form action="abm_orden_compra_encabezado.php" method="post" enctype="multipart/form-data" name="form4" id="form4">	  
        <?php 
		  if ($opcion == 1){	  	
		  	echo '<tr>';
			echo '<td><input type="hidden" name="opcion2" id="opcion2" value="1"></td>';
			echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';			

			echo '<input type="hidden" name="anio_numero_orden_compra2" id="anio_numero_orden_compra2" value="">';
			echo '<input type="hidden" name="fecha2" id="fecha2" value="">';						
			echo '<input type="hidden" name="usuario2" id="usuario2" value="">';									
			echo '<input type="hidden" name="proveedor2" id="proveedor2" value="">';
			echo '<input type="hidden" name="id_unidad" id="id_unidad" value="">';
			echo '<input type="hidden" name="procedimiento_seleccion2" id="procedimiento_seleccion2" value="">';
			echo '<input type="hidden" name="objeto2" id="objeto2" value="">';
			echo '<input type="hidden" name="referencia2" id="referencia2" value="">';
			echo '<input type="hidden" name="firmante2" id="firmante2" value="">';
			echo '<input type="hidden" name="firma_digital2" id="firma_digital2" value="">';			
		  	echo '</tr>';			
		  }else{
		  	echo '<tr>';
			echo '<td><input type="hidden" name="opcion2" id="opcion2" value="3"></td>';
			echo '<input type="hidden" name="numero_orden_compra" id="numero_orden_compra" value="'.$numero_orden_compra.'">';			

			echo '<input type="hidden" name="anio_numero_orden_compra2" id="anio_numero_orden_compra2" value="">';
			echo '<input type="hidden" name="fecha2" id="fecha2" value="">';						
			echo '<input type="hidden" name="usuario2" id="usuario2" value="">';									
			echo '<input type="hidden" name="proveedor2" id="proveedor2" value="">';
			echo '<input type="hidden" name="id_unidad" id="id_unidad" value="">';
			echo '<input type="hidden" name="procedimiento_seleccion2" id="procedimiento_seleccion2" value="">';
			echo '<input type="hidden" name="objeto2" id="objeto2" value="">';
			echo '<input type="hidden" name="referencia2" id="referencia2" value="">';
			echo '<input type="hidden" name="firmante2" id="firmante2" value="">';
			echo '<input type="hidden" name="firma_digital2" id="firma_digital2" value="">';									

		  	echo '</tr>';			
		  }
		?>	 
      </form>	
      <script>
	document.form3.anio_numero_orden_compra.focus();
	function buscarPorCUIT(){
		var cuit = prompt("INGRESE NRO DE CUIT\n(Sin espacios ni guiones)");
		if(cuit!='' && validaCuit(cuit)){
			var cuit = cuit.substr(0, 2)+"-"+cuit.substr(2, 8)+"-"+cuit.substr(10, 2);
			$.post('buscarcuitprov.php?cuit='+cuit, function(data) {
				eval('var obj='+data);
				if(obj.OK){
					$("#opprov_"+obj.id).attr("selected","selected");
					obj.value=obj.id;
					buscarBancos(obj);
				}else{
				}
			});
		}else{
			alert("\n                CUIT INVÁLIDO");
		}
	}

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
</html>
