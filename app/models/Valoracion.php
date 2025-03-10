<?php

class Valoracion {
    private $id;
    private $ferrata_id;
    private $usuario_id;
    private $valor;
    private $created_at;

    public function __construct($ferrata_id, $usuario_id, $valor) {
        $this->ferrata_id = $ferrata_id;
        $this->usuario_id = $usuario_id;
        $this->valor = $valor;
    }

    // Métodos getters
    public function getId() { return $this->id; }
    public function getFerrataId() { return $this->ferrata_id; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getValor() { return $this->valor; }
    public function getCreatedAt() { return $this->created_at; }

    // Guarda la valoración (inserta o actualiza si ya existe)
    public function save() {
        $db = (new Database())->getConnection();
        
        // Comprueba si ya existe una valoración de este usuario para la ferrata
        $stmt = $db->prepare("SELECT id FROM valoraciones WHERE ferrata_id = ? AND usuario_id = ?");
        $stmt->execute([$this->ferrata_id, $this->usuario_id]);
        
        if ($stmt->rowCount() > 0) {
            // Si existe, se actualiza
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $existing['id'];
            $stmt = $db->prepare("UPDATE valoraciones SET valor = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$this->valor, $this->id]);
        } else {
            // Si no existe, se inserta una nueva valoración
            $stmt = $db->prepare("INSERT INTO valoraciones (ferrata_id, usuario_id, valor) VALUES (?, ?, ?)");
            if ($stmt->execute([$this->ferrata_id, $this->usuario_id, $this->valor])) {
                $this->id = $db->lastInsertId();
                return true;
            }
            return false;
        }
    }

    // Obtiene la valoración media y total de valoraciones para una ferrata
    public static function getAverageRating($ferrata_id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT AVG(valor) as promedio, COUNT(*) as total FROM valoraciones WHERE ferrata_id = ?");
        $stmt->execute([$ferrata_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
