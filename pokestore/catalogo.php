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
?>

    <h1>Catálogo de Cartas Pokémon</h1>
    <?php if (isset($_SESSION['usuario'])): ?>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
    <?php endif; ?>
    <ul>
        <?php foreach ($cartas as $id => $carta): ?>
            <li>
                <?php echo htmlspecialchars($carta['nombre']); ?> (<?php echo htmlspecialchars($carta['tipo']); ?>) - <?php echo htmlspecialchars(number_format($carta['precio'], 2)); ?>€
                <a href="detalle.php?id=<?php echo htmlspecialchars($id); ?>">Ver Detalles</a>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>