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
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));

        @$id = $_POST['id'];

        $user_info = json_decode(json_encode($data), true);
        $user_id = $user_info['data']['id'];


        if(empty($id)){
            $array = array("result"=>false,"msg"=>'Error de datos');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        
        
        $sql_1 = "CALL ConsultarOrden($id)";
        $result = mysqli_query($conexion, $sql_1);
        $result = mysqli_fetch_array($result);

        if(!$result){
            $array = array("result"=>false,"msg"=>'La orden del paquete no existe');
             $resultado=json_encode($array);
             echo $resultado;
            return;
        }

        if($user_id != $result['id_cliente']){
            return;
        }

        if($result['status'] != 0){
            $array = array("result"=>false,"msg"=>'El paquete ya ha sido retirado por el usuario');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }
        
        $fecha_retiro = date('Y-m-d', strtotime($result['fecha'] . '+'. $result['meses'] . 'months'));

        $fecha_actual = date("Y-m-d");
        

        if($fecha_retiro >= $fecha_actual){
            $array = array("result"=>false,"msg"=>'El paquete aun sigue en proceso');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }


        $cliente_id = $result['id_cliente'];

        $conexion = conexion();
        $sql_2 = "CALL RetirarPaquete($id)";
        $result_2 = mysqli_query($conexion, $sql_2);

        $saldo_a_sumar = $result['monto']+ ($result['monto'] * $result['porcentaje'] / 100);


        $conexion = conexion();
        $sql_3 = "CALL SumarSaldo('$cliente_id', '$saldo_a_sumar')";
        $result_3 = mysqli_query($conexion, $sql_3);

        $array = array("result"=>true,"msg"=>'Paquete retirado exitosamente');
        $resultado=json_encode($array);
        echo $resultado;
    
        
        // $fecha_retiro_str = DateTime::createFromFormat('U.u', $fecha_retiro);

        // echo "br\n";
        // echo microtime(true);
        // echo json_encode($fecha_retiro_str);


        
       
    } catch (\Exception $e) { // Also tried JwtException
        echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }
}