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
        if(empty($_POST['txtid'])){
             $array = array("result"=>false,"msg"=>'Falta el ID');
                $resultado=json_encode($array);
                echo $resultado;
                return;   
        }

	try {
	$jwt=$token;
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array("result"=>false,"msg"=>'');

        $id=$_POST['txtid'];

        // Cambiando el estado del usuario

        $sql = "SELECT * FROM clientes WHERE id='$id'";
        $query=mysqli_query($conexion,$sql);
        $fila = mysqli_fetch_array($query);

        if($fila){

                 $sql = "UPDATE clientes SET status = '2' WHERE clientes.id = $id";
                $query = mysqli_query($conexion, $sql);

                if($query){
                        $array = array("result"=>true,"msg"=>'El usuario ha sido eliminado con éxito');
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                }else{
                        $array = array("result"=>false,"msg"=>'Error al borrar');
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                }


        }else{
                $array = array("result"=>false,"msg"=>'El cliente no existe');
                $resultado=json_encode($array);
                echo $resultado;
                return;
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