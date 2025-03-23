<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h1 class="text-center mb-4">Registro de Usuario</h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="index.php?accion=registrar" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Nombre -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required>
            </div>

            <!-- Correo -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label for="clave" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="clave" id="clave" required>
            </div>

            <!-- Botón -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>
        </form>

        <!-- Enlace a login -->
        <div class="mt-3 text-center">
            ¿Ya tienes cuenta? <a href="/RedFerratera/login">Inicia sesión</a>
        </div>
    </div>
</div>
