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

        @$mensaje1 = $_POST['txtmensaje1'];
        @$mensaje2 = $_POST['txtmensaje2'];

        if(empty($mensaje1) || empty($mensaje2)){
            $array = array("result"=>false,"msg"=>'Inserte la información en ambos campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $mensaje = $_POST['txtmensaje1'];
        $sql = "UPDATE dashboard_mensajes SET dashboard_mensajes.mensaje_1 = '$mensaje1', dashboard_mensajes.mensaje_2 = '$mensaje2'";
        $query = mysqli_query($conexion, $sql);

        $array = array("result"=>true,"msg"=>'Comunicados actualizados');
        $resultado=json_encode($array);
        echo $resultado;
        

        
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