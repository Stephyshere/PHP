<?php
// index.php

// Incluir el archivo de conexión a la base de datos
require_once 'config.php';

// Puedes incluir otros archivos de funciones o clases aquí
// require_once 'models/Agricultor.php'; // Si decides usar un enfoque más orientado a objetos

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INICIO</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2 class="mt-5 mb-3">Bienvenido a la Cooperativa Pelícano</h2>
        <p>Selecciona una opción para gestionar:</p>
        <div class="list-group">
            <a href="agricultores.php" class="list-group-item list-group-item-action">Gestión de Agricultores</a>
            <a href="productos.php" class="list-group-item list-group-item-action">Gestión de Productos</a>
            <a href="ventas.php" class="list-group-item list-group-item-action">Gestión de Ventas</a>
            <a href="ventas.php" class="list-group-item list-group-item-action">Gestión de Ventas</a>
            <a href="estadisticas.php" class="list-group-item list-group-item-action btn-primary">Panel de Estadísticas</a>
            </div>
    </div>
</body>
</html>