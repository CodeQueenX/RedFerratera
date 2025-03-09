<h1 class="text-center">Contacto</h1>
<p class="text-center">Si tienes dudas, sugerencias o quieres colaborar, contáctanos a través del siguiente formulario.</p>

<form action="/RedFerratera/index.php?accion=enviar_contacto" method="POST">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="mensaje" class="form-label">Mensaje</label>
        <textarea name="mensaje" id="mensaje" class="form-control" rows="4" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<p class="text-center mt-3">
    También puedes enviarnos un correo directamente a:  
    <a href="mailto:megidorico@gmail.com">megidorico@gmail.com</a>
</p>

