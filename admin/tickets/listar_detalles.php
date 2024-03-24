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

        @$id=$_POST['id'];

        if(empty($id)){
            $array = array("result"=>false,"msg"=>'El ID es necesario');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="SELECT t.id, t.codigo_cliente, t.telefono, t.cliente, t.fecha_apertura, t.fecha_cierre,
        t.tiempo_estimado, t.cotizacion, t.hora, t.sede, t.status, t.sugerencia, t.correo, t.observacion,
        t.entrega, c.nombre, sumaValoresRel1(t.id, t.tiempo_estimado, ifnull(t.sugerencia, '')) as 'calCulo', 
        sumaValoresRel2(t.id, t.cotizacion, ifnull(t.sugerencia, '')) as 'calCulo2' 
        from ticket t  INNER JOIN clientes c ON t.telefono = c.telefono WHERE t.id = $id";
        $result=mysqli_query($conexion, $sql);

            while($fila=mysqli_fetch_array($result)){
                $array[] = array(
                    "id" => $fila["id"],
                    "codigo_cliente" => $fila["codigo_cliente"],
                    "telefono" => $fila["telefono"],
                    "cliente" => $fila["cliente"],
                    "fecha_apertura" => $fila["fecha_apertura"],
                    "fecha_cierre" => $fila["fecha_cierre"],
                    "tiempo_estimado" => $fila["calCulo"],
                    "cotizacion" => $fila["calCulo2"], 
                    "hora" => $fila['hora'],
                    "sede" => $fila['sede'],
                    "status" => $fila['status'],
                    "sugerencia" => $fila['sugerencia'],
                    "correo" => $fila['correo'],
                    "observacion" => $fila['observacion'],
                    "entrega" => $fila['entrega'],
                    "nombre" => $fila['nombre'],
                );
            }

        $sql2="SELECT * FROM recepcion WHERE id_ticket = $id";
        $result2=mysqli_query($conexion, $sql2); 
        while($fila2=mysqli_fetch_array($result2)){
            $array2 = array(
                "id" => $fila2["id"],                
                "id_ticket" => $fila2["id_ticket"],                
                "descripcion" => $fila2["descripcion"],                
                "detalle_recepcion" => $fila2["detalle_recepcion"],                
                "status" => $fila2['status']
            );
            array_push($array, $array2);
        }      
        
        
        $sql3="SELECT * FROM diagnostico WHERE id_ticket = $id and status = '1'";
        $result3=mysqli_query($conexion, $sql3); 
        while($fila3=mysqli_fetch_array($result3)){
            $array3 = array(
                "id" => $fila3["id"],                
                "id_ticket" => $fila3["id_ticket"],                
                "descripcion" => $fila3["descripcion"],
                "diagnostico" => $fila3["diagnostico"],                
                "piezas" => $fila3["piezas"],                
                "status" => $fila3['status']
            );
            array_push($array, $array3);
        }    


        $sql4="SELECT * FROM ejecucion WHERE id_ticket = $id and status = '1'";
        $result4=mysqli_query($conexion, $sql4); 
        $comentario_id = "";
        while($fila4=mysqli_fetch_array($result4)){
            $array4 = array(
                "id" => $fila4["id"],                
                "id_ticket" => $fila4["id_ticket"],                
                "fecha" => $fila4["fecha"],                                
                "descripcion" => $fila4["descripcion"],
                "descripcion_2" => $fila4["descripcion_2"],
                "sugerencia" => $fila4["sugerencia"],
                "fecha_sugerencia" => $fila4["fecha_sugerencia"],
                "costo_sugerencia" => $fila4["costo_sugerencia"],
                "status" => $fila4['status']
            );
            $comentario_id = $fila4["descripcion_2"];
            array_push($array, $array4);
        }    


        if ($comentario_id != "") {            
            $sql5="SELECT * FROM comentarios WHERE id_ejecucion = $comentario_id";
            $result5=mysqli_query($conexion, $sql5); 
            $comentarios = array();
            while($fila5=mysqli_fetch_array($result5)){
                $comentarios[] = array(
                    "id" => $fila5["id"],                                
                    "descripcion" => $fila5["descripcion"],  
                    "fecha" => $fila5["fecha"],                
                    "id_ejecucion" => $fila5["id_ejecucion"]
                );
            }                
            $array['comentarios'] = $comentarios;
        }



        $resultado=json_encode($array);
        echo $resultado;
        

           
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