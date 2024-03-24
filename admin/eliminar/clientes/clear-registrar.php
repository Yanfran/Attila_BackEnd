<?php

include("../../conexion/conexion.php");
$conexion = conexion();

require_once '../../jwt/vendor/autoload.php';
use Firebase\JWT\JWT;

$headers = getallheaders();

@$token = getBearerToken($headers["Authorization"]);

if($token ==""){
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
        @$nombre=$_POST['nombre'];
        @$apellido=$_POST['apellido'];
        @$dni=$_POST['dni'];
        @$img=$_POST['img'];
        @$telefono=$_POST['telefono'];
        @$fecha_n=$_POST['fecha_n'];
        @$correo=$_POST['correo'];
        @$contrasena=$_POST['contrasena'];
        @$codigo_ref=$_POST['codigo_ref'];

        if(empty($nombre) || empty($apellido) || empty($dni) || empty($telefono) || empty($fecha_n) || empty($correo) || empty($contrasena)){
    
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
        
            $resultado=json_encode($array);
            echo $resultado;
            return;
        
        }else{
            $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.@";

            for ($i=0; $i<strlen($dni); $i++){ 
                if (strpos($permitidos, substr($dni,$i,1))===false){ 
                    $array = array("result"=>false,"msg"=>'El dni ingresado contiene carácteres inválidos');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                } 
            }

            for ($i=0; $i<strlen($contrasena); $i++){ 
                if (strpos($permitidos, substr($contrasena,$i,1))===false){ 
                    $array = array("result"=>false,"msg"=>'La contraseña ingresada contiene carácteres inválidos');
                    $resultado=json_encode($array);
                    echo $resultado;
                    return;
                } 
            }

            $sql="CALL validarCliente('$dni', '$correo')";
            if(!generar_query($conexion, $sql, false, 'El cliente ya se encuentra registrado')){
                $valid = true;

                $codigoGenerate = "";

                while ($valid) {

                    $codigoGenerate = generador(10,false,true,true,false);
                    $conexion=conexion();
                    $sql2="CALL validarCodigo('$codigoGenerate')";
                    $query2=mysqli_query($conexion,$sql2);
                    $fila2 = mysqli_fetch_array($query2);
                    
                    if(!$fila2){
                        $valid = false;
                    }

                }

                $conexion=conexion();
                $sql3 = "CALL registroClienteAdmin('$nombre', '$apellido', '$dni', '$img', '$telefono', '$fecha_n', '$correo', '$contrasena', '$codigoGenerate', '$codigo_ref')";
                    
                generar_query($conexion, $sql3, true,'Registrado');
                
            }
           
        }

    } catch (\Exception $e) { // Also tried JwtException
        //echo $e->getMessage();  
        $array = array("result"=>false,"msg"=>'No autorizado');
        $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
        echo $resultado;
        // header("HTTP/1.0 404 Not authorized");
        return;
    }
}

function generador($longitud,$letras_min,$letras_may,$numeros,$simbolos){
		//include "src/xampp/langsettings.php";
	// Requests allowed only from localhosz
	/*extract($_SERVER);
	$host = "127.0.0.1";
	$timeout = "1";

	if ($REMOTE_ADDR) {
		if ($REMOTE_ADDR != $host) {
			echo "<p><h2> FORBIDDEN FOR CLIENT $REMOTE_ADDR <h2></p>";
			exit;
		}
	}
	*/
	//Evaluamos [$variable?] si queremos letras minúsculas; Si sí agregamos la letras minúsculas
	// Si NO [:'';] , no agregamos nada.
	$variacteres = $letras_min?'abdefghijklmnopqrstuvwxyz':'';
	//Hacemos lo mismo para letras mayúsculas,numeros y simbolos
	$variacteres .= $letras_may?'ABDCEFGHIJKLMNOPQRSTUVWXYZ':'';
	$variacteres .= $numeros?'0123456789':''; //NOTA: En el tutorial puse mal esta variable debe ser -numeros- y no -numero-.
	$variacteres .= $simbolos?'!#$%&/()?¡¿':'';

	//Inicializamos variable $i y $clv
	$i = 0;
	$clv = '';

	//repetimos el codigo segun la longitud
	while($i<$longitud)
		{
			//Generamos un numero aleatorio
			$numrad = rand(0,strlen($variacteres)-1);
			//Sacamos el la letra al azar
			//La función -substr()- se compone de substr($variable,posición_inicio,longitud de sub cadena);
			$clv .= substr($variacteres,$numrad,1);
			//Aumentamos a $i en 1 cada que entramos al while
			$i++;
		}

		//Mostramos la cadena generada por medio de -echo-


	//$clave= md5($clv);
			/*
			$asunto="Recuperacion de contraseña";
			$desde="From: postmaster@localhost";
			$mensaje="Usuario: ".$usuario."\nContraseña provisional: ".$clv."\nSi usted no realizo esta operación comuniquese con el administrador del sistema";
						if (@mail($email, $asunto, $mensaje, $desde)) {
							echo "Enviado";
						} else {
							echo "Imposible";
						}
			*/


			return $clv;

}

function generar_query($conexion, $sql, $status, $mensaje){
    
    $query = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_array($query);

    if($fila){
        $array = array("result"=>$status,"msg"=>$mensaje);
        $resultado=json_encode($array);
        echo $resultado;
        return true;
    }else{
        return false;
    }
}