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
		@$txtnombre=$_POST['txtnombre'];
		@$txtprecio=$_POST['txtprecio'];
		@$txtporcentaje=$_POST['txtporcentaje'];
        @$txtdescripcion = $_POST['txtdescripcion'];
        @$txtmeses = $_POST['txtmeses'];

		if( empty($txtid) || empty($txtnombre) || empty($txtprecio) || empty($txtporcentaje) || empty($txtmeses) || $txtmeses <= 0 ){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            
            $sql="CALL ValidarPaquete2Admin( '$txtid', '$txtnombre')";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);

            if($fila){

                $array = array("result"=>false,"msg"=>'El nombre ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $sql="CALL EditarPaqueteAdmin( '$txtid', '$txtnombre', '$txtprecio', '$txtporcentaje', '$txtdescripcion', '$txtmeses')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);

                $array = array("result"=>true,"msg"=>'Editado exitosamente');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }
		}
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