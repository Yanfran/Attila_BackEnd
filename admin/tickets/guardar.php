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
        
        @$telefono=$_POST['telefono'];
        @$cliente=$_POST['cliente'];
        @$fecha_apertura= date("Y-m-d");        
        @$fecha_cierre=$_POST['fecha_cierre'];
        @$tiempo_estimado=$_POST['tiempo_estimado'];
        @$cotizacion=$_POST['cotizacion'];
        @$fecha_entraga=$_POST['fecha_entraga'];
        @$hora=date("g:i a");
        @$sede=$_POST['sede'];        
        @$descripcion=$_POST['descripcion'];        
        @$codigo_cliente=$_POST['codigo_cliente'];    
        $tipo_creacion = "web";


		if(empty($cliente) ||  empty($sede) || empty($descripcion)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{

            // $sqlTicket = "SELECT status FROM ticket WHERE telefono = '$telefono' and status != 'ENTREGADO' and status != 'CANCELADO'";
            // $resultSelect=mysqli_query($conexion, $sqlTicket);  

            // if ($resultSelect && mysqli_num_rows($resultSelect) > 0) {
            //     $row = mysqli_fetch_assoc($resultSelect);
            //     $estatus = $row['status'];
        
            //     // var_dump($estatus);
            //     // return;
        
            //     if (
            //       $estatus == 'RECEPCION' ||
            //       $estatus === 'DIAGNOSTICO' || 
            //       $estatus === 'COTIZACION' || 
            //       $estatus === 'EJECUCION' || 
            //       $estatus === 'PENDIENTE' || 
            //       $estatus === 'ENTREGA'
            //       ) {
        
            //         $sqlUpdate2 = "UPDATE clientes SET posicion ='0' WHERE telefono = '$telefono'";
            //         $resultadoUpdate2 = mysqli_query($conexion, $sqlUpdate2);
        
            //         $response = array(
            //           "result" => false,
            //           "msg" => "Ups ya tiene un ticket creado. Debe esperar que se temine el proceso!",
            //           "ticketRes" => false            
            //         );
        
            //       echo json_encode($response);
            //       return;
            //     }
              
            // }
        

            // if($fila){
            //     $array = array("result"=>false,"msg"=>'El nombre ingresado no se encuentra disponible');
            //     $resultado=json_encode($array);
            //     echo $resultado;
            //     return;
            // }else{                                                                              		

                $sql="CALL RelCreateTicket( '$codigo_cliente', '$telefono', '$cliente', '$fecha_apertura', '$sede', '$tipo_creacion')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);

                if ($result) {                    
                    $sql2="SELECT * FROM ticket WHERE cliente = '$cliente' and status = 'RECEPCION'";                    
                    $result2=mysqli_query($conexion, $sql2);
                    $id_user="";
                    while($fila=mysqli_fetch_array($result2)){
						$id_user=$fila[0];
					}                    

                    $sql3="INSERT INTO recepcion (id_ticket, descripcion, status) VALUES ('$id_user', '$descripcion', '1')";
                    $result3=mysqli_query($conexion, $sql3);
                    
                }
                
                $array = array("result"=>true,"msg"=>'Creado exitosamente');
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
