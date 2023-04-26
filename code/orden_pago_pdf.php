<?php

include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
	      //$image_file = PDF_HEADER_LOGO; No funciona en conicet
        $this->Image($image_file, 17, 10, 175, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
	
    public function Footer() {
       // $image_file = "img/bg_bottom_releve.jpg";
        //$this->Image($image_file, 11, 241, 189, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$confecciono = $_GET['confecciono'];
        $this->SetY(-15);
        $this->SetFont('helvetica', 'N', 6);
		$this->Cell(0, 5, 'Confeccionada por: ' . ($confecciono), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 6, $this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');		
        //$this->Cell(0, 5, date("m/d/Y H\hi:s"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Impresión Orden de Pago');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('Helvetica', 'B', 10);

// add a page
$pdf->AddPage();
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('Helvetica', 'C', 10);


$fecha = explode('-', $_GET['fecha']);
$anio = $fecha[2];
$mes = $fecha[1];
if ($mes < 10){
	$mes = substr($mes, 1,1);
}
$dia = $fecha[0];
$meses = array(
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre",							
);

$bd = new Bd;
$bd->AbrirBd();
$numero_orden_pago = $_GET['numero_orden_pago'];
/*if ($numero_orden_pago){
	$numero_orden_pago = '0'.$numero_orden_pago;
}*/
$anio_numero_orden_pago = $_GET['anio_numero_orden_pago'];

$row_orden_pago = $bd->consultar_orden_pago($numero_orden_pago, $anio_numero_orden_pago);
$row_cuenta = $bd->consultar_cta_unidad_ejecutora($row_orden_pago['cuenta']);
$cuenta = $row_cuenta['nro_cuenta'];

// EMISOR /////////////////////////////////////////////////////////////////////////////////////////////////////
//Nota Vani, el emisor pasa a ser datos de la Unidad
$row_unidad = $bd->consultar_unidad_ejecutora($row_orden_pago['id_unidad_ejecutora']);

//$pdf->Write(0, 'Mar del Plata, '. $_GET['fecha'] . ' de  ' . $_GET['fecha'] . ' de ' . $_GET['fecha'], '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'Mar del Plata, '. $dia . ' de ' . $meses[$mes] . ' de ' . $anio, '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Write(0, 'ORDEN DE PAGO '. $numero_orden_pago . '/' .$anio_numero_orden_pago, '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//------------------------------------------------------------------------------------------------------
//nota Vani: estos datos de abajo pareciera no utilizarse
//$id_contacto = $_GET['contacto'];
//$row_contacto = $bd->consultar_usuario($id_contacto);
//$contacto = $row_contacto['apellido'] . ', ' . $row_contacto['nombre'];
//$email = $row_contacto['email'];

//$pdf->Write(0, 'provee '. $_GET['proveedor'] . '/' .$anio_numero_orden_compra, '', 0, 'C', true, 0, false, false, 0);
$id_proveedor = $_GET['proveedor'];
$row_proveedor = $bd->consultar_proveedor_por_id($id_proveedor);
$condicion_iva = $bd->getCondicionIva((int)$row_proveedor['condicion_iva']);
$vprovincia = $bd->consultar_provincia((int)$row_proveedor['provincia']);
$provincia = ($vprovincia['nombre']);

$tbl_emisor_adjudicatario = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0px;
   padding-bottom: 4px;
   padding-left: 5px;
   padding-top: 4px;
}
tr {
   padding-bottom: 2px;
   padding-left: 0px;
   padding-top: 2px;
}
.columna1 {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 80px;
}
.columna2 {
   width: 80px;
}

th {
   background-color: #CCCCCC;
   color: #000000;
   font-family: Helvetica;
   font-weight: bold;
   padding-bottom: 2px;
   padding-left: 4px;
   padding-top: 2px;
   text-align: left;
}
td {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   font-size: 11px;
   width: 170px;
}
</style>
<table border="0" align="left">
  <Tr> 
    <th background="#EEEEEE" colspan="2" width="290">Datos del emisor</th>
    <td width="40">&nbsp;</td>
    <th background="#EEEEEE" colspan="2" width="290">Datos del Beneficiario</th>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>R. Social:</strong></td>
    <td>'.($row_unidad['nombre']).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>R. Social:</strong></td>
    <td>'.($row_proveedor['razon_social']).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>CUIT/IIBB:</strong></td>
    <td>'.$row_unidad['cuit'].'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>CUIT:</strong></td>
    <td>'.$row_proveedor['cuit'].'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>C. IVA:</strong></td>
    <td>Exento</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>C. IVA:</strong></td>
    <td>'.$condicion_iva.'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Cuenta</strong></td>
    <td>'.$cuenta.'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>IIBB:</strong></td>
    <td>'.$bd->getIIBBName($row_proveedor['iibb']).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>&nbsp;</strong></td>
    <td>&nbsp;</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Nro. IIBB:</strong></td>
    <td>'.$row_proveedor['nro_iibb'].'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong></strong></td>
    <td></td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Provincia:</strong></td>
    <td>'.$provincia.'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong></strong></td>
    <td></td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Email:</strong></td>
    <td>'.$row_proveedor['email'].'</td>
  </Tr>
</table>'; 

$pdf->writeHTML($tbl_emisor_adjudicatario, true, false, false, false, '');
$row_banco = $bd->consultar_banco(($row_orden_pago['usa_banco']==1)?$row_proveedor['banco1']:$row_proveedor['banco2']);
$banco = $row_banco['nombre'];

if($row_orden_pago['usa_banco']==1){
	$titular = $row_proveedor['titular_cuenta1'];
}else{
	if ($row_proveedor['titular_cuenta2']){
		$titular = $row_proveedor['titular_cuenta2'];
	}else{
		$titular = $row_proveedor['razon_social'];
	}
}

if(($row_orden_pago['usa_banco']==1)){
	$iCuit = $row_proveedor['cuit_cuenta1'];
}else{
	if ($row_orden_pago['usa_banco']==2){
		$iCuit = $row_proveedor['cuit_cuenta2'];
	}else{
		$iCuit = $row_proveedor['cuit'];
	}
}

switch (($row_orden_pago['usa_banco']==1)?$row_proveedor['tipo_cuenta1']:$row_proveedor['tipo_cuenta2']){
	case 0:
		$tipo_cuenta1 = "----";	
		break;
	case 1:
		$tipo_cuenta1 = "Caja Ahorro en pesos";
		break;		
	case 2:
		$tipo_cuenta1 = "Cuenta Corriente en pesos";
		break;
	case 3:
		$tipo_cuenta1 = "Cuenta Unica en pesos";	
		break;
}

$sCBU = ($row_orden_pago['usa_banco']==1)?$row_proveedor['cbu1']:$row_proveedor['cbu2'];

$nro_cuenta = ($row_orden_pago['usa_banco']==1)?$row_proveedor['numero_cuenta1']:$row_proveedor['numero_cuenta2'];

$fMonto 		= (float)$row_orden_pago['importe'];
$fCM_Porciento 	= (float)$row_orden_pago['cm'];
$fIVA			= (float)$row_orden_pago['iva'];
$fAlicuota		= (float)$row_orden_pago['alicuota'];

$fCM_Monto	= $fMonto * $fCM_Porciento / 100;
$fBase_Imponible	= $fCM_Monto - (($fIVA * $fCM_Monto) / (100+$fIVA));
$fMonto_a_Retener	= $fAlicuota * $fBase_Imponible / 100;
$fMonto_a_Pagar		= $fMonto - $fMonto_a_Retener;

$tbl_retencion = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0px;
   padding-bottom: 4px;
   padding-left: 5px;
   padding-top: 4px;
}
tr {
   padding-bottom: 2px;
   padding-left: 0px;
   padding-top: 2px;
}
.columna1 {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 110px;
   /*text-align:right;*/
}
.columna2 {
   width: 70px;
}

th {
   background-color: #CCCCCC;
   color: #000000;
   font-family: Helvetica;
   font-weight: bold;
   padding-bottom: 2px;
   padding-left: 4px;
   padding-top: 2px;
   text-align: center;
}
td {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   font-size: 11px;
   width: 150px;
}
</style>
<table border="0" align="left">
  <Tr> 
    <th background="#EEEEEE" colspan="4" width="620">Detalles de pago</th>
  </Tr>
  <Tr> 
    <td colspan="4">&nbsp;</td>
  </Tr>
  <Tr> 
    <th background="#EEEEEE" colspan="2" width="290">Datos Comprobante</th>
    <td width="40">&nbsp;</td>
    <th background="#EEEEEE" colspan="2" width="290">Datos Bancarios</th>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Factura:</strong></td>
    <td>'.( $row_orden_pago['factura']).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Banco:</strong></td>
    <td>'.($banco).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Objeto:</strong></td>
    <td>'.( $row_orden_pago['objeto']) .'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Titular:</strong></td>
    <td>'.($titular).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Asig. de Rend.:</strong></td>
    <td>'.( $row_orden_pago['asignacion_rendicion']) .'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>CUIT/CUIL:</strong></td>
    <td>'.$iCuit.'</td>
  </Tr>
</table>';
$pdf->writeHTML($tbl_retencion, true, false, false, false, '');
$pdf->Line(17,$pdf->GetY()-3,98,$pdf->GetY()-3);
$pdf->Line(17,$pdf->GetY()-2,98,$pdf->GetY()-2);

$tbl_retencion = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0px;
   padding-bottom: 4px;
   padding-left: 5px;
   padding-top: 4px;
}
tr {
   padding-bottom: 2px;
   padding-left: 0px;
   padding-top: 2px;
}
.columna1 {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 110px;
   /*text-align:right;*/
}
.columna2 {
   width: 70px;
}

th {
   background-color: #CCCCCC;
   color: #000000;
   font-family: Helvetica;
   font-weight: bold;
   padding-bottom: 2px;
   padding-left: 4px;
   padding-top: 2px;
   text-align: center;
}
td {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   font-size: 11px;
   width: 150px;
}
</style>
<table>
  <Tr> 
    <td class="columna1"><strong>Monto:</strong></td>
    <td>'.$row_orden_pago['signo_moneda'].' '.(number_format($fMonto, 2, ',' , '.')).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>T. Cuenta:</strong></td>
    <td>'.($tipo_cuenta1).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>C. Multilateral:</strong></td>';
	if((int)$fCM_Porciento < 100){
		$tbl_retencion .='<td>'.$row_orden_pago['signo_moneda'].' '.(number_format($fCM_Monto, 2, ',' , '.')).'</td>';
	}else{
		$tbl_retencion .='<td>'.$row_orden_pago['signo_moneda'].' 0.00</td>';
	}
	
    $tbl_retencion .='<td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Nro. Cuenta:</strong></td>
    <td>'.$nro_cuenta.'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>IVA:</strong></td>
    <td>'.(number_format($fIVA, 2, ',' , '.')).'%</td>
	<td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Sucursal:</strong></td>
    <td>'.(substr($sCBU,3,4)).'</td>
    <td></td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Base imponible:</strong></td>
    <td>'.$row_orden_pago['signo_moneda'].' '.(number_format($fBase_Imponible, 2, ',' , '.')).'</td>
	<td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>CBU:</strong></td>
    <td>'.$sCBU.'</td>
    <td></td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Al&iacute;cuota de Ret.:</strong></td>
    <td class="columna2">'.(number_format($fAlicuota, 2, ',' , '.')).'%</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong></strong></td>
  </Tr>
</table>';
$pdf->writeHTML($tbl_retencion, true, false, false, false, '');
$pdf->Line(17,$pdf->GetY()-3,98,$pdf->GetY()-3);
$pdf->Line(17,$pdf->GetY()-2,98,$pdf->GetY()-2);
$tbl_retencion = '
<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0px;
   padding-bottom: 4px;
   padding-left: 5px;
   padding-top: 4px;
}
tr {
   padding-bottom: 2px;
   padding-left: 0px;
   padding-top: 2px;
}
.columna1 {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 110px;
   /*text-align:right;*/
}
.columna2 {
   width: 140px;
}

th {
   background-color: #CCCCCC;
   color: #000000;
   font-family: Helvetica;
   font-weight: bold;
   padding-bottom: 2px;
   padding-left: 4px;
   padding-top: 2px;
   text-align: center;
}
td {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   font-size: 11px;
   width: 150px;
}
</style>
<table>
  <Tr> 
    <td class="columna1"><strong>A Pagar</strong></td>
    <td class="columna2"><strong>'.$row_orden_pago['signo_moneda'].' '.(number_format($fMonto_a_Pagar, 2, ',' , '.')).'</strong></td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>&nbsp;</strong></td>
    <td class="columna2">&nbsp;</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>A Retener:</strong></td>
    <td class="columna2"><strong>'.$row_orden_pago['signo_moneda'].' '.(number_format($fMonto_a_Retener, 2, ',' , '.')).'</strong></td>
    <td class="columna2">&nbsp;</td>
	<td class="columna1">&nbsp;</td>
    <td class="columna2">&nbsp;</td>
  </Tr>
</table>';
$pdf->writeHTML($tbl_retencion, true, false, false, false, '');

// $pdf->SetFont('Helvetica', 'B', 12);
// $pdf->Cell(130,7, 'Total a retener:', 0, 0, 'R', false, '', false, false, 'R', 'M');
// $pdf->SetFont('Helvetica', 'C', 12);
// $pdf->Cell(45,7, '$ '.(number_format($fMonto_a_Retener, 2, ',' , '.')), 0, 1, 'R', false, '', false, false, 'R', 'M');

// $pdf->SetFont('Helvetica', 'B', 12);
// $pdf->Cell(130,7, 'Total a Pagar:', 0, 0, 'R', false, '', false, false, 'R', 'M');
// $pdf->SetFont('Helvetica', 'C', 12);
// $pdf->Cell(45,7, '$ '.(number_format($fMonto_a_Pagar, 2, ',' , '.')), 0, 1, 'R', false, '', false, false, 'R', 'M');

//$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'C', 8);
$html = '<p align="left"><b>&nbsp;&nbsp;&nbsp;&nbsp;Forma de Pago: </b>'. $row_orden_pago['forma_pago_descripcion'].'</p>';
$pdf->writeHTML($html , true, false, true, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

$aviso_p_a = "";
if ($row_orden_pago['no_enviar_aviso_pago_adicional'] == 1)
	$aviso_p_a = "No enviar";
else
	$aviso_p_a = "Enviar a ".$row_orden_pago['titular_aviso_pago_nombre']." (".$row_orden_pago['titular_aviso_pago_email'].")";

$html = '<p align="left"><b>&nbsp;&nbsp;&nbsp;&nbsp;Aviso de pago adicional: </b>'. $aviso_p_a.'</p>';
$pdf->writeHTML($html , true, false, true, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

$html = '<p align="left"><b>&nbsp;&nbsp;&nbsp;&nbsp;Aclaraciones: </b>';
if ($row_orden_pago['aclaraciones'] == "" or is_null($row_orden_pago['aclaraciones']))
	$aclaraciones = "--";
else
	$aclaraciones = $row_orden_pago['aclaraciones'];
$html2 = ( $aclaraciones) .'</p>';
$pdf->writeHTML($html.$html2 , true, false, true, false, '');
$Bd = NULL;

$row_orden_pago = $bd->consultar_orden_pago($numero_orden_pago, $anio_numero_orden_pago);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$row_firmante = $bd->consultar_firmante($row_orden_pago['firmante']);
$row_firmante2 = $bd->consultar_firmante($row_orden_pago['firmante2']);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
/* $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
 */
$linea = '<hr align="left" width="200" size="1" noshade="noshade"/>';
//$pdf->writeHTML($linea, true, false, false, false, '');
//$pdf->Write(0, '----------------------------------------', '', 0, 'L', true, 0, false, false, 0);
//$pdf->Write(0, ($row_firmante['titulo_apellido_nombre']), '', 0, 'L', true, 0, false, false, 0);
//$pdf->Write(0, ($row_firmante['cargo']), '', 0, 'L', true, 0, false, false, 0);
//$pdf->Write(0, ($row_firmante['lugar']), '', 0, 'L', true, 0, false, false, 0);



$iY = $pdf->GetY();
$pdf->Line(16,$iY,80,$iY);
$pdf->Line(130,$iY,194,$iY);
$pdf->Write(0, ($row_firmante['titulo_apellido_nombre']), '', 0, 'L',false , 0, false, false, 0);
$pdf->Write(0, ($row_firmante2['titulo_apellido_nombre']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, ($row_firmante['cargo']), '', 0, 'l', false, 0, false, false, 0);
$pdf->Write(0, ($row_firmante2['cargo']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, ($row_firmante['lugar']), '', 0, 'l', false, 0, false, false, 0);
$pdf->Write(0, ($row_firmante2['lugar']), '', 0, 'R', true, 0, false, false, 0);


// -----------------------------------------------------------------------------

//Close and output PDF document
//$pdf->Output('orden_pago.pdf', 'D');
$pdf->Output('orden_pago'.$numero_orden_pago.'-'.$anio_numero_orden_pago.'.pdf');

//============================================================+
// END OF FILE                                                
//============================================================+
