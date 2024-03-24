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

		$array = array("result"=>false,"msg"=>'');
        
		@$txtid=$_POST['txtid'];
		@$txtasunto=$_POST['txtasunto'];
		@$txtmensaje=$_POST['txtmensaje'];
        
        if(empty($txtid) || empty($txtasunto) || empty($txtmensaje)){
            $array = array("result"=>false,"msg"=>'Información incorrecta');
			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $user_info = json_decode(json_encode($data), true);
        $id_admin = $user_info['data']['id'];
        
        $sql_usuario = $user_info['data']['nombre']. " ". $user_info['data']['apellido']." (Admin)";

        $sql = "CALL EnviarMensajeAdmin('$txtid', '$id_admin', '$sql_usuario', '$txtasunto', '$txtmensaje')";
        $query = mysqli_query($conexion, $sql);

        $array = array("result"=>true,"msg"=>'Mensaje enviado exitosamente');
        $resultado=json_encode($array);
        echo $resultado;
        return;
        
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