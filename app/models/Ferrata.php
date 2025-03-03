<?php
require_once __DIR__ . '/../../config/Database.php';

class Ferrata {
    private $conn;
    private $table_name = "ferratas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las ferratas
    public function obtenerFerratas() {
        $query = "SELECT * FROM ferratas WHERE estado = 'Abierta'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);  
        return $resultados;
    }

    // Insertar una nueva ferrata
    public function agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion) {
        $query = "INSERT INTO ferratas (nombre, ubicacion, comunidad_autonoma, provincia, dificultad, descripcion, coordenadas, estado, fecha_creacion)
              VALUES (:nombre, :ubicacion, :comunidad_autonoma, :provincia, :dificultad, :descripcion, :coordenadas, :estado, :fecha_creacion)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":comunidad_autonoma", $comunidad_autonoma);
        $stmt->bindParam(":provincia", $provincia);
        $stmt->bindParam(":dificultad", $dificultad);
        $stmt->bindParam(":descripcion", $descripcion);
        if ($coordenadas === null) {
            $stmt->bindValue(":coordenadas", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(":coordenadas", $coordenadas);
        }
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":fecha_creacion", $fecha_creacion);
        
        if ($stmt->execute()) {
            $id = $this->conn->lastInsertId();
            echo "Inserción exitosa, ID: $id";
            return $id;
        } else {
            echo "Error al insertar en la base de datos.";
            print_r($stmt->errorInfo()); // 🔍 VER ERROR SQL
            return false;
        }
    }

    // Obtener ferratas añadidas en el último mes
    public function obtenerNuevasFerratas() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Funciones del admin
    public function obtenerSolicitudesPendientes() {
        $query = "SELECT * FROM ferratas WHERE estado = 'Pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function aprobarFerrata($id) {
        $query = "UPDATE ferratas SET estado = 'Abierta' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function rechazarFerrata($id) {
        $query = "DELETE FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function obtenerFerrataPorId($id) {
        $query = "SELECT * FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerFerratasCercanas($lat, $lon, $id_actual, $distancia_km = 50) {
        $query = "SELECT *,
              (6371 * ACOS(COS(RADIANS(:lat)) * COS(RADIANS(SUBSTRING_INDEX(coordenadas, ',', 1)))
              * COS(RADIANS(SUBSTRING_INDEX(coordenadas, ',', -1)) - RADIANS(:lon))
              + SIN(RADIANS(:lat)) * SIN(RADIANS(SUBSTRING_INDEX(coordenadas, ',', 1)))))
              AS distancia
              FROM ferratas
              WHERE estado != 'Pendiente' AND coordenadas IS NOT NULL AND coordenadas != ''
              AND id != :id_actual
              HAVING distancia < :distancia_km
              ORDER BY distancia ASC
              LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":lat", $lat);
        $stmt->bindParam(":lon", $lon);
        $stmt->bindParam(":id_actual", $id_actual);
        $stmt->bindParam(":distancia_km", $distancia_km);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function actualizarDescripcion($id, $nuevaDescripcion) {
        $query = "UPDATE ferratas SET descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':descripcion', $nuevaDescripcion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function editarFerrata($id, $nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion) {
        $query = "UPDATE ferratas SET nombre = :nombre, ubicacion = :ubicacion, comunidad_autonoma = :comunidad_autonoma, provincia = :provincia,
              dificultad = :dificultad, descripcion = :descripcion, coordenadas = :coordenadas, estado = :estado, fecha_creacion = :fecha_creacion";
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":comunidad_autonoma", $comunidad_autonoma);
        $stmt->bindParam(":provincia", $provincia);
        $stmt->bindParam(":dificultad", $dificultad);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":coordenadas", $coordenadas);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(':fecha_creacion', $fecha_creacion);
        
        return $stmt->execute();
    }
    
    public function buscarFerratas($ubicacion, $dificultad, $comunidad, $provincia, $estado) {
        $query = "SELECT * FROM ferratas WHERE estado != 'Pendiente'";
        
        if (!empty($ubicacion)) {
            $query .= " AND (ubicacion LIKE :ubicacion OR comunidad_autonoma LIKE :ubicacion OR provincia LIKE :ubicacion)";
        }
        
        if (!empty($dificultad)) {
            $query .= " AND dificultad = :dificultad";
        }
        
        if (!empty($comunidad)) {
            $query .= " AND comunidad_autonoma = :comunidad";
        }
        
        if (!empty($provincia)) {
            $query .= " AND provincia LIKE :provincia";
        }
        
        if (!empty($estado)) {
            $query .= " AND estado = :estado";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($ubicacion)) {
            $ubicacion = "%$ubicacion%";
            $stmt->bindParam(":ubicacion", $ubicacion);
        }
        
        if (!empty($dificultad)) {
            $stmt->bindParam(":dificultad", $dificultad);
        }
        
        if (!empty($comunidad)) {
            $stmt->bindParam(":comunidad", $comunidad);
        }
        
        if (!empty($provincia)) {
            $provincia = "%$provincia%";
            $stmt->bindParam(":provincia", $provincia);
        }
        
        if (!empty($estado)) {
            $stmt->bindParam(":estado", $estado);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerFerratasOrganizadas() {
        $query = "SELECT * FROM ferratas WHERE estado != 'Pendiente' ORDER BY comunidad_autonoma, provincia, nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ferratas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $organizadas = [];
        foreach ($ferratas as $ferrata) {
            $comunidad = $ferrata['comunidad_autonoma'];
            $provincia = $ferrata['provincia'];
            
            if (!isset($organizadas[$comunidad])) {
                $organizadas[$comunidad] = [];
            }
            if (!isset($organizadas[$comunidad][$provincia])) {
                $organizadas[$comunidad][$provincia] = [];
            }
            
            $organizadas[$comunidad][$provincia][] = $ferrata;
        }
        
        return $organizadas;
    }
    
    public function eliminarFerrata($id) {
        $query = "DELETE FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>
