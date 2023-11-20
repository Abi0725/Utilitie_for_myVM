<?php

require '../config/config.php';
require '../config/database.php';

if(isset($_POST['action'])){

    $action = $_POST['action'];
    $id = isset($_POST['id_prod']) ? $_POST['id_prod'] :0;   

    if($action == 'agregar'){
        $cantidad = isset($_POST['id_prod']) ? $_POST['id_prod'] :0;  
    }

}

function agregar($id_prod, $cantidad){

    $res = 0;
    if($id_prod > 0 && $cantidad > 0 && is_numeric(($cantidad))){
        if(isset($_SESSION['carrito']['producto'][$id_prod])){
            $_SESSION['carrito']['producto'][$id_prod] = $cantidad;
        
            $db = new Database();
            $con = $db-> conectar();

            $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id_prod=? AND activo=1 LIMIT 1");
            $sql->execute([$id_prod]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $res = $cantidad = $precio_desc;

            return $res;
        }
    }else {
        return $res; 
    }

}

?>