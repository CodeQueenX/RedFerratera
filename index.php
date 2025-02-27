<?php
session_start();

// Determinar la acción que se debe ejecutar, por defecto "home"
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'home';

// Manejo de URLs amigables
if (preg_match('/^ferrata\/(\d+)\/([a-zA-Z0-9-]+)$/', $accion, $matches)) {
    $_GET['id'] = $matches[1];
    $accion = 'ver_ferrata';
}

if (preg_match('/^editar-ferrata\/(\d+)$/', $accion, $matches)) {
    $_GET['id'] = $matches[1];
    $accion = 'editar_ferrata';
}

if (preg_match('/^eliminar-comentario\/(\d+)\/ferrata\/(\d+)$/', $accion, $matches)) {
    $_GET['id'] = $matches[1];
    $_GET['ferrata_id'] = $matches[2];
    $accion = 'eliminar_comentario';
}

if (preg_match('/^eliminar-imagen\/(\d+)\/ferrata\/(\d+)$/', $accion, $matches)) {
    $_GET['id'] = $matches[1];
    $_GET['ferrata_id'] = $matches[2];
    $accion = 'eliminar_imagen';
}

if ($accion === 'ferratas') {
    $accion = 'ferratas';
}

if ($accion === 'login') {
    $accion = 'login';
}

if ($accion === 'home' || empty($accion)) {
    $accion = 'home';
}

switch ($accion) {
    case 'home':
        include 'app/views/home.php';
        break;
        
    case 'registrar':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->registrar();
        break;
        
    case 'login':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->login();
        break;
        
    case 'agregar_ferrata':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->agregar();
        break;
        
    case 'nuevas_ferratas':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->nuevas();
        break;
        
    case 'reportes':
        require_once 'app/controllers/ReporteController.php';
        $reporteController = new ReporteController();
        $reporteController->index();
        break;
        
    case 'agregar_reporte':
        require_once 'app/controllers/ReporteController.php';
        $reporteController = new ReporteController();
        $reporteController->agregar();
        break;
        
    case 'guardar_reporte':
        require_once 'app/controllers/ReporteController.php';
        $reporteController = new ReporteController();
        $reporteController->guardar();
        break;
        
    case 'logout':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->logout();
        break;
        
    case 'gestionar_ferratas':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->gestionarSolicitudes();
        break;
        
    case 'panel_admin':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->panel();
        break;
        
    case 'aprobar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->aprobarFerrata($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'rechazar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->rechazarFerrata($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'resolver_reporte':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->resolverReporte($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'aprobar_reporte':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->aprobarReporte($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'rechazar_reporte':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->rechazarReporte($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'editar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->editarFerrata($id);
        } else {
            die("Error: No se proporcionó un ID válido.");
        }
        break;
        
    case 'guardar_edicion_ferrata':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->guardarEdicionFerrata();
        break;
        
    case 'buscar_ferratas':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->buscar();
        break;
        
    case 'ver_ferrata':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->verFerrata();
        break;
        
    case 'agregar_comentario':
        require_once __DIR__ . '/app/controllers/ComentarioController.php';
        $comentarioController = new ComentarioController();
        $comentarioController->agregar();
        break;
        
    case 'subir_imagen':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->subirImagen();
        break;
        
    case 'eliminar_imagen':
        $imagen_id = $_GET['id'] ?? null;
        $ferrata_id = $_GET['ferrata_id'] ?? null;
        
        if ($imagen_id && $ferrata_id) {
            require_once __DIR__ . '/app/controllers/ImagenController.php';
            $imagenController = new ImagenController();
            $imagenController->eliminarImagen($imagen_id, $ferrata_id);
        } else {
            echo "⚠️ Faltan datos para eliminar la imagen.";
            echo "<pre>";
            print_r($_GET);
            echo "</pre>";
        }
        break;
        
    case 'eliminar_comentario':
        require_once 'app/controllers/ComentarioController.php';
        $comentarioController = new ComentarioController();
        $comentarioController->eliminar();
        break;
        
    case 'editar_comentario':
        require_once 'app/controllers/ComentarioController.php';
        $comentarioController = new ComentarioController();
        $comentarioController->editar();
        break;
        
    default:
        // Por defecto, mostramos el listado de ferratas (página principal)
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->index();
        break;
}
?>
