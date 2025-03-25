<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuario;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->usuario = new Usuario();
    }
    
    // Registrar nuevo usuario con verificación por email
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            unset($_SESSION['csrf_token']); // Limpiar token
            
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contraseña = $_POST['clave'] ?? '';
            
            if ($nombre && $email && $contraseña) {
                // Verificar si el email ya existe
                if ($this->usuario->existeEmail($email)) {
                    echo "<div style='text-align:center; padding: 20px; background: #f8d7da; color: #721c24; font-size: 18px; border: 1px solid #f5c6cb;'>
                            ❌ <strong>Error:</strong> Este email ya está registrado.<br><br>
                            <a href='/RedFerratera/registrar' style='display:inline-block; padding:10px 20px; background:#dc3545; color:white; text-decoration:none; border-radius:5px;'>Volver a Registro</a>
                          </div>";
                    return;
                }
                
                $token = bin2hex(random_bytes(32));
                
                if ($this->usuario->registrar($nombre, $email, $contraseña, $token)) {
                    // Enviar correo de activación
                    $asunto = "Activa tu cuenta en Red Ferratera";
                    $mensaje = "Haz clic aquí para activar tu cuenta:\n\n";
                    $mensaje .= "http://localhost/RedFerratera/index.php?accion=activar_cuenta&token=$token";
                    $cabeceras = "From: no-reply@redferratera.com\r\n" .
                        "Reply-To: no-reply@redferratera.com\r\n" .
                        "X-Mailer: PHP/" . phpversion();
                    
                    mail($email, $asunto, $mensaje, $cabeceras);
                    
                    echo "<div style='text-align:center; padding: 20px; background: #dff0d8; color: #3c763d; font-size: 18px; border: 1px solid #d6e9c6;'>
                            ✅ <strong>Registro exitoso.</strong> Revisa tu correo para activar tu cuenta.
                          </div>";
                } else {
                    echo "Error al registrar el usuario.";
                }
            } else {
                echo "Completa todos los campos.";
            }
        } else {
            include __DIR__ . '/../views/registro.php';
        }
    }
    
    // Activar cuenta de usuario
    public function activarCuenta() {
        $token = $_GET['token'] ?? null;
        
        if ($token && $this->usuario->activarCuenta($token)) {
            echo "<div style='text-align:center; padding: 20px; background: #dff0d8; color: #3c763d; font-size: 18px; border: 1px solid #d6e9c6;'>
                    ✅ <strong>Cuenta activada correctamente.</strong> Ahora puedes iniciar sesión. <br><br>
                    <a href='/RedFerratera/login' style='display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Iniciar Sesión</a>
                  </div>";
        } else {
            echo "Error: Token inválido o expirado.";
        }
    }
    
    // Iniciar sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            unset($_SESSION['csrf_token']); // Limpiar token
            
            $email = $_POST['email'] ?? '';
            $contraseña = $_POST['clave'] ?? '';
            
            if ($email && $contraseña) {
                $usuario = $this->usuario->login($email, $contraseña);
                if ($usuario) {
                    $_SESSION['usuario'] = [
                        "id" => $usuario['id'],
                        "nombre" => $usuario['nombre'],
                        "email" => $usuario['email'],
                        "rol" => $usuario['rol'],
                        "verificado" => $usuario['verificado']
                    ];
                    
                    // Guardar cookie si se selecciona "recordar"
                    if (isset($_POST['recordar'])) {
                        setcookie("usuario_email", $usuario['email'], time() + (86400 * 30), "/");
                    }
                    
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Credenciales incorrectas.";
                }
            } else {
                echo "Por favor, completa todos los campos.";
            }
        } else {
            include __DIR__ . '/../views/login.php';
        }
    }
    
    // Enviar enlace de recuperación de contraseña
    public function enviarRecuperacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Error: Token CSRF inválido.");
            }
            
            $email = $_POST['email'] ?? '';
            
            if ($email) {
                $token = bin2hex(random_bytes(32));
                $usuario = new Usuario();
                
                if ($usuario->guardarTokenRecuperacion($email, $token)) {
                    $enlace = "http://localhost/RedFerratera/index.php?accion=restablecer_clave&token=$token";
                    $mensaje = "Haz clic en este enlace para restablecer tu contraseña: <a href='$enlace'>$enlace</a>";
                    $headers = "From: no-reply@redferratera.com\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    
                    if (mail($email, "Restablecer contraseña", $mensaje, $headers)) {
                        echo "<div class='alert alert-success text-center mt-4'>✅ Correo enviado. Revisa tu bandeja de entrada.</div>";
                    } else {
                        echo "<div class='alert alert-danger text-center mt-4'>⚠️ No se pudo enviar el correo.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning text-center mt-4'>⚠️ Este correo no está registrado.</div>";
                }
            } else {
                echo "<div class='alert alert-warning text-center mt-4'>⚠️ Debes ingresar tu correo.</div>";
            }
        }
    }
    
    // Procesar cambio de contraseña desde el enlace
    public function procesarCambioClave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Error: Token CSRF inválido.");
            }
            
            $token = $_POST['token'] ?? null;
            $nuevaClave = $_POST['nueva_clave'] ?? '';
            
            if ($token && $nuevaClave) {
                if ($this->usuario->actualizarClavePorToken($token, $nuevaClave)) {
                    $this->usuario->borrarTokenRecuperacion($token);
                    
                    echo "<div class='alert alert-success text-center mt-4'>✅ Contraseña restablecida correctamente. <a href='/RedFerratera/login'>Iniciar sesión</a></div>";
                } else {
                    echo "<div class='alert alert-danger text-center mt-4'>⚠️ Error al actualizar la contraseña o el token ha expirado.</div>";
                }
            } else {
                echo "<div class='alert alert-warning text-center mt-4'>⚠️ Todos los campos son obligatorios.</div>";
            }
        }
    }
    
    // Cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        setcookie("usuario_email", "", time() - 3600, "/");
        
        header("Location: index.php");
        exit();
    }
}
?>
