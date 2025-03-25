<?php
// Iniciar sesión y generar token si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Formulario de Contacto -->
<h1 class="text-center text-primary fw-bold my-4">Formulario de Contacto</h1>

<!-- Mensaje de introducción -->
<p class="text-center text-muted mb-4">
    ¿Tienes dudas, sugerencias o deseas colaborar?
</p>
<p class="text-center text-muted mb-4">
    Completa el formulario y nos pondremos en contacto contigo lo antes posible
</p>

<!-- Formulario -->
<form action="/RedFerratera/index.php?accion=enviar_contacto" method="POST" class="mx-auto" style="max-width: 700px;">
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Campo nombre -->
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control" required autocomplete="name">
    </div>

    <!-- Campo email -->
    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control" required autocomplete="email">
    </div>

    <!-- Campo mensaje -->
    <div class="mb-3">
        <label for="mensaje" class="form-label">Mensaje <span class="text-danger">*</span></label>
        <textarea name="mensaje" id="mensaje" class="form-control" rows="5" required></textarea>
    </div>

    <!-- Botón enviar -->
    <div class="text-center">
        <button type="submit" class="btn btn-primary px-4">Enviar</button>
    </div>
</form>

<!-- Email alternativo de contacto -->
<p class="text-center mt-4 text-muted">
    También puedes escribirnos directamente a:
    <a href="mailto:megidorico@gmail.com" class="text-decoration-none">megidorico@gmail.com</a>
</p>
