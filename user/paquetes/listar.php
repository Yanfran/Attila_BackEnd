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
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array();

        $sql="CALL ClientePaquetes()";
        $result=mysqli_query($conexion, $sql);
        
        while($fila=mysqli_fetch_array($result)){
            $array[] = array(
                "id" => $fila["id"],
                "nombre" => $fila["nombre"],
                "precio" => $fila["precio"],
                "porcentaje" => $fila["porcentaje"],
                "img" => $fila["img"],
                "descripcion" => $fila["descripcion"],
                "meses" => $fila['meses'],
                "status" => $fila["status"]
            );
        }


    $resultado=json_encode($array);

    // echo $fila;

        echo $resultado;
        return;

           //echo json_encode($data);
    } catch (\Exception $e) { // Also tried JwtException
        // echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }

}
?>