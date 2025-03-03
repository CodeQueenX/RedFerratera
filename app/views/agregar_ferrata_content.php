<h1 class="text-center">Agregar Nueva Ferrata</h1>
<form action="/RedFerratera/index.php?accion=agregar_ferrata" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="nombre">Nombre de la ferrata:</label>
        <input type="text" class="form-control" name="nombre" id="nombre" required>
    </div>
    <div class="mb-3">
        <label for="ubicacion">Ubicación (Provincia, Comunidad):</label>
        <input type="text" class="form-control" name="ubicacion" id="ubicacion" required>
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
        <input type="text" class="form-control" name="provincia" required>
    </div>
    <div class="mb-3">
        <label for="dificultad">Dificultad (K1 - K7):</label>
        <select name="dificultad" id="dificultad" class="form-control" required>
            <option value="K1">K1</option>
            <option value="K2">K2</option>
            <option value="K3">K3</option>
            <option value="K4">K4</option>
            <option value="K5">K5</option>
            <option value="K6">K6</option>
            <option value="K7">K7</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label for="coordenadas">Coordenadas (latitud, longitud):</label>
        <input type="text" class="form-control" name="coordenadas" id="coordenadas">
    </div>
    <div class="mb-3">
        <label for="fecha_creacion">Fecha de creación (AAAA-MM-DD):</label>
        <input type="date" class="form-control" name="fecha_creacion" id="fecha_creacion">
    </div>
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
        <div class="mb-3">
            <label class="form-label">Subir imágenes:</label>
            <input type="file" name="imagenes[]" multiple accept="image/*" class="form-control">
        </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Agregar Ferrata</button>
</form>
