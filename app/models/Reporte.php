<?php
require_once __DIR__ . '/../../config/Database.php';

class Reporte {
    private $conn;
    private $table_name = "reportes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los reportes
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
    
    public function obtenerReportePorId($id) {
        $query = "SELECT * FROM reportes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agregar un nuevo reporte
    public function agregarReporte($ferrata_id, $usuario_id, $descripcion, $fecha_reporte) {
        $query = "INSERT INTO reportes (ferrata_id, usuario_id, mensaje, fecha_reporte)
              VALUES (:ferrata_id, :usuario_id, :mensaje, :fecha_reporte)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ferrata_id", $ferrata_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":mensaje", $descripcion); // Cambiado de "descripcion" a "mensaje" según la base de datos
        $stmt->bindParam(":fecha_reporte", $fecha_reporte);
        
        return $stmt->execute();
    }

    // Actualizar estado de la ferrata cuando un usuario reporta un problema
    private function actualizarEstadoFerrata($ferrata_id, $nuevo_estado) {
        $query = "UPDATE ferratas SET estado = :estado WHERE id = :ferrata_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":ferrata_id", $ferrata_id);
        $stmt->execute();
    }
    
    // Actualizar estado del reporte
    public function marcarComoResuelto($id) {
        $query = "UPDATE reportes SET estado = 'Resuelto' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function cambiarEstadoReporte($id, $estado) {
        echo "🔍 Cambiando estado del reporte ID: $id a $estado<br>";
        $query = "UPDATE reportes SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "✅ Estado actualizado en la base de datos.<br>";
            return true;
        } else {
            echo "❌ Error al actualizar estado en la base de datos.<br>";
            return false;
        }
    }
}
?>
