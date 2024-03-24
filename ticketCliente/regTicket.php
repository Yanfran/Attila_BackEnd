<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$codigo_cliente = $_POST['codigo_cliente'];
  @$descripcion = $_POST['descripcion'];
  @$posicion = $_POST['posicion'];
  @$fecha_apertura= date("Y-m-d");
  $tipo_creacion = "whatsapp";

  // Formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  // $telefono = formatPhoneNumber($telefono);

  if (!empty($telefono)) {


    // $sqlTicket = "SELECT status FROM ticket WHERE telefono = '$telefono' and status != 'ENTREGADO' and status != 'CANCELADO'";
    // $resultSelect=mysqli_query($conexion, $sqlTicket);


    // if ($resultSelect && mysqli_num_rows($resultSelect) > 0) {
    //     $row = mysqli_fetch_assoc($resultSelect);
    //     $estatus = $row['status'];

    //     // var_dump($estatus);
    //     // return;

    //     if (
    //       $estatus == 'RECEPCION' ||
    //       $estatus === 'DIAGNOSTICO' || 
    //       $estatus === 'COTIZACION' || 
    //       $estatus === 'EJECUCION' || 
    //       $estatus === 'PENDIENTE' || 
    //       $estatus === 'ENTREGA'
    //       ) {

    //         $sqlUpdate2 = "UPDATE clientes SET posicion ='0' WHERE telefono = '$telefono'";
    //         $resultadoUpdate2 = mysqli_query($conexion, $sqlUpdate2);

    //         $response = array(
    //           "result" => false,
    //           "msg" => "Ups ya tiene un ticket creado. Debe esperar que se temine el proceso!",
    //           "ticketRes" => false            
    //         );

    //       echo json_encode($response);
    //       return;
    //     }
      
    // }


    $sqlInsert = "INSERT INTO ticket (telefono, codigo_cliente, fecha_apertura, status, tipo_creacion) 
        VALUES ('$telefono', '$codigo_cliente', '$fecha_apertura', 'RECEPCION', '$tipo_creacion')";
    $resultadoInsert = mysqli_query($conexion, $sqlInsert);


    $sql2="SELECT * FROM ticket WHERE codigo_cliente = '$codigo_cliente' and status = 'RECEPCION'";                    
    $result2=mysqli_query($conexion, $sql2);

    $id_user="";
    while($filaX=mysqli_fetch_array($result2)){
    $id_user=$filaX[0];
    }                    

    $sqlRecepcion="INSERT INTO recepcion (id_ticket, descripcion, status) VALUES ('$id_user', '$descripcion', '1')";
    $result3=mysqli_query($conexion, $sqlRecepcion);

    $sqlUpdate = "UPDATE clientes SET posicion ='$posicion' WHERE telefono = '$telefono'";
    $resultadoUpdate = mysqli_query($conexion, $sqlUpdate);



    if ($resultadoInsert) {
    // Obtener todos los datos registrados en la tabla ticket
    $sqlDatos = "SELECT * FROM ticket WHERE telefono = '$telefono'";
    $resultadoDatos = mysqli_query($conexion, $sqlDatos);

        if (mysqli_num_rows($resultadoDatos) > 0) {
          $fila = mysqli_fetch_assoc($resultadoDatos);
          $datosTicket = $fila;

          $response = array(
            "result" => true,
            "msg" => "Los datos se registraron correctamente.",
            "ticketRes" => $datosTicket
          );
        } else {
          $response = array(
          "result" => false,
          "msg" => "No se encontraron resultados para el teléfono $telefono después del registro."
          );
        }
    } else {
      $response = array(
      "result" => false,
      "msg" => "Error al registrar los datos para el teléfono $telefono."
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
