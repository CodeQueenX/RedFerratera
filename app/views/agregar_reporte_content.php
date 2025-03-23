<?php
require_once __DIR__ . '/../models/Ferrata.php';
$ferrataModel = new Ferrata();
$ferratas = $ferrataModel->obtenerFerratasParaReporte();

// Iniciar sesión y generar token si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="container">
  <h1 class="text-center mb-4">Añadir Reporte</h1>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <form action="/RedFerratera/index.php?accion=guardar_reporte" method="POST">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Selección de Ferrata -->
        <div class="mb-3">
          <label for="ferrata_id" class="form-label">Selecciona la Ferrata:</label>
          <select name="ferrata_id" id="ferrata_id" class="form-select" required>
            <option value="">-- Elige una ferrata --</option>
            <?php foreach ($ferratas as $ferrata): ?>
              <option value="<?= $ferrata['id']; ?>"><?= htmlspecialchars($ferrata['nombre']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Descripción -->
        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción del problema:</label>
          <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required minlength="5"></textarea>
          <div class="form-text">Describe el problema con detalle. Mínimo 5 caracteres.</div>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-primary">Enviar Reporte</button>
        </div>
      </form>
    </div>
  </div>
</div>
