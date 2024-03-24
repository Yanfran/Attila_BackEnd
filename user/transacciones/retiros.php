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

        $array = array();

        @$txtid=$_POST['txtid'];

        if( empty($txtid) ){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            $sql="CALL ConsultarRetirosUser('$txtid')";
            $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila[0],
                    "id_monedero" => $fila[1],
                    "descripcion_monedero" => $fila[2],
                    "hash" => $fila[3],
                    "red" => $fila[4],
                    "monto" => $fila[5],
                    "fecha" => $fila[6],
                    "id_cliente" => $fila[7],
                    "nombre_cliente" => $fila[8],
                    "billetera_cliente" => $fila[9],
                    "referencia_pago" => $fila[10],
                    "foto_pago" => $fila[11],
                    "status" => $fila[13],
                );
            }
    
            $resultado=json_encode($array);
    
            echo $resultado;
            return;

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