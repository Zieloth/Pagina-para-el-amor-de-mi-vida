<?php
// db.php
try {
    $db = new PDO('sqlite:amor.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabla modificada para soportar título, imágenes y videos
    $db->exec("CREATE TABLE IF NOT EXISTS recuerdos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        contenido TEXT NOT NULL,
        multimedia TEXT,
        tipo_multimedia TEXT, /* 'imagen', 'video' o 'ninguno' */
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>