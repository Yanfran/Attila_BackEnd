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

        $array = array("result"=>false,"msg"=>'');
        
		@$txtasunto=$_POST['txtasunto'];
		@$txtmensaje=$_POST['txtmensaje'];
        @$txtid_admin = $_POST['txtid_admin'];
        
        if(empty($txtid_admin)){
            $txtid_admin = 0;
        }

        if(empty($txtasunto) || empty($txtmensaje)){
            $array = array("result"=>false,"msg"=>'Información incorrecta');
			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $user_info = json_decode(json_encode($data), true);
        $user_id = $user_info['data']['id'];

        $sql_usuario = $user_info['data']['nombre']. " ". $user_info['data']['apellido'];

        $sql = "CALL EnviarMensajeCliente('$user_id', '$txtid_admin', '$sql_usuario', '$txtasunto', '$txtmensaje')";
        $query = mysqli_query($conexion, $sql);

        $array = array("result"=>true,"msg"=>'Mensaje enviado exitosamente');
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