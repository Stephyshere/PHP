<?php
// estadisticas.php

require_once 'config.php';

// Definición de Saldo (solo para consistencia en el encabezado si se desea)
define('DINERO_BASE', 1000.00); 
$dinero_actual = DINERO_BASE;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas | Cooperativa Pelícano</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <style>
        .wrapper{ max-width: 1200px; margin: 0 auto; }
        .stat-box {
            background-color: #fffaf0; /* Fondo claro para las cajas de estadísticas */
            border: 2px solid #b8860b;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 4px 4px 0px 0px #8B5C3B;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-4 text-center">Panel de Estadísticas de la Cooperativa</h2>
                    
                    <div class="stat-box">
                        <h3>Ventas Totales por Agricultor (Ingresos)</h3>
                        <?php
                        // Consulta usando JOIN para obtener el nombre del agricultor y GROUP BY para sumar las ventas
                        $sql_ventas = "SELECT a.nombre AS nombre_agricultor, 
                                            SUM(v.cantidad) AS cantidad_total_vendida,
                                            SUM(v.cantidad * p.precio) AS ingresos_totales
                                        FROM ventas v
                                        JOIN productos p ON v.id_producto = p.id
                                        JOIN agricultores a ON p.id_agricultor = a.id
                                        GROUP BY a.nombre
                                        ORDER BY ingresos_totales DESC";

                        if($result_ventas = mysqli_query($link, $sql_ventas)){
                            if(mysqli_num_rows($result_ventas) > 0){
                                echo '<table class="table table-bordered table-striped">';
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>Agricultor</th>";
                                            echo "<th>Items Vendidos (Unidades)</th>";
                                            echo "<th>Ingresos Totales (Gold)</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result_ventas)){
                                        echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['nombre_agricultor']) . "</td>";
                                            echo "<td>" . $row['cantidad_total_vendida'] . "</td>";
                                            echo "<td>" . number_format($row['ingresos_totales'], 2) . " €</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result_ventas);
                            } else{
                                echo '<div class="alert alert-info">Aún no hay ventas registradas.</div>';
                            }
                        } else{
                            echo "<div class='alert alert-danger'>ERROR al ejecutar la consulta de ventas.</div>";
                        }
                        ?>
                    </div>

                    <div class="stat-box">
                        <h3>Conteo de Productos por Tipo</h3>
                        <?php
                        // Consulta usando GROUP BY para contar el número de productos de cada tipo
                        $sql_tipos = "SELECT tipo, COUNT(id) AS total_productos
                                        FROM productos
                                        GROUP BY tipo
                                        ORDER BY total_productos DESC";

                        if($result_tipos = mysqli_query($link, $sql_tipos)){
                            if(mysqli_num_rows($result_tipos) > 0){
                                echo '<table class="table table-bordered table-striped">';
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>Tipo de Producto</th>";
                                            echo "<th>Cantidad de Productos Diferentes</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result_tipos)){
                                        echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                            echo "<td>" . $row['total_productos'] . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result_tipos);
                            } else{
                                echo '<div class="alert alert-info">No hay productos registrados en el inventario.</div>';
                            }
                        } else{
                            echo "<div class='alert alert-danger'>ERROR al ejecutar la consulta de tipos.</div>";
                        }
                        ?>
                    </div>
                    
                    <p class="text-center"><a href="index.php" class="btn btn-secondary mt-3">Volver al Inicio</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    // Cierra la conexión al final del script
    mysqli_close($link);
    ?>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>