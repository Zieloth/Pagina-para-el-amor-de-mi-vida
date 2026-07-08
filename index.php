<?php


// 1. Conexión a la base de datos SQLite
$db = new PDO('sqlite:amor.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Crear la tabla de mensajes si no existe
$db->exec("CREATE TABLE IF NOT EXISTS mensajes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    remitente TEXT,
    contenido TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
    
CREATE TABLE IF NOT EXISTS recuerdos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    contenido TEXT NOT NULL,
    multimedia TEXT,
    tipo_multimedia TEXT, /* 'imagen', 'video' o 'ninguno' */
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");


// 2. Procesar el formulario con Redirección (Patrón PRG)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nota'])) {
    $nota = trim($_POST['nota']); // Remueve espacios en blanco innecesarios al inicio y final

    if (!empty($nota)) { // Solo inserta si realmente contiene texto
        $nota_segura = htmlspecialchars($nota);
        $stmt = $db->prepare("INSERT INTO mensajes (remitente, contenido) VALUES ('Ella', :contenido)");
        $stmt->bindParam(':contenido', $nota_segura);
        $stmt->execute();
        
        // Redirección limpia para evitar re-envíos al recargar
        header("Location: " . $_SERVER['PHP_SELF'] . "?enviado=1");
        exit();
    }
}
// Comprobamos si viene el parámetro 'enviado' en la URL
$mensaje_enviado = isset($_GET['enviado']) && $_GET['enviado'] == 1;

// 3. Obtener los mensajes guardados
$resultado = $db->query("SELECT * FROM mensajes ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Para Ti, Siempre</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos específicos para la línea de tiempo visual con línea a la izquierda */
        .linea-tiempo-visual {
            position: relative;
            padding-left: 20px; /* Espacio para la línea */
            border-left: 1px dashed #a69696; /* Línea discontinua a la izquierda */
            margin-left: 10px;
        }

        .momento-visual {
            position: relative;
            margin-bottom: 20px;
        }

        .punto-conexion {
            position: absolute;
            left: -26px; /* Posiciona el punto sobre la línea */
            top: 6px;
            width: 12px;
            height: 12px;
            background-color: #d96b6b;
            border-radius: 50%;
            display: none; /* Ocultar por defecto */
        }

        .fecha-etiqueta {
            font-weight: bold;
            color: #d96b6b;
            display: block;
        }
    </style>
</head>
<body>

<audio id="musica-fondo" loop>
        <source src="Cuando Cuando - José y el Toro - José y el Toro.mp3" type="audio/mpeg">
        Tu navegador no soporta el elemento de audio.
    </audio>

    

    <div class="contenedor">
        <header>
            <h1>Aunque el destino dicte otra cosa...</h1>
            <p class="subtitulo">Mi amor por ti no cambia.</p>
        </header>

        <section class="carta">
            <h2>Para la chica que sigo amando</h2>
            <p>
                A veces la vida nos pone impedimentos y caminos que no podemos cruzar juntos, 
                pero quiero que sepas que las circunstancias no borran lo que siento por ti. 
                Este rincón en la web es tuyo, es una muestra de mi amor por medio del
                arte que amo, un testimonio privado y exclusivo de que mi amor por ti 
                sigue intacto, igual que el primer día.
            </p>
            <p>
                No importa dónde estés, ni el tiempo que passe. Aquí siempre habrá un "todavía Te Amo profundamente" esperándote.
            </p>
        </section>
        
    <section class="carta">
            <h2>La cancion que te dedico</h2>
            <p>
                Esta cancion realmente reflleja lo que siento y lo que estoy dispuesto ha hacer por ti mi amor.
            </p>
            <center><button id="control-musica" onclick="toggleMusica()" class="boton-musica-flotante">
        🎵 Reproducir música
    </button></center>
            
        </section>

        <section class="recuerdos">
            <h3>Nuestro Lazo Indestructible</h3>
            <p class="intro-lazo">Un breve repaso de lo que fuimos, lo que somos y lo que siempre seremos...</p>
            
            <div class="linea-tiempo-visual">
                
                <div class="momento-visual">
                    <div class="punto-conexion"></div>
                    <div class="contenido-momento">
                        <span class="fecha-etiqueta">El Inicio</span>
                        <h4>El día que el mundo cambió</h4>
                        <p>Cuando nuestras miradas coincidieron por primera vez y, sin saberlo, empezamos a escribir la historia más bonita de nuestras vidas.</p>
                    </div>
                </div>

                <div class="momento-visual">
                    <div class="punto-conexion"></div>
                    <div class="contenido-momento">
                        <span class="fecha-etiqueta">El Presente</span>
                        <h4>Un amor que se transforma</h4>
                        <p>Aunque los caminos se congelen o las circunstancias nos obliguen a estar lejos, lo que siento por ti no se extingue; madura y permanece intacto.</p>
                    </div>
                </div>

                <div class="momento-visual">
                    <div class="punto-conexion"></div>
                    <div class="contenido-momento">
                        <span class="fecha-etiqueta">El Siempre</span>
                        <h4>Un eco en la eternidad</h4>
                        <p>Pase lo que pase, este rincón y mi corazón siempre tendrán tu nombre grabado. Eres y serás mi mejor recuerdo mi amor.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="interactivo" style="text-align: center;">
            <h3>Nuestra Historia Completa</h3>
            <div style="margin-top: 15px;">
                <a href="recuerdos.php" class="enlace-linea" style="font-size: 1.3rem;">✨ Ver línea de tiempo de nuestros recuerdos ❤️</a>
            </div>
        </section>

        <section class="interactivo">
            <h3>Tu rincón secreto</h3>
            <p>Si alguna vez pasas por aquí y quieres dejar una huella, una palabra o un suspiro, puedes escribirlo abajo. Solo yo lo sabré.</p>
            
            <?php if ($mensaje_enviado): ?>
                <p class="alerta-exito">❤️ Tu nota se ha guardado en mi corazón (y en la base de datos).</p>
            <?php endif; ?>

            <form action="" method="POST">
                <textarea name="nota" placeholder="Escribe algo aquí si lo deseas..." required></textarea>
                <button type="submit">Dejar una nota</button>
            </form>
        </section>

        <?php if ($resultado->fetch()): ?>
            <section class="notas-recibidas">
                <h3>Ecos del pasado</h3>
                <?php foreach ($resultado as $msg): ?>
                    <div class="nota-item">
                        <p>"<?= htmlspecialchars($msg['contenido']) ?>"</p>
                        <small><?= $msg['fecha'] ?></small>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <footer>
            <p>Hecho con amor eterno • <?= date('Y') ?></p>
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