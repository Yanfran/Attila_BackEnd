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

        @$descripcion=$_POST['descripcion'];

        if(empty($descripcion)){
            $array = array("result"=>false,"msg"=>'Debes realizar una busqueda');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        

        $sql = "SELECT t.id, t.codigo_cliente, t.telefono, t.cliente, t.fecha_apertura, t.fecha_cierre,
        t.tiempo_estimado, t.cotizacion, t.hora, t.sede, t.status, t.sugerencia, t.correo, t.observacion,
        t.entrega, c.nombre, sumaValoresRel1(t.id, t.tiempo_estimado, ifnull(t.sugerencia, '')) as 'calCulo', 
        sumaValoresRel2(t.id, t.cotizacion, ifnull(t.sugerencia, '')) as 'calCulo2'  
        FROM ticket t INNER JOIN clientes c ON t.telefono = c.telefono WHERE
        t.id LIKE '%$descripcion%' OR t.codigo_cliente LIKE '%$descripcion%' OR t.telefono LIKE '%$descripcion%
        ' OR t.cliente LIKE '%$descripcion%'";
        $result = mysqli_query($conexion, $sql);

        

                    

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
            
            
                             
        
        $resultado = json_encode($array);
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