<?php
// config.php

define('DB_SERVER', 'localhost'); // O la IP de tu servidor de base de datos
define('DB_USERNAME', 'root');   // Tu usuario de MySQL
define('DB_PASSWORD', '1234');       // Tu contraseña de MySQL
define('DB_NAME', 'cooperativa_pelicano'); // El nombre de tu base de datos (según la imagen)

/* Intenta conectar a la base de datos MySQL */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Revisa la conexión
if($link === false){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// Opcional: Establecer el conjunto de caracteres a UTF-8 para evitar problemas de codificación
mysqli_set_charset($link, "utf8");

?>