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
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Verificación de token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            // Limpieza del token tras su uso
            unset($_SESSION['csrf_token']);
            
            // Validación básica
            $nombre = trim($_POST['nombre'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
            $provincia = $_POST['provincia'] ?? '';
            $dificultad = $_POST['dificultad'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $coordenadas = isset($_POST['coordenadas']) && $_POST['coordenadas'] !== '' ? $_POST['coordenadas'] : null;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? $_POST['estado'] : 'Pendiente';
            $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d');
            $fecha_inicio_cierre = !empty($_POST['fecha_inicio_cierre']) ? $_POST['fecha_inicio_cierre'] : null;
            $fecha_fin_cierre = !empty($_POST['fecha_fin_cierre']) ? $_POST['fecha_fin_cierre'] : null;
            $recurrente = isset($_POST['recurrente']) ? 1 : 0;
            
            // Comprobar duplicados
            if ($ferrataModel->existeFerrataPorNombre($nombre)) {
                die("Error: Ya existe una ferrata con ese nombre.");
            }
            
            // Insertar ferrata
            $ferrata_id = $ferrataModel->agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente);
            
            // Guardar imágenes
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                        $directorio = "public/img/ferratas/";
                        $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                        $rutaDestino = $directorio . $nombreArchivo;
                        
                        if (move_uploaded_file($tmp_name, $rutaDestino)) {
                            $imagenModel->agregarImagen($ferrata_id, $nombreArchivo);
                        }
                    }
                }
            }
            
            // Redirección según rol
            if (isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'moderador'])) {
                header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            } else {
                header("Location: /RedFerratera/index.php?accion=ferratas");
            }
            exit();
            
        } else {
            // Carga del formulario
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
}
?>
