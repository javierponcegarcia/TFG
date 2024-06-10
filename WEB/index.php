<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAPASTILLAS</title>
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
                    <li><a class="current" href="index.php">Inicio</a></li>
                    <li><a href="listar_producto.php">Listar Productos</a></li>
                    <li><a href="insertar_producto.php">Insertar Producto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="showcase">
        <div class="container">
            <h1>Bienvenido a ZAPASTILLAS</h1>
            <p>Encuentra las mejores zapatillas al mejor precio</p>
        </div>
    </section>

    <div class="container">
        <section id="galeria">
            <div class="imagenes">
                <div class="imagen-container">
                    <img src="./img/index-photo1.jpg" alt="Imagen 1">
                </div>
                <div class="imagen-container">
                    <img src="./img/index-photo1.jpg" alt="Imagen 2">
                </div>
            </div>
        </section>
        <section id="productos">
            <?php
            include './db_connection.php';

            $sql = "SELECT * FROM productos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='producto'>";
                    echo "<h2>" . $row["nombre"] . "</h2>";
                    echo "<p>Precio: $" . $row["precio"] . "</p>";
                    echo "<img src='" . $row["imagen"] . "' alt='" . $row["nombre"] . "'>";
                    echo "</div>";
                }
            } else {
                echo "0 resultados";
            }

            $conn->close();
            ?>
        </section>
    </div>

    <footer>
        <p>ZAPASTILLAS &copy; 2024</p>
    </footer>

    <script>
        function modificarProducto(id) {
            window.location.href = 'modificar_producto.php?id=' + id;
        }
    </script>
</body>
</html>
