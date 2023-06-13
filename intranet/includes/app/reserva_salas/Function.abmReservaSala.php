<?php
// Inicia Session y hace include de algunas clases
include_once("../config.php");



// Mapeo Columnas BD a data en los request
define('id', 'id');
define('usuario_id', 'usuarioId');
define('fecha','sDate');
define('inicio','sTimeIni');
define('fin','sTimeFin');
define('titulo','sTitulo');
define('nombre_solicitante','sTitular');
define('detalles','sMotivo');
define('sala_id','sala');
define('equipos','sEquipos');

// Mapeo de operaciones
define('consulta', 'consulta');
define('alta', 'alta');
define('baja','baja');
define('modificacion','modificacion');

function checkAccess($id_permiso, $operacion) 
{
    global $bd;

    $nombre_usuario = $_SESSION["usuario"];
    if ($bd->nivel_acceso($nombre_usuario) > 1){
        return $bd->checkPerm($_SESSION["id_usuario"],$id_permiso, $operacion);
    } else {
        return true;
    }
}

function reservationIsInTheFuture($date, $time) {

    global $oUtils; 

    // Se asume la fecha en formato 'AAAA-mm-dd'
    return $oUtils->reservationIsInTheFuture($date,  $time);
}

function canEditDelete($usuarioReserva, $fechaReserva, $horaInicioReserva, $operacion) {
    $usuario_loggeado_id = $_SESSION["id_usuario"];
    $reservationIsInTheFuture = reservationIsInTheFuture($fechaReserva, $horaInicioReserva);

    if ($usuarioReserva !== $usuario_loggeado_id) {
        //$id_permiso 39 - Reserva de salas de conferencia - super usuario
        return checkAccess(37, $operacion) and $reservationIsInTheFuture;
    }
    
    return $reservationIsInTheFuture;
}

function canCreate($fechaReserva, $horaInicioReserva) {

    $usuario_loggeado_id = $_SESSION["id_usuario"];
    return checkAccess(37, alta) and reservationIsInTheFuture($fechaReserva, $horaInicioReserva);

}

function sendReleaseEmail($reserva,$user,$remover){
    global $oEmail; 

    list($a,$m,$d) = explode("-",$reserva[fecha]);
    $reserva[fecha] ="$d-$m-$a";

    list($h,$m,$s) = explode(":",$reserva[inicio]);
    $reserva[inicio] ="$h:$m";

    list($h,$m,$s) = explode(":",$reserva[fin]);
    $reserva[fin] ="$h:$m";
    
    $sCC = ""; //informatica@mardelplata-conicet.gob.ar, sps_mdq@hotmail.com
    $subject = utf8_decode("Cancelación De Reserva");

    $message = "<html><head><title>Cancelaci&oacute;n de Reserva</title></head>
    <style>
    table{
    font-family: 'Terminal Dosis', Arial, sans-serif;
    width:700px;
    }
    table th{
    text-align:left;
    padding-left: 10px;
    font-size:13px;
    background-color:#BBBBBB;
    }
    table td{
    text-align:left;
    padding-left: 10px;
    font-size:13px;
    }
    table img{
    width:120px;
    }
    .headerList{
    background-color: rgb(89, 146, 196);
    color:white;
    text-align:center;
    font-size:17px;
    padding: 2px 10px;
    }
    </style>
    <body><table style=\"font-family:'Terminal Dosis', Arial, sans-serif;width:700px;\" >
                <tr>
                    <td class='headerList' colspan='6' style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" >
                </tr>
                <tr rowspan='3'>
                    <td style=\"text-align:left;padding-left:10px;font-size:13px;\">
                        <img src='conicet120px.jpg' style=\"width:120px;\"/>
                    </td>
                    <td colspan='3'  style=\"text-align:left;padding-left:10px;font-size:13px;\">
                        CCT CONICET Mar Del Plata
                    </td>
                </tr>
                <tr>
                    <td class='headerList' colspan='6' style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\">
                        I N F O R M A C I O N &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; I M P O R T A N T E
                    </td>
                </tr>
                <tr>
                    <td colspan='6' style=\"text-align:left;padding-left:10px;font-size:13px;\">
                        Le informamos que una reserva de sala a su nombre fue dada de baja. Los datos de la misma se env&iacute;an a continuaci&oacute;n.<br/>
                    </td>
                </tr>
                <tr>
                    <th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">Fecha</th>
                    <th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">Inicio</th>
                    <th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">Fin</th>
                    <th style=\"width:440px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">T&iacute;tulo</th>
                    <th style=\"width:150px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">Reservado por...</th>
                    <th style=\"width:150px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\">Cancelado por...</th>
                </tr>
                <tr>
                    <td>".$reserva[fecha]."</td>
                    <td>".$reserva[inicio]."</td>
                    <td>".$reserva[fin]."</td>
                    <td>".nl2br(htmlentities($reserva[titulo]))."</td>
                    <td>".nl2br(htmlentities($user['userName']))."</td>
                    <td>".nl2br(htmlentities($remover['userName']))."</td>
                    
                </tr>
                <tr>
                    <td colspan='6' style='background-color:#CCCCCC'></td>
                </tr>
                <tr>
                    <td colspan='6' class='headerList'  style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\">&nbsp;</td>
                </tr>
            </table>
        </body>
    </html>
    ";
    $to = $user['userEmail'] . ", ";
    $to .= $sCC;
    // More headers
    $from = 'CCT CONICET Mar Del Plata<notificaciones.conicet.mdp@gmail.com>';
    if ($oEmail->send_email($to,$from,$subject, $message,$_SERVER['DOCUMENT_ROOT']."/intranet/fichador/fichador/imagenes/conicet120px.jpg"))
        return true;
    else 
        return false;
}

function GetReservaSala()
{
    global $conference;

    if (!checkAccess(37, consulta))
    {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Usuario no esta autorizado a consultar', 'code' => 401)));
    }
    
    try 
    {
        $id = $_GET['id'];
        
        $reserva = $conference->getReservationById($id);

        // En la BD se guarda la fecha como YYYY-MM-DD y el front se usa como DD-MM-AAAA
        $reserva['editable'] = canEditDelete($reserva[usuario_id],$reserva[fecha],$reserva[inicio], modificacion);
        $reserva['eliminable'] = canEditDelete($reserva[usuario_id],$reserva[fecha],$reserva[inicio], baja);

        // Actualizacion de la fecha como se espers en el front
        list($a,$m,$d) = explode("-",$reserva[fecha]);
        $reserva[fecha] ="$d-$m-$a";

        header('Content-Type: application/json');
        print json_encode($reserva);
    
    } catch (exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => $e->getMessage(), 'code' => 1337)));
    }
}

function EditReservaSala()
{
    global $conference;
    global $bd;

    parse_str(file_get_contents('php://input'), $_PUT);

    try {

        $id = $bd->realEscapeString($_PUT[id]);
        $reserva = $conference->getReservationById($id);

        if(!canEditDelete($reserva[usuario_id],$reserva[fecha],$reserva[inicio], modificacion))
        {
            header('HTTP/1.1 401 Unauthorized');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'No se puede editar la reserva.', 'code' => 401)));
        }
        
        $constants = get_defined_constants(true)['user'];// array(id, usrId, fecha, inicio, fin, titulo, titular, motivo, sala);
        $data = [];
    
        foreach($constants as $key => $value) {
            if(isset($_PUT[$value]) && $key != 'equipos' && $key != 'id' && $key != 'usuario_id') {
                $d = $bd->realEscapeString($_PUT[$value]);
                if($d) {
                    $data[$key] = $d;
                }
            }
        }

        $data['edited_by'] = $bd->realEscapeString($_PUT[usuario_id]);

        //En la BD se guarda la fecha como YYYY-MM-DD y el front se usa como DD-MM-AAAA
        if (isset($data['fecha'])) {
            list($d,$m,$a) = explode("-",$data['fecha']);
            $data['fecha'] = "$a-$m-$d"; 
        }   

        // Chequea si la sala aún esta disponible
        if (reservationIsInTheFuture("$a-$m-$d", $data['inicio']) and $conference->isSalaAvailable("$a-$m-$d", $data['inicio'], $data['sala_id'], $data['fin'], $id)) 
        {
            $conference->updateReservation($data, $id);
            
            $equipos = [];
            if (isset($_PUT[equipos])) {
                if($_PUT[equipos] != NULL) {
                    $equipos = json_decode(stripslashes($_PUT[equipos]));
                }
                $conference->updateEquipos($equipos, $id);
            }

            header('Content-Type: application/json');
            print json_encode(true);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'La Sala no esta disponible en el nuevo horario.', 'code' => 1337)));
        }

    } catch (exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => $e->getMessage(), 'code' => 1337)));
    }
}

function CreateReservaSala()
{
    global $conference;
    global $bd;

    try {
        $usuarioId = $bd->realEscapeString($_POST[usuario_id]);
        $sDate   = $bd->realEscapeString($_POST[fecha]);
        $sTimeIni   = $bd->realEscapeString($_POST[inicio]);
        $sTimeFin   = $bd->realEscapeString($_POST[fin]);
        $Titulo   = $bd->realEscapeString($_POST[titulo]);
        $Titular   = $bd->realEscapeString($_POST[nombre_solicitante]);
        $Motivo   = $bd->realEscapeString($_POST[detalles]);
        $SalaId   = $bd->realEscapeString($_POST['sala']);
        $Equipos = json_decode(stripslashes($_POST[equipos]));
        
        // En la BD se guarda la fecha como YYYY-MM-DD y el front se usa como DD-MM-AAAA
        list($d,$m,$a) = explode("-",$sDate);

        if (!canCreate("$a-$m-$d", $sTimeIni)) {
            header('HTTP/1.1 401 Unauthorized');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'No es posible crear la reserva.', 'code' => 401)));
        }

        // Chequea si la sala aún esta disponible
        if ($conference->isSalaAvailable("$a-$m-$d", $sTimeIni,$SalaId, $sTimeFin)) {
            // Reservar la sala
            $rta = $conference->createReservation($usuarioId, $sDate, $sTimeIni, $sTimeFin, $Titulo, $Titular, $Motivo, $SalaId);
            
            $conference->reservarEquipos($Equipos, $rta);
            header('Content-Type: application/json');
            print json_encode(true);

        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'La Sala ha sido ocupada. Seleccione un nuevo horario.', 'code' => 1337)));
        }
    } catch (exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => $e->getMessage(), 'code' => 1337)));
    }

}

function DeleteReservaSala()
{
    // Fecha:Noviembre 2020
    // Autor: Victoria
    //baja de reserva de salas.

    global $conference;
    global $oUser;
    global $bd;

    parse_str(file_get_contents('php://input'), $_DELETE);

    //Existen restricciones sobre que usr puede borrar?

    try 
    {
        $usuario_id = $bd->realEscapeString($_DELETE[usuario_id]);
        $id = $bd->realEscapeString($_DELETE[id]);

        $reserva = $conference->getReservationById($id);
        if(!canEditDelete($reserva[usuario_id],$reserva[fecha],$reserva[inicio], baja))
        {
            header('HTTP/1.1 401 Unauthorized');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'No se puede editar la reserva.', 'code' => 401)));
        }

        //Ver si acá va un control de permisos
        if ($conference->deleteReservation($usuario_id,$id)) {
            if ($usuario_id != $reserva[usuario_id]){
                $userEmail = $oUser->getEmail($reserva[usuario_id]);
                $userName = $oUser->getFullName($reserva[usuario_id]);
                $remName = $oUser->getFullName($usuario_id);
                if ($userEmail != '') {
                    $userCreator = array();
                    $userCreator['userName'] = $userName;
                    $userCreator['userEmail'] = $userEmail;
                    $userRemover = array();
                    $userRemover['userName'] = $remName;
                    if (!sendReleaseEmail($reserva,$userCreator,$userRemover)) {
                        header('HTTP/1.1 500 Internal Server Error');
                        header('Content-Type: application/json; charset=UTF-8');
                        die(json_encode(array('message' => 'No se pudo enviar el mail', 'code' => 1337)));
                    } 
                }
            }
            
            header('Content-Type: application/json');
            print json_encode(true);

        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => $e->getMessage(), 'code' => 1337)));
        }
    } catch (exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => $e->getMessage(), 'code' => 1337)));
    }
}

function Main()
{
// Fecha:Octubre 2020
// Autor: Victoria
// abm reserva de salas.

    if (checkAccess(37, consulta))
    {
    
        switch ($_SERVER['REQUEST_METHOD'])
        {
        case 'GET':
            return GetReservaSala();
        case 'PUT':
            return EditReservaSala();
        case 'POST':
            return CreateReservaSala();
        case 'DELETE':
            return DeleteReservaSala();
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Usuario no esta autorizado a consultar', 'code' => 401)));
    }
}

Main();

?>