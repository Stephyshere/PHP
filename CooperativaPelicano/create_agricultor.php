<?php
// create_agricultor.php

require_once 'config.php';

// Define variables e inicializa con valores vacíos
$nombre = $granja = $correo = "";
$nombre_err = $granja_err = $correo_err = "";

// Procesa los datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
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
        // Prepara una declaración de inserción
        $sql = "INSERT INTO agricultores (nombre, granja, correo) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Vincula las variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "sss", $param_nombre, $param_granja, $param_correo);

            // Establece los parámetros
            $param_nombre = $nombre;
            $param_granja = $granja;
            $param_correo = $correo;

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
                    <h2 class="mt-5">Añadir Nuevo Agricultor</h2>
                    <p>Por favor, llena este formulario para añadir un nuevo agricultor.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                        <input type="submit" class="btn btn-primary" value="Enviar">
                        <a href="agricultores.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>