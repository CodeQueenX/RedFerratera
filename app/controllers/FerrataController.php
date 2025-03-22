<?php
require_once __DIR__ . '/../models/Ferrata.php';

class FerrataController {
    private $ferrata;

    public function __construct() {
        $this->ferrata = new Ferrata();
    }

    // Mostrar todas las ferratas organizadas
    public function index() {
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        // Obtén el listado organizado de ferratas (por comunidad/provincia)
        $ferratasOrganizadas = $ferrataModel->obtenerFerratasOrganizadas();
        
        // Recorremos cada ferrata para actualizar su estado si tiene fechas de cierre definidas
        foreach ($ferratasOrganizadas as $comunidad => $ferratasPorComunidad) {
            foreach ($ferratasPorComunidad as $provincia => $ferratas) {
                // Iteramos usando el índice
                for ($i = 0; $i < count($ferratas); $i++) {
                    $ferrata = $ferratas[$i];
                    if (!empty($ferrata['fecha_inicio_cierre']) && !empty($ferrata['fecha_fin_cierre'])) {
                        // Convertir la bandera recurrente a booleano
                        $recurrente = (bool)$ferrata['recurrente'];
                        // Si la ferrata está en periodo de cierre y su estado es "Abierta"
                        if (Ferrata::estaCerradaRecurrente($ferrata['fecha_inicio_cierre'], $ferrata['fecha_fin_cierre'], $recurrente)) {
                            if (strtolower($ferrata['estado']) === 'abierta') {
                                $ferrataModel->actualizarEstado($ferrata['id'], 'Cerrada');
                                $ferratasOrganizadas[$comunidad][$provincia][$i]['estado'] = 'Cerrada';
                            }
                        } else {
                            // Si no está en periodo de cierre y está marcada como "Cerrada", se reabre
                            if (strtolower($ferrata['estado']) === 'cerrada') {
                                $ferrataModel->actualizarEstado($ferrata['id'], 'Abierta');
                                $ferratasOrganizadas[$comunidad][$provincia][$i]['estado'] = 'Abierta';
                            }
                        }
                    }
                }
            }
        }
        
        include __DIR__ . '/../views/ferratas.php';
    }

    // Agregar una nueva ferrata
    public function agregar() {
        require_once __DIR__ . '/../models/Ferrata.php';
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nombre = ($_POST['nombre'] ?? '');
            $ubicacion = $_POST['ubicacion'] ?? '';
            $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
            $provincia = $_POST['provincia'] ?? '';
            $dificultad = $_POST['dificultad'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $coordenadas = isset($_POST['coordenadas']) && $_POST['coordenadas'] !== '' ? $_POST['coordenadas'] : null;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? $_POST['estado'] : 'Pendiente';
            if (!empty($_POST['fecha_creacion'])) {
                $fecha_creacion = $_POST['fecha_creacion']; // Se guarda directamente, ya está en formato correcto
            } else {
                $fecha_creacion = date('Y-m-d'); // Si no se envía, usa la fecha actual como predeterminado
            }
            $fecha_inicio_cierre = !empty($_POST['fecha_inicio_cierre']) ? $_POST['fecha_inicio_cierre'] : null;
            $fecha_fin_cierre = !empty($_POST['fecha_fin_cierre']) ? $_POST['fecha_fin_cierre'] : null;
            $recurrente = isset($_POST['recurrente']) ? 1 : 0;
            if ($ferrataModel->existeFerrataPorNombre($nombre)) {
                die("Error: Ya existe una ferrata con ese nombre.");
            }
            
            // Insertar la ferrata y obtener su ID
            $ferrata_id = $ferrataModel->agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente);
            
            if ($ferrata_id) {
                // Si el usuario es admin o moderador, redirigir a gestionar ferratas
                if (isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'moderador'])) {
                    header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
                    exit();
                }
                
                // En cualquier otro caso, redirigir a la página de ferratas
                header("Location: /RedFerratera/index.php?accion=ferratas");
                exit();
            }

            // Manejo de imágenes
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                        $directorio = "public/img/ferratas/";
                        $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                        $rutaDestino = $directorio . $nombreArchivo;
                        
                        if (move_uploaded_file($tmp_name, $rutaDestino)) {
                            echo "Imagen subida: $nombreArchivo <br>";
                            $imagenModel->agregarImagen($ferrata_id, $nombreArchivo);
                        } else {
                            echo "Error al mover la imagen: $nombreArchivo <br>";
                        }
                    } else {
                        echo "Error en la imagen {$key}: Código " . $_FILES['imagenes']['error'][$key] . "<br>";
                    }
                }
            } else {
                echo "No se recibieron imágenes o la entrada estaba vacía.<br>";
            }
            
            exit(); // DETENER EJECUCIÓN AQUÍ PARA VER TODO EL DEBUG
        } else {
            include __DIR__ . '/../views/agregar_ferrata.php';
        }
    }

    // Mostrar las ferratas nuevas (último mes)
    public function nuevas() {
        $nuevasFerratas = $this->ferrata->obtenerNuevasFerratas();
        include __DIR__ . '/../views/nuevas_ferratas.php';
    }
    
    // Buscar ferratas para el filtro de búsqueda
    public function buscar() {
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        $ubicacion = $_GET['ubicacion'] ?? '';
        $dificultad = $_GET['dificultad'] ?? '';
        $comunidad = $_GET['comunidad'] ?? '';
        $provincia = $_GET['provincia'] ?? '';
        $estado = $_GET['estado'] ?? '';
        
        $ferratas = $ferrataModel->buscarFerratas($ubicacion, $dificultad, $comunidad, $provincia, $estado);
        
        // Si se encontraron ferratas, organizarlas
        $ferratasOrganizadas = [];
        foreach ($ferratas as $ferrata) {
            $comunidad = $ferrata['comunidad_autonoma'] ?? 'Sin comunidad';
            $provincia = $ferrata['provincia'] ?? 'Sin provincia';
            $ferratasOrganizadas[$comunidad][$provincia][] = $ferrata;
        }
        
        include __DIR__ . '/../views/ferratas.php';
    }
    
    // Buscar ferratas para la búsqueda del menú
    public function buscarGlobal() {
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        // Recoger el término de búsqueda desde el parámetro
        $termino = $_GET['buscar'] ?? '';
        
        // Llamar al método del modelo
        $ferratas = $ferrataModel->buscarFerratasGlobal($termino);

        include __DIR__ . '/../views/busqueda.php';
    }
    
    // Mostrar las ferratas cercanas
    public function obtenerFerratasCercanas($id) {
        require_once __DIR__ . '/../models/Ferrata.php';
        $ferrataModel = new Ferrata();
        
        // Obtener la ferrata actual
        $ferrata = $ferrataModel->obtenerFerrataPorId($id);
        
        if (!$ferrata || empty($ferrata['coordenadas'])) {
            return [];
        }
        
        // Extraer coordenadas
        list($lat, $lon) = explode(",", $ferrata['coordenadas']);
        
        // Obtener ferratas cercanas (radio de 50km)
        return $ferrataModel->obtenerFerratasCercanas(trim($lat), trim($lon));
    }
    
    public function verFerrata() {
        require_once __DIR__ . '/../models/Ferrata.php';
        require_once __DIR__ . '/../models/Comentario.php';
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $comentarioModel = new Comentario();
        $imagenModel = new Imagen();
        
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $ferrata = $ferrataModel->obtenerFerrataPorId($id);
            $comentarios = $comentarioModel->obtenerComentariosPorFerrata($id);
            $imagenes = $imagenModel->obtenerImagenesPorFerrata($id);
            if ($ferrata && !empty($ferrata['coordenadas'])) {
                list($lat, $lon) = explode(",", $ferrata['coordenadas']);
                $ferratasCercanas = $ferrataModel->obtenerFerratasCercanas(trim($lat), trim($lon), $id);
            } else {
                $ferratasCercanas = [];
            }
            // --- Actualización automática del estado por cierre recurrente ---
            if (!empty($ferrata['fecha_inicio_cierre']) && !empty($ferrata['fecha_fin_cierre'])) {
                // Convertir la bandera recurrente a booleano (0/1 a false/true)
                $recurrente = (bool)$ferrata['recurrente'];
                // Si la ferrata está en el periodo de cierre y su estado es 'Abierta'
                if (Ferrata::estaCerradaRecurrente($ferrata['fecha_inicio_cierre'], $ferrata['fecha_fin_cierre'], (bool)$ferrata['recurrente'])) {
                    if (strtolower($ferrata['estado']) === 'abierta') {
                        $ferrataModel->actualizarEstado($ferrata['id'], 'Cerrada');
                        $ferrata['estado'] = 'Cerrada';
                    }
                } else {
                    if (strtolower($ferrata['estado']) === 'cerrada') {
                        $ferrataModel->actualizarEstado($ferrata['id'], 'Abierta');
                        $ferrata['estado'] = 'Abierta';
                    }
                }
            }
        } else {
            $ferrata = null;
            $comentarios = [];
            $imagenes = [];
            $ferratasCercanas = [];
        }
        
        include __DIR__ . '/../views/ferrata_detalle.php';
    }
    
    public function agregarComentario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario'])) {
            $ferrata_id = $_POST['ferrata_id'] ?? null;
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $comentario = $_POST['comentario'] ?? '';
            
            if ($ferrata_id && $comentario) {
                require_once __DIR__ . '/../models/Comentario.php';
                $comentarioModel = new Comentario();
                if ($comentarioModel->agregarComentario($ferrata_id, $usuario_id, $comentario)) {
                    header("Location: index.php?accion=ver_ferrata&id=" . $_POST['ferrata_id']);
                    exit();
                } else {
                    echo "Error al agregar el comentario.";
                }
            } else {
                echo "Debes escribir un comentario.";
            }
        } else {
            echo "Debes iniciar sesión para comentar.";
        }
    }
    
    public function subirImagen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagenes'])) {
            require_once __DIR__ . '/../models/Imagen.php';
            $imagenModel = new Imagen();
            
            $ferrata_id = $_POST['ferrata_id'] ?? null;
            
            if ($ferrata_id && !empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                    $rutaDestino = "public/img/ferratas/" . $nombreArchivo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenModel->guardarImagen($ferrata_id, $nombreArchivo);
                    }
                }
            }
        }
        header("Location: index.php?accion=editar_ferrata&id=$ferrata_id");
        exit();
    }
    
    public function guardarWikiloc() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $wikiloc_embed = isset($_POST['wikiloc']) ? trim($_POST['wikiloc']) : '';
        
        if ($ferrata_id <= 0 || empty($wikiloc_embed)) {
            echo "Datos inválidos.";
            return;
        }
        
        require_once __DIR__ . '/../models/Wikiloc.php';
        $wikiloc = new Wikiloc($ferrata_id, $wikiloc_embed);
        
        if ($wikiloc->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el enlace de Wikiloc.";
        }
    }
    
    public function borrarWikiloc() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;
        if ($id <= 0) {
            echo "Datos inválidos.";
            return;
        }
        
        require_once __DIR__ . '/../models/Wikiloc.php';
        if (Wikiloc::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el enlace de Wikiloc.";
        }
    }
    
    public function subirVideo() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $video_embed = isset($_POST['video']) ? trim($_POST['video']) : '';
        
        if ($ferrata_id <= 0 || empty($video_embed)) {
            echo "Datos inválidos.";
            return;
        }
        
        require_once __DIR__ . '/../models/Video.php';
        $video = new Video($ferrata_id, $video_embed);
        
        if ($video->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el vídeo.";
        }
    }
    
    public function borrarVideo() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;
        if ($id <= 0) {
            echo "Datos inválidos.";
            return;
        }
        
        require_once __DIR__ . '/../models/Video.php';
        if (Video::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el vídeo.";
        }
    }
}
?>
