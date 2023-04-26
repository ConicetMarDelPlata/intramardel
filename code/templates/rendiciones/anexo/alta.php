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
<script src="js/jquery.js" type="text/javascript"></script>
<script src="tabs/tabcontent.js" type="text/javascript"></script>
<script src="lightbox/jquery.lightbox_me.js" type="text/javascript"></script>
<script type="text/javascript" src="overlib421/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<link href="tabs/template6/tabcontent.css" rel="stylesheet" type="text/css" />
<script src="js/validaciones.js" type="text/javascript"></script>

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
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="{var_linkBack}" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">{var_sBack}</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar" style="height: 34px"><p><strong><img src="images/bullet20.gif" width="9" height="9" />Usuario: {var_User}</strong></p>
      <p>&nbsp;</p></td>
	<td align="left" valign="middle">
		<span class="TITULO">:: Abrir Anexo Donaci&oacute;n ::</span>
		<!--<a href="{var_anexo_open_close}"><img src="{var_path_open_close_anexo}" width="27" height="27" border="0" alt="Abrir Anexo Donaci&oacute;n"></a>-->
	</td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<!--BEGIN BLOCK PC-->
			<div class="divIcon" style="text-align:center">
				<a href="{var_linkPC}">
					<img src="{var_iconoPC}" class="imgIcon" vspace="4" border="0" />
				</a>
				<br />
				<a href="{var_linkPC}" class="tituloweb2Copia">{var_nombrePC}</a>
			</div>
		<!--END BLOCK PC-->
	</td>
	<!-- CONTENIDO PRINCIPAL -->
	
	<!-- LISTA ACTAS  -->
    <td width="722" valign="top" id="lista_actas">
		<form name="newAD" method="POST" action="abm_anexo_donacion.php?op=2">
			<table border="0" cellpadding="1" cellspacing="1" class="tabla_form">
				<tr>
					<td class="modo1" style="width:190px;">Nro Identificador</td>
					<td class="modo2"><input type="text" id="ad-id" name="iID" style="width:99%" value="{var_ad-id}"/></td>
				</tr>
				<tr>
					<td class="modo1">Tipo Rendici&oacute;n</td>
					<td class="modo2"><input type="text" id="ad-tr" name="sSubsidio" style="width:99%" value="{var_ad-tr}"/></td>
				</tr>
				<tr>
					<td class="modo1">Titular</td>
					<td class="modo2">
						<select name="iTitular" style="width:99%" onchange="cargaDatos(this.value);">
							<option value="0">---</option>
							<!--BEGIN BLOCK Titulares-->
							<option value="{var_ID-Tit}" {var_Sel-Tit}>{var_Desc-Tit}</option>
							<!--END BLOCK Titulares-->
						</select>
					</td>
				</tr>
				<tr>
					<td class="modo1">Unidad</td>
					<td class="modo2">
						<select name="iUE" style="width:99%" onchange="masInfo(this.value);">
							<option value="0">---</option>
							<!--BEGIN BLOCK UE-->
							<option value="{var_ID-UE}">{var_Desc-UE}</option>
							<!--END BLOCK UE-->
						</select>
					</td>
				</tr>
				<tr>
					<td class="modo1" id="lab-txt" style="display:none;">Laboratorio</td>
					<td class="modo1" id="lab" style="display:none;"><input type="text" name="lab"></td>
				</tr>
				<tr>
					<td class="modo1">Resoluciones de Otorgamiento</td>
					<td class="modo2">
						<textarea name="sRO" style="width:99%; height:200px"></textarea>
					</td>
				</tr>
				<tr>
					<td class="modo1" colspan="2" style="text-align:center; height:30px">M&aacute;s informaci&oacute;n</td>
				</tr>
			</table>
		</form>
		<!--BEGIN BLOCK DESC-->
			<div id="div-{var_ID}" style="display:none;margin-top:-18px">
				<table class="tabla_form" style="width:419px;">
					<tr>
						<td class="modo1" style="width:190px;">CUIT: </td><td class="modo2" style="color:#FFFFFF; text-align:left">{var_CUIT}</td>
					</tr>
					<tr>
						<td class="modo1">Domicilio: </td><td class="modo2" style="color:#FFFFFF; text-align:left">{var_Dom}</td>
					</tr>
					<tr>
						<td class="modo1">Referente: </td><td class="modo2" style="color:#FFFFFF; text-align:left">{var_Ref}</td>
					</tr>
					<tr>
						<td class="modo1">Director: </td><td class="modo2" style="color:#FFFFFF; text-align:left">{var_Dir}</td>
					</tr>
				</table>
			</div>
		<!--END BLOCK DESC-->
		<div style="margin-left:170px">
			<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="enviar();" alt="Grabar datos">
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
function cargaDatos(valor){
	var ad_id = $("#ad-id").val();
	var ad_tr = $("#ad-tr").val();
	window.open('abm_anexo_donacion.php?op=1&titid='+valor+'&adid='+ad_id+'&adtr='+ad_tr,'_self');
}
function masInfo(id){
	if(id != 0){
		if(lastID != 0){
			var obj = document.getElementById("div-"+lastID);
			obj.style.display = "none";
		}
		lastID = id;
		var obj = document.getElementById("div-"+id);
		var lab = document.getElementById("lab");
		var labtxt = document.getElementById("lab-txt");

		if(id == 11){
			lab.style.display = "";
			labtxt.style.display = "";
		}else{
			lab.style.display = "none";
			labtxt.style.display = "none";
		}

		obj.style.display = "";
		
	}else{
		var obj = document.getElementById("div-"+lastID);
		obj.style.display = "none";
		lastID = id;
	}
}

function enviar(){
	//Verifico datos
	if ((document.newAD.iID.value.trim() == "")) {
		alert("Por favor indique un identificador de anexo antes de guardar.");
		document.newAD.iID.focus();
		return (false);
	}
	else if (!isInteger(document.newAD.iID.value.trim())) {
		alert("El identificador de anexo debe ser un numero entero.");
		document.newAD.iID.focus();
		return (false);		
	}
	else if ((document.newAD.iTitular.value.trim() == "0")) {
		alert("Por favor indique un titular antes de guardar.");
		document.newAD.iTitular.focus();
		return (false);
	}		
	else if ((document.newAD.iUE.value.trim() == "0")) {
		alert("Por favor indique una unidad antes de guardar.");
		document.newAD.iUE.focus();
		return (false);
	}	
	else {
		document.forms['newAD'].submit();
	}
}
</script>
</html>
