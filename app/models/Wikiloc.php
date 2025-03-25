<?php
require_once __DIR__ . '/../../config/Database.php';

class Wikiloc {
    private $conn;
    private $table_name = "ferrata_wikiloc";
    private $ferrata_id;
    private $wikiloc_embed;
    
    public function __construct($ferrata_id = null, $wikiloc_embed = null) {
        // Conexión a la base de datos
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Inicializar propiedades si se proporcionan
        if ($ferrata_id && $wikiloc_embed) {
            $this->ferrata_id = $ferrata_id;
            $this->wikiloc_embed = $wikiloc_embed;
        }
    }
    
    // Guardar un nuevo enlace Wikiloc asociado a una ferrata
    public function save() {
        $query = "INSERT INTO " . $this->table_name . " (ferrata_id, wikiloc_embed)
                  VALUES (:ferrata_id, :wikiloc_embed)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $this->ferrata_id, PDO::PARAM_INT);
        $stmt->bindParam(':wikiloc_embed', $this->wikiloc_embed, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    // Eliminar un enlace Wikiloc por su ID
    public static function deleteById($id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM ferrata_wikiloc WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Obtener todos los enlaces Wikiloc asociados a una ferrata
    public function obtenerWikilocPorFerrata($ferrata_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Eliminar todos los enlaces Wikiloc asociados a una ferrata
    public function eliminarWikilocPorFerrata($ferrata_id) {
        $query = "DELETE FROM ferrata_wikiloc WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
