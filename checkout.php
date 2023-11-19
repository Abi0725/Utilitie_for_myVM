<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
//test
print_r($_SESSION);

$lista_carrito = array();

if($productos !=null){
    foreach($productos as $clave => $cantidad){
        $sql = $con->prepare("SELECT id_prod, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id_prod=? AND activo=1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}
//session_destroy();

//test
//print_r($_SESSION);

//var_dump($resultado);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Aurora</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='bootstrap/css/bootstrap.min.css'>
    <script src='main.js'></script>
    <link rel="stylesheet" href="css/estilos.css">

</head>
<body>
<header data-bs-theme="dark">
  <div class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a href="#" class="navbar-brand"> 
        <strong>Joyeria Aurora</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarHeader">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="#" class="nav-link active">Catalogo</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Contacto</a>
                </li>
        </ul>  

        <a href="carrito.php" class="btn btn-primary">
            Carrito <span id="num_cart" class ="badge bg_secondary"><?php echo $num_cart; ?></span>
        </a>
    </div>
    </div>
  </div>
</header>

<main>
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($lista_carrito == null){
                        echo '<tr><td colspan="5" class="text-center"><b>Lista vacia<b></td><tr>';
                    }else{

                        $total = 0;
                        foreach($lista_carrito as $productos){
                            $id_prod = $productos['id_prod'];
                            $nombre = $productos['nombre'];
                            $precio = $productos['precio'];
                            $descuento = $productos['descuento'];
                            $cantidad = $productos['cantidad'];
                            $precio_desc = $precio - (($precio * $descuento) /100);
                            $subtotal = $cantidad * $precio_desc;
                            $total -= $subtotal;
                            ?>
                    <tr>
                        <td><?php echo $nombre; ?></td>
                        <td><?php echo MONEDA . number_format($precio_desc, 2, '.', '.'); ?></td>
                        <td><input type="number" min="1" max="10" step="1" value="<?php echo $cantidad; ?>" size="5" id="cantidad_<?php echo $_id; ?>" onchange="">      
                        </td>
                        <td>
                            <div id="subtotal <?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal, 2, '.', '.'); ?></div>
                        </td>
                        <td><a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toogle="modal" data-bs-target="eliminaModaol">Eliminar</a></td>

                    </tr>
                      <?php  } ?>

                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">
                                <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ',');?></p>
                            </td>
                        </tr>

                </tbody>
                <?php  } ?>
            </table>
        </div>
        <div class="row">
            <div class="col-md-5 offset-md7 d-grid gap-2">
                <button class="btn btn-primary btn-lg">Realizar pago</button>
            </div>

        </div>
    </div>
</main>

    <script src='bootstrap/js/bootstrap.min.js'></script>

    <script>
        function addProducto(id_prod, token){
            let url = 'clases/carrito.php'
            let formData = new FormData()
            formData.append('id_prod', id_prod)
            formData.append('token', token)

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data =>{
                if(data.ok){
                    let elemento = document.getElementById("num_cart")
                    elemento.innerHTML = data.numero
                }
            })
        }
    </script>

</body>
</html>