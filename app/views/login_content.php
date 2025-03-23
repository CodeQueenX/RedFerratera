<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h1 class="text-center mb-4">Iniciar Sesión</h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="index.php?accion=login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Correo electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="email" id="email"
                       value="<?= htmlspecialchars($_COOKIE['usuario_email'] ?? '') ?>" required>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label for="clave" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="clave" id="clave" required>
            </div>

            <!-- Recordar contraseña -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="recordar" id="recordar">
                <label class="form-check-label" for="recordar">Recordar contraseña</label>
            </div>

            <!-- Botón -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>

        <!-- Enlaces -->
        <div class="mt-3 text-center">
            <a href="/RedFerratera/index.php?accion=recuperar_clave">¿Olvidaste tu contraseña?</a><br>
            ¿No tienes cuenta? <a href="/RedFerratera/registrar">Regístrate aquí</a>
        </div>
    </div>
</div>
