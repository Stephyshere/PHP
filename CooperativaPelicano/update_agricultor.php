<?php
// update_agricultor.php

require_once 'config.php';

// Define variables e inicializa con valores vacíos
$nombre = $granja = $correo = "";
$nombre_err = $granja_err = $correo_err = "";

// Procesa los datos del formulario cuando se envía
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Obtiene el valor del campo oculto de ID
    $id = $_POST["id"];

    // Valida nombre
    $input_nombre = trim($_POST["nombre"]);
    if(empty($input_nombre)){
        $nombre_err = "Por favor, ingresa el nombre del agricultor.";
    } elseif(!preg_match("/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/", $input_nombre)){
        $nombre_err = "Por favor, ingresa un nombre válido.";
    } else{
        $nombre = $input_nombre;
    }

    // Valida granja
    $input_granja = trim($_POST["granja"]);
    if(empty($input_granja)){
        $granja_err = "Por favor, ingresa el nombre de la granja.";
    } else{
        $granja = $input_granja;
    }

    // Valida correo electrónico
    $input_correo = trim($_POST["correo"]);
    if(empty($input_correo)){
        $correo_err = "Por favor, ingresa un correo electrónico.";
    } elseif(!filter_var($input_correo, FILTER_VALIDATE_EMAIL)){
        $correo_err = "Por favor, ingresa un formato de correo electrónico válido.";
    } else{
        $correo = $input_correo;
    }

    // Revisa los errores de entrada antes de insertar en la base de datos
    if(empty($nombre_err) && empty($granja_err) && empty($correo_err)){
        // Prepara una declaración de actualización
        $sql = "UPDATE agricultores SET nombre=?, granja=?, correo=? WHERE id=?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Vincula las variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "sssi", $param_nombre, $param_granja, $param_correo, $param_id);

            // Establece los parámetros
            $param_nombre = $nombre;
            $param_granja = $granja;
            $param_correo = $correo;
            $param_id = $id;

            // Intenta ejecutar la declaración preparada
            if(mysqli_stmt_execute($stmt)){
                // Redirige a la página de agricultores con mensaje de éxito
                header("location: agricultores.php?status=success");
                exit();
            } else{
                echo "Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }
        }

        // Cierra la declaración
        mysqli_stmt_close($stmt);
    }

    // Cierra la conexión
    mysqli_close($link);
} else {
    // Revisa si el parámetro 'id' de la URL existe antes de continuar
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Obtiene el parámetro de ID de la URL
        $id = trim($_GET["id"]);

        // Prepara una declaración de selección
        $sql = "SELECT * FROM agricultores WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Vincula las variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Establece los parámetros
            $param_id = $id;

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
    }  else{
        // URL no contiene el parámetro id. Redirigir a la página de error 404
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>UPDATE AGRICULTORES</title>

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
                    <h2 class="mt-5">Actualizar Registro de Agricultor</h2>
                    <p>Por favor, edita los valores y envía para actualizar el registro del agricultor.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre; ?>">
                            <span class="invalid-feedback"><?php echo $nombre_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Granja</label>
                            <input type="text" name="granja" class="form-control <?php echo (!empty($granja_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $granja; ?>">
                            <span class="invalid-feedback"><?php echo $granja_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Correo</label>
                            <input type="text" name="correo" class="form-control <?php echo (!empty($correo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $correo; ?>">
                            <span class="invalid-feedback"><?php echo $correo_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Actualizar">
                        <a href="agricultores.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
