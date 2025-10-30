<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

session_unset(); // Elimina todas las variables de sesión
session_destroy();

include 'includes/header.php'; // Se incluye para el diseño, aunque la sesión ya está destruida
?>

<h2>Cierre de Sesión</h2>

<p class="success">Has cerrado sesión correctamente. ¡Esperamos verte pronto!</p>

<p><a href="index.php">Volver a la página de inicio</a></p>

<?php

?>
</body>
</html>