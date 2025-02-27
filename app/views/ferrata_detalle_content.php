<?php if ($ferrata): ?>
	<?php $ferrata_id = isset($ferrata['id']) ? $ferrata['id'] : ''; ?>
    <h1 class="mt-4"><?= htmlspecialchars($ferrata['nombre']); ?></h1>
    
    <!-- Galería de Imágenes -->
    <?php if (!empty($imagenes)): ?>
        <h3 class="mt-4"><i class="lucide lucide-image"></i> Galería de imágenes</h3>
        <div class="galeria-detalle">
            <?php foreach ($imagenes as $img): ?>
                <div class="position-relative d-inline-block">
                    <img src="/RedFerratera/public/img/ferratas/<?= htmlspecialchars($img['ruta']); ?>" 
                         alt="Imagen de la ferrata"
                         class="img-thumbnail"
                         onerror="this.onerror=null; this.src='/RedFerratera/public/img/default.jpg';">
    
                    <!-- SOLO mostrar la "X" si estamos en la página de editar ferrata -->
                    <?php if (isset($_GET['accion']) && $_GET['accion'] === 'editar_ferrata' && isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                       <a href="/RedFerratera/eliminar-imagen/<?= $img['id']; ?>/ferrata/<?= $ferrata['id']; ?>" 
                          class="btn btn-danger btn-sm position-absolute top-0 end-0"
                          style="transform: translate(50%, -50%);">❌</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Modal para ampliar imagen -->
    <div id="modalImagen" class="modal">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <img class="modal-contenido" id="imagenAmpliada">
    </div>
    
    <script>
    function ampliarImagen(img) {
        document.getElementById("modalImagen").style.display = "flex";
        document.getElementById("imagenAmpliada").src = img.src;
    }
    function cerrarModal() {
        document.getElementById("modalImagen").style.display = "none";
    }
    </script>
    
    <!-- Información de la Ferrata -->
    <div class="mt-3 p-3 border rounded">
        <p><i data-lucide="map-pin"></i> <strong>Ubicación:</strong> <?= htmlspecialchars($ferrata['ubicacion']); ?></p>
        <p><i data-lucide="map"></i> <strong>Provincia:</strong> <?= htmlspecialchars($ferrata['provincia']); ?></p>
        <p><i data-lucide="globe"></i> <strong>Comunidad Autónoma:</strong> <?= htmlspecialchars($ferrata['comunidad_autonoma']); ?></p>
        <p><i data-lucide="activity"></i> <strong>Dificultad:</strong> <?= htmlspecialchars($ferrata['dificultad']); ?></p>
        <p><i data-lucide="alert-circle"></i> <strong>Estado:</strong> <?= htmlspecialchars($ferrata['estado']); ?></p>
        <p><i data-lucide="calendar"></i> <strong>Fecha de Creación:</strong> <?= htmlspecialchars($ferrata['fecha_creacion']); ?></p>
        <p><i data-lucide="file-text"></i> <strong>Descripción:</strong> <?= nl2br(htmlspecialchars($ferrata['descripcion'])); ?></p>
        
        <!-- Coordenadas con API Google Maps -->
        <?php if (!empty($ferrata['coordenadas'])): ?>
            <h3 class="mt-4"><i class="lucide lucide-map-pin"></i> Ubicación en el mapa</h3>
            <div id="map" style="height: 400px; border-radius: 10px;"></div>
        
            <script>
                function initMap() {
                    var coordenadas = "<?= htmlspecialchars($ferrata['coordenadas']); ?>".split(",");
                    if (coordenadas.length < 2 || isNaN(coordenadas[0]) || isNaN(coordenadas[1])) {
                        console.error("Coordenadas inválidas:", coordenadas);
                        return;
                    }
                    
                    var latLng = { lat: parseFloat(coordenadas[0]), lng: parseFloat(coordenadas[1]) };
        
                    var map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 13,
                        center: latLng,
                    });
        
                    new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: "<?= htmlspecialchars($ferrata['nombre']); ?>"
                    });
                }
            </script>
            <script async defer 
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQI4xaz6p1EWwRV5GxoDthHt8YxELrO88&callback=initMap">
            </script>
        <?php endif; ?>
        
        <!-- Coordenadas con API Open-Meteo -->
        <?php if (!empty($ferrata['coordenadas'])): ?>
            <h3 class="mt-4"><i data-lucide="cloud-sun"></i> Clima Actual</h3>
            <div id="weather-container" class="p-3 border rounded bg-light">
                <p>Cargando información del clima...</p>
            </div>
        
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var coordenadas = "<?= htmlspecialchars($ferrata['coordenadas']); ?>".split(",");
                
                console.log("Coordenadas obtenidas:", coordenadas); // 📌 Verifica en consola
                
                if (coordenadas.length < 2 || isNaN(coordenadas[0]) || isNaN(coordenadas[1])) {
                    document.getElementById("weather-container").innerHTML = "<p>⚠️ Coordenadas inválidas.</p>";
                    return;
                }
            
                var lat = coordenadas[0].trim();
                var lon = coordenadas[1].trim();
                console.log("Latitud:", lat, "Longitud:", lon); // 📌 Verifica en consola
            
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&temperature_unit=celsius&wind_speed_unit=kmh&winddirection=true&weathercode=true`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Datos recibidos:", data); // 📌 Verifica en consola
                        var weather = data.current_weather;
                        var weatherCode = weather.weathercode; // Código de tiempo
            
                        // Traducción del código de tiempo a palabras
                        var weatherText = obtenerDescripcionTiempo(weatherCode);
                        var weatherIcon = obtenerIconoTiempo(weatherCode);
            
                        var weatherHtml = `
                            <p><strong>🌡 Temperatura:</strong> ${weather.temperature}°C</p>
                            <p><strong>💨 Viento:</strong> ${weather.windspeed} km/h</p>
                            <p><strong>📍 Dirección del viento:</strong> ${weather.winddirection}°</p>
                            <p><strong>${weatherIcon} Estado:</strong> ${weatherText}</p>
                        `;
                        document.getElementById("weather-container").innerHTML = weatherHtml;
                    })
                    .catch(error => {
                        document.getElementById("weather-container").innerHTML = "<p>⚠️ No se pudo obtener el clima.</p>";
                        console.error("Error obteniendo el clima:", error);
                    });
            });
            
            // Función para traducir el código de tiempo a palabras
            function obtenerDescripcionTiempo(code) {
                const weatherDescriptions = {
                    0: "Despejado 🌞",
                    1: "Mayormente despejado 🌤",
                    2: "Parcialmente nublado ⛅",
                    3: "Nublado ☁️",
                    45: "Niebla 🌫",
                    48: "Niebla con escarcha ❄️🌫",
                    51: "Llovizna ligera 🌦",
                    53: "Llovizna moderada 🌧",
                    55: "Llovizna intensa 🌧💦",
                    56: "Llovizna helada ligera ❄️🌦",
                    57: "Llovizna helada intensa ❄️🌧",
                    61: "Lluvia ligera 🌦",
                    63: "Lluvia moderada 🌧",
                    65: "Lluvia intensa 🌧💦",
                    66: "Lluvia helada ligera ❄️🌦",
                    67: "Lluvia helada intensa ❄️🌧",
                    71: "Nieve ligera 🌨",
                    73: "Nieve moderada ❄️🌨",
                    75: "Nieve intensa ❄️❄️",
                    77: "Granizo 🌩❄️",
                    80: "Chubascos ligeros 🌦",
                    81: "Chubascos moderados 🌧",
                    82: "Chubascos intensos ⛈",
                    85: "Chubascos de nieve ligeros 🌨",
                    86: "Chubascos de nieve intensos ❄️🌨",
                    95: "Tormenta eléctrica ⛈",
                    96: "Tormenta con granizo 🌩❄️",
                    99: "Tormenta severa con granizo ⛈❄️",
                };
                return weatherDescriptions[code] || "Desconocido 🤷‍♂️";
            }
            
            // Función para obtener el icono según el código de tiempo
            function obtenerIconoTiempo(code) {
                if (code >= 0 && code <= 3) return "☀️";
                if (code >= 45 && code <= 48) return "🌫";
                if (code >= 51 && code <= 57) return "🌦";
                if (code >= 61 && code <= 67) return "🌧";
                if (code >= 71 && code <= 77) return "❄️";
                if (code >= 80 && code <= 82) return "🌧";
                if (code >= 85 && code <= 86) return "🌨";
                if (code >= 95 && code <= 99) return "⛈";
                return "❓";
            }
            </script>
        <?php endif; ?>
    </div>
    
    <a href="/RedFerratera/ferratas" class="btn btn-secondary mt-3">Volver al listado</a>
    
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
        <a href="/RedFerratera/editar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-warning mt-3"><i class="lucide lucide-edit"></i> Editar Ferrata</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
        <h3 class="mt-4">Añadir imágenes</h3>
        <form action="index.php?accion=subir_imagen" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ferrata_id" value="<?= $ferrata['id']; ?>">
            <input type="file" name="imagenes[]" multiple accept="image/*" class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Subir imágenes</button>
        </form>
    <?php endif; ?>
    <?php else: ?>
        <p class="text-danger">Ferrata no encontrada.</p>
    <?php endif; ?>
    
    <!-- Ferratas cercanas -->
    <?php if (!empty($ferratasCercanas)): ?>
        <h3 class="mt-4"><i class="lucide lucide-map"></i> Ferratas Cercanas</h3>
        <ul class="list-group">
            <?php foreach ($ferratasCercanas as $cercana): ?>
                <li class="list-group-item">
                    <a href="/RedFerratera/ferrata/<?= $cercana['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $cercana['nombre']))); ?>">
                        <strong><?= htmlspecialchars($cercana['nombre']); ?></strong>
                    </a>
                    <br>
                    <small><i class="lucide lucide-map-pin"></i> <?= htmlspecialchars($cercana['ubicacion']); ?> - 
                    <?= number_format($cercana['distancia'], 1); ?> km</small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

	<!-- Sección de comentarios -->
    <h3 class="mt-4">Comentarios</h3>
    <?php if (!empty($comentarios)): ?>
        <ul class="list-group">
            <?php foreach ($comentarios as $comentario): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($comentario['usuario']); ?>:</strong> 
                    <span id="comentario-texto-<?= $comentario['id']; ?>"><?= htmlspecialchars($comentario['comentario']); ?></span> 
                    <em>(<?= htmlspecialchars($comentario['fecha_comentario']); ?>)</em>
    
                    <!-- Botones para el usuario que hizo el comentario -->
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['id'] === $comentario['usuario_id']): ?>
                        <button class="btn btn-warning btn-sm ms-2" onclick="abrirModalEdicion(<?= $comentario['id']; ?>, '<?= htmlspecialchars($comentario['comentario'], ENT_QUOTES); ?>')">✏️ Editar</button>
                        <a href="/RedFerratera/eliminar-comentario/<?= $comentario['id']; ?>/ferrata/<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm">🗑 Eliminar</a>
                    <?php endif; ?>
    
                    <!-- Botón de eliminar para admin -->
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                        <a href="/RedFerratera/eliminar-comentario/<?= $comentario['id']; ?>/ferrata/<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm">🗑 Eliminar</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No hay comentarios aún. Sé el primero en comentar.</p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['usuario'])): ?>
        <h4 class="mt-4">Añadir comentario</h4>
        <form action="index.php?accion=agregar_comentario" method="POST">
            <input type="hidden" name="ferrata_id" value="<?= $ferrata['id']; ?>">
            <textarea name="comentario" class="form-control mb-2" required></textarea>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    <?php else: ?>
        <p class="mt-3"><a href="/RedFerratera/login">Inicia sesión</a> para comentar.</p>
    <?php endif; ?>
    
    <!-- Modal para editar comentario -->
    <div id="modalEditarComentario" class="modal">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarModalEdicion()">&times;</span>
            <h3>Editar Comentario</h3>
            <form id="formEditarComentario" method="POST" action="index.php?accion=editar_comentario">
                <input type="hidden" name="comentario_id" id="comentario_id">
                <input type="hidden" name="ferrata_id" value="<?= $ferrata['id']; ?>">
                <textarea name="comentario" id="comentario_texto" class="form-control mb-2" required></textarea>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
        </div>
    </div>
    
    <script>
    function abrirModalEdicion(id, texto) {
        document.getElementById("modalEditarComentario").style.display = "flex";
        document.getElementById("comentario_id").value = id;
        document.getElementById("comentario_texto").value = texto;
    }
    function cerrarModalEdicion() {
        document.getElementById("modalEditarComentario").style.display = "none";
    }
    </script>

