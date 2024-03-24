<?php
  include("../conexion/conexion.php");
  $conexion = conexion();

  @$telefono = $_POST['telefono'];
  @$posicion = $_POST['posicion'];
  @$codigo_cliente = $_POST['codigo_cliente'];
  @$nombre = $_POST['nombre'];
  

  if (!empty($nombre)) {

        $sqlVerificar="SELECT nombre FROM clientes WHERE telefono = '$telefono'";                    
        $result2=mysqli_query($conexion, $sqlVerificar);
        $nombreCliente="";
        while($fila=mysqli_fetch_array($result2)){
          $nombreCliente=$fila[0];
        }                            

        if ($nombreCliente) {
          $sqlUpdate1 = "UPDATE clientes SET status = '1', posicion ='$posicion' WHERE telefono = '$telefono'";
          $resultadoUpdate1 = mysqli_query($conexion, $sqlUpdate1);  
        } else {
          $sqlUpdate1 = "UPDATE clientes SET nombre = '$nombre', status = '1', posicion ='$posicion' WHERE telefono = '$telefono'";
          $resultadoUpdate1 = mysqli_query($conexion, $sqlUpdate1);
        }           
                                                                                     

        if ($resultadoUpdate1) {
              
          $response = array(
            "result" => true,
            "msg" => "Nombre agregardo con exito."            
          );
        
        } else {
          $response = array(
            "result" => false,
            "msg" => "Error al actualizar la posición del teléfono $telefono."
          );
        }

    

    echo json_encode($response);
  } else {
    echo "Error: Debe ingregar un correo";
  }

  mysqli_close($conexion);
  return;

  // Función para formatear el número de teléfono eliminando espacios en blanco y caracteres no numéricos
  function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);
    return $phoneNumber;
  }
?>
