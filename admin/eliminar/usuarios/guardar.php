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
	// header("HTTP/1.0 404 Not authorized");
    return;
}else{

    try {
        $jwt=$token;
        $key=$secret_key_admin;
        $data = JWT::decode($jwt, $key, array('HS256'));

		$array = array("result"=>false,"msg"=>'');

		@$nacionalidad=$_POST['nacionalidad'];
		@$txtdocumento=$_POST['txtdocumento'];
		@$txtnombre=$_POST['txtnombre'];
		@$txtapellido=$_POST['txtapellido'];
		@$txtfecha=$_POST['txtfecha'];
		@$txttelefono=$_POST['txttelefono'];
		@$txtcorreo=$_POST['txtcorreo'];
		@$txtdireccion=$_POST['txtdireccion'];
		@$txtuser=$_POST['txtuser'];
		@$txtrol=$_POST['txtrol'];


		$clv=generador(10,true,true,true,false);
		$fecha= date('Y-m-d');
		if(empty($txtdocumento) || empty($nacionalidad) || empty($txtnombre) || empty($txtapellido) || empty($txtfecha) || empty($txttelefono) || empty($txtcorreo) || empty($txtdireccion) || empty($txtuser)  || empty($txtrol)){
			
			$array = array("result"=>false,"msg"=>'Debe llenar todos los campos');

			$resultado=json_encode($array);
			echo $resultado;
			return;

		}else{
			
			$sql="CALL ConsultarUsuarioNombre('$txtuser')";
			$query=mysqli_query($conexion,$sql);
			$fila=mysqli_fetch_array($query);
			if($fila){
				$array = array("result"=>false,"msg"=>'El usuario ingresado ya se encuentra registrado');

				$resultado=json_encode($array);
				echo $resultado;
				return;
			}
			$conexion = conexion();
			$sql_correo="CALL ConsultarUsuarioCorreo('$txtcorreo')";
			$query_correo=mysqli_query($conexion,$sql_correo);
			$fila=mysqli_fetch_array($query_correo);
			if($fila){
				$array = array("result"=>false,"msg"=>'El correo ya se encuentra registrado');

				$resultado=json_encode($array);
				echo $resultado;
				return;
			}
			$conexion = conexion();
			$sql_documento="CALL ConsultarUsuarioDocumento('$txtdocumento')";
			$query_correo=mysqli_query($conexion,$sql_documento);
			$fila=mysqli_fetch_array($query_correo);
			if($fila){
				$array = array("result"=>false,"msg"=>'El documento (DNI) pertenece a otro usuario');

				$resultado=json_encode($array);
				echo $resultado;
				return;
			}
			else{
				$conexion = conexion();
				$sql2="INSERT INTO usuario(nombre,clave,registro,id_rol,status) values('$txtuser','$clv','$fecha','$txtrol','1')";
				// SELECT * FROM usuario INNER JOIN detalle_usuario ON usuario.id = detalle_usuario.id_usuario WHERE detalle_usuario.correo = 'manueldelnogal@gmail.com1' or usuario.nombre = 'juan';
				$query2=mysqli_query($conexion,$sql2);
				if($query2){
					$sql="SELECT * FROM usuario WHERE nombre='$txtuser' and status='1'";
					$query=mysqli_query($conexion,$sql);
					$id_user="";
					while($fila=mysqli_fetch_array($query)){
						$id_user=$fila[0];
					}
						$sql3="INSERT INTO detalle_usuario(documento,nombre,apellido,fecha_nac,telefono,correo,direccion,nacionalidad,id_usuario) values('$txtdocumento','$txtnombre','$txtapellido','$txtfecha','$txttelefono','$txtcorreo','$txtdireccion','$nacionalidad','$id_user')";
						$query3=mysqli_query($conexion,$sql3);
					
					$array = array("result"=>true,"msg"=>'El usuario ha sido registrado con éxito');

					$resultado=json_encode($array);
					echo $resultado;
					return;
				}else{
					$array = array("result"=>false,"msg"=>'Error al guardar');

					$resultado=json_encode($array);
					echo $resultado;
					return;
				}
			}
		}

} catch (\Exception $e) { // Also tried JwtException
	// echo $e->getMessage();  
	$array = array("result"=>false,"msg"=>'No autorizado');
	$resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
	echo $resultado;
	// header("HTTP/1.0 404 Not authorized");
	return;
}

}


function generador($longitud,$letras_min,$letras_may,$numeros,$simbolos)
{
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

?>