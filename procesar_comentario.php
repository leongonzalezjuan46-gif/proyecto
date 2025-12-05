<?php
// Ocultar errores en pantalla
error_reporting(0);
ini_set('display_errors', 0);

if (file_exists('config.php')) {
    include 'config.php';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_comentario'])) {
    $conn = null;
    try {
        $conn = conectarDB();
    } catch (Throwable $e) {
        $conn = null;
        if (function_exists('log_error_local')) {
            log_error_local("Conectar en procesar_comentario: " . $e->getMessage());
        }
    }

    if ($conn) {
        try { crearTabla($conn); } catch (Throwable $e) { if (function_exists('log_error_local')) { log_error_local("Crear tabla desde procesar: " . $e->getMessage()); } }

        if (isset($_POST['nombre']) && isset($_POST['comentario'])) {
            $nombre = trim($_POST['nombre']);
            $comentario = trim($_POST['comentario']);
            if (!empty($nombre) && !empty($comentario)) {
                try {
                    // Usar consulta preparada para evitar errores por caracteres especiales y mejorar seguridad.
                    $stmt = $conn->prepare("INSERT INTO visita (nombre, comentario) VALUES (?, ?)");
                    $stmt->bind_param("ss", $nombre, $comentario);
                    $stmt->execute();
                    $stmt->close();
                    $conn->close();
                    header("Location: index.php?mensaje=exito");
                    exit();
                } catch (Throwable $e) {
                    if (function_exists('log_error_local')) {
                        log_error_local("INSERT falló (excepción): " . $e->getMessage());
                    }
                }
                // Si llegó aquí es que el INSERT no se ejecutó correctamente
                if (function_exists('log_error_local')) {
                    log_error_local("INSERT falló (error mysqli): " . $conn->error);
                }
                if ($conn) { $conn->close(); }
                header("Location: index.php?mensaje=error");
                exit();
            }
            if ($conn) { $conn->close(); }
            header("Location: index.php?mensaje=vacio");
            exit();
        }
        if ($conn) { $conn->close(); }
        header("Location: index.php?mensaje=error");
        exit();
    }
    header("Location: index.php?mensaje=conexion");
    exit();
}
header("Location: index.php");
exit();
?>

