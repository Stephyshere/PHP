<?php
// delete_agricultor.php

// Procesar operación de eliminación después de la confirmación
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Incluir archivo de configuración
    require_once "config.php";

    // Prepara una declaración de eliminación
    $sql = "DELETE FROM agricultores WHERE id = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Vincula las variables a la declaración preparada como parámetros
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Establece los parámetros
        $param_id = trim($_POST["id"]);

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

    // Cierra la conexión
    mysqli_close($link);
} else{
    // Revisa si el parámetro id existe
    if(empty(trim($_GET["id"]))){
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
                    <h2 class="mt-5 mb-3">Eliminar Registro</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>¿Estás seguro de que quieres eliminar este registro de agricultor?</p>
                            <p>
                                <input type="submit" value="Sí, eliminar" class="btn btn-danger">
                                <a href="agricultores.php" class="btn btn-secondary ml-2">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>