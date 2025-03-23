<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h1 class="text-center mb-3">Restablecer Contraseña</h1>
<p class="text-center mb-4">Ingresa tu nueva contraseña para continuar.</p>

<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="/RedFerratera/index.php?accion=procesar_cambio_clave" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="token" value="<?= $_GET['token'] ?? '' ?>">

            <!-- Campo Nueva Contraseña -->
            <div class="mb-3">
                <label for="nueva_clave" class="form-label">Nueva Contraseña</label>
                <input type="password" name="nueva_clave" id="nueva_clave" class="form-control" required>
                <small class="form-text text-muted">Debe tener al menos 6 caracteres.</small>
            </div>

            <!-- Botón Restablecer -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Restablecer</button>
            </div>
        </form>

        <!-- Enlace de regreso -->
        <div class="mt-3 text-center">
            ¿Ya tienes cuenta? <a href="/RedFerratera/login">Inicia sesión</a>
        </div>
    </div>
</div>
