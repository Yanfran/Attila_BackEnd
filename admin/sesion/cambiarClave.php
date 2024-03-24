<?php

include("../../conexion/conexion.php");
$conexion=conexion();


 

        $array = array();        
        @$clave=$_POST['clave'];       
        @$confirmar_clave=$_POST['confirmar_clave'];     
        @$correo = $_POST['correo'];

        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.@";

        for ($i=0; $i<strlen($clave); $i++){ 
            if (strpos($permitidos, substr($clave,$i,1))===false){ 
                $array = array("result"=>false,"msg"=>'La clave ingresado contiene carácteres inválidos');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            } 
        }
    
        for ($i=0; $i<strlen($confirmar_clave); $i++){ 
            if (strpos($permitidos, substr($confirmar_clave,$i,1))===false){ 
                $array = array("result"=>false,"msg"=>'La clave ingresada contiene carácteres inválidos');
                $resultado=json_encode($array);
                echo $resultado;
                return;
            } 
        }
        

        if(empty($clave) ||  empty($confirmar_clave)){			
            $array = array("result"=>false,"msg"=>'Debe llenar todos los campos');
            $resultado=json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $resultado;
            return;
        }
        
        $sql="UPDATE usuario SET clave='$clave' WHERE nombre = '$correo' ";
        $result=mysqli_query($conexion, $sql);
        
        if ($result) {
            $array = array("result"=>true,"msg"=>'Actualizado exitosamente');
            $resultado=json_encode($array);
            echo $resultado;
            return; 
        } else {
            $array = array("result"=>false,"msg"=>'Error al actualizar los datos');
            $resultado=json_encode($array);
            echo $resultado;
            return; 
        }
                                 

        // $resultado=json_encode($array);
        // echo $resultado;
                    

?>