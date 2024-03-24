<?php
include("../conexion/conexion.php");
$conexion = conexion();

@$telefono = $_POST['telefono'];

// echo $telefono ;


// Devuelve el array como una respuesta JSON
header('Content-Type: application/json');

if (!empty($telefono)) {
    // Consulta si existe el teléfono registrado en la tabla "clientes"
    $sqlNum = "SELECT * FROM clientes WHERE telefono = '$telefono'";
    $resultN = mysqli_query($conexion, $sqlNum);
    // echo "Here" ;
    if ($resultN) {
        // Verificar si se encontraron resultados con el teléfono proporcionado
        if (mysqli_num_rows($resultN) > 0) {
            $row = mysqli_fetch_assoc($resultN);

            $response = array(
                "result" => true,
                "msg" => "Numero encontrado SQL",
                "clienteRes" => $row
            );

            echo json_encode($response);
        } else {
            // Registrar el número de teléfono en la tabla "clientes"
            $sqlNumIn = "INSERT INTO clientes (telefono,posicion) VALUES ('$telefono','0')";
            $resultNI = mysqli_query($conexion, $sqlNumIn);

            if ($resultNI) {
                // Obtener los datos del cliente recién registrado
                $sqlNumReg = "SELECT * FROM clientes WHERE telefono = '$telefono'";
                $resultReg = mysqli_query($conexion, $sqlNumReg);

                if ($resultReg) {
                    $rowReg = mysqli_fetch_assoc($resultReg);

                    $response = array(
                        "result" => true,
                        "msg" => "Número no encontrado. Registrando...Registrado exitosamente.",
                        "cliente" => $rowReg
                    );

                    echo json_encode($response);
                } else {
                    $response = array(
                        "result" => false,
                        "msg" => "Error al obtener los datos del cliente registrado: " . mysqli_error($conexion)
                    );

                    echo json_encode($response);
                }
            } else {
                $response = array(
                    "result" => false,
                    "msg" => "Error al registrar el número: " . mysqli_error($conexion)
                );

                echo json_encode($response);
            }
        }
    } else {
        $response = array(
            "result" => false,
            "msg" => "Error en la consulta: " . mysqli_error($conexion)
        );

        echo json_encode($response);
    }
} else {
    $response = array(
        "result" => false,
        "msg" => "El número de teléfono está vacío"
    );

    echo json_encode($response);
}

mysqli_close($conexion);

return;
?>
