<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id_prod']) ? $_GET['id_prod'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

//Prueba de error
echo "ID: $id, Token: $token<br>";


if ($id == '' || $token == '') {
    echo 'Error al procesar la preticion';
    exit;
} else {
    $token_tmp = hash_hmac('sha256', $id, KEY_TOKEN);

    if($token == $token_tmp) {

        $sql = $con->prepare("SELECT count(id_prod) FROM productos WHERE id_prod=? AND activo=1");
        $sql->execute([$id]);

        if($sql->fetchcolumn() > 0){
            $sql = $con->prepare("SELECT nombre, descripcion, precio FROM productos WHERE id_prod=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = isset($row['descuento']) ? $row['descuento'] : 0;
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $dir_images = 'images/productos/' . $id . '/';

            $rutaImg = $dir_images . 'principal.jpg';

            if (!file_exists($rutaImg)) {
                $rutaImg = 'images/no-photo.jpg';
        }

        $imagenes = array();
        $dir = dir($dir_images);
        while (($archivo = $dir->read()) !== false) {
            if ($archivo !='principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg') )){
                $imagenes[] = $dir_images . $archivo;
            }
        }
        $dir->close();
    }
    }else {
        echo 'Error al procesar la petición';
        exit;
    }
}



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

        <a href="carrito.php" class="btn btn-primary">Carrito</a>
    </div>
    </div>
  </div>
</header>

<main>
    <div class="container">
        <div class="row">
            <div class = "col-md-6 order-md-1">

            <div id="carouselImages" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?php echo $rutaImg; ?>" class="d-block w-100" >
                </div>

                <?php foreach($imagenes as $img){ ?>
                <div class="carousel-item">
                <img src="<?php echo $img; ?>" class="d-block w-100" >                   
                
                </div>
                <?php } ?>
    
            </div>
                <a class="carousel-control-prev" href="#carouselImages" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselImages" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
            </a>
            </div>


                
            </div>
            <div class = "col-md-6 order-md-2">
                <h2><?php echo $nombre;?></h2>

                    <?php if($descuento > 0) { ?>
                        <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
                        <h2>
                            <?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?>
                            <small class="text-success"><?php echo $descuento; ?>% descuento</small>
                        </h2>

                        <?php } else {?>
                             <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>

                        <?php } ?>

                <p class="lead">
                    <?php echo $descripcion; ?>
                </p>

                <div class ="d-grid gap-3 col-10 mx-auto">
                    <button class="btn btn-primary" type="button">Comprar ahora</button>
                    <button class="btn btn-outline-primary" type="button">Agregar al carrito</button>
                </div>
            </div>
        </div>

    </div>
</main>

    <script src='bootstrap/js/bootstrap.min.js'></script>
</body>
</html>