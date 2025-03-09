<h1 class="text-center">Recuperar Contraseña</h1>
<p class="text-center">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.</p>

<form action="/RedFerratera/index.php?accion=enviar_recuperacion" method="POST">
    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary mb-3">Enviar</button>
</form>
