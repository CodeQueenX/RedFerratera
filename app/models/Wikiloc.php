<?php
require_once __DIR__ . '/../../config/Database.php';

class Wikiloc {
    private $conn;
    private $table_name = "ferrata_wikiloc";
    private $ferrata_id;
    private $wikiloc_embed;
    
    public function __construct($ferrata_id = null, $wikiloc_embed = null) {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if ($ferrata_id && $wikiloc_embed) {
            $this->ferrata_id = $ferrata_id;
            $this->wikiloc_embed = $wikiloc_embed;
        }
    }
    
    public function save() {
        $query = "INSERT INTO " . $this->table_name . " (ferrata_id, wikiloc_embed) VALUES (:ferrata_id, :wikiloc_embed)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $this->ferrata_id);
        $stmt->bindParam(':wikiloc_embed', $this->wikiloc_embed);
        return $stmt->execute();
    }
    
    public static function deleteById($id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM ferrata_wikiloc WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function obtenerWikilocPorFerrata($ferrata_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
