<?php
include "seg_bd.php";
$nombre_usuario = 'mib';
$contrasenia = 'mibello';


$bd = new Bd;
$link = $bd->AbrirBd(); //nombre de la bd a abrir ONLINE	
$oModulos   =  $bd->getPanel("panel_control");

print_r($oModulos);

/*$bd->AbrirBd();	
 $uName = "conicet";
$uPass = ")OD4]N_Of_,q";
$uBD   = "conicet_cctmar_cct";
 */
/* $con = mysqli_connect('localhost', $uName,$uPass);
dd($con);
if ($con) {echo "conecto";}
else {echo "no conecto";}
 
$bd->lista_usuarios();*/



?>
