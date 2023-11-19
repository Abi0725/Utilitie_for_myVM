<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id_prod, nombre, precio FROM productos WHERE activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

//session_destroy();

//test
print_r($_SESSION);

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

        <a href="checkout.php" class="btn btn-primary">
            Carrito <span id="num_cart" class ="badge bg_secondary"><?php echo $num_cart; ?></span>
        </a>
    </div>
    </div>
  </div>
</header>

<main>
    <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <?php foreach($resultado as $row) { ?>    
    <div class="col">
          <div class="card shadow-sm">
            <?php 
            $id = $row["id_prod"];
            $imagen = "images/productos/" . $id . "/principal.jpg";
           
            if(!file_exists($imagen)) {
                $imagen = "images/no-photo.jpg";
            }

            ?>

            <img src="<?php echo $imagen;?>">
            <div class="card-body">
              <h5 class="card-tittle"><?php echo $row['nombre']; ?></h5>
                <p class="card-text">$ <?php echo number_format($row['precio'], 2 , '.',','); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                <a href="detalles.php?id_prod=<?php echo $row['id_prod']; ?>&token=<?php echo hash_hmac('sha256', $row['id_prod'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>

                </div>
                <button class="btn btn-outline-success" type="button" onclick="addProducto(<?php echo $row['id_prod'];?>, '<?php echo hash_hmac('sha256', $row['id_prod'], KEY_TOKEN); ?>')">Agregar al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
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