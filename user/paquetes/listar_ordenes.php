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

        @$id = $_POST['txtid'];

        if(empty($id)){
            $array = array("result"=>false,"msg"=>'El ID es necesario');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }

        $sql="CALL ConsultarClienteOrdenes($id)";
        $result=mysqli_query($conexion, $sql);

        while($fila=mysqli_fetch_array($result)){
            $array[] = array(
                "id" => $fila['id'],
                "id_cliente" => $fila['id_cliente'],
                "id_paquete" => $fila['id_paquete'],
                "id_monedero" => $fila['id_monedero'],
                "nombre_paquete" => $fila['nombre_paquete'],
                "monto" => $fila['monto'],
                "fecha" => $fila['fecha'],
                "meses" => $fila['meses'],
                "porcentaje" => $fila['porcentaje'],
                "status" => $fila['status'],
                "img" => $fila['img']
            );
            // echo json_encode($fila);
        }
        $result = json_encode($array);

        echo $result;
           //echo json_encode($data);
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