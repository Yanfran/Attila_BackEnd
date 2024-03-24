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

        $descripcion=$_POST['txtdescripcion'];
        $permi=$_POST['check_per'];

        $permisos = json_decode($permi);
        
        if(empty($descripcion) || empty($permisos)){

            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
			$resultado=json_encode($array);
			echo $resultado;
			return;

        }else{

            $sql="SELECT * FROM roles WHERE descripcion='$descripcion' and status!='2'";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);

            if(count($fila)>0){

                $array = array("result"=>false,"msg"=>'El nombre ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $sql2="INSERT INTO roles values('','$descripcion','1')";
                $query2=mysqli_query($conexion,$sql2);

                if($query2){
                    
                    $sql="SELECT * FROM roles WHERE descripcion='$descripcion'";
                    $query=mysqli_query($conexion,$sql);
                    $id_rol="";
                    while($fila=mysqli_fetch_array($query)){
                        $id_rol=$fila[0];
                    }
                    foreach($permisos as $valor){                
                        $sql3="INSERT INTO permisos values('','$id_rol','$valor')";
                        $query3=mysqli_query($conexion,$sql3);
                    }

                    $array = array("result"=>true,"msg"=>'Creado exitosamente');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;

                }else{

                    $array = array("result"=>false,"msg"=>'Error inesperado');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }
            }
        }
        
    } catch (\Exception $e) {
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;        
    }
}
?>