<?php
include 'db_connection.php';

// Verifica si se ha recibido un ID válido del producto a eliminar
if (isset($_GET['id'])) {
    // Escapar el ID del producto para prevenir inyección SQL
    $id_producto = $conn->real_escape_string($_GET['id']);

    // Comienza una transacción para asegurar la consistencia de los datos
    $conn->begin_transaction();

    try {
        // Eliminar registros asociados en la tabla 'inventario' primero
        $sql_inventario = "DELETE FROM inventario WHERE producto_id = '$id_producto'";
        $conn->query($sql_inventario);

        // Luego eliminar el producto de la tabla 'productos'
        $sql_producto = "DELETE FROM productos WHERE id = '$id_producto'";
        $conn->query($sql_producto);

        // Si la eliminación fue exitosa, confirmar la transacción
        $conn->commit();

        // Redirigir a la página de productos
        header("Location: listar_producto.php");
        exit; // Salir del script después de la redirección
    } catch (mysqli_sql_exception $e) {
        // Si ocurre algún error, hacer rollback de la transacción para deshacer los cambios
        $conn->rollback();
        echo "Error al eliminar el producto: " . $e->getMessage();
    }
} else {
    // Si no se proporcionó un ID válido, mostrar un mensaje de error
    echo "ID de producto no válido";
}

// Cerrar la conexión con la base de datos
$conn->close();
?>

