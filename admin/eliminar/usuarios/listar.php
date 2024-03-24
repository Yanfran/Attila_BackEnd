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
    // header("HTTP/1.0 404 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array();

        $sql="CALL consultarUsuarios()";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id_usuario" => $fila[0],
                    "documento" => $fila[1],
                    "nombre" => $fila[2],
                    "apellido" => $fila[3],
                    "telefono" => $fila[4],
                    "correo" => $fila[5],
                    "descripcion_rol" => $fila[6],
                    "id_rol" => $fila[9],
                    "usuario" => $fila[10],
                    "fecha_nac" => $fila[11],
                    "direccion" => $fila[12],
                    "nacionalidad" => $fila[7],
                    "status" => $fila[8],
                    "clave" => $fila[13]
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
        // header("HTTP/1.0 404 Not authorized");
        return;
    }

}
?>