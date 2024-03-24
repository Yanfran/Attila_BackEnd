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
        @$nombre=$_POST['nombre'];
        @$apellido=$_POST['apellido'];
        @$dni=$_POST['dni'];
        @$correo=$_POST['correo'];
        @$telefono=$_POST['telefono'];

        
        if(empty($id) || empty($nombre) || empty($apellido) || empty($dni) || empty($correo) || empty($telefono)){
    
            $array = array("result"=>false,"msg"=>'Faltan datos por ingresar', 'datos' => $_POST);
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }else{
            
            $sql="CALL validarDNI('$id', '$dni')";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);

            // $array = array("result"=>false,"msg"=>$fila);
        
            // $resultado=json_encode($array);
            // echo $resultado;
            // return;
            if($fila != null){

                $array = array("result"=>false,"msg"=>'El DNI ingresado ya se encuentra asignado a otro usuario');

                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $conexion=conexion();
                $sql="CALL validarEmail('$id', '$correo')";
                $query=mysqli_query($conexion,$sql);
                $fila=mysqli_fetch_array($query);
                if($fila != null ){

                    $array = array("result"=>false,"msg"=>'El email ingresado ya se encuentra asignado a otro usuario');

                    $resultado=json_encode($array);
                    echo $resultado;
                    return;

                }else{
                    $conexion=conexion();
                    $sql="CALL updateProfile('$id', '$dni', '$nombre', '$apellido', '$telefono', '$correo')";
                    $ejec = mysqli_query($conexion,$sql);

                    if($ejec){
                        $array = array("result"=>true,"msg"=>'Sus cambios fueron realizados con éxito');
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                    }
                }
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