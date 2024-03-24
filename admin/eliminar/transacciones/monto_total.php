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

        @$tipo = $_POST['tipo'];

        if(empty($tipo)){
            return;
        }

        if($tipo == 1){
            echo consultar_depositos_monto();
            return;
        }

        elseif($tipo == 2){
            echo consultar_retiros_monto();
            return;
        }
        elseif($tipo == 3){
            echo consultar_depositos_monto() - consultar_retiros_monto();
        }

        elseif($tipo == 4){
            $array = array(
                "retiros" => consultar_retiros_monto(),
                "depositos" => consultar_depositos_monto(),
                "total" => consultar_depositos_monto() - consultar_retiros_monto()
            );

            echo json_encode($array);
        }

    } catch (\Exception $e) { // Also tried JwtException
        //echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }

}

function consultar_depositos_monto(){
    $conexion = conexion();
    $sql = "CALL ConsultarAdminMontoDepositos()";
    $result = mysqli_query($conexion, $sql);
    $result = mysqli_fetch_array($result);

    return $result[0];
}

function consultar_retiros_monto(){
    $conexion = conexion();
    $sql = "CALL ConsultarAdminMontoRetiros()";
    $result = mysqli_query($conexion, $sql);
    $result = mysqli_fetch_array($result);

    return $result[0];
}

?>