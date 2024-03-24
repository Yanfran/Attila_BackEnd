<?php

include("../conexion/conexion.php");
$conexion=conexion();



    try {        

		$array = array("result"=>false,"msg"=>'');
                
        @$respuesta_1=$_POST['respuesta_1'];
        @$respuesta_2=$_POST['respuesta_2'];
        @$respuesta_3=$_POST['respuesta_3'];
        @$respuesta_4=$_POST['respuesta_4'];
        @$comentario=$_POST['comentario'];
        @$id_ticket = 1;
        
        


		if(empty($respuesta_1) || empty($respuesta_2) || empty($respuesta_3) ||empty($respuesta_4)){
			
			$array = array("result"=>false,"msg"=>'Debe seleccionar todos los radios');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{
                                                                                		
            $sql="INSERT INTO encuesta (experiencia, calidad, recomendacion, respuesta, comentario, id_ticket) VALUES ('$respuesta_1', '$respuesta_2', '$respuesta_3', '$respuesta_4', '$comentario', '$id_ticket')";
            $conexion=conexion();
            $result=mysqli_query($conexion, $sql);            
            
            
            $array = array("result"=>true,"msg"=>'Encuesta creada exitosamente');
            $resultado=json_encode($array);
            echo $resultado;
            return;
            
		}
    } catch (\Exception $e) { // Also tried JwtException
        echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }


?>
