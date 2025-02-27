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
    
    // Método para registrar un nuevo usuario
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contraseña = $_POST['clave'] ?? '';
            
            if ($nombre && $email && $contraseña) {       
                if ($this->usuario->registrar($nombre, $email, $contraseña)) {
                    echo "Usuario registrado correctamente.";
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
    
    // Método para iniciar sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $contraseña = $_POST['clave'] ?? '';
            
            if ($email && $contraseña) {
                $usuario = $this->usuario->login($email, $contraseña);
                if ($usuario) {
                    $_SESSION['usuario'] = [
                        "id" => $usuario['id'],
                        "nombre" => $usuario['nombre'],
                        "email" => $usuario['email'],
                        "rol" => $usuario['rol']
                    ];
                    
                    // Opción "Recordar contraseña" usando cookies
                    if (isset($_POST['recordar'])) {
                        setcookie("usuario_email", $usuario['email'], time() + (86400 * 30), "/"); // 30 días
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
    
    // Método para cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        setcookie("usuario_email", "", time() - 3600, "/"); // Elimina la cookie
        
        header("Location: index.php");
        exit();
    }
}
?>

