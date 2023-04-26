<?php
//--------------------------- CLASS USER
Class User{
    protected $bd;
    public $id;

    function __construct($bd){
        $this->bd=$bd;
    }

    function getEmail($id = null){
        if(!$id){
            $id = $this->id;
        }
        if(isset($id)){
            $q = "SELECT email FROM usuario WHERE id_usuario = $id";

            $res   = $this->bd->excecuteQuery($q);
            $row = mysqli_fetch_assoc($res);
            if($row){
                return $row['email'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function getAllEmails($sExcepto){
        $q = "SELECT email FROM usuario WHERE email <> '$sExcepto' and baja = 0 and email <> '' and baja = 0";

        $res   = $this->bd->excecuteQuery($q);
        $vData = ""; 
        while($row = mysqli_fetch_assoc($res)){
            $vData.=$row['email'].", ";
        }
        return substr($vData,0,-2);
    }

    function getFirstName($id=null){
        if(!$id){
            $id = $this->id;
        }
        if(isset($id)){
            $q = "SELECT nombre FROM usuario WHERE id_usuario = $id";

            $res   = $this->bd->excecuteQuery($q);
            $row = mysqli_fetch_assoc($res);
            if($row){
                return utf8_encode($row['nombre']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function getLastName($id=null){
        if(!$id){
            $id = $this->id;
        }
        if(isset($id)){
            $q = "SELECT apellido FROM usuario WHERE id_usuario = $id";

            $res   = $this->bd->excecuteQuery($q);
            $row = mysqli_fetch_assoc($res);
            if($row){
                return $row['apellido'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function getFullName($id=null){
        if(!$id){
            $id = $this->id;
        }
        if(isset($id)){
            $q = "SELECT nombre, apellido FROM usuario WHERE id_usuario = $id";

            $res   = $this->bd->excecuteQuery($q);
            $row = mysqli_fetch_assoc($res);
            if($row){
                return utf8_encode($row['apellido'].', '.$row['nombre']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
   
}

?>