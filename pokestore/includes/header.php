<?php
// Inicia la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$tema = $_COOKIE['tema'] ?? 'claro'; 
$clase_tema = ($tema === 'oscuro') ? 'dark-mode' : 'light-mode';

$usuario = $_SESSION['usuario'] ?? 'Invitado';

$total_carrito = count($_SESSION['carrito'] ?? []);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PokeStore</title>
    <style>
        .light-mode { background-color: #f0f0f0; color: #333; }
        .dark-mode { background-color: #333; color: #f0f0f0; }
        body { margin: 0; padding: 20px; }
        .menu { background-color: #555; padding: 10px; color: white; display: flex; justify-content: space-between; }
        .menu a { color: yellow; margin-right: 15px; text-decoration: none; }
        .menu span { margin-left: auto; }
        .card { border: 1px solid #ccc; margin-bottom: 10px; padding: 10px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body class="<?php echo htmlspecialchars($clase_tema); ?>">

    <div class="menu">
        <div>
            <a href="index.php">Inicio</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="carrito.php">Carrito (<?php echo $total_carrito; ?>)</a>
            <a href="preferencias.php">Preferencias</a>
        </div>
        <span>
            [cite_start]Hola, <?php echo htmlspecialchars($usuario);?>
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="cerrar_sesion.php">Cerrar Sesión</a>
            <?php endif; ?>
        </span>
    </div>

    <h1>PokeStore - La Tienda Pokémon</h1>
    <hr>