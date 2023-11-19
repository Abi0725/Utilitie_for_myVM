<?php

require '../config/config.php';

if (isset($_POST['id_prod'])) {

    $id_prod = $_POST['id_prod'];
    $token = $_POST['token'];


    $token_tmp = hash_hmac('sha256', $id_prod, KEY_TOKEN);

    if($token == $token_tmp) {

        if(isset($_SESSION['carrito']['productos'][$id_prod])) {
            $_SESSION['carrito']['productos'][$id_prod] += 1;
        } else {
            $_SESSION['carrito']['productos'][$id_prod] = 1;
    }

    $datos['numero'] = count($_SESSION['carrito']['productos']);
    $datos['ok'] = true;

    }else{
        $datos['ok'] = false;
    }

} else {
    $datos['ok'] = false;
}

echo json_encode($datos);

?>