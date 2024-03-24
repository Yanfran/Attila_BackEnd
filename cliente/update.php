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
        @$nombre=$_POST['nombre'];        
        @$telefono=$_POST['telefono'];    
        @$status=$_POST['status'];      
        @$correo=$_POST['correo'];       

        if(empty($nombre) ||  empty($telefono) ){			
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="UPDATE clientes SET nombre='$nombre', telefono='$telefono', status='$status', correo='$correo'
        WHERE id = $id";
        $result=mysqli_query($conexion, $sql);

        $array = array("result"=>true,"msg"=>'Actualizado exitosamente');
        $resultado=json_encode($array);
        echo $resultado;
        return;                    
       

        // $resultado=json_encode($array);
        // echo $resultado;
        

           
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