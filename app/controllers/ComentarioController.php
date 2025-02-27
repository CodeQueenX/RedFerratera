<?php
require_once __DIR__ . '/../models/Comentario.php';

class ComentarioController {
    private $comentarioModel;

    public function __construct() {
        $this->comentarioModel = new Comentario();
    }

    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ferrata_id = $_POST['ferrata_id'] ?? null;
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $comentario = $_POST['comentario'] ?? '';

            if ($ferrata_id && $usuario_id && $comentario) {
                if ($this->comentarioModel->agregarComentario($ferrata_id, $usuario_id, $comentario)) {
                    header("Location: index.php?accion=ver_ferrata&id=$ferrata_id");
                    exit();
                } else {
                    echo "Error al agregar el comentario.";
                }
            } else {
                echo "Faltan datos obligatorios.";
            }
        }
    }
    
    public function eliminar() {
        if (isset($_GET['id']) && isset($_GET['ferrata_id'])) {
            $id = $_GET['id'];
            $ferrata_id = $_GET['ferrata_id'];
            
            require_once __DIR__ . '/../models/Comentario.php';
            $comentarioModel = new Comentario();
            
            if ($comentarioModel->eliminarComentario($id)) {
                header("Location: index.php?accion=ver_ferrata&id=$ferrata_id");
                exit();
            } else {
                echo "Error al eliminar el comentario.";
            }
        } else {
            echo "Faltan datos para eliminar el comentario.";
        }
    }
    
    public function editar() {
        require_once __DIR__ . '/../models/Comentario.php';
        $comentarioModel = new Comentario();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comentario_id = $_POST['comentario_id'] ?? null;
            $nuevo_comentario = $_POST['comentario'] ?? '';
            
            if ($comentario_id && !empty($nuevo_comentario)) {
                $comentarioExistente = $comentarioModel->obtenerComentarioPorId($comentario_id);
                
                if ($comentarioExistente && $_SESSION['usuario']['id'] == $comentarioExistente['usuario_id']) {
                    if ($comentarioModel->actualizarComentario($comentario_id, $nuevo_comentario)) {
                        header("Location: index.php?accion=ver_ferrata&id=" . $_POST['ferrata_id']);
                        exit();
                    } else {
                        echo "Error al actualizar el comentario.";
                    }
                } else {
                    echo "No tienes permiso para editar este comentario.";
                }
            } else {
                echo "Faltan datos para editar el comentario.";
            }
        }
    }
}
?>
