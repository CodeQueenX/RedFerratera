<?php
require_once __DIR__ . '/../../config/Database.php';

class Imagen {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function guardarImagen($ferrata_id, $ruta) {
        $query = "INSERT INTO imagenes_ferratas (ferrata_id, ruta) VALUES (:ferrata_id, :ruta)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ferrata_id", $ferrata_id);
        $stmt->bindParam(":ruta", $ruta);
        return $stmt->execute();
    }
    
    public function obtenerImagenesPorFerrata($ferrata_id) {
        $query = "SELECT * FROM imagenes_ferratas WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ferrata_id", $ferrata_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerImagenPorId($id) {
        $query = "SELECT * FROM imagenes_ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function eliminarImagen($id) {
        $query = "DELETE FROM imagenes_ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
