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
}
else{

    try {
        $jwt=$token;
        $key=$secret_key;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array();

        @$txtid=$_POST['txtid'];
        @$monedero_id = $_POST['monedero_id'];
        @$descripcion_m = $_POST['descripcion_m'];
        @$hash = $_POST['hash'];
        @$red = $_POST['red'];
        @$monto = $_POST['monto'];
        @$billetera = $_POST['billetera'];

        if( empty($txtid) || empty($monedero_id) || empty($monto) || empty($billetera) || empty($hash) || empty($red) || empty($descripcion_m)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{
            
            $sql="CALL ConsultarClienteId('$txtid')";
            $result=mysqli_query($conexion, $sql);
            $result = mysqli_fetch_array($result);
            while (mysqli_next_result($conexion)) {
                ;}

            if($result){

                $cliente_saldo = $result['saldo'];

                if($cliente_saldo < $monto){
                    $array = array("result"=>false,"msg"=>'Saldo insuficiente');

                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }

                $sql_3="CALL ConsultaMonederoId('$monedero_id')";
                $conexion=conexion();
                $result_3=mysqli_query($conexion, $sql_3);
    
                
                $descripcion_m = '';
                $hash_m = '';
                $red_m = '';
    
                if ($result_3) {
                    
                    while($fila=mysqli_fetch_array($result_3)){
                        $descripcion_m = $fila[1];
                        $hash_m = $fila[2];
                        $red_m = $fila[3];
                    }
                    
                }else {
                    return;
                }
                
                $hoy = date("Y-m-d");
                $nombre_cliente = $result['nombre'] . " " . $result['apellido'];

                $sql_2 = "CALL ClienteRetiro('$monedero_id', '$descripcion_m', '$hash', '$red', $monto, '$hoy','$txtid', '$nombre_cliente', '$billetera' )";

                $conexion=conexion();
                $result_2=mysqli_query($conexion, $sql_2);
                // $result_2 = mysqli_fetch_array($result_2);

                if($result_2){

                    $sql_3 = "CALL RetirarSaldo($txtid, $monto)";
                    $result_3 = mysqli_query($conexion, $sql_3);


                    $array = array("result"=>true,"msg"=>'Retiro solicitado con éxito');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }else{
                    $array = array("result"=>false,"msg"=>'Error al hacer rétiro');

                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }



            }else {
                $array = array("result"=>false,"msg"=>'El usuario no existe');

                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

        }

        //    echo json_encode($data);
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