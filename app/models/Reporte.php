<?php
require_once __DIR__ . '/../../config/Database.php';

class Reporte {
    private $conn;
    private $table_name = "reportes";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Obtener todos los reportes con estado Pendiente
    public function obtenerReportes() {
        $query = "SELECT r.id, r.mensaje, r.fecha_reporte, r.estado, u.nombre AS usuario, f.nombre AS ferrata
                  FROM reportes r
                  JOIN usuarios u ON r.usuario_id = u.id
                  JOIN ferratas f ON r.ferrata_id = f.id
                  WHERE r.estado = 'Pendiente'
                  ORDER BY r.fecha_reporte DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener un reporte específico por su ID
    public function obtenerReportePorId($id) {
        $query = "SELECT * FROM reportes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Agregar un nuevo reporte
    public function agregarReporte($ferrata_id, $usuario_id, $descripcion, $fecha_reporte) {
        $query = "INSERT INTO reportes (ferrata_id, usuario_id, mensaje, fecha_reporte)
                  VALUES (:ferrata_id, :usuario_id, :mensaje, :fecha_reporte)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ferrata_id", $ferrata_id, PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(":mensaje", $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(":fecha_reporte", $fecha_reporte, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    // Marcar un reporte como resuelto
    public function marcarComoResuelto($id) {
        $query = "UPDATE reportes SET estado = 'Resuelto' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Cambiar el estado de un reporte (Aprobado / Rechazado)
    public function cambiarEstadoReporte($id, $estado) {
        $query = "UPDATE reportes SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Eliminar todos los reportes asociados a una ferrata
    public function eliminarReportesPorFerrata($ferrata_id) {
        $query = "DELETE FROM reportes WHERE ferrata_id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ferrata_id', $ferrata_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
