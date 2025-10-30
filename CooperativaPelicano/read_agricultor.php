<?php
// read_agricultor.php

require_once 'config.php';

// Revisa si el parámetro 'id' de la URL existe antes de continuar
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Prepara una declaración de selección
    $sql = "SELECT * FROM agricultores WHERE id = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Vincula las variables a la declaración preparada como parámetros
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Establece los parámetros
        $param_id = trim($_GET["id"]);

        // Intenta ejecutar la declaración preparada
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                /* Extrae la fila de resultados como un array asociativo. Ya que el conjunto de resultados
                contiene solo una fila, no necesitamos usar un bucle while */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Recupera los valores de los campos
                $nombre = $row["nombre"];
                $granja = $row["granja"];
                $correo = $row["correo"];
            } else{
                // URL no contiene un id válido. Redirigir a la página de error 404
                header("location: error.php");
                exit();
            }
        } else{
            echo "Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }
    }

    // Cierra la declaración
    mysqli_stmt_close($stmt);

    // Cierra la conexión
    mysqli_close($link);
} else{
    // URL no contiene el parámetro id. Redirigir a la página de error 404
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AGRICULTORES</title>

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
                    <h2 class="mt-5 mb-3">Ver Detalles de Agricultor</h2>
                    <div class="form-group">
                        <label>Nombre</label>
                        <p><b><?php echo $nombre; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Granja</label>
                        <p><b><?php echo $granja; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <p><b><?php echo $correo; ?></b></p>
                    </div>
                    <p><a href="agricultores.php" class="btn btn-primary">Volver</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>