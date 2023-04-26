<?php
include_once "seguridad_bd.php";
include_once "includes/functions.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

/*class MYPDF extends TCPDF {
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

    public function Header() {
    }
}*/

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sebastian Salerno');
$pdf->SetTitle('Cert. Retencion');
$pdf->SetSubject('CONICET Mar Del Plata');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
//$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 7, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// add a page
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'C', 10);

$bd = new Bd;
$bd->AbrirBd();

$numero_orden_pago = $_GET['numero_orden_pago'];
$anio_numero_orden_pago = $_GET['anio_numero_orden_pago'];


// EMISOR /////////////////////////////////////////////////////////////////////////////////////////////////////
$row_orden_pago = $bd->consultar_orden_pago($numero_orden_pago, $anio_numero_orden_pago);
$row_unidad = $bd->getUE($row_orden_pago['id_unidad_ejecutora']);
$row_proveedor = $bd->consultar_proveedor_por_id($row_orden_pago['proveedor']);

// Siempre debe ser Ines
//$row_firmante = $bd->consultar_firmante($row_orden_pago['firmante']);
$row_firmante = $bd->consultar_firmante(2);

$proveedor = $row_proveedor['razon_social'];
$cuit = $row_proveedor['cuit'];
$nro_iibb = $row_proveedor['nro_iibb'];
$iibb = $bd->getIIBBName((int)$row_proveedor['iibb']);
$condicion_iva = $bd->getCondicionIva((int)$row_proveedor['condicion_iva']);
$domicilio = $row_proveedor['domicilio'];
$vprovincia = $bd->consultar_provincia((int)$row_proveedor['provincia']);
$provincia = ($vprovincia['nombre']);

$fMonto 		= (float)$row_orden_pago['importe'];
$fCM_Porciento 	= (float)$row_orden_pago['cm'];
$fIVA			= (float)$row_orden_pago['iva'];
$fAlicuota		= (float)$row_orden_pago['alicuota'];

$fCM_Monto			= ($fMonto * $fCM_Porciento / 100);
$fBase_Imponible	= $fCM_Monto - (($fIVA * $fCM_Monto) / (100+$fIVA));
$fMonto_a_Retener	= $fAlicuota * $fBase_Imponible / 100;
$fMonto_a_Pagar		= $fMonto - $fMonto_a_Retener;

$style = array('dash' => '2', 'color' => array(0, 0, 0));
$hLine = 7;
$pdf->Write(0, ("Retención IIBB de la Pcia. de Bs. As"), '', 0, 'C', true, 0, false, false, 0);$pdf->Ln();$pdf->Write(0, ($row_unidad['nombre']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'CUIT: '.($row_unidad['cuit']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'I.V.A.: Exento', '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'Nro. IIBB: '. ($row_unidad['iibb']), '', 0, 'R', true, 0, false, false, 0);

$pdf->Write(0, ($proveedor), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ('CUIT: '.$cuit), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ('I.V.A.: '.$condicion_iva), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ('IIBB: '. $iibb), '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, ('Nro. IIBB: '. $nro_iibb), '', 0, 'L', true, 0, false, false, 0);

$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());

$pdf->Write($hLine, (convertir_fecha($row_orden_pago['fecha'])), '', 0, 'C', true, 0, false, false, 0);
$pdf->Write($hLine, "Ord. Pago: ".$row_orden_pago['numero_orden_pago']."/".$row_orden_pago['anio_numero_orden_pago'], '', 0, 'C', true, 0, false, false, 0);
$pdf->Write($hLine, 'Base Imponible: $ ' . (number_format($fBase_Imponible, 2, ',' , '.')), '', 0, 'C', true, 0, false, false, 0);
//$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY(),$style);
$pdf->Write($hLine, ('Alícuota: ' . number_format($fAlicuota, 2, ',' , '.')) . ' %', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write($hLine, 'Nro. Certificado: 0001-' . str_pad((int)$row_orden_pago['cert_ret'], 8, "0", STR_PAD_LEFT), '', 0, 'C', true, 0, false, false, 0);
$pdf->Write($hLine, ('Importe Retención: $ ' . number_format($fMonto_a_Retener, 2, ',' , '.')), '', 0, 'C', true, 0, false, false, 0);
$pdf->Write($hLine, "PESOS ".docenumeros(number_format($fMonto_a_Retener, 2, '.' , '')), '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0); 
$pdf->Image($row_firmante['firma'], 85, $pdf->GetY()-15, 50, 25);
$pdf->Write(0, '----------------------------------------', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Firma Autorizada', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, $row_firmante['titulo_apellido_nombre'], '', 0, 'C', true, 0, false, false, 0);

$confecciono = $_GET['confecciono'];
$pdf->SetY(-25);
$pdf->SetFont('helvetica', 'N', 6);
$pdf->Cell(0, 5, 'Confeccionada por: ' . ($confecciono), 0, false, 'C', 0, '', 0, false, 'T', 'M');
//$pdf->Cell(0, 6, $pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');		

//------------------------------------------------------------------------------------------------------
//Close and output PDF document
//$pdf->Output('recibo_retencion.pdf', 'D');
if (isset($_GET['toFile']) and ($_GET['toFile']=='F')) {
	$pdf->Output(__DIR__ . '/recibosRetencion/reciboRetencion'.$row_orden_pago['cert_ret'].'.pdf', 'F');			
} else {
	$pdf->Output(__DIR__ . '/reciboRetencion'.$row_orden_pago['cert_ret'].'.pdf');
}

//============================================================+
// END OF FILE                                                
//============================================================+
