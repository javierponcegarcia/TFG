<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto</title>
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
                    <li><a class="current" href="insertar_producto.php">Insertar Producto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="main">
        <div class="container">
            <h1>Insertar Producto</h1>
            <form action="./insertar_producto.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" id="precio" name="precio" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>
                <div class="form-group">
                    <label for="imagen">Imagen</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria">
                        <option value="1">Deportivas</option>
                        <option value="2">Casuales</option>
                        <option value="3">Formales</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" required>
                </div>
                <button type="submit">Insertar Producto</button>
            </form>
        </div>
    </section>

    <footer>
        <p>ZAPASTILLAS &copy; 2024</p>
    </footer>
</body>
</html>

<?php
include './db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria_id = $_POST['categoria'];
    $cantidad = $_POST['cantidad'];

    // Procesamiento de la imagen
    $imagen = $_FILES['imagen'];
    $imagen_nombre = $imagen['name'];
    $imagen_temporal = $imagen['tmp_name'];

    // Ruta donde guardar las imágenes (ajústala según tu configuración)
    $ruta_guardado = './img/';
    $imagen_guardada = $ruta_guardado . $imagen_nombre;
    move_uploaded_file($imagen_temporal, $imagen_guardada);

    $response = [];

    $sql = "INSERT INTO productos (nombre, precio, descripcion, imagen, categoria_id) VALUES ('$nombre', '$precio', '$descripcion', '$imagen_guardada', '$categoria_id')";
    if ($conn->query($sql) === TRUE) {
        $producto_id = $conn->insert_id;
        $sql_inventario = "INSERT INTO inventario (producto_id, cantidad) VALUES ('$producto_id', '$cantidad')";
        if ($conn->query($sql_inventario) === TRUE) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
    } else {
        $response['success'] = false;
    }

    $conn->close();
}
?>
