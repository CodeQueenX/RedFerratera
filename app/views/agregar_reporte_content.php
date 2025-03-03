<?php
require_once __DIR__ . '/../models/Ferrata.php';
$ferrataModel = new Ferrata();
$ferratas = $ferrataModel->obtenerFerratas();
?>

<h1>Añadir reporte</h1>
<form action="/RedFerratera/index.php?accion=guardar_reporte" method="POST">
    <!-- Selección de Ferrata -->
    <label for="ferrata_id">Selecciona la Ferrata:</label>
    <select name="ferrata_id" required>
        <?php foreach ($ferratas as $ferrata): ?>
            <option value="<?= $ferrata['id']; ?>"><?= htmlspecialchars($ferrata['nombre']); ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Descripción del problema -->
    <label for="descripcion">Descripción del reporte:</label>
    <textarea name="descripcion" required></textarea>

    <button type="submit">Enviar Reporte</button>
</form>

