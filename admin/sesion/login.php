<?php
include("../../conexion/conexion.php");
$conexion=conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$array = array("result"=>false,"msg"=>'');

@$email=$_POST['email'];
@$password=$_POST['password'];


$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.@";

for ($i=0; $i<strlen($email); $i++){ 
    if (strpos($permitidos, substr($email,$i,1))===false){ 
        $array = array("result"=>false,"msg"=>'El usuario ingresado contiene carácteres inválidos');
        $resultado=json_encode($array);
        echo $resultado;
        return;
    } 
}

for ($i=0; $i<strlen($password); $i++){ 
    if (strpos($permitidos, substr($password,$i,1))===false){ 
        $array = array("result"=>false,"msg"=>'La contraseña ingresada contiene carácteres inválidos');
        $resultado=json_encode($array);
        echo $resultado;
        return;
    } 
}



if(empty($email) || empty($password)){
    
	$array = array("result"=>false,"msg"=>'Debe ingresar sus credenciales');

	$resultado=json_encode($array);
	echo $resultado;
    return;

}else{

    $sql="CALL loginAdmin('$email', '$password')";
    $con = mysqli_query($conexion,$sql);

    $response=false;
    $respuesta=array();

    while($fila = mysqli_fetch_array($con)){
        $respuesta = array(
            "id" => $fila['id'],
            "nombre" => $fila['nombre'],
            "apellido" => $fila['apellido'],
            "dni" => $fila['dni'],
            "img" => $fila['img'],
            "telefono" => $fila['telefono'],
            "fecha_n" => $fila['fecha_n'],
            "correo" => $fila['correo'],
            "id_rol" => $fila['id_rol'],
            "rol" => $fila['descripcion']
            // "img" => 'https://'.$_SERVER['SERVER_NAME'].'/img/'.$fila['img']
        );
        $response=true;
    }

    // Tomando ID's de roles
    $user_info = json_decode(json_encode($respuesta), true);
    $id_admin = $user_info['id_rol'];

    // echo $id_admin;    
    $conexion = conexion();
    $sql_2 = "CALL ConsultarPermisosEnRol('$id_admin')";
    $query = mysqli_query($conexion, $sql_2);

    $roles = array();

    while ($rol_permisos = mysqli_fetch_array($query)) {
        $permisos[] = array(
            "id_permisologia" => $rol_permisos['id_permisologia']
        );
    }

    if (!$response) {
        $array = array("result"=>false,"msg"=>'credenciales invalidas');
    }else{
        $jwt="";
        try {
            $time = time();
            $key = $secret_key_admin;
            $minutos=120;
            $token = array(
                'iat' => $time, // Tiempo que inició el token
                'exp' => $time + (60*$minutos), // Tiempo que expirará el token (+5 min)
                'data' => [ // información del usuario
                    'id' => $respuesta["id"],
                    'nombre' => $respuesta["nombre"],
                    'apellido' => $respuesta["apellido"],
                    'dni' => $respuesta["dni"],
                    'img' => $respuesta["img"],
                    'telefono' => $respuesta["telefono"],
                    'fecha_n' => $respuesta["fecha_n"],
                    'correo' => $respuesta["correo"],
                    'id_rol' => $respuesta["id_rol"],
                    'rol' => $respuesta["rol"],
                    'permisos' => $permisos
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

        // unset($respuesta["id"]);
        // unset($respuesta["id_rol"]);
        // unset($respuesta["rol"]);
        // $respuesta["token"]=$jwt;

        $array = array("result"=>true,"msg"=>'Logueado con éxito', "token" => $jwt);
    }

    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    return;

}



?>