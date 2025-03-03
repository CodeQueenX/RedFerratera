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
        
        // LLamar directamente a obtenerFerratasOrganizadas()
        $ferratasOrganizadas = $ferrataModel->obtenerFerratasOrganizadas();
        
        // Incluir la vista
        include __DIR__ . '/../views/ferratas.php';
    }


    // Agregar una nueva ferrata
    public function agregar() {
        require_once __DIR__ . '/../models/Ferrata.php';
        require_once __DIR__ . '/../models/Imagen.php';
        
        $ferrataModel = new Ferrata();
        $imagenModel = new Imagen();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nombre = $_POST['nombre'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            $comunidad_autonoma = $_POST['comunidad_autonoma'] ?? '';
            $provincia = $_POST['provincia'] ?? '';
            $dificultad = $_POST['dificultad'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $coordenadas = isset($_POST['coordenadas']) && $_POST['coordenadas'] !== '' ? $_POST['coordenadas'] : null;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? $_POST['estado'] : 'Pendiente';
            $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d');
            
            echo "<pre>🔍 Datos recibidos para insertar:\n";
            print_r([
                'nombre' => $nombre,
                'ubicacion' => $ubicacion,
                'comunidad_autonoma' => $comunidad_autonoma,
                'provincia' => $provincia,
                'dificultad' => $dificultad,
                'descripcion' => $descripcion,
                'coordenadas' => $coordenadas,
                'estado' => $estado,
                'fecha_creacion' => $fecha_creacion,
            ]);
            echo "</pre>";
            
            // Insertar la ferrata y obtener su ID
            $ferrata_id = $ferrataModel->agregarFerrata($nombre, $ubicacion, $comunidad_autonoma, $provincia, $dificultad, $descripcion, $coordenadas, $estado, $fecha_creacion);
            
            if ($ferrata_id) {
                // ✅ Si es un usuario normal, redirigir a inicio
                if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                    header("Location: /RedFerratera/index.php");
                    exit();
                }
                
                // ✅ Si es un administrador, redirigir a gestionar ferratas
                header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
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
                            echo "✔ Imagen subida: $nombreArchivo <br>";
                            $imagenModel->agregarImagen($ferrata_id, $nombreArchivo);
                        } else {
                            echo "❌ Error al mover la imagen: $nombreArchivo <br>";
                        }
                    } else {
                        echo "⚠ Error en la imagen {$key}: Código " . $_FILES['imagenes']['error'][$key] . "<br>";
                    }
                }
            } else {
                echo "⚠ No se recibieron imágenes o la entrada estaba vacía.<br>";
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
        header("Location: index.php?accion=ver_ferrata&id=$ferrata_id");
        exit();
    }
}
?>
