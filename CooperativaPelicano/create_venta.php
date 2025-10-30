<?php
// create_venta.php

require_once 'config.php';

// Variables
$id_producto = $cantidad = $fecha = "";
$id_producto_err = $cantidad_err = $fecha_err = "";
$productos = []; // Para la lista desplegable de productos

// Obtener lista de productos con stock > 0
$sql_productos = "SELECT id, nombre, id_producto, stock FROM productos WHERE stock > 0 ORDER BY nombre";
if ($result = mysqli_query($link, $sql_productos)) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Almacenar también el stock actual para referencia
        $productos[] = $row;
    }
    mysqli_free_result($result);
}


// Procesa los datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 1. Validaciones de entrada
    $input_id_producto = trim($_POST["id_producto"]);
    if(empty($input_id_producto) || !is_numeric($input_id_producto)){
        $id_producto_err = "Selecciona un producto válido.";
    } else{
        $id_producto = $input_id_producto;
    }

    $input_cantidad = trim($_POST["cantidad"]);
    if(empty($input_cantidad) || !is_numeric($input_cantidad) || $input_cantidad <= 0 || filter_var($input_cantidad, FILTER_VALIDATE_INT) === false){
        $cantidad_err = "Ingresa una cantidad válida (entero positivo).";
    } else{
        $cantidad = (int)$input_cantidad;
    }
    
    $input_fecha = trim($_POST["fecha"]);
    if(empty($input_fecha)){
        $fecha_err = "Ingresa una fecha de venta.";
    } else{
        $fecha = $input_fecha;
    }


    // 2. Proceso de Venta (Verificar Stock y Actualizar)
    if(empty($id_producto_err) && empty($cantidad_err) && empty($fecha_err)){
        
        // Iniciar transacción para asegurar la consistencia de los datos (stock)
        mysqli_begin_transaction($link);
        $ejecucion_exitosa = true;

        try {
            // A. Consultar el stock actual del producto
            $sql_stock = "SELECT stock FROM productos WHERE id = ?";
            if($stmt_stock = mysqli_prepare($link, $sql_stock)){
                mysqli_stmt_bind_param($stmt_stock, "i", $param_id_producto);
                $param_id_producto = $id_producto;
                mysqli_stmt_execute($stmt_stock);
                $result_stock = mysqli_stmt_get_result($stmt_stock);
                
                if (mysqli_num_rows($result_stock) == 1) {
                    $row_stock = mysqli_fetch_assoc($result_stock);
                    $stock_actual = $row_stock['stock'];

                    // B. Verificar si hay stock suficiente
                    if ($stock_actual >= $cantidad) {
                        
                        // C. 1. Registrar la Venta
                        $sql_insert = "INSERT INTO ventas (id_producto, fecha, cantidad) VALUES (?, ?, ?)";
                        if($stmt_insert = mysqli_prepare($link, $sql_insert)){
                            mysqli_stmt_bind_param($stmt_insert, "isi", $param_id_producto_ins, $param_fecha, $param_cantidad);
                            $param_id_producto_ins = $id_producto;
                            $param_fecha = $fecha;
                            $param_cantidad = $cantidad;
                            
                            if (!mysqli_stmt_execute($stmt_insert)) {
                                $ejecucion_exitosa = false;
                            }
                            mysqli_stmt_close($stmt_insert);
                        } else {
                            $ejecucion_exitosa = false;
                        }

                        // C. 2. Actualizar el Stock
                        if ($ejecucion_exitosa) {
                            $sql_update = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                            if($stmt_update = mysqli_prepare($link, $sql_update)){
                                mysqli_stmt_bind_param($stmt_update, "ii", $param_cantidad_upd, $param_id_producto_upd);
                                $param_cantidad_upd = $cantidad;
                                $param_id_producto_upd = $id_producto;
                                
                                if (!mysqli_stmt_execute($stmt_update)) {
                                    $ejecucion_exitosa = false;
                                }
                                mysqli_stmt_close($stmt_update);
                            } else {
                                $ejecucion_exitosa = false;
                            }
                        }
                    } else {
                        // No hay stock suficiente, redirigir con error
                        mysqli_rollback($link); // Deshace cualquier posible operación
                        header("location: ventas.php?stock_error=insufficient");
                        exit();
                    }
                } else {
                    $ejecucion_exitosa = false; // Producto no encontrado
                }
                mysqli_stmt_close($stmt_stock);
            } else {
                $ejecucion_exitosa = false;
            }

            // 3. Confirmar o Deshacer la Transacción
            if ($ejecucion_exitosa) {
                mysqli_commit($link);
                header("location: ventas.php?status=success");
                exit();
            } else {
                mysqli_rollback($link);
                header("location: ventas.php?status=error");
                exit();
            }

        } catch (Exception $e) {
            mysqli_rollback($link);
            header("location: ventas.php?status=error");
            exit();
        }
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
                    <h2 class="mt-5">Registrar Nueva Venta</h2>
                    <p>Selecciona el producto, la cantidad y la fecha para registrar la venta.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Producto a Vender</label>
                            <select name="id_producto" class="form-control <?php echo (!empty($id_producto_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecciona un Producto (con stock)</option>
                                <?php foreach ($productos as $prod) : ?>
                                    <option value="<?php echo $prod['id']; ?>" <?php echo ($id_producto == $prod['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prod['nombre']) . " (Cód: " . $prod['id_producto'] . " - Stock: " . $prod['stock'] . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $id_producto_err;?></span>
                            <?php if (empty($productos)): ?>
                                <small class="form-text text-danger">No hay productos con stock para vender. Añade más en la sección de Productos.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Cantidad Vendida</label>
                            <input type="number" name="cantidad" min="1" class="form-control <?php echo (!empty($cantidad_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cantidad; ?>">
                            <span class="invalid-feedback"><?php echo $cantidad_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Venta</label>
                            <input type="date" name="fecha" class="form-control <?php echo (!empty($fecha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fecha ?: date('Y-m-d'); ?>">
                            <span class="invalid-feedback"><?php echo $fecha_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Registrar Venta" <?php echo empty($productos) ? 'disabled' : ''; ?>>
                        <a href="ventas.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>