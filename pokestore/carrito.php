<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mensaje = '';

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaciar_carrito'])) {
    $_SESSION['carrito'] = []; // Vaciar la variable de sesión
    $mensaje = '¡El carrito ha sido vaciado!';
}

$carrito = $_SESSION['carrito'];
$total_compra = 0;

include 'includes/header.php';

// Redirige para que haga el log
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>

<h2>Tu Carrito de Compra</h2>

<?php if ($mensaje): ?>
    <p class="success"><?php echo htmlspecialchars($mensaje); ?></p>
<?php endif; ?>

<?php if (!empty($carrito)): ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carrito as $item): ?>
                <?php
                $precio = is_numeric($item['precio']) ? $item['precio'] : 0;
                $total_compra += $precio;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($item['tipo']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($precio, 2)) . ' €'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>
    <h3>Total de la Compra: <?php echo htmlspecialchars(number_format($total_compra, 2)) . ' €'; ?></h3>

    <form action="carrito.php" method="POST">
        <button type="submit" name="vaciar_carrito">Vaciar Carrito</button>
    </form>
<?php else: ?>
    <p>El carrito de la compra está vacío. ¡Añade algunas cartas!</p>
<?php endif; ?>

</body>
</html>