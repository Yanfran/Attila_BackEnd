<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];

  // echo "el teléfono traido del bot es: $telefono";
  // return;
  // echo $telefono ;
  
  if (!empty($telefono)) {
  $sqlPosicion = "SELECT posicion FROM clientes WHERE telefono = '$telefono'";
  $resultado = mysqli_query($conexion, $sqlPosicion);

  if (mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $posicion = $fila['posicion'];
    // echo "La posición para el teléfono $telefono es: $posicion";

    // $rstldo = "La posición para el teléfono $telefono es: $posicion";
    $rstldo =  $posicion;

    $response = array(
      "result" => true,
      "msg" => "Numero no encontrado. Registrando...Registrado exitosamente.",
      "clienteRes" => $rstldo
  );

  echo json_encode($response);

  } else {


    $response = array(
      "result" => true,
      "msg" => "No se encontraron resultados para el telefono"
  );

  echo json_encode($response);
  }

  }else{
    echo "Error vacio";
  }





return;
mysqli_close($conexion);
?>
