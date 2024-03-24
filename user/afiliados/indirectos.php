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

        @$txtref=$_POST['txtref'];

        if( empty($txtref) ){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            $sql="CALL ConsultarReferidosD('$txtref')";
            $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){

                $sql2="CALL ConsultarReferidosD('$fila[9]')";
                $conexion=conexion();
                $result2=mysqli_query($conexion, $sql2);

                while($fila2=mysqli_fetch_array($result2)){

                    $array[] = array(
                        "nombre" => $fila2[1] .' '. $fila2[2],
                        "status" => $fila2[12],
                    );

                }
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