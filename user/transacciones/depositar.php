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

        @$txtid_cliente=$_POST['txtid_cliente'];
        @$txtid_monedero=$_POST['txtid_monedero'];
        @$txtmonto=$_POST['txtmonto'];
        @$txtbilletera=$_POST['txtbilletera'];
        @$txtreferencia=$_POST['txtreferencia'];
        @$txtfecha=$_POST['txtfecha'];

        @$txtimg=$_FILES['txtimg'];

        // $txtimg = 'foto.jpg';

        if( empty($txtid_cliente) || empty($txtid_monedero) || empty($txtmonto) || empty($txtbilletera) || empty($txtreferencia) || empty($txtfecha) || empty($txtimg) ){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{
            $tipo_archivo = $txtimg['type'];
            $nombre_archivo = $txtimg['name'];
            $peso_archivo = $txtimg['size'];

            if(!((strpos($tipo_archivo, 'jpg') || strpos($tipo_archivo, 'jpeg' )|| strpos($tipo_archivo, 'png')) && ($peso_archivo < 5000000))){
                
                $array = array("result"=>false,"msg"=>'La extensión no está permitida, solo se aceptan imagenes: "jpg", "jpeg", "png" menores a 5MB');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }


            $sql="CALL ConsultarClienteId('$txtid_cliente')";
            $result=mysqli_query($conexion, $sql);

            
            $nombre_cliente = '';

            if ($result) {

                while($fila=mysqli_fetch_array($result)){
                    $nombre_cliente = $fila[1] . ' ' . $fila[2];
                }
                
            }else {
                echo "hola1";
                return;
            }



            $sql_2="CALL ConsultaMonederoId('$txtid_monedero')";
            $conexion=conexion();
            $result_2=mysqli_query($conexion, $sql_2);

            
            $descripcion_m = '';
            $hash_m = '';
            $red_m = '';

            if ($result_2) {
                
                while($fila=mysqli_fetch_array($result_2)){
                    $descripcion_m = $fila[1];
                    $hash_m = $fila[2];
                    $red_m = $fila[3];
                }
                
            }else {
                return;
            }
            
            $hoy = date("Y-m-d");

            // Generar extension para la imagen
            $extension = explode(".", $nombre_archivo);
            $extension = $extension[count($extension) - 1];

            // Nombre de la imagen final
            $nombre_archivo = uniqid('screenshot_', true) .'.'. $extension;
            
            // Guardar la imagen y verificar
            if(!(move_uploaded_file($_FILES['txtimg']['tmp_name'], $path . 'screenshots/'. $nombre_archivo))){
                $array = array("result"=>false,"msg"=>'Error al guardar la imagen');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

            $sql_3="CALL ClienteDeposito('$txtid_monedero', '$descripcion_m', '$hash_m', '$red_m', '$txtmonto', '$hoy', '$txtid_cliente', '$nombre_cliente', '$txtbilletera', '$txtreferencia', '$nombre_archivo', '$txtfecha')";
            $conexion=conexion();
            $result_3=mysqli_query($conexion, $sql_3);

            if($result_3){
                $array = array("result"=>true,"msg"=>'Realizado exitosamente');
                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $array = array("result"=>false,"msg"=>'Error en transacción');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

            

        }

           //echo json_encode($data);
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