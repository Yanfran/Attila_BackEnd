<?php

include("../conexion/conexion.php");
$conexion = conexion();

try {
    $array = array("result" => false, "msg" => '');

    
    // Verifica si se envió la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {



        // Obtiene la información de la imagen        
        $imagen = $_FILES['imagen'];
        $nombreUnico = uniqid() . "_" . $_FILES['imagen']['name'];
        $tipoImagen = $imagen['type'];
        $tamanioImagen = $imagen['size'];
        $tmpNombre = $imagen['tmp_name'];

        // Directorio donde se guardará la imagen
        $directorioDestino = "../assets"; // Reemplaza con tu directorio

        // Mueve la imagen al directorio de destino
        $rutaImagenDestino = $directorioDestino . "/" . $nombreUnico;
        move_uploaded_file($tmpNombre, $rutaImagenDestino);

        // Ahora puedes usar $rutaImagenDestino para guardar la ruta de la imagen en la base de datos o hacer cualquier otra acción necesaria.

        // Otros parámetros
        $latitud = $_POST['latitud'];
        $longitud = $_POST['longitud'];
        $nombre = $_POST['nombre'];
        $codigo_empleado = $_POST['codigoEmpleado'];
        $id_sede = $_POST['idsede'];
        $fechaHoraActual = date('Y-m-d');
        $hora=date('H:i:s');

         $sql="INSERT INTO asistencias (nombre_imagen, latitud, longitud, nombre, codigo_empleado, id_sede, fecha_registro, hora) VALUES ('$nombreUnico', '$latitud', '$longitud', '$nombre', '$codigo_empleado', $id_sede, '$fechaHoraActual', '$hora')";
                $conexion=conexion();
                $result=mysqli_query($conexion, $sql);            
                
                
                $array = array("result"=>true,"msg"=>'Creado exitosamente');
                $resultado=json_encode($array);
                echo $resultado;
                return;
        
    } else {
        // No se envió una imagen válida
        $array["msg"] = "No se envió una imagen válida.";
    }

    echo json_encode($array);
} catch (Exception $e) {
    // Manejo de errores generales aquí
    $array["msg"] = "Error en la solicitud: " . $e->getMessage();
    echo json_encode($array);
}
