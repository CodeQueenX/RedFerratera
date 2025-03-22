<?php
require_once __DIR__ . '/../models/Ferrata.php';
$ferrataModel = new Ferrata();
$ferratas = $ferrataModel->obtenerFerratasParaReporte();
?>

<div class="container">
<h1>Añadir reporte</h1>
    <div class="row">
        <div class="col-md-8">
            <form action="/RedFerratera/index.php?accion=guardar_reporte" method="POST">
                <!-- Selección de Ferrata -->
                <div class="mb-3">
                  <label for="ferrata_id" class="form-label">Selecciona la Ferrata:</label>
                  <select name="ferrata_id" id="ferrata_id" class="form-select" required>
                    <?php foreach ($ferratas as $ferrata): ?>
                      <option value="<?= $ferrata['id']; ?>"><?= htmlspecialchars($ferrata['nombre']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <!-- Descripción del problema -->
                <div class="mb-3">
                  <label for="descripcion" class="form-label">Descripción del reporte:</label>
                  <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
                </div>
                <button type="submit">Enviar Reporte</button>
            </form>
        </div>
    </div>
</div>


