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
        
        @$nombre=$_POST['nombre'];        
        @$codigo_empleado=$_POST['codigo_empleado'];    
        @$status=$_POST['status'];        
        @$id_sede=$_POST['sede']; 
        @$clave=$_POST['clave']; 


		if(empty($nombre) ||  empty($codigo_empleado)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            $sql1="SELECT codigo_empleado FROM empleados WHERE codigo_empleado = '$codigo_empleado'";
            $query=mysqli_query($conexion,$sql1);
            $fila=mysqli_fetch_array($query);
            if($fila){
                $array = array("result"=>false,"msg"=>'El codigo ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }else{                                                                              		

                $sql="INSERT INTO empleados (nombre, codigo_empleado, status, clave, id_sede) VALUES ('$nombre', '$codigo_empleado', '$status', '$clave', $id_sede)";
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
