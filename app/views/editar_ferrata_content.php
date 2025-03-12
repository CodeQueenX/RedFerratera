<?php
if ($ferrata):
$ferrata_id = $ferrata['id'];
?>
<h1 class="text-center">Editar Ferrata</h1>

<!-- Formulario para editar datos básicos -->
<form id="editarFerrataForm" action="/RedFerratera/index.php?accion=guardar_edicion_ferrata" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar_edicion_ferrata">
    <input type="hidden" name="id" value="<?= $ferrata_id; ?>">
    <input type="hidden" name="desde_gestion" value="<?= isset($_GET['desde_gestion']) ? 1 : 0; ?>">

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($ferrata['nombre']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="ubicacion" class="form-label">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($ferrata['ubicacion']); ?>" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label for="comunidad_autonoma" class="form-label">Comunidad Autónoma:</label>
        <select name="comunidad_autonoma" id="comunidad_autonoma" class="form-control" required>
            <option value="">Selecciona una comunidad</option>
            <option value="Andalucía" <?= $ferrata['comunidad_autonoma'] === 'Andalucía' ? 'selected' : ''; ?>>Andalucía</option>
            <option value="Aragón" <?= $ferrata['comunidad_autonoma'] === 'Aragón' ? 'selected' : ''; ?>>Aragón</option>
            <option value="Asturias" <?= $ferrata['comunidad_autonoma'] === 'Asturias' ? 'selected' : ''; ?>>Asturias</option>
            <option value="Baleares" <?= $ferrata['comunidad_autonoma'] === 'Baleares' ? 'selected' : ''; ?>>Islas Baleares</option>
            <option value="Canarias" <?= $ferrata['comunidad_autonoma'] === 'Canarias' ? 'selected' : ''; ?>>Canarias</option>
            <option value="Cantabria" <?= $ferrata['comunidad_autonoma'] === 'Cantabria' ? 'selected' : ''; ?>>Cantabria</option>
            <option value="Castilla-La Mancha" <?= $ferrata['comunidad_autonoma'] === 'Castilla-La Mancha' ? 'selected' : ''; ?>>Castilla-La Mancha</option>
            <option value="Castilla y León" <?= $ferrata['comunidad_autonoma'] === 'Castilla y León' ? 'selected' : ''; ?>>Castilla y León</option>
            <option value="Cataluña" <?= $ferrata['comunidad_autonoma'] === 'Cataluña' ? 'selected' : ''; ?>>Cataluña</option>
            <option value="Extremadura" <?= $ferrata['comunidad_autonoma'] === 'Extremadura' ? 'selected' : ''; ?>>Extremadura</option>
            <option value="Galicia" <?= $ferrata['comunidad_autonoma'] === 'Galicia' ? 'selected' : ''; ?>>Galicia</option>
            <option value="Madrid" <?= $ferrata['comunidad_autonoma'] === 'Madrid' ? 'selected' : ''; ?>>Madrid</option>
            <option value="Murcia" <?= $ferrata['comunidad_autonoma'] === 'Murcia' ? 'selected' : ''; ?>>Región de Murcia</option>
            <option value="Navarra" <?= $ferrata['comunidad_autonoma'] === 'Navarra' ? 'selected' : ''; ?>>Navarra</option>
            <option value="País Vasco" <?= $ferrata['comunidad_autonoma'] === 'País Vasco' ? 'selected' : ''; ?>>País Vasco</option>
            <option value="La Rioja" <?= $ferrata['comunidad_autonoma'] === 'La Rioja' ? 'selected' : ''; ?>>La Rioja</option>
            <option value="Valencia" <?= $ferrata['comunidad_autonoma'] === 'Valencia' ? 'selected' : ''; ?>>Comunidad Valenciana</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="provincia" class="form-label">Provincia:</label>
        <input type="text" name="provincia" id="provincia" value="<?= htmlspecialchars($ferrata['provincia']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="dificultad" class="form-label">Dificultad:</label>
        <select name="dificultad" id="dificultad" class="form-select">
            <?php foreach (['K1','K2','K3','K4','K5','K6','K7'] as $nivel): ?>
                <option value="<?= $nivel; ?>" <?= $nivel == $ferrata['dificultad'] ? 'selected' : ''; ?>><?= $nivel; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea name="descripcion" id="descripcion" class="form-control"><?= htmlspecialchars($ferrata['descripcion']); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="coordenadas" class="form-label">Coordenadas:</label>
        <input type="text" name="coordenadas" id="coordenadas" value="<?= htmlspecialchars($ferrata['coordenadas'] ?? ''); ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label for="estado" class="form-label">Estado:</label>
        <select name="estado" id="estado" class="form-select">
            <?php foreach (['Abierta','Cerrada','No operativa'] as $estado): ?>
                <option value="<?= $estado; ?>" <?= $estado == $ferrata['estado'] ? 'selected' : ''; ?>><?= $estado; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="fecha_creacion" class="form-label">Fecha de Creación:</label>
        <input type="date" name="fecha_creacion" id="fecha_creacion" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>

<!-- Sección de administración de imágenes -->
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
<br>
<!-- Sección para añadir nuevas imágenes -->
<form action="/RedFerratera/index.php?accion=subir_imagen" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="ferrata_id" value="<?= $ferrata_id; ?>">
    <div class="mb-3">
        <label for="imagenes_nuevas" class="form-label">Añadir nuevas imágenes:</label>
        <input type="file" name="imagenes[]" id="imagenes_nuevas" multiple accept="image/*" class="form-control">
        <button type="submit" class="btn btn-primary mt-2">Subir imágenes</button>
    </div>
</form>

<!-- Modal para ampliar imágenes (se conserva) -->
<div id="modalImagen" class="modal">
    <span class="cerrar" onclick="cerrarModalImagen()">&times;</span>
    <img class="modal-contenido" id="imagenAmpliada">
</div>

<!-- Sección de administración de vídeos -->
<?php
require_once 'app/models/Video.php';
$videos = Video::getByFerrataId($ferrata['id']);
if (!empty($videos)):
?>
    <h3 class="mt-4">Vídeos</h3>
    <div id="video-container" class="embed-container">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $vid): ?>
                <div class="position-relative embed-item">
                    <!-- Mostrar el iframe embed -->
                    <?= $vid['video_embed']; ?>
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                        <!-- Botón para borrar el vídeo -->
                        <a href="/RedFerratera/index.php?accion=borrar_video&id=<?= $vid['id']; ?>&ferrata_id=<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;">X</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No hay vídeos actualmente.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<!-- Sección para agregar vídeo (siempre visible) -->
<form action="/RedFerratera/index.php?accion=subir_video" method="POST" class="mb-4">
    <input type="hidden" name="ferrata_id" value="<?= $ferrata_id; ?>">
    <div class="mb-3">
        <label for="video_embed" class="form-label">Agregar vídeo (código embed):</label>
        <textarea name="video" id="video_embed" class="form-control" placeholder='<iframe width="560" height="315" src="https://www.youtube.com/embed/VIDEO_ID" frameborder="0" allowfullscreen></iframe>'></textarea>
        <button type="submit" class="btn btn-primary mt-2">Guardar Vídeo</button>
    </div>
</form>

<!-- Sección de administración de enlaces Wikiloc -->
<?php
require_once 'app/models/Wikiloc.php';
$enlaces = Wikiloc::getByFerrataId($ferrata['id']);
if (!empty($enlaces)):
?>
    <h3 class="mt-4">Track de Wikiloc</h3>
    <div id="wikiloc-container" class="embed-container">
        <?php if (!empty($enlaces)): ?>
            <?php foreach ($enlaces as $enlace): ?>
                <div class="position-relative embed-item">
                    <!-- Mostrar el iframe embed -->
                    <?= $enlace['wikiloc_embed']; ?>
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                        <!-- Botón de borrado -->
                        <a href="/RedFerratera/index.php?accion=borrar_wikiloc&id=<?= $enlace['id']; ?>&ferrata_id=<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;">X</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No hay enlaces Wikiloc actualmente.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<!-- Sección para agregar enlace Wikiloc (siempre visible) -->
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

<!-- Sección de comentarios -->
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
