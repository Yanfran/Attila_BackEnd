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

		// $array = array("result"=>false,"msg"=>'');
        
        $sql_1 = "SELECT COUNT(id) from clientes WHERE status != 2;";
        $query_1 = mysqli_query($conexion, $sql_1);
        $query_1 = mysqli_fetch_array($query_1);

        $clientes = $query_1[0];

        // echo $clientes;

        $conexion = conexion();
        $sql_2 = "SELECT COUNT(id) FROM `mensajes` WHERE tipo = 'admin'";
        $query_2 = mysqli_query($conexion, $sql_2);
        $query_2 = mysqli_fetch_array($query_2);

        $mensajes = $query_2[0];

        $conexion = conexion();
        $sql_3 = "SELECT COUNT(id) FROM `monedero`";
        $query_3 = mysqli_query($conexion, $sql_3);
        $query_3 = mysqli_fetch_array($query_3);

        $monederos = $query_3[0];

        $conexion = conexion();
        $sql_4 = "SELECT COUNT(id) FROM `paquetes` WHERE status != 2";
        $query_4 = mysqli_query($conexion, $sql_4);
        $query_4 = mysqli_fetch_array($query_4);

        $paquetes = $query_4[0];

        $conexion = conexion();
        $sql_5 = "SELECT COUNT(id) FROM `usuario` WHERE status != 2";
        $query_5 = mysqli_query($conexion, $sql_5);
        $query_5 = mysqli_fetch_array($query_5);

        $usuarios = $query_5[0];

        $conexion = conexion();
        $sql = "SELECT COUNT(id) FROM `roles` WHERE status != 2";
        $query = mysqli_query($conexion, $sql);
        $query = mysqli_fetch_array($query);

        $roles = $query[0];

        $conexion = conexion();
        $sql = "SELECT COUNT(id) FROM `transacciones` WHERE status != 2 AND tipo = 'retiro'";
        $query = mysqli_query($conexion, $sql);
        $query = mysqli_fetch_array($query);

        $retiros = $query[0];

        $conexion = conexion();
        $sql = "SELECT COUNT(id) FROM `transacciones` WHERE status != 2 AND tipo = 'deposito'";
        $query = mysqli_query($conexion, $sql);
        $query = mysqli_fetch_array($query);

        $depositos = $query[0];

        
        $array[] = array(
            "clientes" => $clientes,
            "mensajes" => $mensajes,
            "monederos" => $monederos,
            "paquetes" => $paquetes,
            "usuarios" => $usuarios,
            "roles" => $roles,
            "retiros" => $retiros,
            "depositos" => $depositos
        );

        echo json_encode($array);
        
    } catch (\Exception $e) { // Also tried JwtException
        echo $e->getMessage();
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }

}
?>