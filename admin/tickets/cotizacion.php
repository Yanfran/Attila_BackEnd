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
        
        @$descripcion=$_POST['descripcion'];
        @$fecha=$_POST['fecha'];    
        @$costo=$_POST['costo'];
        @$sugerencia=$_POST['sugerencia'];
        @$fecha_sugerencia=$_POST['fecha_sugerencia'];        
        @$costo_sugerencia=$_POST['costo_sugerencia'];
        @$id_ticket=$_POST['id_ticket'];      
        @$telefono=$_POST['telefono'];
        @$codigo_cliente=$_POST['codigo_cliente'];  

		if(empty($descripcion) || empty($fecha) || empty($costo)){
			
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

                $sql="INSERT INTO ejecucion (id_ticket, fecha, descripcion, sugerencia, fecha_sugerencia, costo_sugerencia, status) VALUES ('$id_ticket', '$fecha', '$descripcion','$sugerencia','$fecha_sugerencia','$costo_sugerencia','1')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);
                

                if ($result) {                                                       
                    $sql2="UPDATE ticket SET tiempo_estimado='$fecha', cotizacion='$costo' WHERE id = $id_ticket";
                    $result2=mysqli_query($conexion, $sql2);
                    

                    $sql3="SELECT t.codigo_cliente, t.tiempo_estimado, t.cotizacion, t.telefono, d.descripcion, d.diagnostico 
                    FROM ticket t INNER JOIN diagnostico d ON t.id = d.id_ticket WHERE t.id = $id_ticket";
                    $result3=mysqli_query($conexion, $sql3); 

                    $sqlUpdate = "UPDATE clientes SET posicion ='4', codigo_cliente = '$codigo_cliente'  WHERE telefono = '$telefono'";
                    $resultadoUpdate = mysqli_query($conexion, $sqlUpdate);

                    $data = array();
                    while($fila3=mysqli_fetch_array($result3)){
                        $array2 = array(
                            "codigo_cliente" => $fila3["codigo_cliente"],
                            "tiempo_estimado" => $fila3["tiempo_estimado"],
                            "cotizacion" => $fila3["cotizacion"],
                            "telefono" => $fila3["telefono"],
                            "descripcion" => $fila3["descripcion"],
                            "diagnostico" => $fila3['diagnostico']
                        );
                        $data[] = $array2;
                    }      
                    
                    
                }
                
                
                $array = array(
                    "result"=>true,
                    "msg"=>'Reporte creado exitosamente',
                    "data" => $data
                );
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
