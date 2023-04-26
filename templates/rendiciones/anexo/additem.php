<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PANEL CONTROL</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>

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
<script src="js/jquery.js" type="text/javascript"></script>
<script src="tabs/tabcontent.js" type="text/javascript"></script>
<script src="lightbox/jquery.lightbox_me.js" type="text/javascript"></script>
<script type="text/javascript" src="overlib421/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<link href="tabs/template6/tabcontent.css" rel="stylesheet" type="text/css" />

<style type="text/css">
.TITULO {	font-family: Verdana, Geneva, sans-serif;
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
<style type="text/css">
table.myTable { border-collapse:collapse; }
table.myTable th { border:1px solid black;padding:5px; font-size:11px; font-family: Arial;}
table.myTable td { border:none; padding:5px; font-size:11px; font-family: Arial;background-color:#D5E8F2;}
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
    <td class="cerrar" style="height: 34px"><p><strong><img src="images/bullet20.gif" width="9" height="9" />Usuario: {var_User}</strong></p>
      <p>&nbsp;</p></td>
	<td align="left" valign="middle">
		<span class="TITULO">:: Nuevo Registro Anexo Donación ::</span>
		<!--<a href="{var_anexo_open_close}"><img src="{var_path_open_close_anexo}" width="27" height="27" border="0" alt="Abrir Anexo Donaci&oacute;n"></a>-->
	</td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		{var_BarraLateral}
	</td>
	<!-- CONTENIDO PRINCIPAL -->
	
	<!-- LISTA ACTAS  -->
    <td width="722" valign="top" id="lista_actas">
		<form name="newADItem" method="POST" action="ad_additem.php">
			<input type="hidden" name="sAction" value="add"/>
			<input type="hidden" name="iIDAD" value="{var_iIDAD}"/>
			<input type="hidden" name="dFechaCompra" id="dFechaCompra" value="" onchange="document.getElementById('dFechaCompra1').value = this.value;"/>
			<table border="0" cellpadding="1" cellspacing="1" class="tabla_form">
				<tr>
					<td class="modo1" style="width:190px;">Nº Orden</td>
					<td class="modo2"><input type="text" name="iOrden" style="width:99%" disabled value="{var_Orden}"/></td>
				</tr>
				<tr>
					<td class="modo1">Fecha de Compra <span style="color:red; font-size:14px; font-weight:bold; vertical-align:top;">!</span></td>
					<td class="modo2">
						<input type="text" name="dFechaCompra" id="dFechaCompra1" style="width:89%" disabled/>
						<img src="calendario/ima/calendario.png" width="16" height="16" border="0" id="dFC">
					</td>
				</tr>
				<tr>
					<td class="modo1">Cantidad <span style="color:red; font-size:14px; font-weight:bold; vertical-align:top;">!</span></td>
					<td class="modo2"><input type="text" name="iCantidad" style="width:99%" /></td>
				</tr>
				<tr>
					<td class="modo1">Descripción <span style="color:red; font-size:14px; font-weight:bold; vertical-align:top;">!</span></td>
					<td class="modo2"><input type="text" name="sDescripcion" style="width:99%" /></td>
				</tr>
				<tr>
					<td class="modo1">Marca</td>
					<td class="modo2"><input type="text" name="sMarca" style="width:99%" /></td>
				</tr>
				<tr>
					<td class="modo1">Modelo</td>
					<td class="modo2"><input type="text" name="sModelo" style="width:99%" /></td>
				</tr>
				<tr>
					<td class="modo1">U.E</td>
					<td class="modo2"><input type="text" name="sUE" style="width:99%" value="{var_sUE}"disabled/></td>
				</tr>
				<tr>
					<td class="modo1">Nº Serie/ISSN/ISBN <span style="color:red; font-size:14px; font-weight:bold; vertical-align:top;">!</span></td>
					<td class="modo2"><input type="text" name="sSerie" style="width:99%" /></td>
				</tr>
				<tr>
					<td class="modo1">Importe <span style="color:red; font-size:14px; font-weight:bold; vertical-align:top;">!</span></td>
					<td class="modo2">
						<select name="iMoneda" style="width:25%">
							<option value="0">---</option>
							<!--BEGIN BLOCK MONEDA-->
							<option value="{var_iMoneda}">{var_sMoneda}</option>
							<!--END BLOCK MONEDA-->
						</select>
						<input type="text" name="iMonto" style="width:71%" />
					</td>
				</tr>
				<tr>
					<td class="modo1">Motivo de Alta</td>
					<td class="modo2"><input type="text" name="sMAlta" style="width:99%" value="Donación al CONICET" disabled/></td>
				</tr>
				<!--<tr>
					<td class="modo1">Resolución de Otorgamiento</td>
					<td class="modo2"><input type="text" name="sResOtorg" style="width:99%" value="{var_sResOtorg}" disabled/></td>
				</tr>
				<tr>
					<td class="modo1">Fecha de R.O.</td>
					<td class="modo2"><input type="text" name="dResOtorg" style="width:99%" value="{var_dResOtorg}" disabled/></td>
				</tr>-->
				<tr>
					<td class="modo1">Titular</td>
					<td class="modo2"><input type="text" name="sTitular" style="width:99%" value="{var_sTitular}" disabled/></td>
				</tr>
			</table>
		</form>
		<div style="margin-left:170px">
			<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="document.forms.newADItem.submit();" alt="Grabar datos">
				<img src="grabar_datos.png" width="30" heigth="30" border="0">
			</button>
		</div>
	</td>
	<!-- FIN LISTA ACTAS  -->
	<!-- FIN CONTENIDO PRINCIPAL -->
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
var lastID=0;

function masInfo(id){
	if(id != 0){
		if(lastID != 0){
			var obj = document.getElementById("div-"+lastID);
			obj.style.display = "none";
		}
		lastID = id;
		var obj = document.getElementById("div-"+id);
		obj.style.display = "";
	}else{
		var obj = document.getElementById("div-"+lastID);
		obj.style.display = "none";
		lastID = id;
	}
}

function enviar(){
	var obj = document.forms['newAD'];
	obj.submit();
}

Calendar.setup({ 
	inputField     :    "dFechaCompra",     // id del campo de texto 
	ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
	button     :    "dFC"     // el id del botón que lanzará el calendario 
}); 
</script>
</html>
