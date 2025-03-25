<?php
require_once __DIR__ . '/../models/Video.php';

class VideoController {
    private $video;
    
    public function __construct() {
        // Crear instancia del modelo Video
        $this->video = new Video();
    }
    
    // Subir un nuevo vídeo a una ferrata
    public function subirVideo() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar si el usuario tiene permisos de admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        // Obtener datos del formulario
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $video_embed = isset($_POST['video']) ? trim($_POST['video']) : '';
        
        // Validar datos
        if ($ferrata_id <= 0 || empty($video_embed)) {
            echo "Datos inválidos.";
            return;
        }
        
        // Crear nueva instancia de Video y guardar
        $video = new Video($ferrata_id, $video_embed);
        if ($video->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el vídeo.";
        }
    }
    
    // Borrar un vídeo por ID
    public function borrarVideo() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar permisos de administrador
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        // Obtener ID del vídeo y de la ferrata
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;
        
        // Validar ID
        if ($id <= 0) {
            echo "Datos inválidos.";
            return;
        }
        
        // Eliminar el vídeo
        if (Video::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el vídeo.";
        }
    }
}
