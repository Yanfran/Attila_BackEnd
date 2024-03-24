<?php

include("./conexion/conexion.php");
$conexion=conexion();

$sql = "SELECT * FROM dashboard_mensajes";
$query = mysqli_query($conexion, $sql);

$result = mysqli_fetch_array($query);
$array[] = array(
    "mensaje_1" => $result['mensaje_1'],
    "mensaje_2" => $result['mensaje_2'],
);

echo json_encode($array);

?>