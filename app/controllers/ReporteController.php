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
            $ferrata_id = $_POST['ferrata_id'] ?? null;
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $descripcion = $_POST['descripcion'] ?? null;
            $fecha_reporte = date('Y-m-d H:i:s'); // Se asigna la fecha y hora actual automáticamente
            
            if (!$ferrata_id || !$usuario_id || !$descripcion) {
                die("Error: Datos incompletos.");
            }
            
            require_once __DIR__ . '/../models/Reporte.php';
            $reporte = new Reporte();
            
            if ($reporte->agregarReporte($ferrata_id, $usuario_id, $descripcion, $fecha_reporte)) {
                // Redirige según el rol del usuario:
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
