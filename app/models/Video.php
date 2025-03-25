<?php
require_once __DIR__ . '/../../config/Database.php';

class Video {
    private $conn;
    private $table_name = "ferrata_videos";
    private $ferrata_id;
    private $video_embed;
    
    public function __construct($ferrata_id = null, $video_embed = null) {
        // Conexión a la base de datos
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Inicializar propiedades si se proporcionan
        if ($ferrata_id && $video_embed) {
            $this->ferrata_id = $ferrata_id;
            $this->video_embed = $video_embed;
        }
    }
    
    // Guardar un nuevo video asociado a una ferrata
    public function save() {
        $query = "INSERT INTO " . $this->table_name . " (ferrata_id, video_embed)
                  VALUES (:ferrata_id, :video_embed)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $this->ferrata_id, PDO::PARAM_INT);
        $stmt->bindParam(':video_embed', $this->video_embed, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    // Eliminar un video por su ID
    public static function deleteById($id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM ferrata_videos WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Obtener todos los videos de una ferrata
    public function obtenerVideosPorFerrata($ferrata_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Eliminar todos los vídeos asociados a una ferrata
    public function eliminarVideosPorFerrata($ferrata_id) {
        $query = "DELETE FROM ferrata_videos WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
