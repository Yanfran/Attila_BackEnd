<?php

include("../conexion/conexion.php");
$conexion=conexion();

require_once '../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

if($token==""){
    $array = array("result"=>false,"msg"=>'No autorizado');
    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    //header("HTTP/1.0 404 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array();

        @$id=$_POST['id'];               

        if(empty($id)){			
            $array = array("result"=>false,"msg"=>'Debe enviar un parametro');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="UPDATE clientes SET status='2' WHERE id = $id";
        $result=mysqli_query($conexion, $sql);

        $array = array("result"=>true,"msg"=>'Eliminado exitosamente');
        $resultado=json_encode($array);
        echo $resultado;
        return;                                   
        

           
    } catch (\Exception $e) { // Also tried JwtException
        //echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }

}
?>