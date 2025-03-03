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
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        echo "🔍 Recibido en aprobarFerrata():<br>";
        print_r($_GET);
        
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        
        echo "Procesando aprobación de ferrata ID: $id <br>";
        
        if ($ferrataModel->aprobarFerrata($id)) {
            echo "Ferrata aprobada correctamente.<br>";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al aprobar la ferrata.");
        }
    }
    
    public function rechazarFerrata($id) {
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        if ($this->ferrata->rechazarFerrata($id)) {
            echo "Ferrata eliminada correctamente.";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
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
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
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
        
        echo "🔍 Recibido en aprobarReporte():<br>";
        print_r($_GET);
        
        // Verificar si ID es válido
        if (!$id) {
            die("Error: No se recibió un ID válido.");
        }
        
        // Obtener el reporte antes de modificarlo
        echo "🔍 Buscando reporte con ID: $id<br>";
        $reporte = $reporteModel->obtenerReportePorId($id);
        if (!$reporte) {
            die("Error: No se encontró el reporte.");
        }
        
        echo "Reporte encontrado: ";
        print_r($reporte);
        
        // Obtener la ferrata asociada al reporte
        $ferrata = $ferrataModel->obtenerFerrataPorId($reporte['ferrata_id']);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        // Añadir el reporte a la descripción de la ferrata
        $nuevaDescripcion = $ferrata['descripcion'] . "\n🚨 [REPORTE] " . $reporte['mensaje'] . " (Fecha: " . $reporte['fecha_reporte'] . ")";
        $ferrataModel->actualizarDescripcion($reporte['ferrata_id'], $nuevaDescripcion);
        echo "Actualizando descripción: $nuevaDescripcion<br>";
        
        // Cambiar el estado del reporte a "Aprobado"
        echo "⏳ Cambiando estado del reporte a 'Aprobado'<br>";
        if ($reporteModel->cambiarEstadoReporte($id, 'Aprobado')) {
            echo "Reporte aprobado y añadido a la ferrata.";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
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
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
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
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
        $provincia = $_POST['provincia'] ?? '';
        $dificultad = $_POST['dificultad'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $coordenadas = $_POST['coordenadas'] ?? null;
        $estado = $_POST['estado'] ?? '';
        $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d');
        
        // Guardar la ferrata editada
        $ferrataModel->editarFerrata($id, $nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion);
        echo "Ferrata actualizada correctamente.<br>";
        
        // Manejar imágenes
        if (!empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                    $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                    $rutaDestino = "public/img/ferratas/" . $nombreArchivo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenModel->agregarImagen($id, $nombreArchivo);
                        echo "Imagen subida: $nombreArchivo<br>";
                    } else {
                        echo "Error al subir la imagen: $nombreArchivo<br>";
                    }
                }
            }
        }
        
        // Detectar si la edición viene desde gestionar_ferratas o ver_ferrata
        if (!empty($_POST['desde_gestion']) && $_POST['desde_gestion'] == 1) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
        } else {
            header("Location: /RedFerratera/index.php?accion=ver_ferrata&id=$id");
        }
        exit();
    }
    
    public function eliminarFerrata($id) {
        require_once __DIR__ . '/../models/Ferrata.php';
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        
        echo "Eliminando ferrata ID: $id<br>";
        
        // Eliminar imágenes asociadas
        $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
        foreach ($imagenes as $imagen) {
            $ruta = __DIR__ . "/../../public/img/ferratas/" . $imagen['ruta'];
            if (file_exists($ruta)) {
                unlink($ruta); // Borrar la imagen del servidor
                echo "Imagen eliminada: " . $imagen['ruta'] . "<br>";
            }
            $imagenModel->eliminarImagen($imagen['id']); // Borrar de la base de datos
        }
        
        // Eliminar la ferrata de la base de datos
        if ($ferrataModel->eliminarFerrata($id)) {
            echo "Ferrata eliminada correctamente.<br>";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al eliminar la ferrata.<br>";
        }
    }
}
?>
