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

        $array = array();

        @$id_retiro = $_POST['id_retiro'];

        if(empty($id_retiro) ){
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

        }else{

            $sql = "CALL ConsultarRetiroId('$id_retiro')";
            $result = mysqli_query($conexion, $sql);
            $result = mysqli_fetch_array($result);

            if($result){

                $estado = $result['status'];
                $monto = $result['monto'];
                $id_cliente = $result['id_cliente'];

                if($estado != 0){
                    $array = array("result"=>false,"msg"=>'El retiro ya está modificado por el admin');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }

                $conexion = conexion();
                // Llamando el retiro de administrador
                $sql_2 = "CALL RechazarRetiroAdmin('$id_retiro')";
                $result_2 = mysqli_query($conexion, $sql_2);

                if($result_2){

                    $sql_3 = "CALL SumarSaldo($id_cliente, $monto)";
                    $result_3 = mysqli_query($conexion, $sql_3);

                    $array = array("result"=>true,"msg"=>'El retiro ha sido rechazado');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }else{
                    $array = array("result"=>false,"msg"=>'El retiro no existe');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }

                
            }else{
                $array = array("result"=>false,"msg"=>'Error al rechazar el deposito');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

        }


        //echo json_encode($data);
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