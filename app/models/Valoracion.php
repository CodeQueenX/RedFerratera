<?php
require_once __DIR__ . '/../../config/Database.php';

class Valoracion {
    private $conn;
    private $table_name = "valoraciones";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Guardar o actualizar valoración del usuario para una ferrata
    public function save($ferrata_id, $usuario_id, $valor) {
        $query = "INSERT INTO $this->table_name (ferrata_id, usuario_id, valor)
                  VALUES (:ferrata_id, :usuario_id, :valor)
                  ON DUPLICATE KEY UPDATE valor = :valor";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Obtener la media de valoraciones y el total de votos para una ferrata
    public function getAverageRating($ferrata_id) {
        $query = "SELECT ROUND(AVG(valor), 2) AS promedio, COUNT(*) AS total
                  FROM $this->table_name
                  WHERE ferrata_id = :ferrata_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Eliminar todas las valoraciones asociadas a una ferrata
    public function eliminarValoracionesPorFerrata($ferrata_id) {
        $query = "DELETE FROM valoraciones WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
