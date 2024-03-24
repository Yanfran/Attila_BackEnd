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

        @$cliente_id = $_POST['cliente_id'];
        @$paquete_id = $_POST['paquete_id'];
        @$monedero_id = $_POST['monedero_id'];

        if(empty($cliente_id) || empty($paquete_id) ||  empty($monedero_id)){
            $array = array("result"=>false,"msg"=>'Verique los datos del formulario');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $sql_1 = "CALL ConsultarClienteId($cliente_id)";
        $conexion=conexion();
        $result = mysqli_query($conexion, $sql_1);
        $result = mysqli_fetch_array($result);

        if(!$result){
            $array = array("result"=>false,"msg"=>'Error en el usuario');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $saldo = $result['saldo'];
        $conexion = conexion();

        $sql_2 = "CALL ConsultarPaqueteId($paquete_id)";
        $result = mysqli_query($conexion, $sql_2);
        $result = mysqli_fetch_array($result);

        if(!$result){
            $array = array("result"=>false,"msg"=>'El paquete no existe o no está disponible');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $nombre_paquete = $result['nombre'];
        $meses = $result['meses'];
        $porcentaje = $result['porcentaje'];

        $monto_total = $result['precio'];

        if($monto_total > $saldo ){
            $array = array("result"=>false,"msg"=>'Saldo insuficiente');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $fecha_actual = date('Y-m-d');

        $conexion = conexion();
        $sql_3 = "CALL ConsultaMonederoId($monedero_id)";
        $result_2 = mysqli_query($conexion, $sql_3);
        $result_2 = mysqli_fetch_array($result_2);
        
        if(!$result_2){
            $array = array("result"=>false,"msg"=>'El monedero no existe');
            
			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $descripcion = $result_2['nombre'];
        
        $conexion = conexion();
        $sql_4 = "CALL ClienteOrdenarPaquetes($paquete_id, $cliente_id, '$nombre_paquete', $monedero_id, '$descripcion', $monto_total, '$fecha_actual', $meses, $porcentaje)";
        $result_3 = mysqli_query($conexion, $sql_4);

        if(!$result_3){
            $array = array("result"=>false,"msg"=>'Error al ordenar');
            
            $resultado=json_encode($array);
            echo $resultado;
            return;
        }
        $conexion = conexion();
        $sql_5 = "CALL RetirarSaldo($cliente_id, $monto_total)";
        $result_4 = mysqli_query($conexion, $sql_5);
        
        // $conexion = conexion();
        // $sql_6 = "CALL OrdenDePaquetes($paquete_id)";
        // $result_5 = mysqli_query($conexion, $sql_6);

        if(!$result_4 ){
            $array = array("result"=>false,"msg"=>'El monedero no existe');
            
			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        $array = array("result"=>true,"msg"=>'¡Compra exitosa!');
            
		$resultado=json_encode($array);
		echo $resultado;
		return;

    } catch (\Exception $e) { // Also tried JwtException
        echo $e->getMessage();
        echo $e;
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;
    }

}
?>