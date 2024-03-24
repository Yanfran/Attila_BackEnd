<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$entregaBot = $_POST['entrega'];   
  @$codigo_cliente = $_POST['codigo_cliente'];

  if ($entregaBot == 1 ) {
    $entrega = "En persona";
  } else {
    $entrega = "Domicilio";
  }
  

  if (!empty($telefono)) {
    // Actualizar la posición a '1' (como cadena de caracteres)
    $sql = "UPDATE ticket SET entrega ='$entrega' WHERE telefono = '$telefono' and codigo_cliente = '$codigo_cliente'";
    $result = mysqli_query($conexion, $sql);

    if ($result) {      
      $response = array(
        "result" => false,
        "msg" => "Ok"
      );
    }

    echo json_encode($response);
  } else {
    echo "Error: Teléfono vacío";
  }

  mysqli_close($conexion);
  return;

  // Función para formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);
    return $phoneNumber;
  }
?>
