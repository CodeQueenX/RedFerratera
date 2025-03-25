<?php
require_once __DIR__ . '/../../config/Database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Registrar un nuevo usuario
    public function registrar($nombre, $email, $contraseña, $token) {
        // Verificar si ya existe el email
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false; // Ya existe el usuario
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
    
    // Activar cuenta mediante token
    public function activarCuenta($token) {
        $query = "UPDATE usuarios SET verificado = 1, token = NULL WHERE token = :token AND verificado = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    // Verificar login del usuario
    public function login($email, $contraseña) {
        $query = "SELECT * FROM usuarios WHERE email = :email AND verificado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Validar contraseña si el usuario existe
        if ($usuario && password_verify($contraseña, $usuario['clave'])) {
            return $usuario;
        }
        return false;
    }
    
    // Verificar si el email existe en la base de datos
    public function existeEmail($email) {
        $query = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Guardar token de recuperación de contraseña
    public function guardarTokenRecuperacion($email, $token) {
        $query = "UPDATE usuarios SET token = :token WHERE email = :email AND verificado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    // Actualizar clave con token de recuperación
    public function actualizarClavePorToken($token, $nuevaClave) {
        $nuevaClaveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);
        
        $query = "UPDATE usuarios SET clave = :clave, token = NULL WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $nuevaClaveHash, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    // Borrar token de recuperación (después de usarlo)
    public function borrarTokenRecuperacion($token) {
        $query = "UPDATE usuarios SET token = NULL WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
?>
