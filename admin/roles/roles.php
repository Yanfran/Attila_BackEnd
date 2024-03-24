<?php
include("../../conexion/conexion.php");
$conexion=conexion();
$array = array();

$sql="select * from roles where id != 1 and status != 2";
$result=mysqli_query($conexion, $sql);

while($fila=mysqli_fetch_array($result)){
	$permisos = array();
	$sqlz="select a.id,a.id_permisologia,b.descripcion from permisos a inner join permisologia b on a.id_permisologia=b.id where a.id_rol='$fila[0]'";
	//echo $sqlz.'<br>';
	$resultz=mysqli_query($conexion, $sqlz);
	while($filaz=mysqli_fetch_array($resultz)){
		$permisos[] = array(
			"id_permiso" => $filaz[0],
	        "id_permisologia" => $filaz[1],
	        "descripcion" => $filaz[2]
	    );
    }

	$array[] = array(
        "id" => $fila[0],
        "descripcion" => $fila[1],
        "status" => $fila[2],
        "permisos" => $permisos
    );
}


$resultado=json_encode($array);

echo $resultado;
?>