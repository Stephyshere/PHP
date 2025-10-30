<?php
// Array para los lubros
$libros = [
    'Alas de Sangre' => [               //recomendadisimo si te gusta leer fantasia romantica con un poco de tono politico
        'autor' => 'Rebecca Yarros',
        'año' => 2023,
        'disponible' => true, // Disponible
        'juvenil' => false 
    ],
    'Una corte de rosas y espinas' => [         //es el principio de mi saga favorita, tambien fantasia romantica(es el unico genero que leo jaja)
        'autor' => 'Sarah J. Maas',
        'año' => 2015,
        'disponible' => false, // Prestado inicialmente
        'juvenil' => false
    ],
    'Una corte de niebla y furia' => [      
        'autor' => 'Sarah J. Maas',
        'año' => 2016,
        'disponible' => true,
        'juvenil' => false
    ],
    'Crepúsculo' => [           //un clásico
        'autor' => 'Stephenie Meyer',
        'año' => 2005,
        'disponible' => false,
        'juvenil' => true // Clasificado como juvenil para la prueba de edad
    ]
];

// Array numérico para usuarios. 
$usuarios = [
    [
        'nombre' => 'Ana',
        'edad' => 25,
        'prestados' => ['Una corte de rosas y espinas'] // Un libro prestado inicialmente
    ],
    [
        'nombre' => 'Carlos',
        'edad' => 16, // Menor de 18 años para probar restricción (solo juveniles)
        'prestados' => []
    ],
    [
        'nombre' => 'Bea',
        'edad' => 30,
        // Más de 3 préstamos para probar la restricción de límite
        'prestados' => ['Libro A', 'Libro B', 'Libro C', 'Libro D'] 
    ]
];


// A. Registrar Nuevo Libro (mediante variables)
$nuevo_titulo = 'La Casa de Papel';
$nuevo_autor = 'Xavier Alonso';
$nuevo_año = 2024;

// Añadiendo el nuevo libro al array asociativo
$libros[$nuevo_titulo] = [
    'autor' => $nuevo_autor,
    'año' => $nuevo_año,
    'disponible' => true,
    'juvenil' => false
];

// B. Registrar Nuevo Usuario (mediante variables)
$nuevo_nombre = 'David';
$nueva_edad = 40;

// Añadiendo el nuevo usuario al array numérico
$usuarios[] = [
    'nombre' => $nuevo_nombre,
    'edad' => $nueva_edad,
    'prestados' => []
];


/**
 * Función 1: Calcula el número total de libros.
 * @param array $libros El array de libros.
 * @return int El número total de libros.
 */
function calcularTotalLibros($libros) {
    return count($libros);
}

/**
 * Función 2: Calcula el porcentaje de libros prestados.
 * @param array $libros El array de libros.
 * @return float El porcentaje de libros prestados.
 */
function calcularPorcentajePrestados($libros) {
    $total = calcularTotalLibros($libros);
    $prestados = 0;

    foreach ($libros as $libro) { // Uso de bucle foreach
        if (!$libro['disponible']) {
            $prestados++;
        }
    }
    
    // Uso del operador ternario para evitar división por cero
    return ($total > 0) ? ($prestados / $total) * 100 : 0.0; 
}

/**
 * Función 3: Identifica al usuario con más libros en préstamo.
 * @param array $usuarios El array de usuarios.
 * @return string El nombre del usuario o 'Nadie' si no hay préstamos.
 */
function usuarioConMasPrestamos($usuarios) {
    $max_prestamos = -1;
    $nombre_usuario = 'Nadie';

    foreach ($usuarios as $usuario) {
        $num_prestamos = count($usuario['prestados']);
        
        if ($num_prestamos > $max_prestamos) { 
            $max_prestamos = $num_prestamos;
            $nombre_usuario = $usuario['nombre'];
        }
    }
    
    return $nombre_usuario;
}


/**
 * Implementa el préstamo de un libro.
 */
function prestarLibro($titulo, $nombre_usuario, &$libros, &$usuarios) {
    if (!isset($libros[$titulo])) return "Error: El libro '$titulo' no existe.";
    if (!$libros[$titulo]['disponible']) return "Error: El libro '$titulo' ya está prestado.";

    // Buscar al usuario por nombre (Uso de bucle for)
    $indice_usuario = -1;
    $num_usuarios = count($usuarios);
    for ($i = 0; $i < $num_usuarios; $i++) { 
        if ($usuarios[$i]['nombre'] === $nombre_usuario) {
            $indice_usuario = $i;
            break;
        }
    }

    if ($indice_usuario === -1) return "Error: El usuario '$nombre_usuario' no existe.";
    
    $usuario = &$usuarios[$indice_usuario]; 

    // Condicional anidado y restricciones
    $prestamos_actuales = count($usuario['prestados']);

    // Primer condicional: Límite de 3 préstamos
    if ($prestamos_actuales >= 3) { 
        return "Advertencia: El usuario {$nombre_usuario} tiene {$prestamos_actuales} libros. No puede tomar más libros.";
    } else {
        // Condicional anidado: Restricción de edad
        if ($usuario['edad'] < 18) {
            if (!$libros[$titulo]['juvenil']) { // Condición anidada
                return "Advertencia: {$nombre_usuario} es menor de 18 años y solo puede pedir libros juveniles. No se pudo prestar '$titulo'.";
            }
        }
        
        // Implementar el préstamo
        $libros[$titulo]['disponible'] = false; 
        $usuario['prestados'][] = $titulo;     
        return "Éxito: El libro '$titulo' ha sido prestado a {$nombre_usuario}.";
    }
}


/**
 * Implementa la devolución de un libro.
 */
function devolverLibro($titulo, $nombre_usuario, &$libros, &$usuarios) {
    if (!isset($libros[$titulo])) return "Error: El libro '$titulo' no existe.";

    // Buscar al usuario por nombre
    $indice_usuario = -1;
    foreach ($usuarios as $key => $user) {
        if ($user['nombre'] === $nombre_usuario) {
            $indice_usuario = $key;
            break;
        }
    }

    if ($indice_usuario === -1) return "Error: El usuario '$nombre_usuario' no existe.";
    
    $usuario = &$usuarios[$indice_usuario];
    
    // Verificar si el usuario tiene el libro y devolver
    $indice_prestado = array_search($titulo, $usuario['prestados']);

    if ($indice_prestado !== false) {
        // Implementar la devolución
        $libros[$titulo]['disponible'] = true;       
        unset($usuario['prestados'][$indice_prestado]); 
        $usuario['prestados'] = array_values($usuario['prestados']); 
        return "Éxito: El libro '$titulo' ha sido devuelto por {$nombre_usuario}.";
    } else {
        return "Error: El usuario {$nombre_usuario} no tiene prestado el libro '$titulo'.";
    }
}


// Ejecución de pruebas para mostrar la lógica
$pruebas = [];
$pruebas[] = prestarLibro('Alas de Sangre', 'Ana', $libros, $usuarios);            // OK (Ana)
$pruebas[] = prestarLibro('La Casa de Papel', 'Carlos', $libros, $usuarios);       // Bloqueo por edad (no juvenil)
$pruebas[] = prestarLibro('Una corte de niebla y furia', 'Bea', $libros, $usuarios); // Bloqueo por límite de 3 préstamos
$pruebas[] = prestarLibro('Crepúsculo', 'Carlos', $libros, $usuarios);            // OK (Carlos, es juvenil)
$pruebas[] = devolverLibro('Una corte de rosas y espinas', 'Ana', $libros, $usuarios); // Devolución OK
$pruebas[] = devolverLibro('Libro A', 'Bea', $libros, $usuarios);                        // Devolución OK (reduce el límite de Bea)


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Biblioteca</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #2A68B0; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .disponible { color: green; font-weight: bold; }
        .prestado { color: red; font-weight: bold; }
        .log-ok { color: darkgreen; }
        .log-alert { color: orange; }
    </style>
</head>
<body>

    <h1>Gestor de Biblioteca Sencillo</h1>

    <div style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 20px;">
        <h2>Resultados de las Pruebas de Lógica</h2>
        <?php foreach ($pruebas as $log): ?>
            <p class="<?= (strpos($log, 'Éxito') !== false) ? 'log-ok' : 'log-alert' ?>"><?= htmlspecialchars($log) ?></p>
        <?php endforeach; ?>
    </div>

    <h2>1. Listado de Usuarios con Préstamos</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Libros Prestados</th>
                <th>Estado (Ejemplo Switch)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['edad']) ?></td>
                <td>
                    <?php if (empty($usuario['prestados'])): ?>
                        *Ninguno*
                    <?php else: ?>
                        <?= implode(', ', array_map('htmlspecialchars', $usuario['prestados'])) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    // Estructura switch
                    $estado = count($usuario['prestados']);
                    
                    switch (true) {
                        case $estado >= 3:
                            echo '<span class="prestado">MÁX. PRÉSTAMOS ('.$estado.')</span>';
                            break;
                        case $usuario['edad'] < 18:
                            echo '<span class="log-alert">Menor de edad (Solo Juveniles)</span>';
                            break;
                        default:
                            echo 'Sin restricciones';
                            break;
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>2. Listado de Libros y Disponibilidad</h2>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Disponibilidad</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        // Uso de bucle while
        $titulos = array_keys($libros);
        $i = 0;
        $num_libros = count($libros);
        while ($i < $num_libros) { 
            $titulo = $titulos[$i];
            $libro = $libros[$titulo];
        ?>
            <tr>
                <td><?= htmlspecialchars($titulo) ?></td>
                <td><?= htmlspecialchars($libro['autor']) ?></td>
                <td><?= htmlspecialchars($libro['año']) ?></td>
                <td>
                    <?php
                    if ($libro['disponible']) {
                        echo '<span class="disponible">Disponible</span>';
                    } else {
                        $nombre_prestado_a = 'Usuario Desconocido';
                        foreach ($usuarios as $usuario) { // Bucle anidado para búsqueda
                            if (in_array($titulo, $usuario['prestados'])) {
                                $nombre_prestado_a = $usuario['nombre'];
                                break;
                            }
                        }
                        echo '<span class="prestado">Prestado a ' . htmlspecialchars($nombre_prestado_a) . '</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php $i++; }  ?>
        </tbody>
    </table>

    <h2>3. Estadísticas de la Biblioteca</h2>
    
    <?php 
    $total_libros = calcularTotalLibros($libros);
    $porcentaje_prestados = calcularPorcentajePrestados($libros);
    $usuario_top = usuarioConMasPrestamos($usuarios);
    ?>

    <ul>
        <li><strong>Número total de libros:</strong> <?= $total_libros ?></li>
        <li><strong>Porcentaje de libros prestados:</strong> <?= number_format($porcentaje_prestados, 2) ?>%</li>
        <li><strong>Usuario con más libros en préstamo:</strong> <?= htmlspecialchars($usuario_top) ?></li>
    </ul>

</body>
</html>