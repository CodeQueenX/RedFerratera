<?php
class ContactoController {
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $mensaje = $_POST['mensaje'] ?? '';

            if ($nombre && $email && $mensaje) {
                $para = "megidorico@gmail.com"; 
                $asunto = "Nuevo mensaje de contacto";
                $cuerpo = "Nombre: $nombre\nCorreo: $email\nMensaje:\n$mensaje";
                $cabeceras = "From: $email";

                if (mail($para, $asunto, $cuerpo, $cabeceras)) {
                    echo "<div style='text-align:center; padding: 20px; background: #dff0d8; color: #3c763d; font-size: 18px; border: 1px solid #d6e9c6;'>
                            ✅ <strong>Mensaje enviado correctamente.</strong> Nos pondremos en contacto contigo pronto.<br><br>
                            <a href='/RedFerratera/' style='display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Volver a Inicio</a>
                          </div>";
                } else {
                    echo "<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                            ⚠️ <strong>Error al enviar el mensaje.</strong> Inténtalo más tarde.
                          </div>";
                }
            } else {
                echo "<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                        ⚠️ <strong>Todos los campos son obligatorios.</strong>
                      </div>";
            }
        }
    }
}
?>
