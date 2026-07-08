<?php
// index.php
require_once 'db.php';

$mensaje_enviado = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = htmlspecialchars($_POST['titulo']);
    $contenido = htmlspecialchars($_POST['contenido']);
    // Capturamos la fecha enviada por el formulario
    $fecha_recuerdo = !empty($_POST['fecha']) ? $_POST['fecha'] : null; 
    
    $ruta_multimedia = null;
    $tipo_multimedia = 'ninguno';

    // Verificar si se subió un archivo
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['archivo']['name'];
        $tipo_archivo = $_FILES['archivo']['type'];
        $tmp_name = $_FILES['archivo']['tmp_name'];
        
        // Crear carpeta si no existe
        if (!is_dir('archivos')) {
            mkdir('archivos', 0777, true);
        }

        // Renombrar el archivo para evitar duplicados usando la marca de tiempo
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $nuevo_nombre = time() . '_' . uniqid() . '.' . $extension;
        $destino = 'archivos/' . $nuevo_nombre;

        // Validar si es imagen o video
        if (strpos($tipo_archivo, 'image/') === 0) {
            $tipo_multimedia = 'imagen';
        } elseif (strpos($tipo_archivo, 'video/') === 0) {
            $tipo_multimedia = 'video';
        }

        if ($tipo_multimedia !== 'ninguno') {
            if (move_uploaded_file($tmp_name, $destino)) {
                $ruta_multimedia = $destino;
            } else {
                $error = "Hubo un problema al guardar el archivo.";
            }
        } else {
            $error = "Formato no permitido. Solo se aceptan imágenes o videos.";
        }
    }

    // Validar que se haya seleccionado una fecha
    if (empty($fecha_recuerdo)) {
        $error = "Por favor, selecciona la fecha de este recuerdo.";
    }

    // Insertar en la base de datos si no hay errores anteriores
    if (empty($error) && (!empty($titulo) || !empty($contenido))) {
        // Añadimos el campo 'fecha' a la consulta SQL para sobrescribir el DEFAULT CURRENT_TIMESTAMP
        $stmt = $db->prepare("INSERT INTO recuerdos (titulo, contenido, multimedia, tipo_multimedia, fecha) VALUES (:titulo, :contenido, :multimedia, :tipo_multimedia, :fecha)");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':multimedia', $ruta_multimedia);
        $stmt->bindParam(':tipo_multimedia', $tipo_multimedia);
        $stmt->bindParam(':fecha', $fecha_recuerdo); // Vinculamos la fecha elegida
        $stmt->execute();
        $mensaje_enviado = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro Rincón Secreto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="contenedor">
        <header>
            <h1>Para la mujer que es dueña de my heart</h1>
            <p class="subtitulo">Aquí puedes dejar un fragmento de ti, cuando quieras.</p>
        </header>

        <section class="interactivo">
            <h3>Añadir un nuevo recuerdo</h3>
            
            <?php if ($mensaje_enviado): ?>
                <p class="alerta-exito">❤️ Recuerdo guardado con éxito en nuestra línea de tiempo.</p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="alerta-error">⚠️ <?= $error ?></p>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="titulo" placeholder="Título de este momento..." required>
                
                <div class="campo-formulario" style="margin-bottom: 15px; text-align: left;">
                    <label for="fecha" style="display:block; margin-bottom: 5px; color: #a69696; font-size: 0.9rem;">¿Cuándo sucedió este momento?</label>
                    <input type="date" id="fecha" name="fecha" required style="width: 100%; background: #1a1616; border: 1px solid #403535; color: #e0d7d7; padding: 12px; border-radius: 4px; box-sizing: border-box; font-family: inherit;">
                </div>

                <textarea name="contenido" placeholder="Escribe lo que sientes, una nota, un poema, o como te sentiste en este recuerdo..." required></textarea>
                
                <div class="campo-archivo">
                    <label for="archivo">Sube una foto o video de nosotros:</label>
                    <input type="file" id="archivo" name="archivo" accept="image/*,video/*">
                </div>

                <button type="submit">Guardar Recuerdito</button>
            </form>
        </section>

        <div style="text-align: center; margin-top: 20px;">
            <a href="recuerdos.php" class="enlace-linea">✨ Ver la Línea de Tiempo</a>
        </div>
    </div>

</body>
</html>