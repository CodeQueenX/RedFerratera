<?php
class ContactoController {
    
    // Enviar mensaje desde el formulario de contacto
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Verificar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die("<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                        ⚠️ <strong>Error de seguridad.</strong> Token CSRF inválido.
                    </div>");
            }
            
            // Eliminar el token tras su uso
            unset($_SESSION['csrf_token']);
            
            // Recoger y limpiar datos del formulario
            $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
            $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
            $mensaje = trim(strip_tags($_POST['mensaje'] ?? ''));
            
            // Validar que los campos no estén vacíos
            if ($nombre && $email && $mensaje) {
                
                // Validar formato del email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                            ⚠️ <strong>Email inválido.</strong> Por favor revisa tu dirección.
                          </div>";
                    return;
                }
                
                // Datos del email
                $para = "megidorico@gmail.com";
                $asunto = "📬 Nuevo mensaje de contacto desde Red Ferratera";
                $cuerpo = "Has recibido un mensaje desde el formulario de contacto:\n\n";
                $cuerpo .= "Nombre: $nombre\n";
                $cuerpo .= "Correo: $email\n\n";
                $cuerpo .= "Mensaje:\n$mensaje\n";
                
                // Cabeceras del email
                $cabeceras = "From: noreply@redferratera.com\r\n";
                $cabeceras .= "Reply-To: $email\r\n";
                $cabeceras .= "X-Mailer: PHP/" . phpversion();
                
                // Enviar el mensaje
                if (mail($para, $asunto, $cuerpo, $cabeceras)) {
                    echo "<div style='text-align:center; padding: 20px; background: #dff0d8; color: #3c763d; font-size: 18px; border: 1px solid #d6e9c6;'>
                            ✅ <strong>Mensaje enviado correctamente.</strong> Gracias por tu interés.<br><br>
                            <a href='/RedFerratera/' style='display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Volver al inicio</a>
                          </div>";
                } else {
                    echo "<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                            ⚠️ <strong>No se pudo enviar el mensaje.</strong> Inténtalo de nuevo más tarde.
                          </div>";
                }
                
            } else {
                echo "<div style='text-align:center; padding: 20px; background: #f2dede; color: #a94442; font-size: 18px; border: 1px solid #ebccd1;'>
                        ⚠️ <strong>Por favor, completa todos los campos.</strong>
                      </div>";
            }
        }
    }
}
?>
