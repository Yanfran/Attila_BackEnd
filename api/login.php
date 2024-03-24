<?php
include("../conexion/conexion.php");
$conexion = conexion();

$array = array("result" => false, "msg" => '');

@$codigo_empleado = $_POST['codigo_empleado'];
@$clave = $_POST['clave'];

$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.@";

for ($i = 0; $i < strlen($codigo_empleado); $i++) {
    if (strpos($permitidos, substr($codigo_empleado, $i, 1)) === false) {
        $array = array("result" => false, "msg" => 'El usuario ingresado contiene carácteres inválidos');
        $resultado = json_encode($array);
        echo $resultado;
        return;
    }
}

for ($i = 0; $i < strlen($clave); $i++) {
    if (strpos($permitidos, substr($clave, $i, 1)) === false) {
        $array = array("result" => false, "msg" => 'La contraseña ingresada contiene carácteres inválidos');
        $resultado = json_encode($array);
        echo $resultado;
        return;
    }
}

$sql = "SELECT * FROM empleados WHERE codigo_empleado='$codigo_empleado' AND clave='$clave'";
$con = mysqli_query($conexion, $sql);

if (!$con) {
    die("Error en la consulta: " . mysqli_error($conexion));
} else {
    $respuesta = array();

    $id_sede = "";
    while ($fila = mysqli_fetch_array($con)) {
        $respuesta = array(
            "id" => $fila['id'],
            "nombre" => $fila['nombre'],
            "codigo_empleado" => $fila['codigo_empleado'],
            "status" => $fila['status'],
            "id_sede" => $fila['id_sede'],
            "sede" => array() 
        );

        $id_sede = $fila['id_sede'];
    }


    // $respuesta2 = array();
    $sql2 = "SELECT * FROM sede WHERE id = $id_sede";
    $con = mysqli_query($conexion, $sql2);

     while ($filaSede = mysqli_fetch_array($con)) {
         $respuesta["sede"] = array(
            "id" => $filaSede['id'],
            "descripcion" => $filaSede['descripcion'],
            "latitud" => $filaSede['latitud'],
            "longitud" => $filaSede['longitud'],
            "status" => $filaSede['status']         
        );


        // array_push($respuesta, $respuesta2);
    }


    if ($respuesta) {        
        $resultado["result"] = true;
        $resultado["data"] = $respuesta;        
    } else {        
        $resultado = array("result" => false, "msg" => 'Credenciales incorrectas'); // Las credenciales no coinciden
    }

    echo json_encode($resultado);
    return;
}
?>