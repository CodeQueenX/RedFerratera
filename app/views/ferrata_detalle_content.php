<?php
//var_dump($ferrata);
if ($ferrata):
$ferrata_id = isset($ferrata['id']) ? $ferrata['id'] : '';
?>
	
<!-- Título -->
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
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal para ampliar imagen -->
<div id="modalImagen" class="modal">
    <span class="cerrar">&times;</span>
    <img class="modal-contenido" id="imagenAmpliada">
</div>

<!-- Información de la Ferrata -->
<div class="mt-3 p-3 border rounded">
    <p><i data-lucide="map-pin"></i> <strong>Ubicación:</strong> <?= htmlspecialchars($ferrata['ubicacion']); ?></p>
    <p><i data-lucide="map"></i> <strong>Provincia:</strong> <?= htmlspecialchars($ferrata['provincia']); ?></p>
    <p><i data-lucide="globe"></i> <strong>Comunidad Autónoma:</strong> <?= htmlspecialchars($ferrata['comunidad_autonoma']); ?></p>
    <p><i data-lucide="activity"></i> <strong>Dificultad:</strong> <?= htmlspecialchars($ferrata['dificultad']); ?></p>
    <p><i data-lucide="alert-circle"></i> <strong>Estado:</strong> <?= htmlspecialchars($ferrata['estado']); ?></p>
    <p><i data-lucide="calendar"></i> <strong>Fecha de Creación:</strong> <?= date('d-m-Y', strtotime($ferrata['fecha_creacion'])); ?>
    <p><i data-lucide="file-text"></i> <strong>Descripción:</strong> <?= nl2br(htmlspecialchars($ferrata['descripcion'])); ?></p>
    
    <!-- Coordenadas con API Google Maps -->
    <?php if (!empty($ferrata['coordenadas'])): ?>
        <h3 class="mt-4"><i class="lucide lucide-map-pin"></i> Ubicación en el mapa</h3>
        <div id="map" style="height: 400px; border-radius: 10px;" data-coordenadas="<?= htmlspecialchars($ferrata['coordenadas']); ?>"></div>
    <?php endif; ?>
    
    <!-- Coordenadas con API Open-Meteo -->
    <?php if (!empty($ferrata['coordenadas'])): ?>
        <h3 class="mt-4"><i data-lucide="cloud-sun"></i> Clima Actual</h3>
        <div id="weather-container" class="p-3 border rounded bg-light" data-coordenadas="<?= htmlspecialchars($ferrata['coordenadas']); ?>">
            <p>Cargando información del clima...</p>
        </div>
    <?php endif; ?>
    
<div class="botones-acciones d-flex gap-2">
    <!-- Botón de Volver al Listado -->
    <a href="/RedFerratera/index.php?accion=ferratas" class="btn btn-secondary">Volver al Listado</a>

    <!-- Botón de Editar Ferrata (solo admin) -->
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
        <a href="/RedFerratera/editar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-warning">Editar Ferrata</a>

        <!-- Botón de Eliminar Ferrata (solo admin) -->
        <form action="/RedFerratera/index.php?accion=eliminar_ferrata&id=<?= $ferrata['id']; ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta ferrata? Esta acción no se puede deshacer.')">
            <button type="submit" class="btn btn-danger">Eliminar Ferrata</button>
        </form>
    <?php endif; ?>
</div>

<!-- Botón para añadir imágenes (solo para admin) -->
<?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
    <h3 class="mt-4">Añadir imágenes</h3>
    <form action="/RedFerratera/index.php?accion=subir_imagen" method="POST" enctype="multipart/form-data">
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

<!-- Sección de valoración -->
<?php
require_once 'app/models/Valoracion.php';
$ratingData = Valoracion::getAverageRating($ferrata['id']);
$promedio = $ratingData && $ratingData['total'] > 0 ? round($ratingData['promedio'], 2) : 'Sin valoraciones';
$total = $ratingData ? $ratingData['total'] : 0;
?>
<div class="rating-section">
    <h3 class="mt-4">Valoración</h3>
    <p>Media: <span id="averageRating"><?php echo $promedio; ?></span> (<?php echo $total; ?> valoraciones)</p>
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['verificado'] == 1): ?>
        <!-- Widget de valoración con estrellas -->
        <div id="starRating" data-ferrata-id="<?= $ferrata['id']; ?>"></div>
    <?php else: ?>
        <?php if (!isset($_SESSION['usuario'])): ?>
            <p><a href="/RedFerratera/index.php?accion=login">Inicia sesión</a> para valorar esta ferrata.</p>
        <?php else: ?>
            <p>Tu cuenta no está verificada. Verifica tu cuenta para poder valorar esta ferrata.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

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
    <form action="/RedFerratera/index.php?accion=agregar_comentario" method="POST">
    	<input type="hidden" name="accion" value="agregar_comentario">
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
        <span class="cerrar">&times;</span>
        <h3>Editar Comentario</h3>
        <form id="formEditarComentario" method="POST" action="/RedFerratera/index.php?accion=editar_comentario">
            <input type="hidden" name="accion" value="editar_comentario">
            <input type="hidden" name="comentario_id" id="comentario_id">
            <input type="hidden" name="ferrata_id" value="<?= $ferrata['id']; ?>">
            <textarea name="comentario" id="comentario_texto" class="form-control mb-2" required></textarea>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>
