<h1 class="text-center">Editar Ferrata</h1>

<!-- Formulario para editar datos de la ferrata -->
<form action="/RedFerratera/index.php?accion=guardar_edicion_ferrata" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="accion" value="guardar_edicion_ferrata">
    <input type="hidden" name="id" value="<?= $ferrata['id']; ?>">
    
    <!-- Campo oculto para saber si la edición viene de "Gestionar Ferratas" -->
    <input type="hidden" name="desde_gestion" value="<?= isset($_GET['desde_gestion']) ? 1 : 0; ?>">

    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($ferrata['nombre']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Ubicación:</label>
        <input type="text" name="ubicacion" value="<?= htmlspecialchars($ferrata['ubicacion']); ?>" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label>Comunidad Autónoma:</label>
        <select name="comunidad_autonoma" class="form-control" required>
            <option value="">Selecciona una comunidad</option>
            <option value="Andalucía">Andalucía</option>
            <option value="Aragón">Aragón</option>
            <option value="Asturias">Asturias</option>
            <option value="Baleares">Islas Baleares</option>
            <option value="Canarias">Canarias</option>
            <option value="Cantabria">Cantabria</option>
            <option value="Castilla-La Mancha">Castilla-La Mancha</option>
            <option value="Castilla y León">Castilla y León</option>
            <option value="Cataluña">Cataluña</option>
            <option value="Extremadura">Extremadura</option>
            <option value="Galicia">Galicia</option>
            <option value="Madrid">Madrid</option>
            <option value="Murcia">Región de Murcia</option>
            <option value="Navarra">Navarra</option>
            <option value="País Vasco">País Vasco</option>
            <option value="La Rioja">La Rioja</option>
            <option value="Valencia">Comunidad Valenciana</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label>Provincia:</label>
        <input type="text" name="provincia" value="<?= htmlspecialchars($ferrata['provincia']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Dificultad:</label>
        <select name="dificultad" class="form-select">
            <?php foreach (['K1', 'K2', 'K3', 'K4', 'K5', 'K6', 'K7'] as $nivel): ?>
                <option value="<?= $nivel; ?>" <?= $nivel == $ferrata['dificultad'] ? 'selected' : ''; ?>><?= $nivel; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control"><?= htmlspecialchars($ferrata['descripcion']); ?></textarea>
    </div>

    <div class="mb-3">
        <label>Coordenadas:</label>
        <input type="text" name="coordenadas" value="<?= htmlspecialchars($ferrata['coordenadas'] ?? ''); ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-select">
            <?php foreach (['Abierta', 'Cerrada', 'No operativa'] as $estado): ?>
                <option value="<?= $estado; ?>" <?= $estado == $ferrata['estado'] ? 'selected' : ''; ?>><?= $estado; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Fecha de Creación:</label>
        <input type="date" name="fecha_creacion" value="<?= ($ferrata['fecha_creacion'] == '0000-00-00 00:00:00' ? '' : date('Y-m-d', strtotime($ferrata['fecha_creacion']))); ?>" class="form-control">
    </div>

    <!-- Galería de imágenes en edición -->
    <h3 class="mt-4"><i class="lucide lucide-image"></i> Imágenes de la ferrata</h3>
    <div class="galeria-detalle editar-ferrata">
        <?php foreach ($imagenes as $img): ?>
            <div class="position-relative imagen-contenedor">
                <img src="/RedFerratera/public/img/ferratas/<?= htmlspecialchars($img['ruta']); ?>" 
                     alt="Imagen de la ferrata"
                     class="img-thumbnail small-image"
                     onerror="this.onerror=null; this.src='/RedFerratera/public/img/default.jpg';">
                
                <!-- X para eliminar imágenes (solo para administradores) -->
                <a href="/RedFerratera/eliminar-imagen/<?= $img['id']; ?>/ferrata/<?= $ferrata['id']; ?>" 
                   class="boton-eliminar">❌</a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal para ampliar imágenes -->
    <div id="modalImagen" class="modal">
        <span class="cerrar" onclick="cerrarModalImagen()">&times;</span>
        <img class="modal-contenido" id="imagenAmpliada">
    </div>

    <div class="mb-3">
        <label class="form-label">Añadir nuevas imágenes</label>
        <input type="file" name="imagenes[]" multiple accept="image/*" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>

<!-- Botón para eliminar la ferrata -->
<form action="/RedFerratera/index.php?accion=eliminar_ferrata&id=<?= $ferrata['id']; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta ferrata? Esta acción no se puede deshacer.')">
    <button type="submit" class="btn btn-danger mt-3">Eliminar Ferrata</button>
</form>

<h3 class="mt-4">Comentarios</h3>
<?php if (!empty($comentarios)): ?>
    <ul class="list-group">
        <?php foreach ($comentarios as $comentario): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($comentario['usuario']); ?>:</strong> 
                <?= htmlspecialchars($comentario['comentario']); ?> 
                <em>(<?= htmlspecialchars($comentario['fecha_comentario']); ?>)</em>
                <a href="/RedFerratera/eliminar-comentario/<?= $comentario['id']; ?>&ferrata_id=<?= $ferrata['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="text-muted">No hay comentarios aún.</p>
<?php endif; ?>
