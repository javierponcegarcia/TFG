<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Productos</title>
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
                    <li><a class="current" href="listar_producto.php">Listar Productos</a></li>
                    <li><a href="insertar_producto.php">Insertar Producto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="main">
        <div class="container">
            <h1>Listar Productos</h1>
            
            <!-- Formulario de Filtros -->
            <form method="POST" action="listar_producto.php">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria">
                    <option value="">Todas</option>
                    <option value="1">Deportivas</option>
                    <option value="2">Casuales</option>
                    <option value="3">Formales</option>
                </select>
                
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre">
                
                <label for="precio_min">Precio Mínimo:</label>
                <input type="number" id="precio_min" name="precio_min" step="0.01">
                
                <label for="precio_max">Precio Máximo:</label>
                <input type="number" id="precio_max" name="precio_max" step="0.01">
                
                <button type="submit" class="button">Filtrar</button>
            </form>

            <section id="productos">
                <?php
                include './db_connection.php';

                $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
                $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
                $precio_min = isset($_POST['precio_min']) ? $_POST['precio_min'] : '';
                $precio_max = isset($_POST['precio_max']) ? $_POST['precio_max'] : '';

                $sql = "SELECT * FROM productos WHERE 1=1";

                if (!empty($categoria)) {
                    $sql .= " AND categoria_id = '$categoria'";
                }
                if (!empty($nombre)) {
                    $sql .= " AND nombre LIKE '%$nombre%'";
                }
                if (!empty($precio_min)) {
                    $sql .= " AND precio >= $precio_min";
                }
                if (!empty($precio_max)) {
                    $sql .= " AND precio <= $precio_max";
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='producto'>";
                        echo "<h2>" . $row["nombre"] . "</h2>";
                        echo "<p>Precio: $" . $row["precio"] . "</p>";
                        echo "<img src='" . $row["imagen"] . "' alt='" . $row["nombre"] . "'>";
                        echo "<button class='button' onclick='modificarProducto(" . $row["id"] . ")'>Modificar</button>";
                        echo "<button class='button' onclick='borrarProducto(" . $row["id"] . ")'>Borrar</button>";
                        echo "</div>";
                    }
                } else {
                    echo "0 resultados";
                }

                $conn->close();
                ?>
            </section>
        </div>
    </section>

    <footer>
        <p>ZAPASTILLAS &copy; 2024</p>
    </footer>

    <script>
        function modificarProducto(id) {
            window.location.href = 'modificar_producto.php?id=' + id;
        }

        function borrarProducto(id) {
            window.location.href = 'delete_product.php?id=' + id;
        }
    </script>
</body>
</html>
