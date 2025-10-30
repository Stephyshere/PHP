<?php
// ventas.php

require_once 'config.php';

$mensaje = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $mensaje = '<div class="alert alert-success">Venta registrada con éxito. El stock ha sido actualizado.</div>';
    } elseif ($_GET['status'] == 'error') {
        $mensaje = '<div class="alert alert-danger">Error al registrar la venta.</div>';
    } elseif (isset($_GET['stock_error'])) {
        $mensaje = '<div class="alert alert-warning">ERROR: No hay stock suficiente para realizar la venta solicitada.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REGISTRO DE Ventas</title>

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
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Registro de Ventas</h2>
                        <a href="create_venta.php" class="btn btn-primary pull-right"> Registrar Nueva Venta</a>
                    </div>
                    <?php echo $mensaje; ?>
                    <?php
                    // Muestra el historial de ventas
                    $sql = "SELECT v.*, p.nombre AS nombre_producto, p.id_producto AS cod_producto 
                            FROM ventas v 
                            JOIN productos p ON v.id_producto = p.id
                            ORDER BY v.fecha DESC, v.id DESC";
                    
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>ID Venta</th>";
                                        echo "<th>Fecha</th>";
                                        echo "<th>Producto</th>";
                                        echo "<th>Cód. Producto</th>";
                                        echo "<th>Cantidad Vendida</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . date('d-m-Y', strtotime($row['fecha'])) . "</td>";
                                        echo "<td>" . $row['nombre_producto'] . "</td>";
                                        echo "<td>" . $row['cod_producto'] . "</td>";
                                        echo "<td>" . $row['cantidad'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                            mysqli_free_result($result);
                        } else{
                            echo '<div class="alert alert-info">No hay ventas registradas.</div>';
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
</body>
</html>