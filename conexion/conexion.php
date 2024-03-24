<?php
header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$secret_key="my_secret_key";
$secret_key_admin="my_secret_key";
$path = '/xampp/htdocs/ReferidosBack/';

function conexion(){
	
$conexion = mysqli_connect( "localhost", "root", "" ) or die ("No se ha podido conectar al servidor de Base de datos");

$db = mysqli_select_db($conexion, "referidos");	
mysqli_set_charset($conexion,"utf8");

return $conexion;
}
setlocale(LC_TIME, 'es_MX'); 
date_default_timezone_set('America/Mexico_City');

// setlocale(LC_TIME, 'es_VE'); 
// date_default_timezone_set('America/Caracas');



function getBearerToken($headers) {
    //$headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

?>
