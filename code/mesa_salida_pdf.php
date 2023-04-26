<?php

include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

class ConPies extends TCPDF {
    
	public function Footer() {
		/* establecemos el color del texto */
		$this->SetTextColor(0,0,300);
		/* insertamos numero de pagina y total de paginas*/
		$this->Cell(0, 10, 'IMPORTANTE: Una vez recepcionada la documentación, enviar esta planilla con sello y firma de la recepción a la siguiente dirección: Moreno 3527, Piso 3 (7600) Mar del Plata, Provincia de Bs As', 0, false, 'C', 0, '', 1, false, 'L', 'M'); 
		$this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage(). ' de '. $this-> getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetDrawColor(255,0,0);
		/* dibujamos una linea roja delimitadora del pie de página */
		$this->Line(15,282,195,282);
	}
}

$pdf = new ConPies();

// create new PDF document

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Mesa de Salida');

// set default header data
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
$pdf->SetAutoPageBreak(TRUE, 20); //PDF_MARGIN_BOTTOM

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 14);

// add a page
$pdf->AddPage();
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Mesa de Salida', '', 0, 'C', true, 0, false, false, 0);

$numero_remito = $_GET["numero_remito"];
$anio = $_GET["anio"];
$fecha = $_GET["fecha"];
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Documentación enviada a CONICET.' , '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0,' Remito Número: ' . $numero_remito . ' de fecha: '. $fecha,'', 0, 'C', true, 0 , false, false, 0);

$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 10);

// -----------------------------------------------------------------------------

// NON-BREAKING TABLE (nobr="true")

//-------------------------------------------------------------------------------------------------------------------- 
 $tbl_header = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0 10px;
   align: center;
}
tr {
   padding: 3px 0;
}

th {
   background-color: #CCCCCC;
   border: 1px solid #FFFFFF;
   color: #333333;
   font-family: Helvetica;
   padding-bottom: 4px;
   padding-left: 6px;
   padding-top: 5px;
   text-align: left;
}
td {
   background-color: #EEEEEE;
   border: 1px solid #FFFFFF;
   color: #369;
   padding: 3px 7px 2px;
}
</style>
<table id="gallerytab" width="100%" cellspacing="0" cellpadding="7" border="0">
<tr>
       <th width="50"><font face="Arial, Helvetica, sans-serif">Num. Orden</font></th>
       <th width="60"><font face="Arial, Helvetica, sans-serif">Num. Tramite</font></th>
       <th width="100"><font face="Arial, Helvetica, sans-serif">Remitente</font></th>
       <th width="165"><font face="Arial, Helvetica, sans-serif">Documento</font></th>
       <th width="170"><font face="Arial, Helvetica, sans-serif">Destinatario</font></th>
       <th width="55"><font face="Arial, Helvetica, sans-serif">Copias</font></th>
       <th width="40"><font face="Arial, Helvetica, sans-serif">C/H</font></th>
     </tr>'; 
//--------------------------------------------------------------------------------------------------------------------  

$bd = new Bd;
$bd->AbrirBd();
$q = 'SELECT * FROM mesa_salida	WHERE numero_remito ="' . $numero_remito . '"'. ' AND anio_numero_tramite = '.$anio.' ORDER By numero_orden';
$r = $bd->excecuteQuery($q);
$tbl = '';

while ($row_mesa_salida = mysqli_fetch_array($r)){
	$row_destinatario = $bd->consultar_destinatario($row_mesa_salida['destinatario']);
  $tbl.='<tr>
	  <td align="center">'.$row_mesa_salida['numero_orden'].'</td>
	  <td>'.$row_mesa_salida['numero_tramite']. '</td>
	  <td>'.$row_mesa_salida['remitente']. '</td>
	  <td>'.$row_mesa_salida['documento'].'</td>
	  <td>'.$row_destinatario['descripcion'].'</td>
	  <td align="center">'.$row_mesa_salida['copias'].'</td>
	  <td align="center">'.$row_mesa_salida['cantidad_hojas'].'</td>
	  </tr>';
}
$row_remito = $bd->consultar_mesa_salida($numero_remito, 0, $anio);
$row_firmante = $bd->consultar_firmante($row_remito['firmante']);
$firmante = $row_firmante['titulo_apellido_nombre'];

$Bd = NULL;
$tbl_footer = '</table>';
$pdf->writeHTML($tbl_header . $tbl . $tbl_footer, true, false, false, false, '');
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

if($pdf->getY() > 180){
	$pdf->AddPage();
}

$tbl_header1 = '<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   margin: 0 10px;
   align: center;
}
tr {
   padding: 3px 0;
}

th {
   background-color: #CCCCCC;
   border: 1px solid #FFFFFF;
   color: #333333;
   font-family: trebuchet MS;
   padding-bottom: 4px;
   padding-left: 6px;
   padding-top: 5px;
   text-align: left;
}

</style>
<table id="gallerytab" width="100%" cellspacing="0" cellpadding="3" border="0">
<tr>
	<th><font face="Arial, Helvetica, sans-serif">Firma<p>'.utf8_encode($firmante).'</p><p>'.utf8_encode($row_firmante['cargo']).'</p><p>'.utf8_encode($row_firmante['lugar']).'</p></font></th>
    <th><font face="Arial, Helvetica, sans-serif">Firmante<p></p></font></th>
    <th><font face="Arial, Helvetica, sans-serif">Firmante<p></p></font></th>
</tr>'; 
$tbl_footer1 = '</table>';
$pdf->writeHTML($tbl_header1 . $tbl_footer1, true, false, false, false, '');
// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('mesa_salida.pdf', 'D');

//============================================================+
// END OF FILE                                                
//============================================================+
