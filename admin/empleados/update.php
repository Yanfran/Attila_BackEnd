<?php

include("../../conexion/conexion.php");
$conexion=conexion();

require_once '../../jwt/vendor/autoload.php';
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
        @$codigo_empleado=$_POST['codigo_empleado'];    
        @$status=$_POST['status'];  
        @$id_sede=$_POST['sede'];
        @$clave=$_POST['clave'];       

        if(empty($nombre) ||  empty($codigo_empleado)){			
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="UPDATE empleados SET nombre='$nombre', codigo_empleado='$codigo_empleado', status='$status', 
        clave = '$clave', id_sede = $id_sede
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