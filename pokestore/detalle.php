<?php
session_start();
require_once 'includes/header.php';

$cartas = [
    1 => ['nombre' => 'Eevee', 'tipo' => 'Normal', 'precio' => 10.00, 'descripcion' => 'Un Pokémon con el potencial de evolucionar en múltiples formas.'],
    2 => ['nombre' => 'Sylveon', 'tipo' => 'Hada', 'precio' => 18.50, 'descripcion' => 'Evolución de Eevee que usa sus lazos para desarmar a sus presas.'],
    3 => ['nombre' => 'Umbreon', 'tipo' => 'Siniestro', 'precio' => 22.00, 'descripcion' => 'Evolución de Eevee que brilla en la oscuridad. Ataca en la noche.'],
    4 => ['nombre' => 'Espeon', 'tipo' => 'Psíquico', 'precio' => 20.00, 'descripcion' => 'Evolución de Eevee que puede predecir el futuro con su pelaje.'],
    5 => ['nombre' => 'Mimikyu', 'tipo' => 'Fantasma/Hada', 'precio' => 35.00, 'descripcion' => 'Un Pokémon solitario que se esconde bajo un disfraz. No le gusta la luz.'],
    6 => ['nombre' => 'Gengar', 'tipo' => 'Fantasma/Veneno', 'precio' => 28.00, 'descripcion' => 'Un Pokémon sombra. Se dice que roba el calor de las personas.'],
];

$carta_id = null;
$carta = null;
$mensaje = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $carta_id = (int)$_GET['id'];
    if (array_key_exists($carta_id, $cartas)) {
        $carta = $cartas[$carta_id];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $carta !== null) {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    // Guardar una copia simple de la carta o solo la ID si es más complejo
    $_SESSION['carrito'][] = ['id' => $carta_id, 'nombre' => $carta['nombre'], 'precio' => $carta['precio']];
    $mensaje = 'Carta ' . htmlspecialchars($carta['nombre']) . ' añadida al carrito.';
}

if ($carta === null):
?>
    <h1>Error</h1>
    <p>ID de carta no válida o inexistente.</p>
    <p><a href="catalogo.php">Volver al Catálogo</a></p>
<?php else: ?>
    <h1>Detalles de la Carta: <?php echo htmlspecialchars($carta['nombre']); ?></h1>
    <?php if (!empty($mensaje)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>
    <ul>
        <li>Tipo: <?php echo htmlspecialchars($carta['tipo']); ?></li>
        <li>Precio: <?php echo htmlspecialchars(number_format($carta['precio'], 2)); ?>€</li>
        <li>Descripción: <?php echo htmlspecialchars($carta['descripcion']); ?></li>
    </ul>

    <h2>Añadir al Carrito</h2>
    <form method="POST">
        <button type="submit" name="add_to_cart" value="<?php echo htmlspecialchars($carta_id); ?>">Añadir <?php echo htmlspecialchars($carta['nombre']); ?> al Carrito</button>
    </form>

    <p><a href="catalogo.php">Volver al Catálogo</a></p>
<?php endif; ?>

</body>
</html>