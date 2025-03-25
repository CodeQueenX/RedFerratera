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
        
        // Obtener listado organizado por comunidad y provincia
        $ferratasOrganizadas = $ferrataModel->obtenerFerratasOrganizadas();
        
        // Actualizar estado de cada ferrata en base a fechas de cierre
        foreach ($ferratasOrganizadas as $comunidad => $ferratasPorComunidad) {
            foreach ($ferratasPorComunidad as $provincia => $ferratas) {
                for ($i = 0; $i < count($ferratas); $i++) {
                    $ferrata = $ferratas[$i];
                    
                    if (!empty($ferrata['fecha_inicio_cierre']) && !empty($ferrata['fecha_fin_cierre'])) {
                        $recurrente = (bool)$ferrata['recurrente'];
                        
                        if (Ferrata::estaCerradaRecurrente($ferrata['fecha_inicio_cierre'], $ferrata['fecha_fin_cierre'], $recurrente)) {
                            if (strtolower($ferrata['estado']) === 'abierta') {
                                $ferrataModel->actualizarEstado($ferrata['id'], 'Cerrada');
                                $ferratasOrganizadas[$comunidad][$provincia][$i]['estado'] = 'Cerrada';
                            }
                        } else {
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
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            unset($_SESSION['csrf_token']);
            
            // Recoger y validar datos del formulario
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
            
            if ($ferrataModel->existeFerrataPorNombre($nombre)) {
                die("Error: Ya existe una ferrata con ese nombre.");
            }
            
            // Insertar ferrata
            $ferrata_id = $ferrataModel->agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion, $fecha_inicio_cierre, $fecha_fin_cierre, $recurrente);
            
            // Guardar imágenes si hay
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                        $directorio = "public/img/ferratas/";
                        $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                        $rutaDestino = $directorio . $nombreArchivo;
                        
                        if (move_uploaded_file($tmp_name, $rutaDestino)) {
                            $imagenModel->guardarImagen($ferrata_id, $nombreArchivo);
                        }
                    }
                }
            }
            
            // Redirigir según rol
            if (isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'moderador'])) {
                header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
            } else {
                header("Location: /RedFerratera/index.php?accion=ferratas");
            }
            exit();
        } else {
            include __DIR__ . '/../views/agregar_ferrata.php';
        }
    }
    
    // Mostrar ferratas nuevas (último mes)
    public function nuevas() {
        $nuevasFerratas = $this->ferrata->obtenerNuevasFerratas();
        include __DIR__ . '/../views/nuevas_ferratas.php';
    }
    
    // Buscar ferratas con filtros del formulario
    public function buscar() {
        $ferrataModel = new Ferrata();
        
        $ubicacion = $_GET['ubicacion'] ?? '';
        $dificultad = $_GET['dificultad'] ?? '';
        $comunidad = $_GET['comunidad'] ?? '';
        $provincia = $_GET['provincia'] ?? '';
        $estado = $_GET['estado'] ?? '';
        
        $ferratas = $ferrataModel->buscarFerratas($ubicacion, $dificultad, $comunidad, $provincia, $estado);
        
        $ferratasOrganizadas = [];
        foreach ($ferratas as $ferrata) {
            $comunidad = $ferrata['comunidad_autonoma'] ?? 'Sin comunidad';
            $provincia = $ferrata['provincia'] ?? 'Sin provincia';
            $ferratasOrganizadas[$comunidad][$provincia][] = $ferrata;
        }
        
        include __DIR__ . '/../views/ferratas.php';
    }
    
    // Buscar ferratas desde la barra de búsqueda global
    public function buscarGlobal() {
        $ferrataModel = new Ferrata();
        $termino = $_GET['buscar'] ?? '';
        $ferratas = $ferrataModel->buscarFerratasGlobal($termino);
        include __DIR__ . '/../views/busqueda.php';
    }
    
    // Obtener ferratas cercanas a una en base a coordenadas
    public function obtenerFerratasCercanas($id) {
        $ferrataModel = new Ferrata();
        $ferrata = $ferrataModel->obtenerFerrataPorId($id);
        
        if (!$ferrata || empty($ferrata['coordenadas'])) {
            return [];
        }
        
        list($lat, $lon) = explode(",", $ferrata['coordenadas']);
        return $ferrataModel->obtenerFerratasCercanas(trim($lat), trim($lon));
    }
    
    // Ver detalle de una ferrata
    public function verFerrata() {
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
            
            if (!empty($ferrata['fecha_inicio_cierre']) && !empty($ferrata['fecha_fin_cierre'])) {
                $recurrente = (bool)$ferrata['recurrente'];
                if (Ferrata::estaCerradaRecurrente($ferrata['fecha_inicio_cierre'], $ferrata['fecha_fin_cierre'], $recurrente)) {
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
