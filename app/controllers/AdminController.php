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
        
        // Solo admin o moderador pueden acceder
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        $this->ferrata = new Ferrata();
    }
    
    // Mostrar solicitudes de ferratas y reportes
    public function gestionarSolicitudes() {
        $solicitudesFerratas = $this->ferrata->obtenerSolicitudesPendientes();
        
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        $solicitudesReportes = $reporteModel->obtenerReportes();
        
        include __DIR__ . '/../views/admin_solicitudes.php';
    }
    
    // Aprobar una ferrata
    public function aprobarFerrata($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        if ($this->ferrata->aprobarFerrata($id)) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al aprobar la ferrata.");
        }
    }
    
    // Rechazar (eliminar) una ferrata pendiente
    public function rechazarFerrata($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        if (!$ferrata) {
            die("Error: No se encontró la ferrata.");
        }
        
        if ($this->ferrata->rechazarFerrata($id)) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al eliminar la ferrata.");
        }
    }
    
    // Aprobar un reporte y añadirlo a la descripción
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
        
        // Añadir mensaje del reporte a la descripción de la ferrata
        $nuevaDescripcion = $ferrata['descripcion'] . "\n🚨 [REPORTE] " . $reporte['mensaje'] . " (Fecha: " . $reporte['fecha_reporte'] . ")";
        $ferrataModel->actualizarDescripcion($reporte['ferrata_id'], $nuevaDescripcion);
        
        if ($reporteModel->cambiarEstadoReporte($id, 'Aprobado')) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al aprobar el reporte.");
        }
    }
    
    // Rechazar un reporte (cambiar estado)
    public function rechazarReporte($id) {
        if ($this->usuarioRol !== 'admin' && $this->usuarioRol !== 'moderador') {
            die("Acceso denegado.");
        }
        
        require_once __DIR__ . '/../models/Reporte.php';
        $reporteModel = new Reporte();
        
        if ($reporteModel->cambiarEstadoReporte($id, 'Rechazado')) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al rechazar el reporte.");
        }
    }
    
    // Mostrar vista de edición de ferrata
    public function editarFerrata($id) {
        require_once __DIR__ . '/../models/Imagen.php';
        require_once __DIR__ . '/../models/Comentario.php';
        
        $imagenModel = new Imagen();
        $comentarioModel = new Comentario();
        
        if (!$id) {
            die("ID de ferrata no proporcionado.");
        }
        
        $ferrata = $this->ferrata->obtenerFerrataPorId($id);
        $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
        $comentarios = $comentarioModel->obtenerComentariosPorFerrata($id);
        
        include __DIR__ . '/../views/editar_ferrata.php';
    }
    
    // Guardar cambios al editar una ferrata
    public function guardarEdicionFerrata() {
        require_once __DIR__ . '/../models/Imagen.php';
        
        $imagenModel = new Imagen();
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        
        // Recoger datos del formulario
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
        $provincia = $_POST['provincia'] ?? '';
        $dificultad = $_POST['dificultad'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $coordenadas = $_POST['coordenadas'] ?? null;
        $estado = $_POST['estado'] ?? '';
        $fecha_creacion = !empty($_POST['fecha_creacion']) ? $_POST['fecha_creacion'] : date('Y-m-d');
        $fecha_inicio_cierre = !empty($_POST['fecha_inicio_cierre']) ? $_POST['fecha_inicio_cierre'] : null;
        $fecha_fin_cierre = !empty($_POST['fecha_fin_cierre']) ? $_POST['fecha_fin_cierre'] : null;
        $recurrente = isset($_POST['recurrente']) ? 1 : 0;
        
        // Guardar cambios
        $this->ferrata->editarFerrata($id, $nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente);
        
        // Guardar imágenes si se han subido
        if (!empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                    $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                    $rutaDestino = "public/img/ferratas/" . $nombreArchivo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenModel->guardarImagen($id, $nombreArchivo);
                    }
                }
            }
        }
        
        // Redirigir según procedencia
        if (!empty($_POST['desde_gestion']) && $_POST['desde_gestion'] == 1) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
        } else {
            header("Location: /RedFerratera/index.php?accion=ver_ferrata&id=$id");
        }
        exit();
    }
    
    // Eliminar ferrata y todo lo relacionado
    public function eliminarFerrata($id) {
        require_once __DIR__ . '/../models/Imagen.php';
        require_once __DIR__ . '/../models/Comentario.php';
        require_once __DIR__ . '/../models/Reporte.php';
        require_once __DIR__ . '/../models/Video.php';
        require_once __DIR__ . '/../models/Wikiloc.php';
        require_once __DIR__ . '/../models/Valoracion.php';
        
        // Validar sesión y rol
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            header("Location: /RedFerratera/index.php?accion=home");
            exit();
        }
        
        $imagenModel = new Imagen();
        $comentarioModel = new Comentario();
        $reporteModel = new Reporte();
        $videoModel = new Video();
        $wikilocModel = new Wikiloc();
        $valoracionModel = new Valoracion();
        
        if (!$id) {
            die("Error: ID de ferrata no válido.");
        }
        
        // Eliminar imágenes (y archivos)
        $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
        foreach ($imagenes as $imagen) {
            $ruta = __DIR__ . "/../../public/img/ferratas/" . $imagen['ruta'];
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            $imagenModel->eliminarImagen($imagen['id']);
        }
        
        // Eliminar datos asociados
        $comentarioModel->eliminarComentariosPorFerrata($id);
        $reporteModel->eliminarReportesPorFerrata($id);
        $videoModel->eliminarVideosPorFerrata($id);
        $wikilocModel->eliminarWikilocPorFerrata($id);
        $valoracionModel->eliminarValoracionesPorFerrata($id);
        
        // Eliminar ferrata
        if ($this->ferrata->eliminarFerrata($id)) {
            header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            exit();
        } else {
            die("Error al eliminar la ferrata.");
        }
    }
}
?>
