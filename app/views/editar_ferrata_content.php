<?php
if ($ferrata):
$ferrata_id = $ferrata['id'];
?>
<!-- Editar Ferrata -->
<h1 class="text-center">Editar Ferrata</h1>

<!-- Formulario para editar datos básicos -->
<form id="editarFerrataForm" action="/RedFerratera/index.php?accion=guardar_edicion_ferrata" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar_edicion_ferrata">
    <input type="hidden" name="id" value="<?= $ferrata_id; ?>">
    <input type="hidden" name="desde_gestion" value="<?= isset($_GET['desde_gestion']) ? 1 : 0; ?>">

    <!-- Nombre -->
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($ferrata['nombre']); ?>" class="form-control" required>
    </div>

    <!-- Ubicación -->
    <div class="mb-3">
        <label for="ubicacion" class="form-label">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($ferrata['ubicacion']); ?>" class="form-control" required>
    </div>
    
    <!-- Comunidad Autónoma -->
    <div class="mb-3">
        <label for="comunidad_autonoma" class="form-label">Comunidad Autónoma:</label>
        <select name="comunidad_autonoma" id="comunidad_autonoma" class="form-control" required>
            <option value="">Selecciona una comunidad</option>
            <?php
            $comunidades = [
                "Andalucía", "Aragón", "Asturias", "Baleares", "Canarias", "Cantabria",
                "Castilla-La Mancha", "Castilla y León", "Cataluña", "Extremadura", "Galicia",
                "Madrid", "Murcia", "Navarra", "País Vasco", "La Rioja", "Valencia"
            ];
            foreach ($comunidades as $comunidad):
            ?>
                <option value="<?= $comunidad ?>" <?= $ferrata['comunidad_autonoma'] === $comunidad ? 'selected' : ''; ?>><?= $comunidad ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Provincia -->
    <div class="mb-3">
        <label for="provincia" class="form-label">Provincia:</label>
        <input type="text" name="provincia" id="provincia" value="<?= htmlspecialchars($ferrata['provincia']); ?>" class="form-control" required>
    </div>

    <!-- Dificultad -->
    <div class="mb-3">
        <label for="dificultad" class="form-label">Dificultad:</label>
        <select name="dificultad" id="dificultad" class="form-select">
            <?php foreach (['K1','K2','K3','K4','K5','K6','K7'] as $nivel): ?>
                <option value="<?= $nivel; ?>" <?= $nivel == $ferrata['dificultad'] ? 'selected' : ''; ?>><?= $nivel; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Descripción -->
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea name="descripcion" id="descripcion" class="form-control"><?= htmlspecialchars($ferrata['descripcion']); ?></textarea>
    </div>

    <!-- Coordenadas -->
    <div class="mb-3">
        <label for="coordenadas" class="form-label">Coordenadas:</label>
        <input type="text" name="coordenadas" id="coordenadas" value="<?= htmlspecialchars($ferrata['coordenadas'] ?? ''); ?>" class="form-control">
    </div>

    <!-- Estado -->
    <div class="mb-3">
        <label for="estado" class="form-label">Estado:</label>
        <select name="estado" id="estado" class="form-select">
            <?php foreach (['Abierta','Cerrada','No operativa', 'Precaución'] as $estado): ?>
                <option value="<?= $estado; ?>" <?= $estado == $ferrata['estado'] ? 'selected' : ''; ?>><?= $estado; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Fecha de creación -->
    <div class="mb-3">
        <label for="fecha_creacion" class="form-label">Fecha de Creación:</label>
        <input type="date" name="fecha_creacion" id="fecha_creacion" class="form-control" value="<?= htmlspecialchars($ferrata['fecha_creacion']); ?>" required>
    </div>

    <!-- Fechas de cierre -->
    <div class="mb-3">
        <label for="fecha_inicio_cierre" class="form-label">Fecha de Inicio de Cierre:</label>
        <input type="date" name="fecha_inicio_cierre" id="fecha_inicio_cierre" class="form-control" value="<?= htmlspecialchars($ferrata['fecha_inicio_cierre']); ?>">
    </div>
    <div class="mb-3">
        <label for="fecha_fin_cierre" class="form-label">Fecha de Fin de Cierre:</label>
        <input type="date" name="fecha_fin_cierre" id="fecha_fin_cierre" class="form-control" value="<?= htmlspecialchars($ferrata['fecha_fin_cierre']); ?>">
    </div>

    <!-- Cierre recurrente -->
    <div class="mb-3">
        <label for="recurrente" class="form-label">Cierre recurrente:</label>
        <input type="checkbox" name="recurrente" id="recurrente" value="1" <?= (isset($ferrata['recurrente']) && $ferrata['recurrente'] == 1) ? 'checked' : ''; ?>>
        <small class="form-text text-muted">Si está marcado, las fechas de cierre se aplican cada año.</small>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>

<!-- Imágenes existentes -->
<h3 class="mt-4">Imágenes</h3>
<div class="galeria-detalle editar-ferrata">
    <?php foreach ($imagenes as $img): ?>
        <div class="position-relative imagen-contenedor">
            <img src="/RedFerratera/public/img/ferratas/<?= htmlspecialchars($img['ruta']); ?>" 
                 alt="Imagen de la ferrata"
                 class="img-thumbnail small-image"
                 onerror="this.onerror=null; this.src='/RedFerratera/public/img/default.jpg';">
            <a href="/RedFerratera/eliminar-imagen/<?= $img['id']; ?>/ferrata/<?= $ferrata_id; ?>" class="boton-eliminar">❌</a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Subida de nuevas imágenes -->
<form action="/RedFerratera/index.php?accion=subir_imagen" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="ferrata_id" value="<?= $ferrata_id; ?>">
    <div class="mb-3">
        <label for="imagenes_nuevas" class="form-label">Añadir nuevas imágenes:</label>
        <input type="file" name="imagenes[]" id="imagenes_nuevas" multiple accept="image/*" class="form-control">
        <button type="submit" class="btn btn-primary mt-2">Subir imágenes</button>
    </div>
</form>

<!-- Modal para ver imagen ampliada -->
<div id="modalImagen" class="modal">
    <span class="cerrar" onclick="cerrarModalImagen()">&times;</span>
    <img class="modal-contenido" id="imagenAmpliada">
</div>

<!-- Vídeos -->
<?php
require_once 'app/models/Video.php';
$videoModel = new Video();
$videos = $videoModel->obtenerVideosPorFerrata($ferrata['id']);
if (!empty($videos)):
?>
    <h3 class="mt-4">Vídeos</h3>
    <div id="video-container" class="embed-container">
        <?php foreach ($videos as $vid): ?>
            <div class="position-relative embed-item">
                <?= $vid['video_embed']; ?>
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                    <a href="/RedFerratera/index.php?accion=borrar_video&id=<?= $vid['id']; ?>&ferrata_id=<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;">X</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Formulario para añadir vídeo -->
<form action="/RedFerratera/index.php?accion=subir_video" method="POST" class="mb-4">
    <input type="hidden" name="ferrata_id" value="<?= $ferrata_id; ?>">
    <div class="mb-3">
        <label for="video_embed" class="form-label">Agregar vídeo (código embed):</label>
        <textarea name="video" id="video_embed" class="form-control" placeholder='<iframe ...></iframe>'></textarea>
        <button type="submit" class="btn btn-primary mt-2">Guardar Vídeo</button>
    </div>
</form>

<!-- Wikiloc -->
<?php
require_once 'app/models/Wikiloc.php';
$wikilocModel = new Wikiloc();
$wikilocs = $wikilocModel->obtenerWikilocPorFerrata($ferrata['id']);
if (!empty($wikilocs)):
?>
    <h3 class="mt-4">Track de Wikiloc</h3>
    <div id="wikiloc-container" class="embed-container">
        <?php foreach ($wikilocs as $wikiloc): ?>
            <div class="position-relative embed-item">
                <?= $wikiloc['wikiloc_embed']; ?>
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                    <a href="/RedFerratera/index.php?accion=borrar_wikiloc&id=<?= $wikiloc['id']; ?>&ferrata_id=<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;">X</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Formulario para añadir enlace Wikiloc -->
<form action="/RedFerratera/index.php?accion=guardar_wikiloc" method="POST" class="mb-4">
    <input type="hidden" name="ferrata_id" value="<?= $ferrata_id; ?>">
    <div class="mb-3">
        <label for="wikiloc_embed" class="form-label">Agregar enlace Wikiloc (código embed):</label>
        <textarea name="wikiloc" id="wikiloc_embed" class="form-control" placeholder="Pega aquí el código embed de Wikiloc"></textarea>
        <button type="submit" class="btn btn-primary mt-2">Guardar Enlace Wikiloc</button>
    </div>
</form>

<!-- Botón para eliminar la ferrata -->
<form action="/RedFerratera/index.php?accion=eliminar_ferrata&id=<?= $ferrata_id; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta ferrata? Esta acción no se puede deshacer.')">
    <button type="submit" class="btn btn-danger mt-3">Eliminar Ferrata</button>
</form>

<!-- Comentarios -->
<h3 class="mt-4">Comentarios</h3>
<?php if (!empty($comentarios)): ?>
    <ul class="list-group">
        <?php foreach ($comentarios as $comentario): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($comentario['usuario']); ?>:</strong>
                <?= htmlspecialchars($comentario['comentario']); ?>
                <em>(<?= htmlspecialchars($comentario['fecha_comentario']); ?>)</em>
                <a href="/RedFerratera/index.php?accion=eliminar_comentario/<?= $comentario['id']; ?>&ferrata_id=<?= $ferrata_id; ?>" class="btn btn-danger btn-sm">Eliminar</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="text-muted">No hay comentarios aún.</p>
<?php endif; ?>
<?php endif; ?>
