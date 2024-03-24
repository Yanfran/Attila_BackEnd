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

        $user_info = json_decode(json_encode($data), true);
        $user_id = $user_info['data']['id'];

        $array = [];

        $sql = "CALL ConsultarMensajesEnviadosCliente('$user_id')";
        $query = mysqli_query($conexion, $sql);

        if(!$query){
			return;    
        }

        while($fila = mysqli_fetch_array($query)){
            $array[] = array(
                "id" =>  $fila["id"],
                "id_admin" => $fila['id_admin'],
                "usuario" => $fila['usuario'],
                "asunto" => $fila['asunto'],
                "mensaje" => $fila['mensaje'],
                "tipo" => $fila['tipo']
            );
        }

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