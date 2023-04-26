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
		$fecha = explode('-', $_GET['fecha']);
		$anio = $fecha[2];
		
		$confecciono = urldecode($_GET['confecciono']);
        $this->SetY(-15);
        $this->SetFont('helvetica', 'N', 8);
		$this->Cell(0, 5, 'Confeccionada por: ' . ($confecciono) . ' - Orden de compra: ' . $_GET['numero_orden_compra'] . '/' . $anio, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 6, $this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');		
        //$this->Cell(0, 5, date("m/d/Y H\hi:s"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//var_dump($pdf->GetY());

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Conicet MDP');
$pdf->SetTitle('Orden de Compra');
$pdf->SetSubject('Orden de Compra');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('Helvetica', 'B', 12);

// add a page
$pdf->AddPage();
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('Helvetica', 'C', 10);

//--------------------------------------------------------------------------------------------------------------------  
$tbl_header = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0 10px;
}
tr {
   padding: 3px 0;
}

th {
   background-color: #CCCCCC;
   border: 1px solid #FFFFFF;
   color: #000000;
   font-family: Helvetica;
   padding-bottom: 4px;
   padding-left: 6px;
   padding-top: 5px;
   text-align: left;
   font-weight: bold;
}
td {
   background-color: #EEEEEE;
   border: 1px solid #FFFFFF;
   color: #000000;
   padding: 3px 7px 2px;
}
</style>
<table id="gallerytab" width="100%" cellspacing="0" cellpadding="7" border="0">
<tr>
       <th align="center" width="50"><font face="Arial, Helvetica, sans-serif">Item Nro.</font></th>
       <th width="300"><font face="Arial, Helvetica, sans-serif">Descripci&oacute;n del componente</font></th>
       <th align="center" width="80"><font face="Arial, Helvetica, sans-serif">Cantidad</font></th>
	   <th align="center" width="75"><font face="Arial, Helvetica, sans-serif">Unidad de medida</font></th>
       <th align="center" width="75"><font face="Arial, Helvetica, sans-serif">Precio</font></th>
       <th align="center" width="80"><font face="Arial, Helvetica, sans-serif">Subtotal</font></th>
     </tr>'; 
//--------------------------------------------------------------------------------------------------------------------  

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
$numero_orden_compra = $_GET['numero_orden_compra'];
/*if ($numero_orden_compra < 10){
	$numero_orden_compra = '0'.$numero_orden_compra;
}*/
$anio_numero_orden_compra = $_GET['anio_numero_orden_compra'];
$row_orden_compra = $bd->consultar_numero_orden_compra($numero_orden_compra,$anio_numero_orden_compra);

//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Write(0, 'Mar del Plata, '. $dia . ' de ' . $meses[$mes] . ' de ' . $anio, '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Write(0, 'ORDEN DE COMPRA '. $numero_orden_compra . '/' .$anio_numero_orden_compra, '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Write(0, '', '', 0, '', true, 0, false, false, 0);
//------------------------------------------------------------------------------------------------------
$id_contacto = $_GET['contacto'];
$row_contacto = $bd->consultar_usuario($id_contacto);
$contacto = $row_contacto['apellido'] . ', ' . $row_contacto['nombre'];
$email = $row_contacto['email'];

$id_proveedor = $_GET['proveedor'];
$row_proveedor = $bd->consultar_proveedor_por_id($id_proveedor);
$condicion_iva = $bd->getCondicionIva($row_proveedor['condicion_iva']);
//El emisor se cambia por unidad ejecutora
$row_unidad = $bd->getUE((int)$row_orden_compra['id_unidad_ejecutora']);

$row_provincia = $bd->consultar_provincia($row_proveedor['provincia']);
$provincia = $row_provincia['nombre'];

$tbl_emisor_adjudicatario = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   padding-bottom: 2px;
   padding-left: 5px;
   padding-top: 2px;
}
tr {
   padding-bottom: 5px;
   padding-left: 0px;
   padding-top: 2px;
}
.columna1 {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 70px;
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
   padding-top: 2px;
   text-align: left;
}
td {
   border: 1px solid #FFFFFF;
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   width: 195px;
}
</style>
<table width="100%" border="0" align="left" margin="0" width="100%">
  <Tr>
    <th background="#EEEEEE" width="290">Datos del emisor</th>
    <td width="40">&nbsp;</td>
    <th background="#EEEEEE" colspan="2" width="290">Datos del Adjudicatario</th>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Se&ntilde;ores:</strong></td>
    <td>'.($row_unidad['nombre']).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Se&ntilde;ores:</strong></td>
    <td>'.$row_proveedor['razon_social'].'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>CUIT:</strong></td>
    <td>'.($row_unidad['cuit']).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>CUIT:</strong></td>
    <td>'.($row_proveedor['cuit']).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>C. IVA:</strong></td>
    <td>Exento</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>C. IVA:</strong></td>
    <td>'.($condicion_iva).'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Domicilio:</strong></td>
    <td>'.($row_unidad['domicilio']).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Domicilio:</strong></td>
    <td>'.$row_proveedor['domicilio'].'</td>
  </Tr>
  <Tr>
    <td class="columna1"><strong>Provincia:</strong></td>
    <td>Buenos Aires</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Provincia:</strong></td>
    <td>'.$provincia.'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Contacto:</strong></td>
    <td>'.($contacto).'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Contacto:</strong></td>
    <td>'.$row_proveedor['contacto'].'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Telefono:</strong></td>
    <td>'.$row_unidad['telefono'].'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Telefono:</strong></td>
    <td>'.$row_proveedor['telefono'].'</td>
  </Tr>
  <Tr> 
    <td class="columna1"><strong>Email:</strong></td>
    <td>'.$email.'</td>
    <td class="columna2">&nbsp;</td>
    <td class="columna1"><strong>Email:</strong></td>
    <td>'.$row_proveedor['email'].'</td>
  </Tr>
</table>'; 

$pdf->writeHTML(($tbl_emisor_adjudicatario), true, false, false, false, '');

//------------------------------------------------------------------------------------------------------
$row_procedimiento = $bd->consultar_procedimiento_seleccion($row_orden_compra['procedimiento_seleccion']);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$html = '<p align="left"><b>Procedimiento de compra: </b>';
$pdf->SetFont('Helvetica', 'C', 9);
$html2 = ( $row_procedimiento['descripcion']) .'</p>';
$pdf->writeHTML($html.$html2 , true, false, true, false, '');
//$pdf->Write(0, 'Objeto de compra: '. ($row_orden_compra['objeto']), '', 0, 'L', true, 0, false, false, 0);
$html = '<p align="left"><b>Objeto de compra: </b>';
$pdf->SetFont('Helvetica', 'C', 9);
$html2 = ( $row_orden_compra['objeto']) .'</p>';
$pdf->writeHTML($html.$html2 , true, false, true, false, '');
//$pdf->Write(0, 'Presupuesto de referencia: '. ($row_orden_compra['referencia']), '', 0, 'L', true, 0, false, false, 0);
$html = '<p align="left"><b>Presupuesto de referencia: </b>';
$pdf->SetFont('Helvetica', 'C', 9);
$html2 = ( $row_orden_compra['referencia']) .'</p>';
$pdf->writeHTML($html.$html2 , true, false, true, false, '');
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 9);
$html = '<p align="left"><b>Detalle de la compra:</b>';
$pdf->writeHTML($html , true, false, true, false, '');
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 12);

$q = 'SELECT * FROM orden_compra oc INNER JOIN moneda m ON oc.signo_moneda = m.id_moneda WHERE numero_orden_compra ="' . $numero_orden_compra . '" AND anio_numero_orden_compra = '.$anio_numero_orden_compra;
$r = $bd->excecuteQuery($q);
$i = 1;
$total = 0;
$tbl = "";
while ($row_orden_compra = mysqli_fetch_array($r)){
	 if ($row_orden_compra['numero_item'] != NULL){
		$signo = $row_orden_compra['signo'];
		//htmlentities($row_orden_compra['descripcion_componente'], ENT_COMPAT,'ISO-8859-1', true). '/'. $dmy[0] .'</td>
		 $tbl.='<tr>
			  <td align="center">'.$i.'</td>			  
			  <td>'.$row_orden_compra['descripcion_componente'] .'</td>
			  <td align="center">'.$row_orden_compra['cantidad'].'</td>
			  <td align="center">'.$row_orden_compra['unidad'].'</td>		  
			  <td align="center"> '.$signo.' '.number_format($row_orden_compra['precio_unitario'], 2, ',' , '.').'</td>
			  <td align="center"> '.$signo.' '.number_format($row_orden_compra['subtotal'], 2, ',' , '.').'</td>
			 </tr>
			';
 	 }
	 $total = $total + $row_orden_compra['subtotal'];
	 $i++;
}

$Bd = NULL;
$pdf->SetFont('Helvetica', 'B', 14);
$tbl.='<tr>
	<td align="rigth" colspan="6"><strong>Total: '.$signo.' '.number_format($total, 2, ',' , '.').'</strong></td>
</tr>
';
$tbl_footer = '</table>';
$pdf->SetFont('Helvetica', '', 9);
//echo $tbl_header . $tbl . $tbl_footer;exit;
$pdf->writeHTML(($tbl_header . $tbl . $tbl_footer), true, false, true, false, '');
//$pdf->Write(0, ('La provisi&oacute;n / reparaci&oacute;n comprender&aacute; en l&iacute;neas generales, la obligaci&oacute;n del adjudicatario, de cumplir con la misma en tiempo y en forma, en la calidad del material o del servicio prestado, as&iacute; como tambi&eacute;n de los gastos de transporte de los insumos a proveer, en caso de ser necesario.'), '', 0, 'J', true, 0, false, false, 0);
$texto_imprimir = "<p style=\"width:320px;margin-right:25px;margin-left:25px;text-align:center;color:rgb( 0,0,0);font-size:10pt; border: 1px solid #333;\"> La provisi&oacute;n/reparaci&oacute;n comprender&aacute; en l&iacute;neas generales, la obligaci&oacute;n del adjudicatario, de cumplir con la misma en tiempo y en forma, en la calidad del material o del servicio prestado, as&iacute; como tambi&eacute;n de los gastos de transporte de los insumos a proveer, en caso de ser necesario.</p>";
$pdf->writeHTML(($texto_imprimir), true, false, false, false, '');

$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->writeHTML('Condiciones de pago', true, false, false, false, '');
//$pdf->Write(0, 'Condiciones de pago', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 9);
//$pdf->Write(0, ('Diez d&iacute;as h&aacute;biles posteriores a la entrega de la correspondiente factura original, debidamente firmada por el Director / Investigador, en la oficina del CCT CONICET Mar del Plata sito en calle San Luis 1458 3er Piso. Horario de pago a proveedores: martes y jueves de 9 a 13 hs.'), '', 0, 'J', true, 0, false, false, 0);
$texto_imprimir = "<p style=\"width:380px;margin-right:20px;text-align:justify;color:rgb( 0,0,0);font-size:10pt;text-indent: 0px;\">Diez d&iacute;as h&aacute;biles posteriores a la entrega de la correspondiente factura original, debidamente firmada por el Director / Investigador, en la oficina del CCT CONICET Mar del Plata sito en calle Moreno 3527 3er Piso. Horario de pago a proveedores: martes y jueves de 9 a 13 hs.</p>";
$pdf->writeHTML(($texto_imprimir), true, false, false, false, '');

$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->writeHTML('Datos Bancarios', true, false, false, false, '');
$pdf->SetFont('Helvetica', '', 10);
$pdf->writeHTML(('Se deber&aacute;n entregar los siguientes datos:'), true, false, false, false, '');
$pdf->writeHTML(('a. Raz&oacute;n Social'), true, false, false, false, '');
$pdf->writeHTML(('b. N&uacute;mero de CUIT'), true, false, false, false, '');
$pdf->writeHTML(('c. Banco'), true, false, false, false, '');
$pdf->writeHTML(('d. Tipo de cuenta'), true, false, false, false, '');
$pdf->writeHTML(('e. N&uacute;mero de cuenta'), true, false, false, false, '');
$pdf->writeHTML(('f. CBU'), true, false, false, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
//$pdf->AddPage();
//$pdf->SetFont('Helvetica', 'B', 10);
//$pdf->AddPage(); //Salto de pagina para firma
//$pdf->writeHTML('Importante', true, false, false, false, '');
//$pdf->Write(0, 'Importante', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', '', 9);
//$pdf->Write(0, ('Es obligaci&oacute;n del proveedor contratado conformar la facturaci&oacute;n con las firmas correspondientes, como as&iacute; tambi&eacute;n entregar la misma personalmente o por personal administrativo de su firma, en la oficina del CCT CONICET Mar del Plata.'), '', 0, 'J', true, 0, false, false, 0);
$texto_imprimir = "<h3>Importante</h3><p style=\"width:380px;margin-right:20px;text-align:justify;color:rgb( 0,0,0);font-size:9pt;text-indent: 0px;\">Es obligaci&oacute;n del proveedor contratado conformar la factura con las firmas correspondientes, como as&iacute; tambi&eacute;n entregar la misma personalmente o por personal administrativo de su firma, en la oficina del CCT CONICET Mar del Plata.</p>";
$pdf->writeHTML(($texto_imprimir), true, false, false, false, '');

//$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
//$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);
//$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);
//$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);

$row_orden_compra = $bd->consultar_numero_orden_compra($numero_orden_compra,$anio_numero_orden_compra);

$row_firmante = $bd->consultar_firmante($row_orden_compra['firmante']);

//$pdf->SetY(-10);
if ($row_orden_compra['firma_digital'] == 1 ){
	if ($row_firmante['firma']!=NULL){
		$firma_digital = '<img src="'. $row_firmante['firma'] . '" width="150" height="75" border="0">';
		$pdf->writeHTML($firma_digital, true, false, false, false, '');
	}
}else{
	$firma_digital = '<img src="SinFirma.jpg" width="150" height="75" border="0">';
	$pdf->writeHTML($firma_digital, true, false, false, false, '');	
}
$linea = '<hr align="left" width="200" size="1" noshade="noshade"/>';
$pdf->writeHTML($linea, true, false, false, false, '');


$pdf->Write(0, ($row_firmante['titulo_apellido_nombre']), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ($row_firmante['cargo']), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ($row_firmante['lugar']), '', 0, 'L', true, 0, false, false, 0);
//$pdf->Write(0, $xx, '', 0, 'L', true, 0, false, false, 0);

// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('orden_compra-'.$numero_orden_compra.'.pdf');

//============================================================+
// END OF FILE                                                
//============================================================+
