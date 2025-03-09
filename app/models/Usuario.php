<?php
require_once __DIR__ . '/../../config/Database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Método para registrar un nuevo usuario
    public function registrar($nombre, $email, $contraseña, $token) {
        // Verificar si el email ya está registrado
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([":email" => $email]);
        
        if ($stmt->fetch()) {
            return false; // Usuario ya existe
        }
        
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
        $rol = "usuario";
        
        $query = "INSERT INTO usuarios (nombre, email, clave, rol, token, verificado)
              VALUES (:nombre, :email, :clave, :rol, :token, 0)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":nombre" => $nombre,
            ":email" => $email,
            ":clave" => $contraseña_hash,
            ":rol" => $rol,
            ":token" => $token
        ]);
    }
    
    // Función para activar la cuenta
    public function activarCuenta($token) {
        $query = "UPDATE usuarios SET verificado = 1, token = NULL WHERE token = :token AND verificado = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    // Método para verificar usuario en el login
    public function login($email, $contraseña) {
        $query = "SELECT * FROM usuarios WHERE email = :email AND verificado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo "Usuario no encontrado o cuenta no activada.<br>";
            return false;
        }
        
        // Verificar la contraseña encriptada
        if (password_verify($contraseña, $usuario['clave'])) {
            return $usuario;
        } else {
            return false;
        }
    }
    
    public function existeEmail($email) {
        $query = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function guardarTokenRecuperacion($email, $token) {
        $query = "UPDATE usuarios SET token = :token WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":token" => $token,
            ":email" => $email
        ]);
    }
    
    public function actualizarClavePorToken($token, $nuevaClave) {
        $nuevaClaveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("UPDATE usuarios SET clave = :clave WHERE token = :token");
        return $stmt->execute([
            ":clave" => $nuevaClaveHash,
            ":token" => $token
        ]);
    }
}
?>

