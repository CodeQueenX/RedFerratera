<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
//var_dump($_SESSION['usuario']);
require_once 'config/Database.php';

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

// Restringir acceso a usuarios no verificados
if (isset($_SESSION['usuario']) && (!isset($_SESSION['usuario']['verificado']) || $_SESSION['usuario']['verificado'] != 1)) {
    $paginasRestringidas = ['agregar_ferrata', 'agregar_reporte'];
    
    if (in_array($accion, $paginasRestringidas)) {
        die("⚠️ Debes verificar tu cuenta para acceder a esta página.");
    }
}

$contenido = null;

switch ($accion) {
    case 'home':
        $contenido = 'app/views/home_content.php';
        break;
        
    case 'registrar':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->registrar();
        break;
        
    case 'activar_cuenta':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->activarCuenta();
        break;
        
    case 'login':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->login();
        break;
        
    case 'recuperar_clave':
        include 'app/views/recuperar_clave.php';
        break;
        
    case 'enviar_recuperacion':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->enviarRecuperacion();
        break;
        
    case 'restablecer_clave':
        include 'app/views/restablecer_clave.php';
        break;
        
    case 'procesar_cambio_clave':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->procesarCambioClave();
        break;
        
    case 'logout':
        require_once 'app/controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        $usuarioController->logout();
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
        
    case 'buscarGlobal':
        require_once __DIR__ . '/app/controllers/FerrataController.php';
        $busquedaController = new FerrataController();
        $busquedaController->buscarGlobal();
        break;
        
    case 'ver_ferrata':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->verFerrata();
        break;
        
    case 'guardar_valoracion':
        require_once 'app/controllers/ValoracionController.php';
        $valoracionController = new ValoracionController();
        $valoracionController->guardar();
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
            echo "Faltan datos para eliminar la imagen.";
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
        
    case 'eliminar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $adminController = new AdminController();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->eliminarFerrata($id);
        } else {
            echo "Error: No se proporcionó un ID válido para eliminar la ferrata.";
        }
        break;
        
    case 'contacto':
        $contenido = 'app/views/contacto_content.php';
        break;
        
    case 'enviar_contacto':
        require_once 'app/controllers/ContactoController.php';
        $contactoController = new ContactoController();
        $contactoController->enviar();
        break;
        
    case 'faq':
        $contenido = 'app/views/faq_content.php';
        break;
        
    case 'aviso_legal':
        $contenido = 'app/views/aviso_legal_content.php';
        break;
        
    case 'politica_privacidad':
        $contenido = 'app/views/politica_privacidad_content.php';
        break;
        
    case 'politica_cookies':
        $contenido = 'app/views/politica_cookies_content.php';
        break;
        
    case 'sitemap':
        $contenido = 'app/views/sitemap_content.php';
        break;
        
    case 'guardar_wikiloc':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->guardarWikiloc();
        break;
        
    case 'borrar_wikiloc':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->borrarWikiloc();
        break;
        
    case 'subir_video':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->subirVideo();
        break;
        
    case 'borrar_video':
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->borrarVideo();
        break;
        
    default:
        // Por defecto, mostramos la página principal
        require_once 'app/controllers/FerrataController.php';
        $ferrataController = new FerrataController();
        $ferrataController->index();
        break;
}
if (!empty($contenido) && file_exists($contenido)) {
    include 'app/views/layout.php';
} else {
    if ($accion !== 'home') {
        echo "";
    }
}
?>
