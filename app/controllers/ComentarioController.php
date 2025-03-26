<?php
require_once __DIR__ . '/../models/Comentario.php';

class ComentarioController {
    private $comentarioModel;
    
    public function __construct() {
        $this->comentarioModel = new Comentario();
    }
    
    // Agregar un nuevo comentario
    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die('Error: Token CSRF inválido o ausente.');
            }
            
            unset($_SESSION['csrf_token']);
            
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
    
    // Eliminar un comentario por su ID
    public function eliminar() {
        if (isset($_GET['id']) && isset($_GET['ferrata_id'])) {
            $id = $_GET['id'];
            $ferrata_id = $_GET['ferrata_id'];
            
            if ($this->comentarioModel->eliminarComentario($id)) {
                header("Location: /RedFerratera/index.php?accion=ver_ferrata&id=" . $ferrata_id, true, 303);
                exit();
            } else {
                echo "Error al eliminar el comentario.";
            }
        } else {
            echo "Faltan datos para eliminar el comentario.";
        }
    }
    
    // Editar un comentario existente
    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comentario_id = $_POST['comentario_id'] ?? null;
            $nuevo_comentario = $_POST['comentario'] ?? '';
            
            if ($comentario_id && !empty($nuevo_comentario)) {
                $comentarioExistente = $this->comentarioModel->obtenerComentarioPorId($comentario_id);
                
                if ($comentarioExistente && $_SESSION['usuario']['id'] == $comentarioExistente['usuario_id']) {
                    if ($this->comentarioModel->actualizarComentario($comentario_id, $nuevo_comentario)) {
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
