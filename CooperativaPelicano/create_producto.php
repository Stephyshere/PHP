<?php
// create_producto.php

require_once 'config.php';

// Variables
$id_producto = $nombre = $tipo = $id_agricultor = "";
$precio = $stock = 0;
$id_producto_err = $nombre_err = $tipo_err = $precio_err = $stock_err = $id_agricultor_err = "";
$agricultores = []; // Para la lista desplegable

// Obtener lista de agricultores para el select
$sql_agricultores = "SELECT id, nombre FROM agricultores ORDER BY nombre";
if ($result = mysqli_query($link, $sql_agricultores)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $agricultores[] = $row;
    }
    mysqli_free_result($result);
}

// Procesa los datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 1. Validaciones
    $input_id_producto = trim($_POST["id_producto"]);
    if(empty($input_id_producto)){
        $id_producto_err = "Ingresa el ID único del producto.";
    } else{
        $id_producto = $input_id_producto;
    }

    $input_nombre = trim($_POST["nombre"]);
    if(empty($input_nombre)){
        $nombre_err = "Ingresa el nombre del producto.";
    } else{
        $nombre = $input_nombre;
    }

    $input_tipo = trim($_POST["tipo"]);
    if(empty($input_tipo)){
        $tipo_err = "Ingresa el tipo (Ej: Fruta, Verdura, Huevo).";
    } else{
        $tipo = $input_tipo;
    }

    $input_precio = trim($_POST["precio"]);
    if(empty($input_precio) || !is_numeric($input_precio) || $input_precio < 0){
        $precio_err = "Ingresa un precio válido (número positivo).";
    } else{
        $precio = $input_precio;
    }

    $input_stock = trim($_POST["stock"]);
    if(!is_numeric($input_stock) || $input_stock < 0 || filter_var($input_stock, FILTER_VALIDATE_INT) === false){
        $stock_err = "Ingresa un valor de stock válido (entero positivo o cero).";
    } else{
        $stock = (int)$input_stock;
    }
    
    $input_id_agricultor = trim($_POST["id_agricultor"]);
    if(empty($input_id_agricultor) || !is_numeric($input_id_agricultor)){
        $id_agricultor_err = "Selecciona un agricultor válido.";
    } else{
        $id_agricultor = $input_id_agricultor;
    }


    // 2. Insertar en DB si no hay errores
    if(empty($id_producto_err) && empty($nombre_err) && empty($tipo_err) && empty($precio_err) && empty($stock_err) && empty($id_agricultor_err)){
        $sql = "INSERT INTO productos (id_producto, nombre, tipo, precio, stock, id_agricultor) VALUES (?, ?, ?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssdii", $param_id_prod, $param_nombre, $param_tipo, $param_precio, $param_stock, $param_id_agricultor);

            $param_id_prod = $id_producto;
            $param_nombre = $nombre;
            $param_tipo = $tipo;
            $param_precio = $precio;
            $param_stock = $stock;
            $param_id_agricultor = $id_agricultor;

            if(mysqli_stmt_execute($stmt)){
                header("location: productos.php?status=success");
                exit();
            } else{
                // Manejo de error de clave duplicada (si id_producto ya existe)
                if (mysqli_errno($link) == 1062) {
                     $id_producto_err = "El ID de producto ya existe.";
                } else {
                     echo "Algo salió mal al insertar el producto. " . mysqli_error($link);
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Título de la Página</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Añadir Nuevo Producto</h2>
                    <p>Por favor, llena este formulario para añadir un nuevo producto.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>ID Producto (Único)</label>
                            <input type="text" name="id_producto" class="form-control <?php echo (!empty($id_producto_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $id_producto; ?>">
                            <span class="invalid-feedback"><?php echo $id_producto_err;?></span>
                        </div>
                         <div class="form-group">
                            <label>Agricultor</label>
                            <select name="id_agricultor" class="form-control <?php echo (!empty($id_agricultor_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecciona un Agricultor</option>
                                <?php foreach ($agricultores as $ag) : ?>
                                    <option value="<?php echo $ag['id']; ?>" <?php echo ($id_agricultor == $ag['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ag['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $id_agricultor_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre; ?>">
                            <span class="invalid-feedback"><?php echo $nombre_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Tipo</label>
                            <input type="text" name="tipo" class="form-control <?php echo (!empty($tipo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tipo; ?>">
                            <span class="invalid-feedback"><?php echo $tipo_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Precio (€)</label>
                            <input type="number" step="0.01" name="precio" class="form-control <?php echo (!empty($precio_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $precio; ?>">
                            <span class="invalid-feedback"><?php echo $precio_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Stock (Unidades)</label>
                            <input type="number" name="stock" class="form-control <?php echo (!empty($stock_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $stock; ?>">
                            <span class="invalid-feedback"><?php echo $stock_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Guardar Producto">
                        <a href="productos.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>