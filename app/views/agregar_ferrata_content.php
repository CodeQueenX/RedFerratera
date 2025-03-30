<?php
// Iniciar sesión y generar token si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Añadir Ferrata -->
<div class="container">
    <h1 class="text-center mb-4">Añadir Nueva Ferrata</h1>

    <form action="/RedFerratera/index.php?accion=agregar_ferrata" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Nombre -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la ferrata:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <!-- Ubicación -->
        <div class="mb-3">
            <label for="ubicacion" class="form-label">Ubicación (Localidad):</label>
            <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
        </div>

        <!-- Comunidad Autónoma -->
        <div class="mb-3">
            <label for="comunidad_autonoma" class="form-label">Comunidad Autónoma:</label>
            <select id="comunidad_autonoma" name="comunidad_autonoma" class="form-select" required>
                <option value="">Selecciona una comunidad</option>
                <?php
                $comunidades = [
                    "Andalucía", "Aragón", "Asturias", "Baleares", "Canarias", "Cantabria",
                    "Castilla-La Mancha", "Castilla y León", "Cataluña", "Extremadura", "Galicia",
                    "Madrid", "Murcia", "Navarra", "País Vasco", "La Rioja", "Comunidad Valenciana"
                ];
                foreach ($comunidades as $comunidad):
                ?>
                    <option value="<?= $comunidad ?>"><?= $comunidad ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Provincia -->
        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia:</label>
            <input type="text" id="provincia" name="provincia" class="form-control" required>
        </div>

        <!-- Dificultad -->
        <div class="mb-3">
            <label for="dificultad" class="form-label">Dificultad (K1 - K7):</label>
            <select id="dificultad" name="dificultad" class="form-select" required>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <option value="K<?= $i ?>">K<?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Descripción -->
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
        </div>

        <!-- Coordenadas -->
        <div class="mb-3">
            <label for="coordenadas" class="form-label">Coordenadas (latitud, longitud):</label>
            <input type="text" id="coordenadas" name="coordenadas" class="form-control">
        </div>

        <!-- Fecha de creación -->
        <div class="mb-3">
            <label for="fecha_creacion" class="form-label">Fecha de Creación:</label>
            <input type="date" id="fecha_creacion" name="fecha_creacion" class="form-control" required>
        </div>

        <!-- Fechas de cierre -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_inicio_cierre" class="form-label">Fecha de Inicio de Cierre:</label>
                <input type="date" id="fecha_inicio_cierre" name="fecha_inicio_cierre" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="fecha_fin_cierre" class="form-label">Fecha de Fin de Cierre:</label>
                <input type="date" id="fecha_fin_cierre" name="fecha_fin_cierre" class="form-control">
            </div>
        </div>

        <!-- Cierre recurrente -->
        <div class="mb-3 form-check">
            <input type="checkbox" id="recurrente" name="recurrente" value="1" class="form-check-input" <?= (isset($ferrata['recurrente']) && $ferrata['recurrente'] == 1) ? 'checked' : ''; ?>>
            <label for="recurrente" class="form-check-label">Cierre recurrente</label>
            <div class="form-text">Las fechas de cierre se aplican cada año si está marcado.</div>
        </div>

        <!-- Subir imágenes (solo admin) -->
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
            <div class="mb-3">
                <label for="imagenes" class="form-label">Subir imágenes:</label>
                <input type="file" id="imagenes" name="imagenes[]" class="form-control" multiple accept="image/*">
            </div>
        <?php endif; ?>

        <!-- Botón -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-4">Agregar Ferrata</button>
        </div>
    </form>
</div>
