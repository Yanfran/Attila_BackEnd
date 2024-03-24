<?php
include("../../conexion/conexion.php");
$conexion = conexion();

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

        $array = array("result"=>false,"msg"=>'');

        @$id=$_POST['idtxt'];

        if(empty($id)){
            $array = array("result"=>false,"msg"=>'Falta el ID');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }

        $user_info = json_decode(json_encode($data), true);
        $id_admin = $user_info['data']['id'];

        echo $id_admin;

        if($id_admin != 1){
            $array = array("result"=>false,"msg"=>'Usted no tiene acceso a modificar esta información');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }


        // Cambiando el estado del usuario

        $sql = "SELECT * FROM usuario WHERE id='$id' and status != 2";
        $query=mysqli_query($conexion,$sql);
        $fila = mysqli_fetch_array($query);

        if(!$fila){
        	$array = array("result"=>false,"msg"=>'El usuario no existe');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }
        else{
            $conexion = conexion();
            $sql = "UPDATE usuario SET status = 2 WHERE id='$id'";
            $query=mysqli_query($conexion,$sql);
            $array = array("result"=>true,"msg"=>'Usuario eliminado');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }


	} catch (\Exception $e) { // Also tried JwtException
		echo $e->getMessage();  
    $array = array("result"=>false,"msg"=>'No autorizado');
    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    // header("HTTP/1.0 404 Not authorized");
    return;
	}
}

?>