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

        $id=$_POST['txtid'];
        $descripcion=$_POST['txtdescripcion'];
        $permi=$_POST['check_per'];

        $permisos = json_decode($permi);

        if(empty($descripcion) || empty($permisos) || empty($id)){

            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
			$resultado=json_encode($array);
			echo $resultado;
			return;

        }else{

            $sql="select * from roles where descripcion='$descripcion' and id!='$id' and status!='2'";
            $query=mysqli_query($conexion,$sql);
            $fila=mysqli_fetch_array($query);

            if(count($fila)>0){

                $array = array("result"=>false,"msg"=>'El nombre ingresado no se encuentra disponible');
                $resultado=json_encode($array);
                echo $resultado;
                return;

            }else{

                $sql2="update roles set descripcion='$descripcion' where id='$id'";
                $query2=mysqli_query($conexion,$sql2);

                if($query2){
                    
                    $sql_elim="DELETE FROM permisos WHERE id_rol='$id'";
                    $query_elim=mysqli_query($conexion,$sql_elim);

                    if($query_elim){
                        foreach($permisos as $valor){
                            $sql3="INSERT INTO permisos values('','$id','$valor')";
                            $query3=mysqli_query($conexion,$sql3);

                        }

                        $array = array("result"=>true,"msg"=>'Editado exitosamente');
                        $resultado=json_encode($array);
                        echo $resultado;
                        return;
                    }

                }else{

                    $array = array("result"=>false,"msg"=>'Error inesperado');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                }
            }
        }
        
    } catch (\Exception $e) {
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        //header("HTTP/1.0 404 Not authorized");
        return;        
    }
}
?>