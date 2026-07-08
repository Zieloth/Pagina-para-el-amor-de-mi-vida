<?php
// recuerdos.php
require_once 'db.php';

// Consulta ordenada por fecha de forma descendente
$resultado = $db->query("SELECT * FROM recuerdos ORDER BY fecha ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Línea de Tiempo - Nuestros Ecos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <audio id="musica-fondo" loop>
        <source src="Cuando Cuando - José y el Toro - José y el Toro.mp3" type="audio/mpeg">
        Tu navegador no soporta el elemento de audio.
    </audio>

    <center><button id="control-musica" onclick="toggleMusica()" class="boton-musica-flotante">
        🎵 Reproducir música
    </button></center>

    <div class="contenedor">
        <header>
            <h1>Nuestra Línea de Tiempo</h1>
            <p class="subtitulo">Momentos e historias que el tiempo no podrá borrar.</p>
            <a href="index.php" class="enlace-volver">← Volver al formulario</a>
        </header>

        <div class="linea-tiempo-completa">
            <?php 
            $hay_recuerdos = false;
            foreach ($resultado as $recuerdo): 
                $hay_recuerdos = true;
                // Formatear la fecha para que se vea más natural
                $fecha_formateada = date("d M, Y", strtotime($recuerdo['fecha']));
            ?>
                <article class="tarjeta-recuerdo">
                    <div class="encabezado-recuerdo">
                        <h2><?= htmlspecialchars($recuerdo['titulo']) ?></h2>
                        <span class="fecha-recuerdo">🕒 <?= $fecha_formateada ?></span>
                    </div>

                    <?php if (!empty($recuerdo['multimedia'])): ?>
                        <div class="contenedor-multimedia">
                            <?php if ($recuerdo['tipo_multimedia'] === 'imagen'): ?>
                                <img src="<?= $recuerdo['multimedia'] ?>" alt="<?= htmlspecialchars($recuerdo['titulo']) ?>" class="media-elemento">
                            <?php elseif ($recuerdo['tipo_multimedia'] === 'video'): ?>
                                <video src="<?= $recuerdo['multimedia'] ?>" controls class="media-elemento"></video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="cuerpo-recuerdo">
                        <p><?= nl2br(htmlspecialchars($recuerdo['contenido'])) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (!$hay_recuerdos): ?>
                <p class="no-recuerdos">Aún no hay recuerdos guardados. Sé el primero en inmortalizar uno.</p>
            <?php endif; ?>
        </div>

            <p class="no-recuerdos"><strong></strong><a href="subir_recuerdo.php">Sube tus recuerdos tambien uwu</a></strong></p>
        <footer>
            <p>Guardado para siempre • <?= date('Y') ?></p>
        </footer>
    </div>
<script>
    function toggleMusica() {
        var audio = document.getElementById('musica-fondo');
        var boton = document.getElementById('control-musica');
        
        if (audio.paused) {
            audio.play().then(() => {
                boton.innerHTML = "⏸️ Pausar música";
                boton.classList.add('sonando');
            }).catch(error => {
                console.log("El navegador bloqueó el inicio automático: ", error);
            });
        } else {
            audio.pause();
            boton.innerHTML = "🎵 Reproducir música";
            boton.classList.remove('sonando');
        }
    }
    </script>
</body>
</html>