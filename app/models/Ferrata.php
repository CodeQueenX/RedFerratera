<?php
require_once __DIR__ . '/../../config/Database.php';

class Ferrata {
    private $conn;
    private $table_name = "ferratas";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Obtener todas las ferratas con estado 'Abierta'
    public function obtenerFerratas() {
        $query = "SELECT * FROM ferratas WHERE estado = 'Abierta'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener ferratas cuyo estado no sea 'Pendiente'
    public function obtenerFerratasParaReporte() {
        $query = "SELECT * FROM ferratas WHERE estado != 'Pendiente' ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Insertar una nueva ferrata
    public function agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente = 0) {
        $fecha_creacion = !empty($fecha_creacion) ? date('Y-m-d', strtotime(str_replace('-', '/', $fecha_creacion))) : date('Y-m-d');
        
        $query = "INSERT INTO ferratas (nombre, ubicacion, comunidad_autonoma, provincia, dificultad, descripcion, coordenadas, estado, fecha_creacion, fecha_inicio_cierre, fecha_fin_cierre, recurrente)
                  VALUES (:nombre, :ubicacion, :comunidad_autonoma, :provincia, :dificultad, :descripcion, :coordenadas, :estado, :fecha_creacion, :fecha_inicio_cierre, :fecha_fin_cierre, :recurrente)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":comunidad_autonoma", $comunidad_autonoma);
        $stmt->bindParam(":provincia", $provincia);
        $stmt->bindParam(":dificultad", $dificultad);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindValue(":coordenadas", $coordenadas !== null ? $coordenadas : null, $coordenadas !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":fecha_creacion", $fecha_creacion);
        $stmt->bindParam(":fecha_inicio_cierre", $fecha_inicio_cierre);
        $stmt->bindParam(":fecha_fin_cierre", $fecha_fin_cierre);
        $stmt->bindParam(":recurrente", $recurrente);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Obtener ferratas del último mes
    public function obtenerNuevasFerratas() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener solicitudes pendientes (estado = Pendiente)
    public function obtenerSolicitudesPendientes() {
        $query = "SELECT * FROM ferratas WHERE estado = 'Pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Aprobar ferrata (cambiar estado a 'Abierta')
    public function aprobarFerrata($id) {
        $query = "UPDATE ferratas SET estado = 'Abierta' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Rechazar ferrata (eliminarla)
    public function rechazarFerrata($id) {
        $query = "DELETE FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Obtener una ferrata por ID
    public function obtenerFerrataPorId($id) {
        $query = "SELECT * FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener ferratas cercanas en un radio (por coordenadas)
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
    
    // Actualizar descripción
    public function actualizarDescripcion($id, $nuevaDescripcion) {
        $query = "UPDATE ferratas SET descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':descripcion', $nuevaDescripcion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Editar ferrata completa
    public function editarFerrata($id, $nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente) {
        $fecha_creacion = !empty($fecha_creacion) ? date('Y-m-d', strtotime(str_replace('-', '/', $fecha_creacion))) : date('Y-m-d');
        
        $query = "UPDATE ferratas SET nombre = :nombre, ubicacion = :ubicacion, comunidad_autonoma = :comunidad_autonoma, provincia = :provincia,
                  dificultad = :dificultad, descripcion = :descripcion, coordenadas = :coordenadas, estado = :estado,
                  fecha_creacion = :fecha_creacion, fecha_inicio_cierre = :fecha_inicio_cierre, fecha_fin_cierre = :fecha_fin_cierre, recurrente = :recurrente
                  WHERE id = :id";
        
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
        $stmt->bindParam(":fecha_creacion", $fecha_creacion);
        $stmt->bindParam(":fecha_inicio_cierre", $fecha_inicio_cierre);
        $stmt->bindParam(":fecha_fin_cierre", $fecha_fin_cierre);
        $stmt->bindParam(":recurrente", $recurrente);
        return $stmt->execute();
    }
    
    // Búsqueda filtrada de ferratas (por campos)
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
    
    // Búsqueda global desde la cabecera
    public function buscarFerratasGlobal($termino) {
        $termino = '%' . strtolower(trim($termino)) . '%';
        
        $query = "SELECT * FROM ferratas
                  WHERE estado != 'Pendiente'
                  AND (
                      LOWER(nombre) LIKE :termino OR
                      LOWER(ubicacion) LIKE :termino OR
                      LOWER(provincia) LIKE :termino OR
                      LOWER(comunidad_autonoma) LIKE :termino OR
                      LOWER(dificultad) LIKE :termino
                  )
                  ORDER BY comunidad_autonoma, provincia, ubicacion, nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Agrupar ferratas por comunidad y provincia
    public function obtenerFerratasOrganizadas() {
        $query = "SELECT * FROM ferratas WHERE estado != 'Pendiente' ORDER BY comunidad_autonoma, provincia, ubicacion, nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ferratas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $organizadas = [];
        foreach ($ferratas as $ferrata) {
            $comunidad = $ferrata['comunidad_autonoma'];
            $provincia = $ferrata['provincia'];
            $organizadas[$comunidad][$provincia][] = $ferrata;
        }
        
        return $organizadas;
    }
    
    // Eliminar una ferrata por ID
    public function eliminarFerrata($id) {
        $query = "DELETE FROM ferratas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Comprobar si una ferrata está cerrada por fecha recurrente
    public static function estaCerradaRecurrente($fechaInicio, $fechaFin, $recurrente) {
        $hoy = date("Y-m-d");
        
        if (!$recurrente) {
            return ($hoy >= $fechaInicio && $hoy <= $fechaFin);
        }
        
        $hoy_md = date("m-d");
        $inicio_md = date("m-d", strtotime($fechaInicio));
        $fin_md = date("m-d", strtotime($fechaFin));
        
        if ($inicio_md <= $fin_md) {
            return ($hoy_md >= $inicio_md && $hoy_md <= $fin_md);
        } else {
            return ($hoy_md >= $inicio_md || $hoy_md <= $fin_md);
        }
    }
    
    // Actualizar estado de una ferrata
    public function actualizarEstado($id, $nuevoEstado) {
        $stmt = $this->conn->prepare("UPDATE ferratas SET estado = ? WHERE id = ?");
        return $stmt->execute([$nuevoEstado, $id]);
    }
    
    // Verificar si ya existe una ferrata con ese nombre
    public function existeFerrataPorNombre($nombre) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ferratas WHERE nombre = ?");
        $stmt->execute([$nombre]);
        return $stmt->fetchColumn() > 0;
    }
}
