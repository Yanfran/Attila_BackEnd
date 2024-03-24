<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$codigo_cliente = $_POST['codigo_cliente'];

  // Formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  $telefono = formatPhoneNumber($telefono);

  if (!empty($codigo_cliente)) {
    $sqlSelect = "SELECT * FROM ticket WHERE codigo_cliente = '$codigo_cliente'";
    $resultadoSelect = mysqli_query($conexion, $sqlSelect);

    if (mysqli_num_rows($resultadoSelect) > 0) {
      $fila = mysqli_fetch_assoc($resultadoSelect);
      $datosTicket = $fila;

      $response = array(
        "result" => true,
        "msg" => "El código de cliente $codigo_cliente existe en la tabla ticket.",
        "ticketRes" => $datosTicket
      );
    } else {
      // $numero_aleatorio = generarNumeroAleatorio();
      // $sqlInsert = "INSERT INTO ticket (telefono, codigo_cliente) VALUES ('$telefono', '$numero_aleatorio')";
      // $resultadoInsert = mysqli_query($conexion, $sqlInsert);

      // if ($resultadoInsert) {
      //   $sqlDatos = "SELECT * FROM ticket WHERE codigo_cliente = '$numero_aleatorio'";
      //   $resultadoDatos = mysqli_query($conexion, $sqlDatos);

      //   if (mysqli_num_rows($resultadoDatos) > 0) {
      //     $fila = mysqli_fetch_assoc($resultadoDatos);
      //     $datosTicket = $fila;

      //     $response = array(
      //       "result" => true,
      //       "msg" => "Se generó un número aleatorio para el código de cliente $codigo_cliente y se registró en la tabla ticket.",
      //       "ticketRes" => $datosTicket
      //     );
      //   } else {
      //     $response = array(
      //       "result" => false,
      //       "msg" => "No se encontraron resultados después de insertar el número aleatorio para el código de cliente $codigo_cliente."
      //     );
      //   }
      // } else {
      //   $response = array(
      //     "result" => false,
      //     "msg" => "Error al insertar el número aleatorio para el código de cliente $codigo_cliente."
      //   );
      // }
      $response = array(
        "result" => false,
        "msg" => "El numero de folio enviado no es correcto"
      );
    }

    echo json_encode($response);
  } else {
    echo "Error: Código de cliente vacío";
  }

  mysqli_close($conexion);
  return;

  // Función para formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);
    return $phoneNumber;
  }

  // Función para generar un número aleatorio de entre 1 y 10 dígitos
  function generarNumeroAleatorio() {
    return strval(rand(1, pow(10, 10) - 1));
  }
?>
