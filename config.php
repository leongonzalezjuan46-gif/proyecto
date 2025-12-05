<?php
$servername = "sql100.infinityfree.com";
$username   = "if0_40528385";
$password   = "pQlf7VmAyhF";
$database   = "if0_40528385_interactivo";

// Log sencillo a archivo local para depurar en hosting que oculta errores.
function log_error_local($msg) {
    $line = "[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL;
    @file_put_contents(__DIR__ . "/php_errors.log", $line, FILE_APPEND);
}

// Forzar mysqli a lanzar excepciones para capturarlas.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function conectarDB() {
    global $servername, $username, $password, $database;
    try {
        $conn = new mysqli($servername, $username, $password, $database, 3306);
        $conn->set_charset("utf8");
        return $conn;
    } catch (Throwable $e) {
        log_error_local("Conexión fallida: " . $e->getMessage());
        return null;
    }
}

function crearTabla($conn) {
    if (!$conn) return false;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS visita (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            comentario TEXT NOT NULL
        )";
        return $conn->query($sql);
    } catch (Throwable $e) {
        log_error_local("Crear tabla visita falló: " . $e->getMessage());
        return false;
    }
}
?>

