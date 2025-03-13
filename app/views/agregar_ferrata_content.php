<h1 class="text-center">Agregar Nueva Ferrata</h1>
<form action="/RedFerratera/index.php?accion=agregar_ferrata" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la ferrata:</label>
        <input type="text" class="form-control" name="nombre" id="nombre" required>
    </div>
    <div class="mb-3">
        <label for="ubicacion" class="form-label">Ubicación (Provincia, Comunidad):</label>
        <input type="text" class="form-control" name="ubicacion" id="ubicacion" required>
    </div>
    <div class="mb-3">
        <label for="comunidad_autonoma" class="form-label">Comunidad Autónoma:</label>
        <select name="comunidad_autonoma" id="comunidad_autonoma" class="form-control" required>
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
        <label for="provincia" class="form-label">Provincia:</label>
        <input type="text" class="form-control" name="provincia" id="provincia" required>
    </div>
    <div class="mb-3">
        <label for="dificultad" class="form-label">Dificultad (K1 - K7):</label>
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
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label for="coordenadas" class="form-label">Coordenadas (latitud, longitud):</label>
        <input type="text" class="form-control" name="coordenadas" id="coordenadas">
    </div>
    <div class="mb-3">
        <label for="fecha_creacion" class="form-label">Fecha de Creación de la Ferrata:</label>
        <input type="date" name="fecha_creacion" id="fecha_creacion" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="fecha_inicio_cierre" class="form-label">Fecha de Inicio de Cierre:</label>
        <input type="date" name="fecha_inicio_cierre" id="fecha_inicio_cierre" class="form-control">
    </div>
    <div class="mb-3">
        <label for="fecha_fin_cierre" class="form-label">Fecha de Fin de Cierre:</label>
        <input type="date" name="fecha_fin_cierre" id="fecha_fin_cierre" class="form-control">
    </div>
    <div class="mb-3">
        <label for="recurrente" class="form-label">Cierre recurrente:</label>
        <!-- Usamos checkbox; cuando se marque, el valor será "1" -->
        <input type="checkbox" name="recurrente" id="recurrente" value="1" <?= (isset($ferrata['recurrente']) && $ferrata['recurrente'] == 1) ? 'checked' : ''; ?>>
        <small class="form-text text-muted">Si está marcado, las fechas de cierre se aplican cada año.</small>
    </div>
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
        <div class="mb-3">
            <label for="imagenes" class="form-label">Subir imágenes:</label>
            <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*" class="form-control">
        </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Agregar Ferrata</button>
</form>