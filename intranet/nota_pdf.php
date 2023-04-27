<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2010-08-08
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Footer() {
       // $image_file = "img/bg_bottom_releve.jpg";
        //$this->Image($image_file, 11, 241, 189, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$confecciono = $_GET['confecciono'];
        $this->SetY(-20);
        $this->SetFont('helvetica', 'C', 8);
		$this->Cell(0, 5, 'www.mardelplata-conicet.gob.ar - Tel: 54 223 495-2233/4466', 0, true, 'C', 0, '', 0, false, 'T', 'M');
		
        $this->SetFont('helvetica', 'C', 6);
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
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 048');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 40);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('Helvetica', 'C', 12);
//var_dump($pdf->getMargins());
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
$id_nota = $_GET['id_nota'];
$row_nota = $bd->consultar_nota($id_nota, $anio);

$anio = $row_nota['anio_numero_nota'];
$numero_nota = $row_nota['numero_nota'];
if ($numero_nota <10){
	$numero_nota = '0'. $numero_nota;
}
$anio_nota = $row_nota['anio_numero_nota'];
$destinatario = $row_nota['destinatario'];
$lugar_trabajo = $row_nota['lugar_trabajo'];
$row_firmante = $bd->consultar_firmante($row_nota['firmante']);
$row_firmante1 = $bd->consultar_firmante($row_nota['firmante1']);
$firmante = $row_firmante['titulo_apellido_nombre'];
$firmante1 = $row_firmante1['titulo_apellido_nombre'];
$referencia = $row_nota['referencia'];


//$pdf->Write(0, 'Mar del Plata, '. $_GET['fecha'] . ' de  ' . $_GET['fecha'] . ' de ' . $_GET['fecha'], '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'Mar del Plata, '. $dia . ' de ' . $meses[$mes] . ' de ' . $anio, '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Write(0, 'Nota nro. '. $numero_nota . '/' .$anio_nota, '', 0, 'L', true, 0, false, false, 0);

$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//$texto_imprimir  = "<p style=\"font-size:10pt;line-height:5px;text-indent: 40px;font-weight: bold;\">Referencia: ";
//$pdf->writeHTML($texto_imprimir.($referencia).'</p>' , true, false, false, false, '');
//$pdf->Write(0, 'Referencia: '. ($referencia), '', 0, 'R', true, 0, false, false, 0);


$html = '<p align="rigth"><b>Referencia: </b>';
$pdf->SetFont('Helvetica', 'C', 10);
$html2 = ($referencia) .'</p>';
$pdf->writeHTML($html.$html2 , true, false, true, false, '');

$pdf->SetFont('Helvetica', 'C', 10);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, ($destinatario), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ($lugar_trabajo), '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'U', 10);
$pdf->Write(0, 'S / D', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'C', 10);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('Helvetica', '', 10);

$q = 'SELECT * FROM nota WHERE id_nota ="' . $id_nota . '"';
$r = $bd->excecuteQuery($q);
while ($row_nota = mysqli_fetch_array($r)){	
 //$row_destinatario = $bd->consultar_destinatario($row_mesa_entrada['destinatario']);
 //$dmy = explode('-', $row_mesa_entrada['fecha']);
 $texto = nl2br($row_nota['texto'])??''; //htmlentities()
}
$Bd = NULL;
$pdf->SetMargins( 20, 40);
//var_dump($texto);
//$texto = str_replace("<br />","</p><p style=\"width:380px;margin-right:20px;text-align:justify;color:rgb( 0,0,0);font-size:10pt;text-indent: 40px;padding-top:20px;\">",$texto);

$texto = str_replace(".---","</ul>",$texto);
$texto = str_replace("---","<ul style='margin-top:-20px'>",$texto);
$texto = str_replace("[/negrita]","</b>",$texto);
$texto = str_replace("[negrita]","<b>",$texto);
//line-height:5px; ESTO VA ABAJO
//var_dump($texto);
$texto_imprimir = "<p style=\"width:380px;margin-right:20px;text-align:justify;color:rgb( 0,0,0);font-size:10pt;text-indent: 40px;\">$texto</p>";
//var_dump($texto_imprimir);
$pdf->writeHTML(($texto_imprimir), true, false, false, false, '');
$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);
//$pdf->writeHTML($texto, true, false, false, false, '');
$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0,'' , '', 0, 'R', true, 0, false, false, 0);

$row_nota = $bd->consultar_nota($id_nota, $anio);
$row_firmante = $bd->consultar_firmante($row_nota['firmante']);
$row_firmante1 = $bd->consultar_firmante($row_nota['firmante1']);
if ($row_nota['firma_digital'] == 1 ){		
	if(is_file($row_firmante['firma'])){
		$firma_digital = '<img src="'. $row_firmante['firma'] . '" width="150" height="75" border="0" style="float:left">';
		$pdf->writeHTML($firma_digital, true, false, false, false, '');
	}
	if(is_file($row_firmante1['firma'])){
		$iY = $pdf->GetY();
		$pdf->Image($row_firmante1['firma'],135,$iY - 25,50,25);
		//$firma_digital = '<img src="'. $row_firmante1['firma'] . '" width="150" height="75" border="0" style="float:right">';
		//$pdf->writeHTML($firma_digital, true, false, false, false, '');
	}
}

/*$linea = '<hr style="float:left;align:left;" width="200" size="1" noshade="noshade"/>';
$linea1 = '<hr style="float:right;align:right;" width="200" size="1" noshade="noshade"/>';
$pdf->writeHTML($linea, true, false, false, false, '');
$pdf->writeHTML($linea1, true, false, false, false, '');*/
$iY = $pdf->GetY();
if($firmante){
	$pdf->Line(16,$iY,80,$iY);
}
if($firmante1){
	$pdf->Line(130,$iY,194,$iY);
}
$bNewLine = ($firmante1)?false:true;
$pdf->Write(0, ($firmante), '', 0, 'L',$bNewLine , 0, false, false, 0);
if($firmante1){
	$pdf->Write(0, ($firmante1), '', 0, 'R', true, 0, false, false, 0);
}
$pdf->Write(0, ($row_firmante['cargo']), '', 0, 'l', $bNewLine, 0, false, false, 0);
if($firmante1){
	$pdf->Write(0, ($row_firmante1['cargo']), '', 0, 'R', true, 0, false, false, 0);
}
$pdf->Write(0, ($row_firmante['lugar']), '', 0, 'l', $bNewLine, 0, false, false, 0);
if($firmante1){
	$pdf->Write(0, ($row_firmante1['lugar']), '', 0, 'R', true, 0, false, false, 0);
}

//COPIA
if($row_nota['CC']){
	$pdf->Ln(15);
	$pdf->Write(0, "CC: " . ($row_nota['CC']), '', 0, 'L', true, 0, false, false, 0);
}
// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('nota.pdf', 'D');

//============================================================+
// END OF FILE                                                
//============================================================+
