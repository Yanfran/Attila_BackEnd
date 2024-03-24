<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];      

  if (!empty($telefono)) {    
    $sql = "SELECT id FROM ticket WHERE telefono = '$telefono' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conexion, $sql);        

    if ($result && mysqli_num_rows($result) > 0) {
      $fila = mysqli_fetch_assoc($result);
      $id_ticket = $fila['id'];      

      $sql1 = "SELECT * FROM ejecucion WHERE status = '1' and id_ticket = '$id_ticket'";
      $result1 = mysqli_query($conexion, $sql1); 

      if (mysqli_num_rows($result1) > 0) {
        $fila1 = mysqli_fetch_assoc($result1);
        $sugerencia = $fila1;
  
        $response = array(
          "result" => true,
          "msg" => "Ok",
          "clienteRes" => $sugerencia
        );
      } else {
        $response = array(
          "result" => false,
          "msg" => "No se encontraron resultados para el teléfono $telefono después de la actualización."
        );
      }

    }     

    echo json_encode($response);
  } else {
    echo "Error: Teléfono vacío";
  }

  mysqli_close($conexion);
  return;
  
?>
