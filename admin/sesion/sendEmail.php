<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

include("../../conexion/conexion.php");
$conexion = conexion();

require_once '../../jwt/vendor/autoload.php';
require_once '../../vendor/autoload.php';


use Firebase\JWT\JWT;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ... (otras partes del código)

$headers = getallheaders();
@$token = getBearerToken($headers["Authorization"]);

    // $email = "yanfranblancosalas@gmail.com";
    $nombre_usuario = "Usuario de ATTILA";

    $correo = $_POST['email'];

    
    $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.@";

    for ($i=0; $i<strlen($correo); $i++){ 
        if (strpos($permitidos, substr($correo,$i,1))===false){ 
            $array = array("result"=>false,"msg"=>'El correo ingresado contiene carácteres inválidos');
            $resultado=json_encode($array);
            echo $resultado;
            return;
        } 
    }   


    $mail = new PHPMailer(true);

    
    $sql1 = "SELECT nombre FROM usuario WHERE nombre = '$correo'";
    $query = mysqli_query($conexion, $sql1);

    if (!$query) {
        // Manejo de errores en la consulta SQL
        $array = array("result" => false, "msg" => 'Error en la consulta SQL');
        $resultado = json_encode($array);
        echo $resultado;
        return;
    }

    $fila = mysqli_fetch_array($query);

    if ($fila) {        
    

        try {
            
            $key = $secret_key_admin;

            // Genera un token para restablecimiento de contraseña con expiración de 60 segundos
            $tokenData = array(
                "correo" => $correo,
                "exp" => time() + 120, // Tiempo de expiración (60 segundos)
            );
            // $resetToken = JWT::encode($tokenData, $key, array('HS256'));
            $resetToken = JWT::encode($tokenData, $secret_key_admin, 'HS256');

            // Envía un correo electrónico al usuario
            $resetLink = 'http://187.188.105.205:8082/changePassword/' . $resetToken;
            


            // Configuración de PHPMailer
            // $mail = new PHPMailer(true);
            // $mail->isSMTP();
            // $mail->Host = 'tu_servidor_smtp';
            // $mail->SMTPAuth = true;
            // $mail->Username = 'tu_correo_electronico';
            // $mail->Password = 'tu_contraseña';
            // $mail->SMTPSecure = 'tls';
            // $mail->Port = 587;

            // Configuración de PHPMailer GMAIL
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vargashumjose@gmail.com'; // Tu dirección de correo de Gmail
            $mail->Password = 'akdo yjvx oikn nwum'; // Contraseña de tu cuenta de Gmail o contraseña de aplicación
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom($correo, 'ATTILA');
            $mail->addAddress($correo, $nombre_usuario);
            $mail->Subject = 'Restablecer clave';        

            $msg = '<html>
            <head>
                    <title>Invitation Email</title>
                    <style>
                        body {
                                background-color: #ffffff;
                        }
                        .logo{
                            background: url("http://187.188.105.205:8082/static/media/logo@2x.c3636e7065c2f853fa32.png");
                            width: 200px;
                            height: 70px;  
                            margin-left: 120px;
                            background-size: cover;
                            text-aling: center;                          
                        }
                        #content {
                                width: 550px;
                                height: 400px;                            
                                /*border: 1px solid black;*/
                                margin: 0 auto;
                        }
                        #dateTime {
                                margin-left: 65px;                                
                                font: 25px Impact;
                                position: relative;
                                top: 130px;
                                line-height: 50px;
                                text-transform: uppercase;
                        }



                        #enlace {          
                                margin-left: 20px;
                                position: relative;
                                top: 180px;                            
                                height: 90px;                            
                                font: 18px Helvetica, Arial, sans-serif;
                                letter-spacing: 1px;
                        }                     
                        
                        .boton{
                            font-size: 18px;
                            margin-left: 120px;
                        }
                        .boton a {
                            
                            background-color: #007bff; 
                            color: white; 
                            padding: 10px 20px; 
                            text-decoration: none; 
                            border-radius: 5px;
                        }
                    </style>
            </head>
            <body style="background-color: #ffffff;">                

                    <div id="content">
                        <div class="logo"></div>
                        &nbsp;
                        <div id="dateTime">
                                Restablecimiento de Contraseña<br/>                            
                        </div>    
                        <div id="enlace">Haz clic en el siguiente boton para restablecer tu contraseña:</div>               
                        
                        <p class="boton" style="font-size: 18px;">
                            <a href="' . $resetLink . '" 
                            style="">
                            Restablecer Contraseña
                            </a>
                        </p>                    
                    </div>
            </body>
            </html>';
            
            $mail->Body = $msg;
            $mail->IsHTML(true);                


            $mail->send();

            // Devuelve una respuesta indicando que se ha enviado el correo
            $array = array("result" => true, "msg" => 'Correo electrónico enviado con éxito');
            $resultado = json_encode($array);
            echo $resultado;
            return;
        } catch (\Exception $e) {
            // Manejo de errores al generar el token o enviar el correo
            $array = array("result" => false, "msg" => 'No se pudo enviar el correo electrónico');
            $resultado = json_encode($array);
            echo $resultado;
            return;
        }
    } else {
         // El correo no existe en la base de datos
        $array = array("result" => false, "msg" => 'El correo no está registrado');
        $resultado = json_encode($array);
        echo $resultado;
        return;
    }

    

?>