<?php

include("../../conexion/conexion.php");
$conexion = conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

if($token ==""){
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

        $id = $_POST['id'];

        
        if(empty($id)){
    
            $array = array("result"=>false,"msg"=>'Faltó el ID del cliente a verificar', 'datos' => $_POST);
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }else{
            $conexion=conexion();
            $sql="CALL activarCliente('$id')";
            $ejec = mysqli_query($conexion,$sql);

            if($ejec){
                $array = array("result"=>true,"msg"=>'El cliente ha sido verificado');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

        }
    } catch (\Exception $e) { // Also tried JwtException
        //echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        // header("HTTP/1.0 404 Not authorized");
        return;
    }
}

?>