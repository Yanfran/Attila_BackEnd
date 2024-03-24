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

        @$img = $_FILES['img'];
        @$paquete_id = $_POST['paquete_id'];

        if(empty($img) || empty($paquete_id)){

            $array = array("result"=>false,"msg"=>'Verique los datos del formulario');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }else{



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
            $nombre_archivo = uniqid('paquete_', true) .'.'. $extension;

            // Guardar la imagen y verificar
            if(!(move_uploaded_file($_FILES['img']['tmp_name'],$path. 'paquetes_imagenes/'. $nombre_archivo))){
                $array = array("result"=>false,"msg"=>'Error al guardar la imagen');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }


            $sql="CALL ConsultarPaqueteId($paquete_id)";
            $conexion=conexion();
            $result=mysqli_query($conexion, $sql);
            $result = mysqli_fetch_array($result);

            if($result){
                if($result['img']){
                    unlink($path. 'paquetes_imagenes/' . $result['img']);
                }
            }else{
                $array = array("result"=>false,"msg"=>'El paquete no existe');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }

            $sql_2 = "CALL EditarImagenPaqueteAdmin('$paquete_id', '$nombre_archivo')";
            $conexion = conexion();
            $result_2 = mysqli_query($conexion, $sql_2);
            
            if($result_2){
                $array = array("result"=>true,"msg"=>'Imagen actualizada', 'img'=>$nombre_archivo);
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }else{
                $array = array("result"=>false,"msg"=>'Error al actualzar imagen');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }
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
