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
    public function registrar($nombre, $email, $contraseña) {
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $rol = "usuario";

    // Consulta
    $query = "INSERT INTO usuarios (nombre, email, clave, rol) VALUES (:nombre, :email, :clave, :rol)";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
        ":nombre" => $nombre,
        ":email" => $email,
        ":clave" => $contraseña_hash,
        ":rol" => $rol
    ]);

    return true;
    }

    // Método para verificar usuario en el login
    public function login($email, $contraseña) {
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo "Usuario no encontrado.<br>";
            return false;
        }
        
        // Verificar la contraseña encriptada
        if (password_verify($contraseña, $usuario['clave'])) {
            return $usuario;
        } else {
            return false;
        }
    }
}
?>

