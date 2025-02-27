<h1 class="text-center">Registro de Usuario</h1>
<form action="index.php?accion=registrar" method="POST">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" class="form-control" name="nombre" id="nombre" required>
    </div>
    <div class="form-group">
        <label for="email">Correo electrónico:</label>
        <input type="email" class="form-control" name="email" id="email" required>
    </div>
    <div class="form-group">
        <label for="contraseña">Contraseña:</label>
        <input type="password" class="form-control" name="clave" id="clave" required>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Registrarse</button>
</form>
<p class="mt-3">¿Ya tienes cuenta? <a href="/RedFerratera/login">Inicia sesión</a></p>
