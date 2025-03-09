<h1 class="text-center">Restablecer Contraseña</h1>
<p class="text-center">Ingresa tu nueva contraseña.</p>

<form action="/RedFerratera/index.php?accion=procesar_cambio_clave" method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token'] ?? ''; ?>">

    <div class="mb-3">
        <label for="nueva_clave" class="form-label">Nueva Contraseña</label>
        <input type="password" name="nueva_clave" id="nueva_clave" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Restablecer</button>
</form>
