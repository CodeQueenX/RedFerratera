<?php
require_once __DIR__ . '/../../config/Database.php';

class Wikiloc {
    private $id;
    private $ferrata_id;
    private $wikiloc_embed; // Almacena el código embed completo (iframe)
    private $created_at;
    
    public function __construct($ferrata_id, $wikiloc_embed) {
        $this->ferrata_id = $ferrata_id;
        $this->wikiloc_embed = $wikiloc_embed;
    }
    
    public function getId() { return $this->id; }
    public function getFerrataId() { return $this->ferrata_id; }
    public function getWikilocEmbed() { return $this->wikiloc_embed; }
    public function getCreatedAt() { return $this->created_at; }
    
    public function save() {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO ferrata_wikiloc (ferrata_id, wikiloc_embed) VALUES (?, ?)");
        if ($stmt->execute([$this->ferrata_id, $this->wikiloc_embed])) {
            $this->id = $db->lastInsertId();
            return true;
        }
        return false;
    }
    
    public static function getByFerrataId($ferrata_id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM ferrata_wikiloc WHERE ferrata_id = ?");
        $stmt->execute([$ferrata_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function deleteById($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM ferrata_wikiloc WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>

