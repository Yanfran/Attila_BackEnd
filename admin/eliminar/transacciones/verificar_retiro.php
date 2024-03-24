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

        @$referencia = $_POST['referencia'];
        @$img = $_FILES['img'];
        @$_id = $_POST['id'];

        if(empty($referencia) || empty($img) || empty($_id)){
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

        }else{

            $sql = "CALL ConsultarRetiroId('$_id')";
            $result = mysqli_query($conexion, $sql);
            $result = mysqli_fetch_array($result);

            if (!$result) {
                $array = array("result" => false, "msg" => 'No existe el retiro');
                $resultado = json_encode($array);
                echo $resultado;
                return;
            }

            if($result['status'] != 0){
                $array = array("result"=>false,"msg"=>'El deposito ya está modificado por el admin');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

            $monto = $result['monto'];
            $id_cliente = $result['id_cliente'];

            $conexion = conexion();

            $tipo_archivo = $img['type'];
            $nombre_archivo = $img['name'];
            $peso_archivo = $img['size'];

            if(!((strpos($tipo_archivo, 'jpg') || strpos($tipo_archivo, 'jpeg' )|| strpos($tipo_archivo, 'png')) && ($peso_archivo < 5000000))){
                
                $array = array("result"=>false,"msg"=>'La extensión no está permitida, solo se aceptan imagenes: "jpg", "jpeg", "png" menores a 5MB');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

            // Generar extension para la imagen
             $extension = explode(".", $nombre_archivo);
             $extension = $extension[count($extension) - 1];

            // Nombre de la imagen final
            $nombre_archivo = uniqid('screenshot_', true) .'.'. $extension;

            // Guardar la imagen y verificar
            if(!(move_uploaded_file($_FILES['img']['tmp_name'], $path . 'screenshots/'. $nombre_archivo))){
                $array = array("result"=>false,"msg"=>'Error al guardar la imagen');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }
            // Llamando el retiro de administrador
            $sql = "CALL ValidarRetiroAdmin('$referencia', '$nombre_archivo', '$_id')";
            $result = mysqli_query($conexion, $sql);

            if($result){

                // $sql_2 = "CALL RetirarSaldo('$id_cliente', $monto)";
                // $result_2 = mysqli_query($conexion, $sql_2);

                $array = array("result"=>true,"msg"=>'Realizado exitosamente');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }else{
                unlink($path.'screenshots/' . $nombre_archivo);
                $array = array("result"=>false,"msg"=>'Error al realizar el retiro');
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