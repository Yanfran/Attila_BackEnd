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

        $sql="CALL ConsultarDepositosAdmin()";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila['id'],
                    "id_monedero" => $fila['id_monedero'],
                    "descripcion_monedero" => $fila['descripcion_monedero'],
                    "hash" => $fila['hash'],
                    "red" => $fila['red'],
                    "monto" => $fila['monto'],
                    "fecha" => $fila['fecha'],
                    "id_cliente" => $fila['id_cliente'],
                    "nombre_cliente" => $fila['nombre_cliente'],
                    "billetera_cliente" => $fila['billetera_cliente'],
                    "referencia_pago" => $fila['referencia_pago'],
                    "foto_pago" => $fila['foto_pago'],
                    "status" => $fila['status'],
                    "dni" => $fila['dni'],
                    "telefono" => $fila['telefono'],
                    "correo" => $fila['correo']
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