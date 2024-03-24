<?php

include("../../conexion/conexion.php");
$conexion=conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

// echo "hola"."  ".$token;
// return;

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
        
        @$descripcion=$_POST['descripcion'];
        @$diagnostico=$_POST['diagnostico'];
        @$piezas=$_POST['piezas'];
        @$id_ticket=$_POST['id_ticket'];    
                

		if(empty($descripcion) || empty($piezas) || empty($diagnostico)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

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

                $sql="INSERT INTO diagnostico (id_ticket, descripcion, diagnostico, piezas, status) VALUES ('$id_ticket','$descripcion','$diagnostico','$piezas','1')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);                
                

                if ($result) {                                                       
                    $sql2="UPDATE ticket SET status='COTIZACION' WHERE id = $id_ticket";
                    $result2=mysqli_query($conexion, $sql2);
                    
                }
                
                $array = array("result"=>true,"msg"=>'Reporte creado exitosamente');
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
