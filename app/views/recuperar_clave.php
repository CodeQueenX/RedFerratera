<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h1 class="text-center mb-3">Recuperar Contraseña</h1>
<p class="text-center mb-4">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecerla.</p>

<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="/RedFerratera/index.php?accion=enviar_recuperacion" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Campo Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <!-- Botón Enviar -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>

        <!-- Enlace de regreso -->
        <div class="mt-3 text-center">
            ¿Recuerdas tu contraseña? <a href="/RedFerratera/login">Inicia sesión</a>
        </div>
    </div>
</div>
