<h1 class="text-center">Iniciar Sesión</h1>
<form action="index.php?accion=login" method="POST">
    <div class="form-group mt-3">
        <label for="email">Correo electrónico:</label>
        <input type="email" class="form-control" name="email" id="email" value="<?php echo $_COOKIE['usuario_email'] ?? ''; ?>" required>
    </div>
    <div class="form-group mt-3">
        <label for="contraseña">Contraseña:</label>
        <input type="password" class="form-control" name="clave" id="clave" required>
    </div>
    <div class="form-check mt-3">
        <input type="checkbox" class="form-check-input" name="recordar" id="recordar">
        <label class="form-check-label" for="recordar">Recordar contraseña</label>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Iniciar Sesión</button>
</form>
<p class="mt-3">
    <a href="/RedFerratera/index.php?accion=recuperar_clave">¿Olvidaste tu contraseña?</a>
</p>
<p>¿No tienes cuenta? <a href="/RedFerratera/registrar">Regístrate aquí</a></p>

