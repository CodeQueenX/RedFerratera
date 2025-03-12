<?php
require_once __DIR__ . '/../../config/Database.php';

class Video {
    private $id;
    private $ferrata_id;
    private $video_embed; // Guardamos el código embed
    private $created_at;
    
    public function __construct($ferrata_id, $video_embed) {
        $this->ferrata_id = $ferrata_id;
        $this->video_embed = $video_embed;
    }
    
    public function getId() { return $this->id; }
    public function getFerrataId() { return $this->ferrata_id; }
    public function getVideoEmbed() { return $this->video_embed; }
    public function getCreatedAt() { return $this->created_at; }
    
    public function save() {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO ferrata_videos (ferrata_id, video_embed) VALUES (?, ?)");
        if ($stmt->execute([$this->ferrata_id, $this->video_embed])) {
            $this->id = $db->lastInsertId();
            return true;
        }
        return false;
    }
    
    public static function getByFerrataId($ferrata_id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM ferrata_videos WHERE ferrata_id = ?");
        $stmt->execute([$ferrata_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function deleteById($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM ferrata_videos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>


