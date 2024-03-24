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
        
		@$txtnombre=$_POST['txtnombre'];
		@$txthash=$_POST['txthash'];
		@$txtred=$_POST['txtred'];

		if(empty($txtnombre) || empty($txthash) || empty($txtred)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{
            $sql="CALL CrearMonederosAdmin( '$txtnombre', '$txthash', '$txtred')";
            $conexion=conexion();
            $result=mysqli_query($conexion, $sql);

            $array = array("result"=>true,"msg"=>'Creado exitosamente');
            $resultado=json_encode($array);
            echo $resultado;
            return;
		}
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