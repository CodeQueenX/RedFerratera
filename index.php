<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'config/Database.php';

// Acción por defecto
$accion = $_GET['accion'] ?? 'home';

// URLs amigables
if (preg_match('/^ferrata\/(\d+)\/([a-zA-Z0-9-]+)$/', $accion, $m)) {
    $_GET['id'] = $m[1];
    $accion = 'ver_ferrata';
}
if (preg_match('/^editar-ferrata\/(\d+)$/', $accion, $m)) {
    $_GET['id'] = $m[1];
    $accion = 'editar_ferrata';
}
if (preg_match('/^eliminar-comentario\/(\d+)\/ferrata\/(\d+)$/', $accion, $m)) {
    $_GET['id'] = $m[1];
    $_GET['ferrata_id'] = $m[2];
    $accion = 'eliminar_comentario';
}
if (preg_match('/^eliminar-imagen\/(\d+)\/ferrata\/(\d+)$/', $accion, $m)) {
    $_GET['id'] = $m[1];
    $_GET['ferrata_id'] = $m[2];
    $accion = 'eliminar_imagen';
}

// Acceso restringido a usuarios no verificados
if (isset($_SESSION['usuario']) && (!isset($_SESSION['usuario']['verificado']) || $_SESSION['usuario']['verificado'] != 1)) {
    $restringidas = ['agregar_ferrata', 'agregar_reporte'];
    if (in_array($accion, $restringidas)) {
        die("⚠️ Debes verificar tu cuenta para acceder a esta página.");
    }
}

$contenido = null;

// Ruteo
switch ($accion) {
    case 'home':
        $contenido = 'app/views/home_content.php';
        break;
        
    case 'registrar':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->registrar();
        break;
        
    case 'activar_cuenta':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->activarCuenta();
        break;
        
    case 'login':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->login();
        break;
        
    case 'recuperar_clave':
        $contenido = 'app/views/recuperar_clave.php';
        break;
        
    case 'enviar_recuperacion':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->enviarRecuperacion();
        break;
        
    case 'restablecer_clave':
        $contenido = 'app/views/restablecer_clave.php';
        break;
        
    case 'procesar_cambio_clave':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->procesarCambioClave();
        break;
        
    case 'logout':
        require_once 'app/controllers/UsuarioController.php';
        (new UsuarioController())->logout();
        break;
        
    case 'agregar_ferrata':
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->agregar();
        break;
        
    case 'nuevas_ferratas':
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->nuevas();
        break;
        
    case 'reportes':
        require_once 'app/controllers/ReporteController.php';
        (new ReporteController())->index();
        break;
        
    case 'agregar_reporte':
        require_once 'app/controllers/ReporteController.php';
        (new ReporteController())->agregar();
        break;
        
    case 'guardar_reporte':
        require_once 'app/controllers/ReporteController.php';
        (new ReporteController())->guardar();
        break;
        
    case 'gestionar_ferratas':
        require_once 'app/controllers/AdminController.php';
        (new AdminController())->gestionarSolicitudes();
        break;
        
    case 'aprobar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->aprobarFerrata($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'rechazar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->rechazarFerrata($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'resolver_reporte':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->resolverReporte($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'aprobar_reporte':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->aprobarReporte($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'rechazar_reporte':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->rechazarReporte($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'editar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->editarFerrata($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'guardar_edicion_ferrata':
        require_once 'app/controllers/AdminController.php';
        (new AdminController())->guardarEdicionFerrata();
        break;
        
    case 'buscar_ferratas':
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->buscar();
        break;
        
    case 'buscarGlobal':
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->buscarGlobal();
        break;
        
    case 'ver_ferrata':
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->verFerrata();
        break;
        
    case 'guardar_valoracion':
        require_once 'app/controllers/ValoracionController.php';
        (new ValoracionController())->guardar();
        break;
        
    case 'agregar_comentario':
        require_once 'app/controllers/ComentarioController.php';
        (new ComentarioController())->agregar();
        break;
        
    case 'editar_comentario':
        require_once 'app/controllers/ComentarioController.php';
        (new ComentarioController())->editar();
        break;
        
    case 'eliminar_comentario':
        require_once 'app/controllers/ComentarioController.php';
        (new ComentarioController())->eliminar();
        break;
        
    case 'subir_imagen':
        require_once 'app/controllers/ImagenController.php';
        (new ImagenController())->subirImagen();
        break;
        
    case 'eliminar_imagen':
        $imagen_id = $_GET['id'] ?? null;
        $ferrata_id = $_GET['ferrata_id'] ?? null;
        if ($imagen_id && $ferrata_id) {
            require_once 'app/controllers/ImagenController.php';
            (new ImagenController())->eliminarImagen($imagen_id, $ferrata_id);
        } else {
            echo "Faltan datos para eliminar la imagen.";
        }
        break;
        
    case 'eliminar_ferrata':
        require_once 'app/controllers/AdminController.php';
        $id = $_GET['id'] ?? null;
        $id ? (new AdminController())->eliminarFerrata($id) : die("Error: No se proporcionó un ID válido.");
        break;
        
    case 'contacto':
        $contenido = 'app/views/contacto_content.php';
        break;
        
    case 'enviar_contacto':
        require_once 'app/controllers/ContactoController.php';
        (new ContactoController())->enviar();
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
        require_once 'app/controllers/WikilocController.php';
        (new WikilocController())->guardar();
        break;
        
    case 'borrar_wikiloc':
        require_once 'app/controllers/WikilocController.php';
        (new WikilocController())->borrar();
        break;
        
    case 'subir_video':
        require_once 'app/controllers/VideoController.php';
        (new VideoController())->subirVideo();
        break;
        
    case 'borrar_video':
        require_once 'app/controllers/VideoController.php';
        (new VideoController())->borrarVideo();
        break;
        
    default:
        require_once 'app/controllers/FerrataController.php';
        (new FerrataController())->index();
        break;
}

// Carga del layout con contenido
if (!empty($contenido) && file_exists($contenido)) {
    include 'app/views/layout.php';
}
