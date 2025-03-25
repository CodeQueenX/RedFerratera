<?php
require_once 'app/models/Valoracion.php';

class ValoracionController {
    private $valoracion;
    
    public function __construct() {
        $this->valoracion = new Valoracion();
    }
    
    // Guardar o actualizar valoración de una ferrata
    public function guardar() {
        // Iniciar sesión si es necesario
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar el buffer de salida para enviar JSON correctamente
        if (!ob_get_length()) {
            ob_start();
        } else {
            ob_clean();
        }
        
        // Cabecera para respuesta JSON
        header('Content-Type: application/json');
        
        // Verificar que el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario']['id'];
        
        // Verificar que el usuario está verificado
        if ($_SESSION['usuario']['verificado'] != 1) {
            echo json_encode(['error' => 'Usuario no verificado']);
            return;
        }
        
        // Obtener y validar los datos del POST
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $valor = isset($_POST['valor']) ? intval($_POST['valor']) : 0;
        
        if ($ferrata_id <= 0 || $valor < 1 || $valor > 5) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Guardar valoración en la base de datos
        if ($this->valoracion->save($ferrata_id, $usuario_id, $valor)) {
            // Obtener media y total actualizados tras guardar
            $valoracion = new Valoracion();
            $avgData = $valoracion->getAverageRating($ferrata_id);
            
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
