<?php
require_once __DIR__ . '/../../config/Database.php';

class Comentario {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function agregarComentario($ferrata_id, $usuario_id, $comentario) {
        $query = "INSERT INTO comentarios (ferrata_id, usuario_id, comentario) VALUES (:ferrata_id, :usuario_id, :comentario)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ferrata_id", $ferrata_id, PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(":comentario", $comentario, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        } else {
            die("Error al insertar comentario: " . implode(" - ", $stmt->errorInfo()));
        }
    }
    
    // Obtener los comentarios de una ferrata específica
    public function obtenerComentariosPorFerrata($ferrata_id) {
        $query = "SELECT c.*, u.nombre AS usuario FROM comentarios c
              JOIN usuarios u ON c.usuario_id = u.id
              WHERE c.ferrata_id = :ferrata_id
              ORDER BY c.fecha_comentario DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ferrata_id", $ferrata_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarComentario($id) {
        $query = "DELETE FROM comentarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    
    public function editarComentario($id, $nuevo_comentario) {
        $query = "UPDATE comentarios SET comentario = :comentario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comentario', $nuevo_comentario);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function obtenerComentarioPorId($comentario_id) {
        $query = "SELECT * FROM comentarios WHERE id = :comentario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comentario_id", $comentario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function actualizarComentario($comentario_id, $nuevo_comentario) {
        $query = "UPDATE comentarios SET comentario = :comentario WHERE id = :comentario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comentario", $nuevo_comentario);
        $stmt->bindParam(":comentario_id", $comentario_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function eliminarComentariosPorFerrata($ferrata_id) {
        $query = "DELETE FROM comentarios WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
