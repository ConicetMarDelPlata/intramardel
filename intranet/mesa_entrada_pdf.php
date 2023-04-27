<?php

include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');
ini_set('max_execution_time',0);

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Mesa de Entrada');
$pdf->SetHeaderData(PDF_HEADER_LOGO, 270);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->setLanguageArray($l);

// ---------------------------------------------------------

$pdf->SetFont('Helvetica', 'B', 14);

$pdf->AddPage();
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Mesa de Entrada', '', 0, 'C', true, 0, false, false, 0);

$fecha_desde = $_POST["fecha_desde"];
$fecha_hasta = $_POST["fecha_hasta"];

$pdf->SetFont('Helvetica', 'B', 12);
$title = 'Documentación ingresada ';
if ($fecha_desde != '') {
   if ($fecha_hasta != '') {
      $title .= 'entre el '. $fecha_desde .' y el '. $fecha_hasta; 
   } else {
      $title .= 'desde el '. $fecha_desde; 
   }
} else {
   $title .= 'hasta el '. $fecha_hasta;   
}
$pdf->Write(0, $title  , '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('Helvetica', '', 10);

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
   color: #333333;
   font-family: Helvetica;
   /*font-size: 25px;*/
   padding-left: 5px;
/*   padding-bottom: 1px;
   padding-top: 2px; */
   text-align: left;
}
td {
   background-color: #EEEEEE;
   border: 1px solid #FFFFFF;
   /*font-size: 25px;*/
   color: #369;
   /*padding: 3px 7px 2px;*/
}
</style>
<table id="gallerytab" width="100%" cellspacing="0" cellpadding="3" border="0">
<tr>
       <th align="center" width="45"><font face="Arial, Helvetica, sans-serif">Orden</font></th>
       <th width="70"><font face="Arial, Helvetica, sans-serif">Tramite</font></th>
       <th width="74"><font face="Arial, Helvetica, sans-serif">Fecha</font></th>
       <th width="145"><font face="Arial, Helvetica, sans-serif">Remitente</font></th>
       <th width="340"><font face="Arial, Helvetica, sans-serif">Documento</font></th>
       <th width="240"><font face="Arial, Helvetica, sans-serif">Destinatario</font></th>
       <th width="36"><font face="Arial, Helvetica, sans-serif">Cant.</font></th>
     </tr>'; 
//--------------------------------------------------------------------------------------------------------------------  
$tbl = '';
if (($fecha_desde != '') || ($fecha_hasta != '')) {
   $bd = new Bd;
   $bd->AbrirBd();
   $q = '
      SELECT * FROM mesa_entrada
      WHERE 1
      '; 
   if ($fecha_desde != '') {
      $q .= ' AND fecha >="' . convertir_fecha_sql($fecha_desde) . '"';
   }
   if ($fecha_hasta != '') {
      $q .= ' AND fecha <="' . convertir_fecha_sql($fecha_hasta) . '"';
   }

   $r = $bd->excecuteQuery($q);
   $i = 1;
   while ($row = mysqli_fetch_assoc($r)) {
      $row_destinatario = $bd->consultar_destinatario($row['destinatario']);
      $dmy = explode('-', $row['fecha']);
      $tbl.='<tr>
         <td align="center">'.$i.'</td>
         <td align="center">'.$row['numero_tramite']. '/'. $dmy[0] .'</td>
         <td align="center">'.convertir_fecha($row['fecha']).'</td>
         <td>'.$row['remitente'].'</td>
         <td>'.$row['documento'].'</td>
         <td>'.$row_destinatario['descripcion'].'</td> 
         <td align="center">'.$row['cantidad'].'</td>
         </tr>
      ';
      $i++;
   }
   /* while ($row_mesa_entrada = mysqli_fetch_assoc($r)){	
   //  
      $row_destinatario = $bd->consultar_destinatario($row_mesa_entrada['destinatario']);
      $dmy = explode('-', $row_mesa_entrada['fecha']);
      $tbl.='<tr>
         <td align="center">'.$i.'</td>
         <td align="center">'.$row_mesa_entrada['numero_tramite']. '/'. $dmy[0] .'</td>
         <td align="center">'.convertir_fecha($row_mesa_entrada['fecha']).'</td>
         <td>'.utf8_encode($row_mesa_entrada['remitente']).'</td>
         <td>'.utf8_encode($row_mesa_entrada['documento']).'</td>
         <td>'.utf8_encode($row_destinatario['descripcion']).'</td>
         <td align="center">'.$row_mesa_entrada['cantidad'].'</td>
         </tr>
      ';
   $i++;
   } */
   $Bd = NULL;
} else {
   $pdf->Cell(0, 0, 'Ingrese al menos una fecha para realizar el reporte.', 1, 1, 'C', 0, '', 0);
}

$tbl_footer = '</table>';
$pdf->writeHTML($tbl_header . $tbl . $tbl_footer, true, false, false, false, ''); 
//$pdf->writeHTML($tbl_footer, true, false, true, false, ''); 

$pdf->Output('mesa_entrada.pdf');