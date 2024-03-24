<?php
include("../conexion/conexion.php");
$conexion=conexion();

require_once '../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$array = array("result"=>false,"msg"=>'');

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);



if($token==""){
    $array = array("result"=>false,"msg"=>'No autorizadoaqui');
    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    //header("HTTP/1.0 401 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));


        @$nombre=$_POST['txtnombre'];
        @$apellido=$_POST['txtapellido'];
        @$dni=$_POST['txtdni'];
        @$correo=$_POST['txtcorreo'];
        @$telefono=$_POST['txttelefono'];

        if(empty($nombre) || empty($apellido) || empty($dni) || empty($correo) || empty($telefono)){
    
            $array = array("result"=>false,"msg"=>'Faltan datos por ingresar');
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        
        }else{

            $id=$data->data->id;

            $sql="CALL validarDNI('$id', '$dni')";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);
            if($fila){

                $array = array("result"=>false,"msg"=>'El DNI ingresado ya se encuentra asignado a otro usuario');

                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $conexion=conexion();
                $sql="CALL validarEmail('$id', '$correo')";
                $query=mysqli_query($conexion,$sql);
                $fila=mysqli_fetch_array($query);
                if($fila){

                    $array = array("result"=>false,"msg"=>'El email ingresado ya se encuentra asignado a otro usuario');

                    $resultado=json_encode($array);
                    echo $resultado;
                    return;

                }else{

                    $conexion=conexion();
                    $sql="CALL updateProfile('$id', '$dni', '$nombre', '$apellido', '$telefono', '$correo')";
                    $ejec = mysqli_query($conexion,$sql);

                    if ($ejec) {

                        $jwt="";
                        try {
                            $time = time();
                            $key = $secret_key;
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
                                    'codigo_ref' => $data->data->codigo_ref,
                                    'referido' => $data->data->referido,
                                    'id_rol' => 0,
                                    'rol' => 'cliente'
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
        //header("HTTP/1.0 404 Not authorized");
        return;
    }
    

}




?>