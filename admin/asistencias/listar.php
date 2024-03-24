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


        $sql="SELECT asistencias.id, asistencias.nombre_imagen, asistencias.latitud, asistencias.longitud, asistencias.nombre,
        asistencias.codigo_empleado, asistencias.fecha_registro, asistencias.hora, sede.descripcion 
        FROM asistencias 
        INNER JOIN sede ON sede.id = asistencias.id_sede
        ORDER BY asistencias.id DESC
        ";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila["id"],
                    "nombre_imagen" => $fila["nombre_imagen"],
                    "latitud" => $fila["latitud"],
                    "longitud" => $fila["longitud"],
                    "nombre" => $fila["nombre"],
                    "codigo_empleado" => $fila["codigo_empleado"],                    
                    "fecha" => $fila['fecha_registro'],   
                    "hora" => $fila['hora'],
                    "descripcion" => $fila['descripcion']                    
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