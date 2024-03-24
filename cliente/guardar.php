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
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

		$array = array("result"=>false,"msg"=>'');
                
        @$nombre=$_POST['nombre'];        
        @$telefono=$_POST['telefono'];    
        @$status=$_POST['status'];    
        @$correo=$_POST['correo'];             


		if(empty($nombre)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            $sql1="SELECT telefono FROM clientes WHERE telefono = '$telefono'";
            $query=mysqli_query($conexion,$sql1);
            $fila=mysqli_fetch_array($query);
            if($fila){
                $array = array("result"=>false,"msg"=>'El telefono ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }else{                                                                              		

                $sql="INSERT INTO clientes (nombre, telefono, status, correo) VALUES ('$nombre', '$telefono', '$status', '$correo')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);            
                
                
                $array = array("result"=>true,"msg"=>'Creado exitosamente');
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
