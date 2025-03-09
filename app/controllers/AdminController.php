<?php
require_once __DIR__ . '/../models/Ferrata.php';

class AdminController {
    private $ferrata;
    private $usuarioRol;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            die("Acceso denegado.");
        }
        
        // Guardamos el rol del usuario
        $this->usuarioRol = $_SESSION['usuario']['rol'];
        
        // Si no es admin o moderador, no puede acceder a este controlador
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        $this->ferrata = new Ferrata();
    }
    
    public function gestionarSolicitudes() {
        // Los moderadores y admins pueden acceder
        $solicitudesFerratas = $this->ferrata->obtenerSolicitudesPendientes();
        
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        $solicitudesReportes = $reporteModel->obtenerReportes();
        
        include __DIR__ . '/../views/admin_solicitudes.php';
    }
    
    public function aprobarFerrata($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        if ($this->ferrata->aprobarFerrata($id)) {
            echo "Ferrata aprobada correctamente.";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al aprobar la ferrata.";
        }
    }
    
    public function rechazarFerrata($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
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
    
    public function aprobarReporte($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        require_once __DIR__ . '/../models/Reporte.php';
        require_once __DIR__ . '/../models/Ferrata.php';
        
        $reporteModel = new Reporte();
        $ferrataModel = new Ferrata();
        
        $reporte = $reporteModel->obtenerReportePorId($id);
        if (!$reporte) {
            die("Error: No se encontró el reporte.");
        }
        
        $ferrata = $ferrataModel->obtenerFerrataPorId($reporte['ferrata_id']);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        $nuevaDescripcion = $ferrata['descripcion'] . "\n🚨 [REPORTE] " . $reporte['mensaje'] . " (Fecha: " . $reporte['fecha_reporte'] . ")";
        $ferrataModel->actualizarDescripcion($reporte['ferrata_id'], $nuevaDescripcion);
        
        if ($reporteModel->cambiarEstadoReporte($id, 'Aprobado')) {
            echo "Reporte aprobado y añadido a la ferrata.";
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            echo "Error al aprobar el reporte.";
        }
    }
    
    public function rechazarReporte($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
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
        if (!empty($_POST['fecha_creacion'])) {
            $fecha_creacion = $_POST['fecha_creacion']; // Se guarda directamente, ya está en formato correcto
        } else {
            $fecha_creacion = date('Y-m-d'); // Si no se envía, usa la fecha actual como predeterminado
        }
        
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
        require_once __DIR__ . '/../models/Comentario.php';
        require_once __DIR__ . '/../models/Reporte.php';
        
        // Verificar sesión y rol de usuario
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            header("Location: /RedFerratera/index.php?accion=home"); // Bloqueo de acceso
            exit();
        }
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        $comentarioModel = new Comentario();
        $reporteModel = new Reporte();
        
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        
        // Eliminar imágenes asociadas
        $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
        foreach ($imagenes as $imagen) {
            $ruta = __DIR__ . "/../../public/img/ferratas/" . $imagen['ruta'];
            if (file_exists($ruta)) {
                unlink($ruta); // Borra la imagen del servidor
            }
            $imagenModel->eliminarImagen($imagen['id']); // Borra la imagen de la BD
        }
        
        // Eliminar comentarios relacionados
        $comentarioModel->eliminarComentariosPorFerrata($id);
        
        // Eliminar reportes relacionados
        $reporteModel->eliminarReportesPorFerrata($id);
        
        // Eliminar la ferrata de la base de datos
        if ($ferrataModel->eliminarFerrata($id)) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al eliminar la ferrata.");
        }
    }
}
?>
