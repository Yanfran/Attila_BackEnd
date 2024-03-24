<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$sugerencia = $_POST['sugerencia'];   

  // Formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  // $telefono = formatPhoneNumber($telefono);
  

  if (!empty($telefono)) {
    // Actualizar la posición a '1' (como cadena de caracteres)
    $sql = "UPDATE ticket SET sugerencia ='$sugerencia' WHERE telefono = '$telefono' and status != 'ENTREGADO' and status != 'CANCELADO'";
    $result = mysqli_query($conexion, $sql);

    if ($result) {      
      $response = array(
        "result" => false,
        "msg" => "Error al actualizar la posición del teléfono $telefono."
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
