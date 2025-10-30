<?php
// buy_product.php

require_once 'config.php';

// 1. CONFIGURACIÓN DEL DINERO BASE
// Podemos simular nuestro dinero como una variable estática para esta simple funcionalidad.
// ¡Empieza con 1000g (Golds)!
define('DINERO_BASE', 1000.00); 
$dinero_actual = DINERO_BASE; // Lo usaremos para el cálculo

// Función para obtener/actualizar un saldo base (en un proyecto real, esto vendría de una tabla de usuario)
function get_current_money() {
    // Aquí podrías leer el saldo desde una base de datos.
    // Para simplificar, devolvemos el valor fijo.
    return DINERO_BASE;
}

// 2. PROCESAMIENTO DE LA COMPRA
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $id_producto_interno = filter_var($_POST["id_producto_interno"], FILTER_VALIDATE_INT);
    $cantidad_comprar = filter_var($_POST["cantidad_comprar"], FILTER_VALIDATE_INT);
    $precio_unidad = filter_var($_POST["precio_unidad"], FILTER_VALIDATE_FLOAT);
    $fecha_venta = date('Y-m-d');
    
    // 3. VALIDACIONES BÁSICAS
    if (!$id_producto_interno || !$cantidad_comprar || $cantidad_comprar <= 0 || !$precio_unidad) {
        header("location: productos.php?status=error&msg=Datos_invalidos");
        exit();
    }

    $costo_total = $cantidad_comprar * $precio_unidad;
    $dinero_actual = get_current_money();

    if ($costo_total > $dinero_actual) {
        header("location: productos.php?status=error&msg=Dinero_insuficiente");
        exit();
    }
    
    // 4. INICIO DE LA TRANSACCIÓN (Stock y Registro de Venta)
    mysqli_begin_transaction($link);
    $ejecucion_exitosa = true;

    try {
        // A. Consultar el stock actual
        $sql_stock = "SELECT stock, nombre FROM productos WHERE id = ?";
        if($stmt_stock = mysqli_prepare($link, $sql_stock)){
            mysqli_stmt_bind_param($stmt_stock, "i", $id_producto_interno);
            mysqli_stmt_execute($stmt_stock);
            $result_stock = mysqli_stmt_get_result($stmt_stock);
            
            if (mysqli_num_rows($result_stock) == 1) {
                $row_stock = mysqli_fetch_assoc($result_stock);
                $stock_actual = $row_stock['stock'];
                $nombre_producto = $row_stock['nombre'];

                // B. Verificar Stock
                if ($stock_actual >= $cantidad_comprar) {
                    
                    // C. 1. Registrar la Venta (¡Nuestra Compra!)
                    $sql_insert = "INSERT INTO ventas (id_producto, fecha, cantidad) VALUES (?, ?, ?)";
                    if($stmt_insert = mysqli_prepare($link, $sql_insert)){
                        mysqli_stmt_bind_param($stmt_insert, "isi", $id_producto_interno_ins, $fecha_venta_ins, $cantidad_comprar_ins);
                        $id_producto_interno_ins = $id_producto_interno;
                        $fecha_venta_ins = $fecha_venta;
                        $cantidad_comprar_ins = $cantidad_comprar;
                        
                        if (!mysqli_stmt_execute($stmt_insert)) {
                            $ejecucion_exitosa = false;
                        }
                        mysqli_stmt_close($stmt_insert);
                    } else {
                        $ejecucion_exitosa = false;
                    }

                    // C. 2. Actualizar el Stock (Restar la cantidad comprada)
                    if ($ejecucion_exitosa) {
                        $sql_update = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                        if($stmt_update = mysqli_prepare($link, $sql_update)){
                            mysqli_stmt_bind_param($stmt_update, "ii", $cantidad_comprar_upd, $id_producto_interno_upd);
                            $cantidad_comprar_upd = $cantidad_comprar;
                            $id_producto_interno_upd = $id_producto_interno;
                            
                            if (!mysqli_stmt_execute($stmt_update)) {
                                $ejecucion_exitosa = false;
                            }
                            mysqli_stmt_close($stmt_update);
                        } else {
                            $ejecucion_exitosa = false;
                        }
                        
                        // NOTA: En un proyecto real, aquí también se restaría el costo total del saldo del usuario.
                    }
                } else {
                    // Stock insuficiente
                    mysqli_rollback($link); 
                    header("location: productos.php?status=error&msg=Stock_insuficiente&prod=" . urlencode($nombre_producto));
                    exit();
                }
            } else {
                $ejecucion_exitosa = false; // Producto no encontrado
            }
            mysqli_stmt_close($stmt_stock);
        } else {
            $ejecucion_exitosa = false;
        }

        // 5. FINALIZAR TRANSACCIÓN
        if ($ejecucion_exitosa) {
            mysqli_commit($link);
            header("location: productos.php?status=success&msg=Compra_exitosa&prod=" . urlencode($nombre_producto));
            exit();
        } else {
            mysqli_rollback($link);
            header("location: productos.php?status=error&msg=Error_transaccion");
            exit();
        }

    } catch (Exception $e) {
        mysqli_rollback($link);
        header("location: productos.php?status=error&msg=Excepcion_desconocida");
        exit();
    }
} else {
    header("location: productos.php");
    exit();
}

mysqli_close($link);
?>