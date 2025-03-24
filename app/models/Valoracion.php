<?php
require_once __DIR__ . '/../../config/Database.php';

class Valoracion {
    private $conn;
    private $table_name = "valoraciones";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function save($ferrata_id, $usuario_id, $valor) {
        $query = "INSERT INTO $this->table_name (ferrata_id, usuario_id, valor)
                  VALUES (:ferrata_id, :usuario_id, :valor)
                  ON DUPLICATE KEY UPDATE valor = :valor";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':valor', $valor);
        
        return $stmt->execute();
    }
    
    public static function getAverageRating($ferrata_id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT ROUND(AVG(valor), 2) AS promedio, COUNT(*) AS total
                  FROM valoraciones
                  WHERE ferrata_id = :ferrata_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
