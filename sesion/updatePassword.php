<?php
include("../conexion/conexion.php");
$conexion=conexion();

require_once '../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$array = array("result"=>false,"msg"=>'');

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

if($token==""){
    $array = array("result"=>false,"msg"=>'No autorizado');
    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    // header("HTTP/1.0 401 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));


        @$oldPass=$_POST['txtoldpassword'];
        @$newPass=$_POST['txtnewpassword'];

        if(empty($oldPass) || empty($newPass)){
    
            $array = array("result"=>false,"msg"=>'Faltan datos por ingresar');
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        
        }else{

            if($oldPass==$newPass){

                $array = array("result"=>false,"msg"=>'Disculpe la nueva contraseña ingresada debe ser distinta a la anterior');
        
                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{
                $id=$data->data->id;

                $sql="CALL validarPassword('$id', '$oldPass')";
                $query=mysqli_query($conexion,$sql);

                $response=false;

                while($fila = mysqli_fetch_array($query)){
                    $response=true;
                }

                if($response==true){

                    $conexion=conexion();

                    $sql="CALL updatePassword('$id', '$newPass')";
                    $query=mysqli_query($conexion,$sql);
                    
                    if($query) {
                        $array = array("result"=>true,"msg"=>'Su contraseña ha sido modificada con éxito');
    
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                    }else{
                        $array = array("result"=>false,"msg"=>'No se pudieron realizar los cambios');
    
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                    }

                    
                }else{
                    $array = array("result"=>false,"msg"=>'La contraseña antigua ingresada es inválida');
    
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }
    
                
            }
            
        }

        
    
        //echo json_encode($data);
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