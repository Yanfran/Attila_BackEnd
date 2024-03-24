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
        
        
        @$observacion=$_POST['observacion'];        
        @$id_ticket=$_POST['id_ticket'];      

		if(empty($observacion)){
			
			$array = array("result"=>false,"msg"=>'Debe agregar una observación');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            // $sql="CALL ValidarPaqueteAdmin('$txtnombre')";
            // $query=mysqli_query($conexion,$sql);
            // $fila=mysqli_fetch_array($query);
            // if($fila){
            //     $array = array("result"=>false,"msg"=>'El nombre ingresado no se encuentra disponible');
            //     $resultado=json_encode($array);
            //     echo $resultado;
            //     return;
            // }else{                                                                              		

                $sql="UPDATE ticket SET observacion = '$observacion' WHERE id='$id_ticket' ";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);

                // if ($result) {                                                       
                //     $sql2="UPDATE ticket SET status='PENDIENTE' WHERE id = $id_ticket";
                //     $result2=mysqli_query($conexion, $sql2);
                    
                // }
                                
                
                $array = array("result"=>true,"msg"=>'Observacion creada con éxito');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            // }
		}
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
