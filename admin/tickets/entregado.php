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

        @$id_ticket=$_POST['id_ticket'];   
        @$fecha_cierre= date("Y-m-d");      
                	
            
                $sql2="UPDATE ticket SET fecha_cierre='$fecha_cierre', status='ENTREGADO' WHERE id = $id_ticket";
                $result2=mysqli_query($conexion, $sql2);                            
                            
            
            $array = array("result"=>true,"msg"=>'Entregado');
            $resultado=json_encode($array);
            echo $resultado;
            return;            
		


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
