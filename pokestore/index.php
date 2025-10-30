<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirigir directamente al catálogo si ya está logueado [cite: 18]
if (isset($_SESSION['usuario'])) {
    header('Location: catalogo.php');
    exit;
}

$error_login = '';

// Parte 1: Procesar el formulario de login (POST) [cite: 16]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 6. Validación de datos: Comprobar que existe el parámetro [cite: 41]
    if (isset($_POST['nombre_entrenador'])) {
        $nombre = trim($_POST['nombre_entrenador']);

        // 6. Validación de datos: Comprobar que el campo no esté vacío [cite: 19]
        if (!empty($nombre)) {
            // Guardar en la sesión [cite: 17]
            $_SESSION['usuario'] = $nombre;

            // Redirigir al catálogo [cite: 17]
            header('Location: catalogo.php');
            exit;
        } else {
            $error_login = '¡El nombre de entrenador no puede estar vacío!';
        }
    } else {
        $error_login = 'Faltan datos del formulario.';
    }
}

include 'includes/header.php';
?>

<h2>Inicio de Sesión</h2>

<?php if ($error_login): ?>
    <p class="error"><?php echo htmlspecialchars($error_login); ?></p>
<?php endif; ?>

<form action="index.php" method="POST">
    <label for="nombre_entrenador">Nombre de Entrenador Pokémon:</label>
    <input type="text" id="nombre_entrenador" name="nombre_entrenador" required>
    <button type="submit">Iniciar Sesión</button>
</form>

</body>
</html>
