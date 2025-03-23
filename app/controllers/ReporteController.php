<?php
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController {
    private $reporte;

    public function __construct() {
        $this->reporte = new Reporte();
    }

    // Mostrar todos los reportes
    public function index() {
        $reportes = $this->reporte->obtenerReportes();
        include __DIR__ . '/../views/reportes.php';
    }

    // Agregar un nuevo reporte
    public function agregar() {
        include __DIR__ . '/../views/agregar_reporte.php';
    }
    
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Iniciar sesión si aún no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Validación del token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            // Limpiar e interpretar datos
            $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : null;
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fecha_reporte = date('Y-m-d H:i:s');
            
            // Validaciones adicionales
            if (!$ferrata_id || !$usuario_id || empty($descripcion)) {
                die("Error: Datos incompletos.");
            }
            if (strlen($descripcion) < 5) {
                die("Error: La descripción del reporte debe tener al menos 5 caracteres.");
            }
            
            // Cargar modelo y guardar
            require_once __DIR__ . '/../models/Reporte.php';
            $reporte = new Reporte();
            
            if ($reporte->agregarReporte($ferrata_id, $usuario_id, $descripcion, $fecha_reporte)) {
                unset($_SESSION['csrf_token']); // ✅ Eliminar token una vez usado
                
                // Redirección según el rol del usuario
                if (isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'moderador'])) {
                    header("Location: /RedFerratera/index.php?accion=gestionar_ferratas");
                } else {
                    header("Location: /RedFerratera/index.php?accion=reportes");
                }
                exit();
            } else {
                die("Error al guardar el reporte.");
            }
        } else {
            die("No se recibió una solicitud POST.");
        }
    }
}
?>
