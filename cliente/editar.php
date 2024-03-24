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

        if(empty($id)){
            $array = array("result"=>false,"msg"=>'El ID es necesario');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="SELECT * FROM clientes WHERE id = $id";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila["id"],
                    "nombre" => $fila["nombre"],
                    "telefono" => $fila["telefono"],
                    "status" => $fila['status'],
                    "fecha" => $fila["fecha"],
                    "posicion" => $fila["posicion"],
                    "correo" => $fila["correo"]
                );
            }                            


        // $arreglo['data']=$array;
        // $resultado=json_encode($arreglo);
        // echo $resultado;

        $resultado=json_encode($array);
        echo $resultado;
        

           
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