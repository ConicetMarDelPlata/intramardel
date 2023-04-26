<?php
 
  Class Bd{
    //Actualizado a PHP > 7 
    //Victoria Ganuza
    //Fecha: 20/09/2022

    public $conn;
    public $userData;

    function AbrirBd($bd=null){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

      switch($_SERVER['SERVER_NAME']){
          case "localhost":
          case "servidor":
          case "192.168.0.6":
              $uName = "root";
              $uPass = "";
              $uBD   = "cctmar_cct";
          break;
          default:
              $uName = "conicet";
              $uPass = ")OD4]N_Of_,q";
              $uBD   = "conicet_cctmar_cct";
          break;	
      }
      $this->conn = mysqli_connect('localhost', $uName,$uPass); // CONEXION ONLINE
      $select_bd = mysqli_select_db($this->conn, $uBD);
      //return $con;
    }

    function excecuteQuery($q) {
        /*
        * Fecha: Noviembre 2020
        * Autor: Victoria
        * Maneja lo básico de realizar una consulta a la BD
        */
        //Actualizado a PHP > 7 
        //Victoria Ganuza
        //Fecha: 20/09/2022

        $r = mysqli_query($this->conn,$q);
        if (mysqli_errno($this->conn)) {
          $error = mysqli_errno($this->conn) . ": " . mysqli_error($this->conn) . "\n";
          error_log($error);
          throw new Exception($error);
        }
        if ($r) {
          return $r;
        } else {
          return false;
        }
    }

    function usuario_registrado($nombre_usuario, $contrasenia){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

      $q = 'SELECT 
          * 
        FROM 
          usuario
        WHERE 
          baja = 0 and
          nombre_usuario ="' . $nombre_usuario . '" AND 
          contrasenia ="' . $contrasenia . '"';
        try {
          $r = $this->excecuteQuery($q);
          if (mysqli_num_rows($r)==1){
            return TRUE;
          }else
          {
            return FALSE;
          }
        } catch (exception $e) {
          echo $e->getMessage();
        }
    }

    public function getUserByUserName($sUserName){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$sSQL = "SELECT * FROM usuario WHERE nombre_usuario = '$sUserName'";
			$res =  $this->excecuteQuery($sSQL);
			$row = mysqli_fetch_assoc($res);
			if($row){
				return $row;
			}else{
				return false;
			}
		}

    public function getPanel($sPanel, $iID = null){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			if($iID){
				$sWhere = " WHERE id = $iID ";
			}else{
				$sWhere = "";
			}
			
			$sSQL = "SELECT * FROM $sPanel $sWhere ORDER BY id";
			$res = $this->excecuteQuery($sSQL);
			if(!$iID){
				while($row = mysqli_fetch_assoc($res)){
					$vData[] = $row;
				}		
			}else{
				$vData = mysqli_fetch_assoc($res);
			}
			return $vData;
		}
  }
?>