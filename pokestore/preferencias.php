<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mensaje = '';

// Parte 5: Procesar la elección de tema (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tema'])) {
    $tema_elegido = $_POST['tema'];

    // 6. Validación de datos: Comprobar que el tema es uno de los válidos [cite: 41]
    if ($tema_elegido === 'claro' || $tema_elegido === 'oscuro') {
        // Guardar la elección en una cookie llamada 'tema' que dure 7 días [cite: 35]
        $caducidad = time() + (7 * 24 * 60 * 60); // 7 días en segundos
        setcookie('tema', $tema_elegido, $caducidad, "/"); // "/" hace que esté disponible en toda la web

        $mensaje = '¡Tema actualizado a ' . htmlspecialchars($tema_elegido) . '! Recarga la página para verlo.';
    } else {
        $mensaje = 'Error: Tema seleccionado no válido.';
    }
}

include 'includes/header.php';

// Redirigir si no está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>

<h2>Preferencias de Usuario (Tema)</h2>

<?php if ($mensaje): ?>
    <p class="success"><?php echo htmlspecialchars($mensaje); ?></p>
<?php endif; ?>

<form action="preferencias.php" method="POST">
    <p>Elige tu tema favorito:</p>
    
    <input type="radio" id="tema_claro" name="tema" value="claro" 
           <?php echo (($_COOKIE['tema'] ?? 'claro') === 'claro') ? 'checked' : ''; ?>>
    <label for="tema_claro">Tema Claro</label><br>

    <input type="radio" id="tema_oscuro" name="tema" value="oscuro"
           <?php echo (($_COOKIE['tema'] ?? 'claro') === 'oscuro') ? 'checked' : ''; ?>>
    <label for="tema_oscuro">Tema Oscuro</label><br><br>

    <button type="submit">Guardar Preferencia</button>
</form>

</body>
</html>