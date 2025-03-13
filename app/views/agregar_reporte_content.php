<?php
require_once __DIR__ . '/../models/Ferrata.php';
$ferrataModel = new Ferrata();
$ferratas = $ferrataModel->obtenerFerratasParaReporte();
?>

<h1>Añadir reporte</h1>
<form action="/RedFerratera/index.php?accion=guardar_reporte" method="POST">
    <!-- Selección de Ferrata -->
    <label for="ferrata_id">Selecciona la Ferrata:</label><br>
    <select name="ferrata_id" id="ferrata_id" required>
        <?php foreach ($ferratas as $ferrata): ?>
            <option value="<?= $ferrata['id']; ?>"><?= htmlspecialchars($ferrata['nombre']); ?></option>
        <?php endforeach; ?>
    </select><br>

    <!-- Descripción del problema -->
    <label for="descripcion">Descripción del reporte:</label><br>
    <textarea name="descripcion" id="descripcion" required></textarea><br>
    <button type="submit">Enviar Reporte</button>
</form>


