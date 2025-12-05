<?php
// Ocultar errores en pantalla (solo mostrar mensaje de conexión)
error_reporting(0);
ini_set('display_errors', 0);

$comentarios = array();
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
$db_error = '';

if (!file_exists('config.php')) {
    $db_error = 'No se encontró config.php';
} else {
    include 'config.php';

    if (function_exists('conectarDB')) {
        try {
            $conn = conectarDB();
        } catch (Throwable $e) {
            $conn = null;
        }

        if ($conn) {
            if (function_exists('crearTabla')) {
                try { crearTabla($conn); } catch (Throwable $e) { /* ya se loguea en config */ }
            }

            try {
                $result = $conn->query("SELECT * FROM visita ORDER BY id DESC");
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $comentarios[] = $row;
                    }
                    $result->free();
                }
            } catch (Throwable $e) {
                if (function_exists('log_error_local')) {
                    log_error_local("SELECT falló: " . $e->getMessage());
                }
                $db_error = 'Error consultando la tabla visita.';
            }

            $conn->close();
        } else {
            $db_error = 'No se pudo conectar a la base de datos. Verifica host, usuario, contraseña y nombre de BD.';
        }
    } else {
        $db_error = 'No se cargaron las funciones de conexión.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <style>
    #header {
      background-color: #922608;
      border-radius: 10px;
      padding: 5px;
    }

    .nav {
      padding-right: 20px;
    }

    .nav ul li{
      display:inline;
      padding:0 10px
    }

    h1 {
      text-align: center;
      color: black;
      margin: 8px;
    }
    #seccion {
      display: flex;
      flex-direction: row;
      flex-wrap:wrap
    }

    .barra {
      padding: 10px;
      margin:5px;
      background: rgb(1, 4, 105);
      flex:3;
      border-radius:10px;
      display:flex;
      flex-flow:column;
      align-items:center;
    }

    .contenido {
      padding: 10px;
      border-radius:10px;
      background: rgb(44, 98, 215);
      margin:5px;
      flex:1 150px;
    }

    .subcontenido2 {
      display:flex;
      flex-direction:row;
      flex-wrap:wrap;
      justify-content:center;
    }

    #resultado {
      display:flex;
      flex-direction:column;
      flex-wrap:wrap;
      align-items: center;
    }
    
    #footer {
      background-color: white;
      height: 100%;
      text-align: center;
      padding-top: 10px;
      border-radius: 10px;
      display:flex;
      flex-flow:row;
    }
    .pie {
      font-size: 25px;
      font-weight: bold;
    }

    #galeria{
      display:flex;
      flex-direction:row;
      flex-wrap:wrap; 
      height:100px;
    }

    .proyecto{
      
      height:150px;
      max-width:150px;
      background-color:rgb(203, 120, 120);
      border-radius:10px;
      margin:10px;
      flex:1 100px;
    }

    .barra a {
      display:block;
    }
    #imagen-interactiva {
      position: relative;
      width: 300px;
      height: 600px;
      background-image: url('imagenes/guitarr.png');
      background-size: cover;
      border: 2px solid #937474;
      border-radius: 10px;
    }

    #hotspot1 {
      position: absolute;
      top: 50px;
      left: 10px;
      width: 40px;
      height: 40px;
      background-color: #0015ff80;
      border-radius: 50%;
      cursor: pointer;
    }

    #hotspot2 {
      position: absolute;
      top: 280px;
      left: 20px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }
    #hotspot3 {
      position: absolute;
      top: 435px;
      left: 10px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }

    #hotspot4 {
      position: absolute;
      top: 70px;
      left: 250px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }
#hotspot5 {
      position: absolute;
      top: 140px;
      left: 250px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }
#hotspot6 {
      position: absolute;
      top: 220px;
      left: 250px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }
#hotspot7 {
      position: absolute;
      top: 470px;
      left: 260px;
      width: 40px;
      height: 40px;
      background-color: rgba(0, 21, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }


    .text-hotspot {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: rgb(239, 239, 239);
      font-weight: bold;
    }

    #instrucciones {
      flex:1;
      cursor: pointer;
    }

    #prueba {
      flex:1;
      cursor:pointer;
    }

    .texto-rojo{
            font-size:40px;
            font-weight: bold;
            color:rgb(108, 6, 6);
    }

    #comentarios {
      flex:1;
      cursor: pointer;
    }

    #seccionComentarios {
      background-color: rgb(44, 98, 215);
      padding: 20px;
      border-radius: 10px;
      margin: 10px;
    }

    #listaComentarios {
      max-height: 400px;
      overflow-y: auto;
    }

    .comentario-item {
      background-color: white;
      padding: 15px;
      margin: 10px 0;
      border-radius: 8px;
      border-left: 4px solid #922608;
    }

    .comentario-nombre {
      font-weight: bold;
      color: #922608;
      margin-bottom: 5px;
    }

    .comentario-texto {
      color: #333;
      line-height: 1.5;
    }

    .comentario-puntaje {
      color: #922608;
      font-weight: bold;
      margin-top: 8px;
      font-size: 14px;
    }

    .comentario-fecha {
      color: #999;
      font-size: 12px;
      margin-top: 5px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .btn-submit {
      background-color: #922608;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      width: 100%;
    }

    .btn-submit:hover {
      background-color: #6b1c06;
    }
  </style>
</head>
<body>
  <div id="header">
    <div class="nav" style="text-align:center;">
      <h3>Bienvenido a "Conociendo el cuerpo humano"</h3>
    </div>
  </div>
  <div id="seccion">
    <div class="barra">
        <div id="imagen-interactiva">
          <div id="hotspot1"><span class="text-hotspot">1</span></div>
          <div id="hotspot2"><span class="text-hotspot">2</span></div>
          <div id="hotspot3"><span class="text-hotspot">3</span></div>
          <div id="hotspot4"><span class="text-hotspot">4</span></div>
          <div id="hotspot5"><span class="text-hotspot">5</span></div>
          <div id="hotspot6"><span class="text-hotspot">6</span></div>
          <div id="hotspot7"><span class="text-hotspot">7</span></div>
          

        </div>
    </div>
    <div class="contenido">
      <div class="subcontenido1">
        <h2 id="titulo-info">Instrucciones</h2>
        <p id="texto-info">Haz click en cada uno de los puntos de la imagen para ver su descripción. Una vez visualizado el interactivo, realiza el test de conocimientos.</p>
      </div>
      <div class="subcontenido2">
        <div class="proyecto">
          <img id="imagen-info" src="imagenes/hotspot-cursor.png" alt="Imagen info" style="width:100%; height:100%; border-radius:10px;">
        </div>
      </div>
      <div id="resultado" class="invisible">
        <h2>Tu resultado:</h2>
        <span id="calif" class="texto-rojo">0/0</span>
        <img id="imagen-resultado" width="200px" src="gif/sii.gif" />
      </div>      
    </div>
  </div>
  <div id="footer">
    <div id="instrucciones"><img width="100px" src="imagenes/instrucciones.png" /></div>
    <div id="prueba"><img width="100px" src="imagenes/test.png" /></div>
  </div>

  <!-- Sección de comentarios -->
  <div id="seccionComentarios">
    <h2>Comentarios y Puntuaciones</h2>
    <?php if (!empty($db_error)): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <strong>Error de conexión:</strong> <?php echo htmlspecialchars($db_error); ?>
      </div>
    <?php endif; ?>
    
    <?php if ($mensaje == 'exito'): ?>
      <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <strong>¡Éxito!</strong> Tu comentario ha sido guardado correctamente.
      </div>
    <?php elseif ($mensaje == 'error'): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <strong>Error:</strong> No se pudo guardar el comentario. Por favor, intenta nuevamente.
      </div>
    <?php elseif ($mensaje == 'vacio'): ?>
      <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
        <strong>Advertencia:</strong> Por favor, completa todos los campos.
      </div>
    <?php elseif ($mensaje == 'conexion'): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <strong>Error:</strong> No se pudo conectar a la base de datos. Por favor, intenta más tarde.
      </div>
    <?php endif; ?>
    
    <!-- Formulario para añadir comentarios -->
    <form method="POST" action="procesar_comentario.php" style="background-color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="comentario">Comentario:</label>
        <textarea id="comentario" name="comentario" required></textarea>
      </div>
      <button type="submit" name="guardar_comentario" class="btn-submit">Enviar Comentario</button>
    </form>

    <!-- Lista de comentarios -->
    <h3>Comentarios recibidos:</h3>
    <div id="listaComentarios">
      <?php if (empty($comentarios)): ?>
        <div style="background-color: white; padding: 20px; border-radius: 8px; text-align: center; color: #666;">
          <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
        </div>
      <?php else: ?>
        <?php foreach ($comentarios as $c): ?>
          <div class="comentario-item">
            <div class="comentario-nombre"><?php echo htmlspecialchars($c['nombre']); ?></div>
            <div class="comentario-texto"><?php echo htmlspecialchars($c['comentario']); ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script type="text/javascript">
    let puntaje = 0;
    let pregunta1 = "";

    document.getElementById('hotspot1').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Truss rod cover (Cubierta del alma o varilla de ajuste)';
      document.getElementById('texto-info').textContent = 'Es una tapa que protege el acceso a la varilla de ajuste (truss rod) que va dentro del mástil.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/mast.jpg');    
    };
    document.getElementById('hotspot2').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Neck (Mástil)';
      document.getElementById('texto-info').textContent = 'Es la parte larga que une la cabeza con el cuerpo y en ella se apoyan los dedos al tocar. Dentro del mástil lleva una varilla metálica llamada alma, que ayuda a mantenerlo recto y a resistir la tensión de las cuerdas.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/mastil.jpg');
    };
    
    document.getElementById('hotspot3').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Body (Cuerpo)';
      document.getElementById('texto-info').textContent = 'El estómago es un órgano del sistema digestivo que descompone los alimentos mediante ácidos y enzimas para facilitar la absorción de nutrientes.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/cuerpogu.jpg');
    };
    
    document.getElementById('hotspot4').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Tuning machine (Clavijas de afinación)';
      document.getElementById('texto-info').textContent = 'Son los pequeños mecanismos metálicos que están en la parte superior de la guitarra, en el clavijero. Sirven para tensar o aflojar las cuerdas y así afinar la guitarra cambiando el tono de cada cuerda.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/clav.jpg');
    };
    
    document.getElementById('hotspot5').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Nut (Cejuela)';
      document.getElementById('texto-info').textContent = 'Es una pequeña pieza blanca de hueso, plástico o metal que se encuentra justo después del clavijero. Su función es sostener las cuerdas y mantenerlas separadas correctamente al pasar hacia el mástil, ayudando también a mantener la afinación.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/hues.jpg');
    };
    
    document.getElementById('hotspot6').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Fingerboard and fret (Diapasón y trastes)';
      document.getElementById('texto-info').textContent = 'El diapasón es la superficie de madera sobre el mástil donde se presionan las cuerdas para producir las notas. Los trastes son las pequeñas barras metálicas que dividen el diapasón y marcan las posiciones de las diferentes notas musicales.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/trates.png');
    };
    
    document.getElementById('hotspot7').onclick = function() {
      document.getElementById('titulo-info').textContent = 'Bridge (Puente)';
      document.getElementById('texto-info').textContent = 'Es la pieza ubicada en la parte inferior del cuerpo donde terminan las cuerdas. Su función es sostenerlas y transmitir sus vibraciones al cuerpo de la guitarra. También permite ajustar la altura y la longitud de las cuerdas, y en algunos modelos incluye una palanca de trémolo para hacer efectos de vibrato.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/puente.png');
    };

    document.getElementById('instrucciones').onclick = function(){
      document.getElementById('titulo-info').textContent = 'Instrucciones';
      document.getElementById('texto-info').textContent = 'Haz click en cada uno de los puntos de la imagen para ver su descripción. Una vez visualizado el interactivo, realiza el test de conocimientos.';
      document.getElementById('imagen-info').setAttribute('src', 'imagenes/hotspot-cursor.png');
    };
    document.getElementById('prueba').onclick = function(){
    puntaje = 0; 
    alert("Bienvenidos a la prueba de conocimientos sobre la guitarra 🎸");
    alert("Responde correctamente todas las preguntas para ganar");

    // Pregunta 1
    pregunta1 = prompt("1. ¿Qué parte de la guitarra es responsable de sostener las cuerdas en la parte inferior del cuerpo?\nA) Cejuela\nB) Puente\nC) Mástil\nD) Clavijas");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! El puente sostiene las cuerdas en la parte inferior.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Puente.");
    }

    // Pregunta 2
    pregunta1 = prompt("2. ¿Qué función tienen las clavijas de afinación?\nA) Decorar la guitarra\nB) Tensar o aflojar las cuerdas\nC) Sostener el mástil\nD) Proteger el cuerpo");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! Las clavijas sirven para tensar o aflojar las cuerdas y afinar la guitarra.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Tensar o aflojar las cuerdas.");
    }

    // Pregunta 3
    pregunta1 = prompt("3. ¿Dónde se encuentra la cejuela (nut) en la guitarra?\nA) En el puente\nB) Justo después del clavijero\nC) En el cuerpo\nD) En la parte trasera");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! La cejuela está justo después del clavijero.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Justo después del clavijero.");
    }

    // Pregunta 4
    pregunta1 = prompt("4. ¿Qué son los trastes?\nA) Cuerdas de repuesto\nB) Barras metálicas que dividen el diapasón\nC) Tipos de madera\nD) Accesorios decorativos");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! Los trastes son las barras metálicas que dividen el diapasón.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Barras metálicas que dividen el diapasón.");
    }

    // Pregunta 5
    pregunta1 = prompt("5. ¿Qué parte protege el acceso a la varilla de ajuste (truss rod)?\nA) El puente\nB) La cejuela\nC) El truss rod cover\nD) Las clavijas");
    if (pregunta1 && pregunta1.toLowerCase() === "c") {
        puntaje++;
        alert("¡Correcto! El truss rod cover protege el acceso a la varilla de ajuste.");
    } else {
        alert("Incorrecto. La respuesta correcta es C) El truss rod cover.");
    }

    // Pregunta 6
    pregunta1 = prompt("6. ¿Cuál es la función principal del mástil (neck)?\nA) Amplificar el sonido\nB) Unir la cabeza con el cuerpo\nC) Afinar las cuerdas\nD) Decorar la guitarra");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! El mástil une la cabeza con el cuerpo.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Unir la cabeza con el cuerpo.");
    }

    // Pregunta 7
    pregunta1 = prompt("7. ¿Qué parte de la guitarra contiene el alma o varilla de ajuste?\nA) El cuerpo\nB) El puente\nC) El mástil\nD) La cejuela");
    if (pregunta1 && pregunta1.toLowerCase() === "c") {
        puntaje++;
        alert("¡Correcto! El alma o varilla de ajuste va dentro del mástil.");
    } else {
        alert("Incorrecto. La respuesta correcta es C) El mástil.");
    }

    // Pregunta 8
    pregunta1 = prompt("8. ¿Sobre qué parte de la guitarra se apoyan los dedos al tocar?\nA) El cuerpo\nB) El mástil\nC) El puente\nD) Las clavijas");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! Los dedos se apoyan en el mástil al tocar.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) El mástil.");
    }

    // Pregunta 9
    pregunta1 = prompt("9. ¿Qué parte de la guitarra transmite las vibraciones de las cuerdas al cuerpo?\nA) La cejuela\nB) El puente\nC) Los trastes\nD) El truss rod cover");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! El puente transmite las vibraciones al cuerpo de la guitarra.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) El puente.");
    }

    // Pregunta 10
    pregunta1 = prompt("10. ¿Qué permite ajustar la palanca de trémolo en algunos puentes?\nA) El volumen\nB) Efectos de vibrato\nC) La afinación inicial\nD) El color del sonido");
    if (pregunta1 && pregunta1.toLowerCase() === "b") {
        puntaje++;
        alert("¡Correcto! La palanca de trémolo permite hacer efectos de vibrato.");
    } else {
        alert("Incorrecto. La respuesta correcta es B) Efectos de vibrato.");
    }

    // Mostrar resultado final
    alert(`¡Prueba completada! Tu puntaje final es: ${puntaje}/10`);
    
    
    // Mostrar resultado en la página
    document.getElementById('titulo-info').textContent = 'Resultado del Test';
    document.getElementById('texto-info').textContent = `Has completado la prueba de conocimientos sobre la guitarra.`;
    document.getElementById('calif').textContent = `${puntaje}/10`;
    document.getElementById('resultado').classList.remove('invisible');
    
    
    if (puntaje >= 8) {
        document.getElementById('imagen-resultado').setAttribute('src', 'gif/sii.gif');
        document.getElementById('texto-info').textContent += ' ¡Excelente! Eres un experto en guitarras.';
    } else if (pregunta1 >= 5) {
        document.getElementById('imagen-resultado').setAttribute('src', 'gif/bien.gif');
        document.getElementById('texto-info').textContent += ' ¡Buen trabajo! Sigue aprendiendo.';
    } else {
        document.getElementById('imagen-resultado').setAttribute('src', 'gif/estudia.gif');
        document.getElementById('texto-info').textContent += ' Sigue practicando y revisa el material nuevamente.';
    }
};
  </script>
</body>
</html>