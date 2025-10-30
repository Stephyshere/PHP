<?php
// productos.php

require_once 'config.php';

// 1. CONFIGURACIÓN DEL DINERO BASE (Usado para mostrar el saldo al usuario)
// **NOTA:** Debe coincidir con la constante DINERO_BASE definida en buy_product.php
define('DINERO_BASE', 1000.00); 
$dinero_actual = DINERO_BASE; // Si implementas un sistema de usuarios real, esta variable se cargaría desde la DB.


// 2. MANEJO DE MENSAJES DE ESTADO (Incluye Compra y Errores CRUD)
$mensaje = '';
if (isset($_GET['status'])) {
    $msg_code = isset($_GET['msg']) ? $_GET['msg'] : '';
    $prod_name = isset($_GET['prod']) ? htmlspecialchars($_GET['prod']) : 'el producto';

    if ($_GET['status'] == 'success') {
        if ($msg_code == 'Compra_exitosa') {
            $mensaje = '<div class="alert alert-success">¡Compra de **' . $prod_name . '** registrada! El stock ha sido actualizado.</div>';
        } else {
            $mensaje = '<div class="alert alert-success">Operación realizada con éxito.</div>';
        }
    } elseif ($_GET['status'] == 'error') {
        // Manejo de errores de compra
        if ($msg_code == 'Dinero_insuficiente') {
            $mensaje = '<div class="alert alert-danger">ERROR de Compra: No tienes dinero suficiente para esta transacción (Tu saldo es: ' . number_format($dinero_actual, 2) . '€).</div>';
        } elseif ($msg_code == 'Stock_insuficiente') {
            $mensaje = '<div class="alert alert-danger">ERROR de Compra: **' . $prod_name . '** no tiene el stock solicitado.</div>';
        } else {
            // Errores genéricos (CRUD o transacciones fallidas)
            $mensaje = '<div class="alert alert-danger">Hubo un error en la operación (' . $msg_code . ').</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos | Cooperativa Pelícano</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <style>
        .wrapper{ max-width: 1000px; margin: 0 auto; }
        /* Aumentamos el ancho de la última columna para el formulario de compra */
        .table th:last-child, .table td:last-child { width: 220px; } 
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Inventario de Productos</h2>
                        <h5 class="pull-right" style="color: #3C6A2E; text-shadow: 1px 1px 0 #F7F1E5;">SALDO ACTUAL: **<?php echo number_format($dinero_actual, 2); ?>** €</h5>
                        <a href="create_producto.php" class="btn btn-success pull-right"> Añadir Nuevo Producto</a>
                    </div>
                    <?php echo $mensaje; ?>
                    <?php
                    // Intenta seleccionar todos los productos, incluyendo el nombre del agricultor
                    $sql = "SELECT p.*, a.nombre AS nombre_agricultor 
                            FROM productos p 
                            JOIN agricultores a ON p.id_agricultor = a.id
                            ORDER BY p.nombre";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>ID Producto</th>";
                                        echo "<th>Nombre</th>";
                                        echo "<th>Tipo</th>";
                                        echo "<th>Precio</th>";
                                        echo "<th>Stock</th>";
                                        echo "<th>Agricultor</th>";
                                        echo "<th>Gestión / Compra</th>"; echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['id_producto']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                        echo "<td>" . number_format($row['precio'], 2) . "€</td>";
                                        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nombre_agricultor']) . "</td>";
                                        echo "<td>";
                                            // 3.1. Enlaces CRUD (Gestión)
                                            echo '<div class="mb-2">';
                                            echo '<a href="read_producto.php?id='. $row['id'] .'" class="mr-3" title="Ver"><span class="fa fa-eye"></span></a>';
                                            echo '<a href="update_producto.php?id='. $row['id'] .'" class="mr-3" title="Actualizar"><span class="fa fa-pencil"></span></a>';
                                            echo '<a href="delete_producto.php?id='. $row['id'] .'" title="Eliminar"><span class="fa fa-trash"></span></a>';
                                            echo '</div>';
                                            
                                            echo '<hr style="margin: 5px 0; border-top: 1px dashed #B88B5F;">';
                                            
                                            // 3.2. Formulario de Compra Rápida
                                            echo '<form action="buy_product.php" method="post" style="display:flex; align-items:center; gap:5px;">';
                                                // Campos ocultos para pasar la lógica a buy_product.php
                                                echo '<input type="hidden" name="id_producto_interno" value="'. $row['id'] .'">';
                                                echo '<input type="hidden" name="precio_unidad" value="'. $row['precio'] .'">';
                                                
                                                // Input de cantidad. Min 1, Max el stock disponible.
                                                echo '<input type="number" name="cantidad_comprar" value="1" min="1" max="'. $row['stock'] .'" class="form-control" style="width: 70px; padding: 2px; height: 32px;">';
                                                
                                                // El botón solo aparece si hay stock
                                                if ($row['stock'] > 0) {
                                                    echo '<input type="submit" class="btn btn-primary btn-sm" value="Comprar">';
                                                } else {
                                                    echo '<span class="badge badge-danger" style="background-color: #B33939; color: #fff; padding: 5px 8px; border-radius: 3px;">SIN STOCK</span>';
                                                }
                                            echo '</form>';
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                            mysqli_free_result($result);
                        } else{
                            echo '<div class="alert alert-info">No se encontraron productos.</div>';
                        }
                    } else{
                        echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
                    }
                    mysqli_close($link);
                    ?>
                    <p><a href="index.php" class="btn btn-secondary mt-3">Volver al Inicio</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>