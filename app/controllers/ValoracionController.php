<?php
require_once 'app/models/Valoracion.php';

class ValoracionController {
    private $valoracion;
    
    public function __construct() {
        $this->valoracion = new Valoracion();
    }
    
    public function guardar() {
        // Iniciar sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar buffer de salida
        if (!ob_get_length()) {
            ob_start();
        } else {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        // Validar sesión
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario']['id'];
        
        // Validar usuario verificado
        if ($_SESSION['usuario']['verificado'] != 1) {
            echo json_encode(['error' => 'Usuario no verificado']);
            return;
        }
        
        // Validar datos POST
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $valor = isset($_POST['valor']) ? intval($_POST['valor']) : 0;
        
        if ($ferrata_id <= 0 || $valor < 1 || $valor > 5) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Guardar valoración
        if ($this->valoracion->save($ferrata_id, $usuario_id, $valor)) {
            $avgData = Valoracion::getAverageRating($ferrata_id);
            echo json_encode([
                'success' => true,
                'promedio' => round($avgData['promedio'], 2),
                'total' => $avgData['total']
            ]);
        } else {
            echo json_encode(['error' => 'Error al guardar la valoración']);
        }
    }
}
