<?php

include("../../conexion/conexion.php");
$conexion=conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$array = array("result"=>false,"msg"=>'');

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


        @$nombre=$_POST['txtnombre'];
        @$apellido=$_POST['txtapellido'];
        @$dni=$_POST['txtdni'];
        @$correo=$_POST['txtcorreo'];
        @$telefono=$_POST['txttelefono'];

        $user_info = json_decode(json_encode($data), true);
        $id_admin = $user_info['data']['id'];

        if($id_admin != 1){
            $array = array("result"=>false,"msg"=>'Usted no tiene acceso a modificar esta información');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        if(empty($nombre) || empty($apellido) || empty($dni) || empty($correo) || empty($telefono)){
    
            $array = array("result"=>false,"msg"=>'Faltan datos por ingresar');
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        
        }else{

            $id=$data->data->id;

            $sql="CALL validarDNIAdmin('$id', '$dni')";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);
            if($fila){

                $array = array("result"=>false,"msg"=>'El DNI ingresado ya se encuentra asignado a otro usuario');

                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $conexion=conexion();
                $sql="CALL validarEmailAdmin('$id', '$correo')";
                $query=mysqli_query($conexion,$sql);
                $fila=mysqli_fetch_array($query);
                if($fila){

                    $array = array("result"=>false,"msg"=>'El email ingresado ya se encuentra asignado a otro usuario');

                    $resultado=json_encode($array);
                    echo $resultado;
                    return;

                }else{

                    $conexion=conexion();
                    $sql="CALL updateProfileAdmin('$id', '$dni', '$nombre', '$apellido', '$telefono', '$correo')";
                    $ejec = mysqli_query($conexion,$sql);

                    if ($ejec) {

                        $jwt="";
                        try {
                            $time = time();
                            $key = $secret_key_admin;
                            $minutos=120;
                            $token = array(
                                'iat' => $time, // Tiempo que inició el token
                                'exp' => $time + (60*$minutos), // Tiempo que expirará el token (+5 min)
                                'data' => [ // información del usuario
                                    'id' => $id,
                                    'nombre' => $nombre,
                                    'apellido' => $apellido,
                                    'dni' => $dni,
                                    'img' => $data->data->img,
                                    'telefono' => $telefono,
                                    'fecha_n' => $data->data->fecha_n,
                                    'correo' => $correo,
                                    'id_rol' => $data->data->id_rol,
                                    'rol' => $data->data->rol
                                ]
                            );
                        
                            $jwt = JWT::encode($token, $key);
                        
                        } catch (\Exception $e) { // Also tried JwtException
                            //echo $e->getMessage();  
                            $array = array("result" => false, "msg"=>$e->getMessage());
                            $resultado=json_encode($array);
                            echo $resultado;
                            return;
                        }

                        $array = array("result"=>true,"msg"=>'Sus cambios fueron realizados con éxito', "token" => $jwt);
                        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
                        echo $resultado;
                        return;

                    }else{
                        $array = array("result"=>true,"msg"=>"No se pudieron realizar los cambios");
                        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
                        echo $resultado;
                        return;
                    }
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