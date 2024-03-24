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

        $sql="CALL ConsultarClientesAdmin()";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila[0],
                    "nombre" => $fila[1],
                    "apellido" => $fila[2],
                    "dni" => $fila[3],
                    "img" => $fila[4],
                    "telefono" => $fila[5],
                    "fecha_nac" => $fila[6],
                    "correo" => $fila[7],
                    "codigo_ref" => $fila[9],
                    "referido" => $fila[10],
                    "saldo" => $fila[11],
                    "status" => $fila[12]
                );
            }

        $resultado=json_encode($array);

        echo $resultado;

           //echo json_encode($data);
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