<?php
require_once __DIR__ . '/../models/Video.php';

class VideoController {
    private $video;

    public function __construct() {
        $this->video = new Video();
    }

    public function subirVideo() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }

        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $video_embed = isset($_POST['video']) ? trim($_POST['video']) : '';

        if ($ferrata_id <= 0 || empty($video_embed)) {
            echo "Datos inválidos.";
            return;
        }

        $video = new Video($ferrata_id, $video_embed);
        if ($video->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el vídeo.";
        }
    }

    public function borrarVideo() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;

        if ($id <= 0) {
            echo "Datos inválidos.";
            return;
        }

        if (Video::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el vídeo.";
        }
    }
} 
