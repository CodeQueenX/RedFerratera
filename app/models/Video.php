<?php
require_once __DIR__ . '/../../config/Database.php';

class Video {
    private $conn;
    private $table_name = "ferrata_videos";
    
    public function __construct($ferrata_id = null, $video_embed = null) {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if ($ferrata_id && $video_embed) {
            $this->ferrata_id = $ferrata_id;
            $this->video_embed = $video_embed;
        }
    }
    
    public function save() {
        $query = "INSERT INTO " . $this->table_name . " (ferrata_id, video_embed) VALUES (:ferrata_id, :video_embed)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $this->ferrata_id);
        $stmt->bindParam(':video_embed', $this->video_embed);
        return $stmt->execute();
    }
    
    public static function deleteById($id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM ferrata_videos WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function obtenerVideosPorFerrata($ferrata_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
