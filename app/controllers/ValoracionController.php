<?php

require_once 'app/models/Valoracion.php';

class ValoracionController {
    // Método para guardar la valoración recibida por POST
    public function guardar() {
        // Iniciar sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Iniciar el buffer de salida para evitar salidas inesperadas
        if (!ob_get_length()) {
            ob_start();
        } else {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        // Verificar que el usuario esté en la sesión
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }
        
        // Asignar el ID del usuario de la sesión
        $usuario_id = $_SESSION['usuario']['id'];
        
        // Verificar el usuario
        if ($_SESSION['usuario']['verificado'] != 1) {
            echo json_encode(['error' => 'Usuario no verificado']);
            return;
        }
        
        // Recoger y validar datos (ferrata_id y valor)
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $valor = isset($_POST['valor']) ? intval($_POST['valor']) : 0;
        if ($ferrata_id <= 0 || $valor < 1 || $valor > 5) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        $rating = new Valoracion($ferrata_id, $usuario_id, $valor);
        
        if ($rating->save()) {
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
?>
