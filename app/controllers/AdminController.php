<?php
require_once __DIR__ . '/../models/Ferrata.php';

class AdminController {
    private $ferrata;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            die("Acceso denegado.");
        }
        $this->ferrata = new Ferrata();
    }
    
    public function gestionarSolicitudes() {
        $solicitudesFerratas = $this->ferrata->obtenerSolicitudesPendientes();
        
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        $solicitudesReportes = $reporteModel->obtenerReportes();
        
        include __DIR__ . '/../views/admin_solicitudes.php';
    }
    
    public function aprobarFerrata($id) {
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        echo "<h2>Detalles de la Ferrata:</h2>";
        echo "<pre>";
        print_r($ferrata);
        echo "</pre>";
        
        if ($this->ferrata->aprobarFerrata($id)) {
            echo "Ferrata aprobada correctamente.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al aprobar la ferrata.";
        }
    }
    
    public function rechazarFerrata($id) {
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        echo "<h2>Detalles de la Ferrata:</h2>";
        echo "<pre>";
        print_r($ferrata);
        echo "</pre>";
        
        if ($this->ferrata->rechazarFerrata($id)) {
            echo "Ferrata eliminada correctamente.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al eliminar la ferrata.";
        }
    }
    
    public function resolverReporte($id) {
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        
        if ($reporteModel->marcarComoResuelto($id)) {
            echo "Reporte marcado como resuelto.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al marcar el reporte como resuelto.";
        }
    }
    
    public function aprobarReporte($id) {
        require_once __DIR__ . '/../models/Reporte.php';
        require_once __DIR__ . '/../models/Ferrata.php';
        
        $reporteModel = new Reporte();
        $ferrataModel = new Ferrata();
        
        // Obtener el reporte antes de modificarlo
        $reporte = $reporteModel->obtenerReportePorId($id);
        if (!$reporte) {
            die("Error: No se encontró el reporte.");
        }
        
        // Obtener la ferrata asociada al reporte
        $ferrata = $ferrataModel->obtenerFerrataPorId($reporte['ferrata_id']);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        // Añadir el reporte a la descripción de la ferrata
        $nuevaDescripcion = $ferrata['descripcion'] . "\n🚨 [REPORTE] " . $reporte['mensaje'] . " (Fecha: " . $reporte['fecha_reporte'] . ")";
        $ferrataModel->actualizarDescripcion($reporte['ferrata_id'], $nuevaDescripcion);
        
        // Cambiar el estado del reporte a "Aprobado"
        if ($reporteModel->cambiarEstadoReporte($id, 'Aprobado')) {
            echo "Reporte aprobado y añadido a la ferrata.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al aprobar el reporte.";
        }
    }
    
    public function rechazarReporte($id) {
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        
        if ($reporteModel->cambiarEstadoReporte($id, 'Rechazado')) {
            echo "Reporte rechazado.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al rechazar el reporte.";
        }
    }
    
    public function editarFerrata($id) {
        require_once __DIR__ . '/../models/Ferrata.php';
        require_once __DIR__ . '/../models/Imagen.php';
        require_once __DIR__ . '/../models/Comentario.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        $comentarioModel = new Comentario();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID de ferrata no proporcionado.");
        }
        
        $ferrata = $ferrataModel->obtenerFerrataPorId($id);
        $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
        $comentarios = $comentarioModel->obtenerComentariosPorFerrata($id);
        
        include __DIR__ . '/../views/editar_ferrata.php';
    }
    
    public function guardarEdicionFerrata() {
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
        $provincia = $_POST['provincia'] ?? '';
        $dificultad = $_POST['dificultad'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $coordenadas = $_POST['coordenadas'] ?? null;
        $estado = $_POST['estado'] ?? '';
        $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d');
        
        // Manejar imagen
        if (!empty($_FILES['imagen']['name'])) {
            $directorio = "public/img/ferratas/";
            $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
            $rutaDestino = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                $imagen = $nombreArchivo;
            } else {
                $imagen = null;
            }
        } else {
            $imagen = $_POST['imagen_actual'] ?? null;
        }
        
        if ($id && $nombre && $ubicacion && $dificultad && $descripcion && $estado) {
            $ferrataModel->editarFerrata($id, $nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $imagen);
            echo "Ferrata actualizada correctamente.";
            header("Location: index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error: Completa todos los campos.";
        }
    }
}
?>
