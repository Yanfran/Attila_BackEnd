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
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array();
        @$id = $_POST['txtid'];

        if(empty($id)){
            $array = array("result"=>false,"msg"=>'Error en el usuario');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $sql="CALL ConsultarSaldo('$id')";
        $result=mysqli_query($conexion, $sql);
        $result = mysqli_fetch_array($result);

        echo json_encode($result);

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