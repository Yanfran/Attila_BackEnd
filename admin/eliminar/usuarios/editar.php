<?php
include('../../conexion/conexion.php');
$conexion=conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

if($token==""){
    $array = array("result"=>false,"msg"=>'No autorizado');
    $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $resultado;
    // header("HTTP/1.0 404 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

        $array = array("result"=>false,"msg"=>'');

        $id=$_POST['idtxt'];
        $nacionalidad=$_POST['nacionalidad'];
        $txtdocumento=$_POST['txtdocumento'];
        $txtnombre=$_POST['txtnombre'];
        $txtapellido=$_POST['txtapellido'];
        $txtfecha=$_POST['txtfecha'];
        $txttelefono=$_POST['txttelefono'];
        $txtcorreo=$_POST['txtcorreo'];
        $txtdireccion=$_POST['txtdireccion'];
        $txtuser=$_POST['txtuser'];
        $txtrol=$_POST['txtrol'];

        $user_info = json_decode(json_encode($data), true);
        $id_admin = $user_info['data']['id'];


        if($id_admin != 1){
            $array = array("result"=>false,"msg"=>'Usted no tiene acceso a modificar esta información');

			$resultado=json_encode($array);
			echo $resultado;
			return;
        }

        /*detalle_usuario.nacionalidad=$nacionalidad,*/

            $sql="SELECT nombre FROM usuario WHERE nombre = '$txtuser' AND id != '$id' ";
			$query=mysqli_query($conexion,$sql);
			$fila=mysqli_fetch_array($query);
			if($fila){
				$array = array("result"=>false,"msg"=>'El usuario ingresado ya se encuentra registrado');

				$resultado=json_encode($array);
				echo $resultado;
				return;
			}

            $conexion = conexion();
            $sql="SELECT * FROM detalle_usuario WHERE correo='$txtcorreo' and id_usuario!='$id'";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);
            if($fila){
                $array = array("result"=>false,"msg"=>'El correo ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }
            $conexion = conexion();
            $sql="SELECT * FROM detalle_usuario WHERE documento=$txtdocumento and id_usuario!='$id'";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);
            if($fila){
                $array = array("result"=>false,"msg"=>'El DNI ya pertenece a otro usuario');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            }
            else{
            $conexion = conexion();
            $sql="UPDATE usuario INNER JOIN detalle_usuario on detalle_usuario.id_usuario SET detalle_usuario.documento='$txtdocumento',detalle_usuario.nombre='$txtnombre',detalle_usuario.apellido='$txtapellido',detalle_usuario.fecha_nac='$txtfecha',detalle_usuario.telefono='$txttelefono',detalle_usuario.correo='$txtcorreo',detalle_usuario.direccion='$txtdireccion',usuario.nombre='$txtuser', usuario.id_rol='$txtrol' WHERE usuario.id='$id' and detalle_usuario.id_usuario='$id'";
            $query=mysqli_query($conexion,$sql);
            if($query){
                $array = array("result"=>true,"msg"=>'El usuario ha sido actualizado con éxito');

                $resultado=json_encode($array);
                echo $resultado;
                return;
            }else{
                $array = array("result"=>false,"msg"=>'Error al actualizar');

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
    // header("HTTP/1.0 404 Not authorized");
    return;
}

}

?>