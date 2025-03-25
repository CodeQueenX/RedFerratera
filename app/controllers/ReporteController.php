<?php
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController {
    private $reporte;
    
    public function __construct() {
        $this->reporte = new Reporte();
    }
    
    // Mostrar todos los reportes pendientes
    public function index() {
        $reportes = $this->reporte->obtenerReportes();
        include __DIR__ . '/../views/reportes.php';
    }
    
    // Cargar formulario para agregar reporte
    public function agregar() {
        include __DIR__ . '/../views/agregar_reporte.php';
    }
    
    // Guardar un nuevo reporte
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            // Obtener datos del formulario
            $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : null;
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fecha_reporte = date('Y-m-d H:i:s');
            
            // Validar campos requeridos
            if (!$ferrata_id || !$usuario_id || empty($descripcion)) {
                die("Error: Datos incompletos.");
            }
            
            // Validar longitud mínima de la descripción
            if (strlen($descripcion) < 5) {
                die("Error: La descripción del reporte debe tener al menos 5 caracteres.");
            }
            
            // Guardar reporte
            if ($this->reporte->agregarReporte($ferrata_id, $usuario_id, $descripcion, $fecha_reporte)) {
                unset($_SESSION['csrf_token']); // Limpiar token tras uso
                
                // Redirigir según rol del usuario
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
