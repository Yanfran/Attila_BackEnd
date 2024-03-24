<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$posicion = $_POST['posicion'];

  // Formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  // $telefono = formatPhoneNumber($telefono);

  if (!empty($telefono)) {
    // Actualizar la posición a '1' (como cadena de caracteres)
    $sqlUpdate = "UPDATE clientes SET posicion ='$posicion', codigo_cliente ='' WHERE telefono = '$telefono'";
    $resultadoUpdate = mysqli_query($conexion, $sqlUpdate);

    if ($resultadoUpdate) {
      // Obtener todos los datos actualizados del número
      $sqlDatos = "SELECT * FROM clientes WHERE telefono = '$telefono'";
      $resultadoDatos = mysqli_query($conexion, $sqlDatos);

      if (mysqli_num_rows($resultadoDatos) > 0) {
        $fila = mysqli_fetch_assoc($resultadoDatos);
        $datosCliente = $fila;

        $response = array(
          "result" => true,
          "msg" => "La posición para el teléfono $telefono se actualizó correctamente.",
          "clienteRes" => $datosCliente
        );
      } else {
        $response = array(
          "result" => false,
          "msg" => "No se encontraron resultados para el teléfono $telefono después de la actualización."
        );
      }
    } else {
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
