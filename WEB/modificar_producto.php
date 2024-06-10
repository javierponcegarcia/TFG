<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>ZAPASTILLAS</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="listar_producto.php">Listar Productos</a></li>
                    <li><a class="current" href="modificar_producto.php">Modificar Producto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="main">
        <div class="container">
            <h1>Modificar Producto</h1>
            <?php
            include './db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT p.id AS producto_id, p.nombre, p.precio, p.descripcion, p.imagen, p.categoria_id, i.cantidad 
            FROM productos p 
            JOIN inventario i ON p.id = i.producto_id 
            WHERE p.id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <form action="modificar_producto.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $row['producto_id']; ?>">
            <div class="form-group">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $row['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" value="<?php echo $row['precio']; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" required><?php echo $row['descripcion']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>">
            </div>
            <div class="form-group">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria">
                    <option value="1" <?php echo ($row['categoria_id'] == 1) ? 'selected' : ''; ?>>Deportivas</option>
                    <option value="2" <?php echo ($row['categoria_id'] == 2) ? 'selected' : ''; ?>>Casuales</option>
                    <option value="3" <?php echo ($row['categoria_id'] == 3) ? 'selected' : ''; ?>>Formales</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
            </div>
            <button type="submit">Modificar Producto</button>
        </form>
        <?php
    } else {
        echo "Producto no encontrado";
    }
}

include './db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria_id = $_POST['categoria'];
    $cantidad = $_POST['cantidad'];

    // Procesamiento de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen'];
        $imagen_nombre = $imagen['name'];
        $imagen_temporal = $imagen['tmp_name'];

        // Ruta donde guardar las imágenes (ajústala según tu configuración)
        $ruta_guardado = 'img/';
        $imagen_guardada = $ruta_guardado . basename($imagen_nombre);

        // Mover la imagen subida a la ruta especificada
        if (move_uploaded_file($imagen_temporal, $imagen_guardada)) {
            $sql = "UPDATE productos SET nombre='$nombre', precio='$precio', descripcion='$descripcion', imagen='$imagen_guardada', categoria_id='$categoria_id' WHERE id=$id";
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    } else {
        // Si no se sube una nueva imagen, no actualizar el campo de la imagen
        $sql = "UPDATE productos SET nombre='$nombre', precio='$precio', descripcion='$descripcion', categoria_id='$categoria_id' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        // Verificar si alguna fila fue afectada
        if ($conn->affected_rows > 0) {
            // Actualizar el inventario
            $sql_inventario = "UPDATE inventario SET cantidad='$cantidad' WHERE producto_id=$id";
            if ($conn->query($sql_inventario) === TRUE) {
                echo "Producto actualizado correctamente";
            } else {
                echo "Error al actualizar el inventario: " . $conn->error;
            }
        } else {
            echo "No se encontró el producto para actualizar";
        }
    } else {
        echo "Error al actualizar el producto: " . $conn->error;
    }
}

$conn->close();
?>
        </div>
    </section>

    <footer>
        <p>ZAPASTILLAS &copy; 2024</p>
    </footer>
</body>
</html>
